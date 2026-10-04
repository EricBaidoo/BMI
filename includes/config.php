<?php
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/paths.php';
require_once __DIR__ . '/client_ip.php';

// Site identity
$siteName = 'Bridge Ministries International';
$siteTagline = 'Faith, Fellowship, and Service';
$siteDescription = 'Bridge Ministries International is a Bible-believing church family in Accra, Ghana, helping people know Christ, grow in faith, and live on mission.';
$siteUrl = rtrim((string) env('APP_URL', 'http://localhost/BMI'), '/');

// Database
$dbHost = (string) env('DB_HOST', 'localhost');
$dbPort = (int) env('DB_PORT', 3306);
$dbName = (string) env('DB_NAME', 'church_website');
$dbUser = (string) env('DB_USER', 'root');
$dbPass = (string) env('DB_PASS', '');

// Church time zone: every time entered in the admin (services, livestream, events) is in this zone.
// Ghana is GMT all year. Visitors elsewhere see their own local time next to it.
$appTimezone = (string) env('APP_TIMEZONE', 'Africa/Accra');
date_default_timezone_set(in_array($appTimezone, timezone_identifiers_list(), true) ? $appTimezone : 'Africa/Accra');

// Application
$appEnv = (string) env('APP_ENV', 'production');
$appDebug = (bool) env('APP_DEBUG', false);
$appSecret = (string) env('APP_SECRET', 'change-me');

// Integrations
$paystackPublicKey = (string) env('PAYSTACK_PUBLIC_KEY', '');
$analyticsDomain = (string) env('ANALYTICS_DOMAIN', '');

// Mail
$mailFrom = (string) env('MAIL_FROM', 'no-reply@example.com');
$mailTo = (string) env('MAIL_TO', $mailFrom);

if ($appDebug) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// Global Security Headers (and don't announce the PHP version)
if (!headers_sent()) {
    header_remove('X-Powered-By');
}
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-XSS-Protection: 1; mode=block');
}

// Global Exception Handler
set_exception_handler(function (Throwable $e) use ($appDebug) {
    if (!headers_sent()) {
        http_response_code(500);
    }
    
    // Always log the error securely
    require_once __DIR__ . '/logger.php';
    log_exception($e, 'uncaught');

    if ($appDebug) {
        echo "<div style='border:1px solid red; padding:20px; background:#fdd; font-family:monospace; margin:20px;'>";
        echo "<h3>Uncaught Exception</h3>";
        echo nl2br(htmlspecialchars((string)$e));
        echo "</div>";
    } else {
        echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>500 - Internal Server Error</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-slate-50 flex items-center justify-center h-screen'><div class='max-w-md text-center p-8 bg-white rounded-2xl shadow-xl'><h1 class='text-4xl font-bold text-slate-800 mb-4'>Oops!</h1><p class='text-slate-600 mb-6'>Something went wrong on our servers. We've logged the error and our team is looking into it.</p><a href='/' class='inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors'>Return Home</a></div></body></html>";
    }
    exit;
});
