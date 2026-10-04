<?php
/**
 * Shrinks oversized images in assets/image (command line only).
 *
 * - Keeps every file at the same path and in the same format, so nothing that links to it breaks.
 * - Longest side capped at --max (default 2000px); JPEG/WebP re-compressed at --quality (default 82).
 * - Applies the camera's EXIF rotation so phone photos display upright.
 * - Only replaces a file when the result is smaller. Originals are copied to --backup first (default: BACKUP_DIR/original-images, outside the website).
 *
 * Usage: php bin/optimize-images.php [--min-kb=300] [--max=2000] [--quality=82] [--backup=DIR] [--dry-run]
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
if (!extension_loaded('gd')) {
    fwrite(STDERR, "The PHP GD extension is required.\n");
    exit(1);
}

$opts = getopt('', ['min-kb::', 'max::', 'quality::', 'backup::', 'dry-run']);
$minBytes = (int) ($opts['min-kb'] ?? 300) * 1024;
$maxSide = (int) ($opts['max'] ?? 2000);
$quality = (int) ($opts['quality'] ?? 82);
$dryRun = isset($opts['dry-run']);
$root = dirname(__DIR__);
// Default: next to the database backups (BACKUP_DIR in .env), which must be outside the website.
require_once $root . '/includes/env.php';
$backup = rtrim((string) ($opts['backup'] ?? (rtrim((string) env('BACKUP_DIR', dirname($root, 2) . '/bmi-backups'), '/\\') . '/original-images')), '/\\');
$realRootParent = strtolower(str_replace('\\', '/', realpath(dirname($root)) ?: dirname($root)));
if (str_starts_with(strtolower(str_replace('\\', '/', $backup)) . '/', $realRootParent . '/')) {
    fwrite(STDERR, "Backup folder {$backup} is inside the web server folder. Set BACKUP_DIR in .env or pass --backup=DIR outside it.\n");
    exit(1);
}
ini_set('memory_limit', '1024M');

$before = 0;
$after = 0;
$changed = 0;
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/public/assets/image', FilesystemIterator::SKIP_DOTS));
foreach ($it as $file) {
    $path = $file->getPathname();
    $ext = strtolower($file->getExtension());
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) || $file->getSize() < $minBytes) {
        continue;
    }
    $relative = str_replace('\\', '/', substr($path, strlen($root) + 1));
    $size = $file->getSize();

    $img = match ($ext) {
        'jpg', 'jpeg' => @imagecreatefromjpeg($path),
        'png' => @imagecreatefrompng($path),
        'webp' => @imagecreatefromwebp($path),
    };
    if (!$img) {
        echo "skip (unreadable) {$relative}\n";
        continue;
    }

    // Rotate according to EXIF so the saved file (which drops EXIF) still displays upright.
    if (in_array($ext, ['jpg', 'jpeg'], true) && function_exists('exif_read_data')) {
        $exif = @exif_read_data($path);
        $rotate = [3 => 180, 6 => -90, 8 => 90][(int) ($exif['Orientation'] ?? 1)] ?? 0;
        if ($rotate !== 0) {
            $rotated = imagerotate($img, $rotate, 0);
            imagedestroy($img);
            $img = $rotated;
        }
    }

    $w = imagesx($img);
    $h = imagesy($img);
    $scale = min(1, $maxSide / max($w, $h));
    if ($scale < 1) {
        $nw = (int) round($w * $scale);
        $nh = (int) round($h * $scale);
        $resized = imagecreatetruecolor($nw, $nh);
        if ($ext !== 'jpg' && $ext !== 'jpeg') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($img);
        $img = $resized;
    }

    $tmp = $path . '.tmp';
    $ok = match ($ext) {
        'jpg', 'jpeg' => (function () use ($img, $tmp, $quality) { imageinterlace($img, true); return imagejpeg($img, $tmp, $quality); })(),
        'png' => (function () use ($img, $tmp) { imagesavealpha($img, true); return imagepng($img, $tmp, 9); })(),
        'webp' => imagewebp($img, $tmp, $quality),
    };
    imagedestroy($img);
    clearstatcache(true, $tmp);
    $newSize = $ok && is_file($tmp) ? filesize($tmp) : 0;

    if (!$ok || $newSize === 0 || $newSize >= $size) {
        @unlink($tmp);
        echo sprintf("keep  %-70s %7.1f KB (no saving)\n", $relative, $size / 1024);
        continue;
    }

    $before += $size;
    $after += $newSize;
    $changed++;
    echo sprintf("%s %-70s %7.1f KB -> %7.1f KB\n", $dryRun ? 'would' : 'done ', $relative, $size / 1024, $newSize / 1024);

    if ($dryRun) {
        @unlink($tmp);
        continue;
    }
    $backupPath = $backup . '/' . $relative;
    if (!is_file($backupPath)) {
        @mkdir(dirname($backupPath), 0755, true);
        if (!copy($path, $backupPath)) {
            @unlink($tmp);
            fwrite(STDERR, "Could not back up {$relative}; stopping without changing it.\n");
            exit(1);
        }
    }
    if (!rename($tmp, $path)) {
        @unlink($tmp);
        fwrite(STDERR, "Could not replace {$relative}.\n");
    }
}

printf("\n%d image(s) %s: %.1f MB -> %.1f MB (saved %.1f MB)%s\n",
    $changed, $dryRun ? 'would shrink' : 'shrunk', $before / 1048576, $after / 1048576, ($before - $after) / 1048576,
    $dryRun ? '' : "\nOriginals copied to {$backup}");
