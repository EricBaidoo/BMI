<?php
require_once __DIR__ . '/env.php';

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
