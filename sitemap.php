<?php
/**
 * Dynamic XML sitemap. Apache rewrites /sitemap.xml -> /sitemap.php.
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/xml; charset=utf-8');

$base = rtrim($siteUrl, '/');
$urls = [
    ['/',           '1.0', 'weekly'],
    ['/about',      '0.8', 'monthly'],
    ['/beliefs',    '0.7', 'yearly'],
    ['/visit',      '0.9', 'monthly'],
    ['/sermons',    '0.9', 'weekly'],
    ['/events',     '0.9', 'weekly'],
    ['/ministries', '0.7', 'monthly'],
    ['/livestream', '0.7', 'weekly'],
    ['/blog',       '0.7', 'weekly'],
    ['/donate',     '0.5', 'monthly'],
    ['/contact',    '0.5', 'monthly'],
];

try {
    $pdo = db_connect();
    $rows = $pdo->query("SELECT slug, GREATEST(COALESCE(published_at, '1970-01-01'), created_at) AS lastmod
                         FROM posts WHERE published_at IS NOT NULL ORDER BY published_at DESC LIMIT 500")->fetchAll();
    foreach ($rows as $r) {
        $urls[] = ['/blog?post=' . rawurlencode((string) $r['slug']), '0.6', 'monthly', (string) $r['lastmod']];
    }
} catch (Throwable $e) {
    // ignore — still emit static URLs
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo "  <url>\n";
    echo '    <loc>' . htmlspecialchars($base . $u[0], ENT_XML1) . "</loc>\n";
    if (!empty($u[3])) {
        echo '    <lastmod>' . date('c', strtotime($u[3])) . "</lastmod>\n";
    }
    echo '    <changefreq>' . $u[2] . "</changefreq>\n";
    echo '    <priority>' . $u[1] . "</priority>\n";
    echo "  </url>\n";
}
echo '</urlset>' . "\n";
