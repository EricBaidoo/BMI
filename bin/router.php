<?php
/**
 * Router for PHP's built-in web server, mirroring the Apache rules in public/.htaccess
 * (clean addresses, sitemap/robots, blocked dotfiles). For local development and CI only.
 *
 *   APP_URL=http://127.0.0.1:8000 php -S 127.0.0.1:8000 -t public bin/router.php
 */
$public = dirname(__DIR__) . '/public';
$path = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$query = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);

// The 404 page is loaded at the bottom, at top level: pages rely on global configuration
// variables, which they would not see if included from inside a function.
$file = null;

// Dotfiles, and the public folder's own name, are never served.
if (preg_match('~(^|/)\.~', $path) || preg_match('~^/public(/|$)~', $path)) {
    $path = null;
}

$aliases = ['/sitemap.xml' => '/sitemap.php', '/robots.txt' => '/robots.php'];
if ($path !== null && isset($aliases[$path])) {
    $path = $aliases[$path];
}

// /page.php -> /page (public pages only; admin forms post to .php addresses)
if ($path !== null && str_ends_with($path, '.php') && !str_starts_with($path, '/admin/') && !in_array($path, $aliases, true) && is_file($public . $path)) {
    $clean = preg_replace('~/index\.php$~', '/', substr($path, 0, -4));
    header('Location: ' . $clean . ($query !== '' ? '?' . $query : ''), true, 301);
    return true;
}

if ($path !== null) {
    $file = $public . $path;
    if (is_dir($file)) {
        $file = rtrim($file, '/') . '/index.php';
        $path = rtrim($path, '/') . '/index.php';
    }
    if (is_file($file) && !str_ends_with($file, '.php')) {
        return false; // static file: let the built-in server send it
    }
    if (!is_file($file) && is_file($file . '.php')) {
        $file .= '.php';
        $path .= '.php';
    }
    if (!is_file($file)) {
        $file = null;
    }
}

if ($file === null) {
    http_response_code(404);
    $file = $public . '/404.php';
    $path = '/404.php';
}

$_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'] = $path;
$_SERVER['SCRIPT_FILENAME'] = $file;
chdir(dirname($file));
require $file;
return true;
