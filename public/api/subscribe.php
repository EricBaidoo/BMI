<?php
/**
 * Footer newsletter sign-up. Saves the address to the admin Inbox (type "newsletter")
 * so staff can add it to the church's mailing service.
 *
 * No session is started (public pages stay cacheable), so instead of a CSRF token the request
 * must come from this site (Origin/Referer check), pass a hidden honeypot field, and respect
 * a per-IP rate limit.
 */
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/settings.php';

$back = function (string $status) {
    $ref = (string) ($_SERVER['HTTP_REFERER'] ?? '');
    $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
    $path = '../';
    if ($ref !== '' && parse_url($ref, PHP_URL_HOST) === parse_url('http://' . $host, PHP_URL_HOST)) {
        $path = (string) parse_url($ref, PHP_URL_PATH);
        parse_str((string) parse_url($ref, PHP_URL_QUERY), $query);
        unset($query['subscribed']);
        $query['subscribed'] = $status;
        $path .= '?' . http_build_query($query);
    } else {
        $path .= '?subscribed=' . urlencode($status);
    }
    header('Location: ' . $path . '#newsletter', true, 303);
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $back('error');
}

// Same-site check: Origin (or Referer) must match this host.
$origin = (string) ($_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '');
$host = strtolower((string) parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST));
if ($origin === '' || strtolower((string) parse_url($origin, PHP_URL_HOST)) !== $host) {
    http_response_code(403);
    exit('Sign-up must come from this website.');
}

if (trim((string) ($_POST['website'] ?? '')) !== '') {
    $back('ok'); // honeypot filled: pretend success
}

$email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
if (!$email || strlen($email) > 150) {
    $back('invalid');
}

// Rate limit: 5 sign-ups per IP per hour.
$limitFile = sys_get_temp_dir() . '/bmi_subscribe_ratelimit.json';
$fh = fopen($limitFile, 'c+');
flock($fh, LOCK_EX);
$log = json_decode(stream_get_contents($fh) ?: '[]', true) ?: [];
$now = time();
$ip = client_ip();
$log = array_values(array_filter($log, fn ($e) => $e['t'] > $now - 3600));
$limited = count(array_filter($log, fn ($e) => $e['ip'] === $ip)) >= 5;
if (!$limited) {
    $log[] = ['t' => $now, 'ip' => $ip];
    ftruncate($fh, 0);
    rewind($fh);
    fwrite($fh, json_encode($log));
}
flock($fh, LOCK_UN);
fclose($fh);
if ($limited) {
    $back('limit');
}

try {
    $pdo = db_connect();
    $exists = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE type = 'newsletter' AND email = :e");
    $exists->execute([':e' => strtolower($email)]);
    if ((int) $exists->fetchColumn() === 0) {
        $pdo->prepare("INSERT INTO messages (full_name, email, subject, message, type) VALUES ('Newsletter subscriber', :e, 'Newsletter sign-up', :m, 'newsletter')")
            ->execute([':e' => strtolower($email), ':m' => 'Signed up from ' . (string) parse_url((string) ($_SERVER['HTTP_REFERER'] ?? ''), PHP_URL_PATH)]);
    }
    $back('ok');
} catch (Throwable $e) {
    log_exception($e, 'subscribe');
    $back('error');
}
