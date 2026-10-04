<?php
/**
 * Smoke test: loads every public page and checks the things that must always hold.
 *
 *   php bin/smoke-test.php http://localhost/BMI
 *   php bin/smoke-test.php https://bmiglobal.org        (safe on the live site: read-only GET requests)
 *
 * Exits with code 1 if any check fails, so it can gate a deployment.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$base = rtrim($argv[1] ?? 'http://localhost/BMI', '/');

final class Tally
{
    public static int $checks = 0;
    public static int $failures = 0;
}

function fetch(string $url): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_USERAGENT => 'BMI-smoke-test',
    ]);
    $raw = curl_exec($ch);
    $info = curl_getinfo($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($raw === false) {
        return ['status' => 0, 'headers' => '', 'body' => '', 'location' => '', 'error' => $err];
    }
    $headers = substr($raw, 0, $info['header_size']);
    return [
        'status' => (int) $info['http_code'],
        'headers' => $headers,
        'body' => substr($raw, $info['header_size']),
        'location' => (string) $info['redirect_url'],
        'error' => '',
    ];
}

function check(string $label, bool $ok, string $detail = ''): void
{
    Tally::$checks++;
    if (!$ok) {
        Tally::$failures++;
    }
    printf("%s %s%s\n", $ok ? '  ok ' : 'FAIL ', $label, (!$ok && $detail !== '') ? "  ({$detail})" : '');
}

function php_errors(string $body): string
{
    return preg_match('~(Fatal error|Parse error|Warning|Notice|Deprecated|Uncaught)\s*:~', $body, $m) ? $m[0] : '';
}

echo "Smoke test for {$base}\n\n== Public pages\n";
$pages = ['/', '/about', '/beliefs', '/visit', '/sermons', '/events', '/flagship-programs', '/ministries',
    '/livestream', '/blog', '/donate', '/contact', '/privacy', '/locations'];
foreach ($pages as $p) {
    $r = fetch($base . $p);
    $err = php_errors($r['body']);
    check("{$p} loads", $r['status'] === 200 && $err === '' && str_contains($r['body'], '</html>'),
        $r['error'] ?: "status {$r['status']}" . ($err ? ", {$err}" : ''));
    if ($r['status'] === 200) {
        check("{$p} has one <h1>", substr_count($r['body'], '<h1') === 1, substr_count($r['body'], '<h1') . ' found');
        check("{$p} has a canonical link", str_contains($r['body'], 'rel="canonical"'));
    }
}

echo "\n== Detail pages\n";
$home = fetch($base . '/')['body'];
foreach (['sermon\?id=\d+', 'event-detail\?id=\d+', 'ministry_detail\?id=\d+'] as $pattern) {
    if (preg_match('~href="(' . $pattern . ')"~', $home, $m)) {
        $r = fetch($base . '/' . html_entity_decode($m[1]));
        check("/{$m[1]} loads", $r['status'] === 200 && php_errors($r['body']) === '', "status {$r['status']}");
    }
}
foreach (['/sermon?id=999999999', '/event-detail?id=999999999', '/blog?post=no-such-post-xyz'] as $p) {
    $r = fetch($base . $p);
    check("{$p} returns 404", $r['status'] === 404, "status {$r['status']}");
}

echo "\n== Addresses\n";
$r = fetch($base . '/about.php');
check('/about.php redirects to /about', in_array($r['status'], [301, 308], true) && str_ends_with($r['location'], '/about'), "status {$r['status']} -> {$r['location']}");
$r = fetch($base . '/sitemap.xml');
check('sitemap.xml is valid XML with pages', $r['status'] === 200 && @simplexml_load_string($r['body']) !== false && substr_count($r['body'], '<loc>') > 10);
check('sitemap has no .php addresses', !str_contains($r['body'], '.php'));
$r = fetch($base . '/robots.txt');
check('robots.txt points to an absolute sitemap', $r['status'] === 200 && preg_match('~Sitemap: https?://~', $r['body']) === 1);
$r = fetch($base . '/api/live_state');
check('livestream API returns JSON', $r['status'] === 200 && (json_decode($r['body'], true)['status'] ?? '') === 'success');
$r = fetch($base . '/admin/login.php');
check('admin login page loads', $r['status'] === 200 && str_contains($r['body'], 'name="password"'));
$r = fetch($base . '/admin/users.php');
check('admin pages require sign-in', $r['status'] === 302 && str_contains($r['location'], 'login.php'), "status {$r['status']}");

echo "\n== Private files are never served\n";
$blocked = ['/.env', '/.env.production', '/composer.json', '/composer.lock', '/includes/auth.php', '/includes/db.php',
    '/templates/admin/header.php', '/database/run_migrations.php', '/database/schema.sql', '/bin/smoke-test.php',
    '/logs/', '/vendor/autoload.php', '/public/index.php', '/README.md', '/assets/css/../../includes/config.php',
    '/church_website_backup.sql', '/.git/config'];
foreach ($blocked as $p) {
    $r = fetch($base . $p);
    check("{$p} is blocked", in_array($r['status'], [403, 404], true), "status {$r['status']}");
}

echo "\n== Security headers\n";
$r = fetch($base . '/');
// The Content-Security-Policy comes from Apache (public/.htaccess); PHP's built-in server can't send it.
$headers = ['X-Content-Type-Options', 'X-Frame-Options', 'Referrer-Policy'];
if (!in_array('--builtin-server', $argv, true)) {
    $headers[] = 'Content-Security-Policy';
}
foreach ($headers as $h) {
    check("header {$h}", stripos($r['headers'], $h . ':') !== false);
}
check('no PHP version disclosed', stripos($r['headers'], 'X-Powered-By: PHP') === false);

printf("\n%d checks, %d failed\n", Tally::$checks, Tally::$failures);
exit(Tally::$failures > 0 ? 1 : 0);
