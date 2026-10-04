<?php
/**
 * Sending email.
 *
 * MAIL_TRANSPORT in .env chooses how:
 *   smtp  authenticated SMTP via PHPMailer (recommended in production: Hostinger email,
 *         Google Workspace, Brevo…). Needs MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD,
 *         MAIL_ENCRYPTION (tls or ssl).
 *   mail  PHP's built-in mail() through the server (works on some shared hosts, often lands in spam)
 *   log   nothing is sent; each message is written to logs/mail-YYYY-MM-DD.log (local development)
 * MAIL_FROM / MAIL_FROM_NAME set the sender. Always send from an address on your own domain.
 */
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/paths.php';
require_once __DIR__ . '/logger.php';

if (is_file(APP_ROOT . '/vendor/autoload.php')) {
    require_once APP_ROOT . '/vendor/autoload.php';
}

/**
 * @param string|string[] $to
 * @param array{html?: bool, reply_to?: string, reply_name?: string} $options
 */
function send_mail($to, string $subject, string $body, array $options = []): bool
{
    $recipients = array_values(array_filter(array_map('trim', (array) $to), fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
    if (!$recipients) {
        return false;
    }
    // Header injection guard: subjects and names must be a single line.
    $subject = trim(preg_replace('/[\r\n]+/', ' ', $subject));
    $html = !empty($options['html']);
    $replyTo = filter_var((string) ($options['reply_to'] ?? ''), FILTER_VALIDATE_EMAIL) ?: '';
    $replyName = trim(preg_replace('/[\r\n]+/', ' ', (string) ($options['reply_name'] ?? '')));

    $host = parse_url((string) env('APP_URL', 'https://bmiglobal.org'), PHP_URL_HOST) ?: 'bmiglobal.org';
    $from = filter_var((string) env('MAIL_FROM', ''), FILTER_VALIDATE_EMAIL) ?: 'no-reply@' . $host;
    $fromName = (string) env('MAIL_FROM_NAME', 'Bridge Ministries International');
    $transport = strtolower((string) env('MAIL_TRANSPORT', env('MAIL_HOST', '') !== '' ? 'smtp' : 'mail'));

    if ($transport === 'log') {
        $entry = sprintf("==== %s\nFrom: %s <%s>\nTo: %s\n%sSubject: %s\nContent-Type: %s\n\n%s\n\n",
            date('Y-m-d H:i:s'), $fromName, $from, implode(', ', $recipients),
            $replyTo !== '' ? "Reply-To: {$replyTo}\n" : '', $subject, $html ? 'text/html' : 'text/plain', $body);
        log_mail_write($entry);
        return true;
    }

    if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
        log_message('error', 'PHPMailer is not installed (run composer install). Falling back to mail().');
        $transport = 'mail';
    }

    try {
        if ($transport === 'smtp' || $transport === 'mail') {
            if (class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->CharSet = 'UTF-8';
                if ($transport === 'smtp') {
                    $mail->isSMTP();
                    $mail->Host = (string) env('MAIL_HOST', '');
                    $mail->Port = (int) env('MAIL_PORT', 587);
                    $mail->SMTPAuth = env('MAIL_USERNAME', '') !== '';
                    $mail->Username = (string) env('MAIL_USERNAME', '');
                    $mail->Password = (string) env('MAIL_PASSWORD', '');
                    $encryption = strtolower((string) env('MAIL_ENCRYPTION', 'tls'));
                    if ($encryption === 'none') {
                        // Only for a local test mail server; never use on the internet.
                        $mail->SMTPSecure = '';
                        $mail->SMTPAutoTLS = false;
                    } else {
                        $mail->SMTPSecure = $encryption === 'ssl' ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    }
                    $mail->Timeout = 15;
                } else {
                    $mail->isMail();
                }
                $mail->setFrom($from, $fromName);
                foreach ($recipients as $r) {
                    $mail->addAddress($r);
                }
                if ($replyTo !== '') {
                    $mail->addReplyTo($replyTo, $replyName);
                }
                $mail->Subject = $subject;
                if ($html) {
                    $mail->isHTML(true);
                    $mail->Body = $body;
                    $mail->AltBody = trim(html_entity_decode(strip_tags(preg_replace('#<br\s*/?>|</p>#i', "\n", $body)), ENT_QUOTES, 'UTF-8'));
                } else {
                    $mail->Body = $body;
                }
                return $mail->send();
            }

            // Last resort when vendor/ is missing.
            $headers = "From: {$fromName} <{$from}>\r\n" . ($replyTo !== '' ? "Reply-To: {$replyTo}\r\n" : '')
                . 'Content-Type: ' . ($html ? 'text/html' : 'text/plain') . "; charset=UTF-8\r\nMIME-Version: 1.0\r\n";
            return @mail(implode(',', $recipients), $subject, $body, $headers);
        }
        log_message('error', "Unknown MAIL_TRANSPORT '{$transport}'.");
        return false;
    } catch (Throwable $e) {
        // Don't use log_exception here: its alert email could fail the same way and loop.
        log_message('error', 'Email to ' . implode(', ', $recipients) . " failed ({$subject}): " . $e->getMessage());
        return false;
    }
}

function log_mail_write(string $entry): void
{
    $dir = APP_ROOT . '/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0750, true);
    }
    @file_put_contents($dir . '/mail-' . date('Y-m-d') . '.log', $entry, FILE_APPEND | LOCK_EX);
}
