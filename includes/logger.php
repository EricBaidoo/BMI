<?php
/**
 * Application error logging.
 *
 * - Writes one line per error to logs/app-YYYY-MM-DD.log (the logs/ folder is never served).
 * - Also forwards to PHP's error_log so hosting control panels still see it.
 * - When ALERT_EMAIL is set in .env, emails a short alert at most once per hour.
 */
require_once __DIR__ . '/env.php';

if (!function_exists('log_exception')) {
    function log_exception(Throwable $e, string $context = ''): void
    {
        $line = sprintf(
            "[%s] %s%s: %s in %s:%d | %s %s\n%s\n",
            date('Y-m-d H:i:s'),
            $context !== '' ? $context . ' | ' : '',
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            $_SERVER['REQUEST_URI'] ?? '',
            $e->getTraceAsString()
        );
        for ($prev = $e->getPrevious(); $prev; $prev = $prev->getPrevious()) {
            $line .= sprintf("  Caused by %s: %s in %s:%d\n", get_class($prev), $prev->getMessage(), $prev->getFile(), $prev->getLine());
        }
        log_write($line);
        error_log(rtrim($line));
        log_alert($e, $context);
    }

    function log_message(string $level, string $message): void
    {
        log_write(sprintf("[%s] %s: %s\n", date('Y-m-d H:i:s'), strtoupper($level), $message));
    }

    function log_write(string $line): void
    {
        $dir = dirname(__DIR__) . '/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0750, true);
        }
        if (!is_file($dir . '/.htaccess')) {
            @file_put_contents($dir . '/.htaccess', "Require all denied\n");
        }
        @file_put_contents($dir . '/app-' . date('Y-m-d') . '.log', $line, FILE_APPEND | LOCK_EX);
    }

    function log_alert(Throwable $e, string $context): void
    {
        $to = (string) env('ALERT_EMAIL', '');
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        $stamp = sys_get_temp_dir() . '/bmi_last_alert';
        if (is_file($stamp) && (time() - (int) @filemtime($stamp)) < 3600) {
            return;
        }
        @touch($stamp);
        $host = parse_url((string) env('APP_URL', 'https://bmiglobal.org'), PHP_URL_HOST) ?: 'bmiglobal.org';
        $body = "An error occurred on {$host}.\n\n"
            . ($context !== '' ? "Where: {$context}\n" : '')
            . 'Error: ' . get_class($e) . ': ' . $e->getMessage() . "\n"
            . 'URL: ' . ($_SERVER['REQUEST_URI'] ?? 'CLI') . "\n\n"
            . "Full details are in logs/app-" . date('Y-m-d') . ".log on the server.\n"
            . "Further alerts are paused for one hour.";
        @mail($to, "[{$host}] Website error", $body, "From: no-reply@{$host}\r\n");
    }

    /**
     * Message that is safe to show to staff for a caught exception.
     * Validation messages (RuntimeException that is not a database error) are shown as written;
     * anything else is logged and replaced with a generic message.
     */
    function user_error_message(Throwable $e, string $fallback = 'Something went wrong. Please try again.'): string
    {
        if ($e instanceof RuntimeException && !($e instanceof PDOException) && $e->getPrevious() === null) {
            return $e->getMessage();
        }
        log_exception($e);
        return $fallback;
    }
}
