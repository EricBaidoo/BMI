<?php
/**
 * Syntax-checks every PHP file in the project (php -l). Exits 1 on any error.
 * Usage: php bin/lint.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
$root = dirname(__DIR__);
$skip = ['vendor', 'node_modules', '.git'];
$it = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
    fn ($f) => !($f->isDir() && in_array($f->getFilename(), $skip, true))
));
$php = escapeshellarg(PHP_BINARY);
$count = 0;
$bad = 0;
foreach ($it as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }
    $count++;
    exec("{$php} -l " . escapeshellarg($file->getPathname()) . ' 2>&1', $out, $code);
    if ($code !== 0) {
        $bad++;
        echo implode("\n", $out), "\n";
    }
    $out = [];
}
echo "{$count} PHP files checked, {$bad} with errors\n";
exit($bad > 0 ? 1 : 0);
