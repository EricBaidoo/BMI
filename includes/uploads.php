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
function upload_image(?array $file, string $prefix = 'upload', int $maxBytes = 5 * 1024 * 1024): ?string
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
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string) $finfo->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG, GIF, and WebP images are allowed.');
    }
    $ext = $allowed[$mime];

    $uploadDir = dirname(__DIR__) . '/assets/image/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
        throw new RuntimeException('Upload directory is not writable.');
    }

    $name = sprintf('%s_%s_%s.%s', preg_replace('/[^a-z0-9_-]/i', '', $prefix), time(), bin2hex(random_bytes(6)), $ext);
    $destination = $uploadDir . $name;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Could not save uploaded image.');
    }

    @chmod($destination, 0644);
    return 'assets/image/' . $name;
}

/**
 * Safely delete a previously-uploaded image given its public-relative path.
 * Refuses paths that escape the assets/image directory.
 */
function upload_delete(?string $relativePath): void
{
    if (!$relativePath) {
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
