<?php
/**
 * Hardened image upload helper.
 *
 * - Validates real MIME via finfo_file (does NOT trust $_FILES['type']).
 * - Whitelists extensions explicitly.
 * - Generates a random filename — never reuses user-supplied names.
 * - Caps file size.
 *
 * Returns the public-relative path (e.g., "assets/image/foo.jpg") or null when no file uploaded.
 * Throws RuntimeException on validation/IO failure.
 */
function upload_image(?array $file, string $prefix = 'upload', int $maxBytes = 20 * 1024 * 1024): ?string
{
    if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed (error code ' . (int) $file['error'] . ').');
    }

    if (($file['size'] ?? 0) > $maxBytes) {
        throw new RuntimeException('Image too large. Maximum size is ' . round($maxBytes / 1024 / 1024, 1) . 'MB.');
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Upload rejected.');
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/x-icon' => 'ico',
        'image/vnd.microsoft.icon' => 'ico',
        'image/svg+xml' => 'svg',
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string) $finfo->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG, GIF, WebP, SVG, and ICO images are allowed.');
    }
    $ext = $allowed[$mime];

    $uploadDir = dirname(__DIR__) . '/assets/image/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
        throw new RuntimeException('Upload directory is not writable.');
    }

    if (in_array($ext, ['webp', 'ico', 'svg'])) {
        $name = sprintf('%s_%s_%s.%s', preg_replace('/[^a-z0-9_-]/i', '', $prefix), time(), bin2hex(random_bytes(6)), $ext);
        $destination = $uploadDir . $name;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('Could not save uploaded image.');
        }
    } else {
        $name = sprintf('%s_%s_%s.webp', preg_replace('/[^a-z0-9_-]/i', '', $prefix), time(), bin2hex(random_bytes(6)));
        $destination = $uploadDir . $name;
        
        $image = false;
        if ($ext === 'jpg') {
            $image = @imagecreatefromjpeg($file['tmp_name']);
        } elseif ($ext === 'png') {
            $image = @imagecreatefrompng($file['tmp_name']);
            if ($image) {
                imagepalettetotruecolor($image);
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }
        } elseif ($ext === 'gif') {
            $image = @imagecreatefromgif($file['tmp_name']);
            if ($image) {
                imagepalettetotruecolor($image);
            }
        }

        if (!$image || !imagewebp($image, $destination, 80)) {
            // Fallback: if conversion fails, just save the original file
            if ($image) {
                imagedestroy($image);
            }
            $name = sprintf('%s_%s_%s.%s', preg_replace('/[^a-z0-9_-]/i', '', $prefix), time(), bin2hex(random_bytes(6)), $ext);
            $destination = $uploadDir . $name;
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new RuntimeException('Could not save uploaded image.');
            }
        } else {
            imagedestroy($image);
        }
    }

    @chmod($destination, 0644);
    return 'assets/image/' . $name;
}

/**
 * Safely delete a previously-uploaded image given its public-relative path.
 * Refuses paths that escape the assets/image directory.
 * Ignores absolute URLs (http:// or https://).
 */
function upload_delete(?string $relativePath): void
{
    if (!$relativePath || strpos($relativePath, 'http://') === 0 || strpos($relativePath, 'https://') === 0) {
        return;
    }
    $base = realpath(dirname(__DIR__) . '/assets/image');
    if (!$base) {
        return;
    }
    $absolute = realpath(dirname(__DIR__) . '/' . ltrim($relativePath, '/'));
    if (!$absolute || strpos($absolute, $base) !== 0) {
        return;
    }
    @unlink($absolute);
}

/**
 * Handles logic for either uploading a new file, or using a provided URL.
 * File upload takes precedence over URL.
 *
 * @param array|null $file The $_FILES['input_name'] array.
 * @param string $url The provided URL string (from $_POST).
 * @param string $prefix Prefix for the uploaded file.
 * @return string|null The resulting path/URL or null.
 */
function handle_image_upload_or_link(?array $file, string $url, string $prefix = 'upload'): ?string
{
    $uploaded = upload_image($file, $prefix);
    if ($uploaded !== null) {
        return $uploaded;
    }
    
    $url = trim($url);
    if ($url !== '') {
        return $url;
    }

    return null;
}
