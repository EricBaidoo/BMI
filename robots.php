<?php
/**
 * robots.txt (Apache rewrites /robots.txt here) so the sitemap address is always absolute
 * and matches APP_URL on each server.
 */
require_once __DIR__ . '/includes/config.php';

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: public, max-age=86400');
$base = rtrim($siteUrl, '/');
$path = rtrim((string) parse_url($base, PHP_URL_PATH), '/');

echo "User-agent: *\n";
echo "Allow: /\n";
foreach (['admin/', 'api/', 'database/', 'includes/', 'bin/', 'logs/'] as $dir) {
    echo "Disallow: {$path}/{$dir}\n";
}
echo "\nSitemap: {$base}/sitemap.xml\n";
