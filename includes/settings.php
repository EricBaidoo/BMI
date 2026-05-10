<?php
require_once __DIR__ . '/db.php';

/**
 * In-process cache so each page load hits the DB at most once for settings.
 */
function settings_all(bool $forceRefresh = false): array
{
    static $cache = null;
    if ($cache !== null && !$forceRefresh) {
        return $cache;
    }
    try {
        $pdo = db_connect();
        $rows = $pdo->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
        $cache = [];
        foreach ($rows as $r) {
            $cache[(string) $r['setting_key']] = (string) ($r['setting_value'] ?? '');
        }
    } catch (Throwable $e) {
        $cache = [];
    }
    return $cache;
}

/**
 * Read a single setting with a fallback.
 */
function setting(string $key, $default = ''): string
{
    $all = settings_all();
    $value = $all[$key] ?? null;
    if ($value === null || $value === '') {
        return (string) $default;
    }
    return (string) $value;
}

/**
 * Group helper: returns all settings whose key starts with "{group}.".
 * Strips the group prefix in returned keys.
 */
function settings_group(string $group): array
{
    $prefix = $group . '.';
    $out = [];
    foreach (settings_all() as $k => $v) {
        if (str_starts_with($k, $prefix)) {
            $out[substr($k, strlen($prefix))] = $v;
        }
    }
    return $out;
}

/**
 * Bulk update. $kv is ['site.name' => 'BMI', ...].
 * Existing keys are updated; unknown keys are ignored (we don't allow arbitrary new keys from the form).
 */
function settings_save(array $kv): void
{
    $pdo = db_connect();
    $pdo->beginTransaction();
    try {
        $update = $pdo->prepare('UPDATE site_settings SET setting_value = :v WHERE setting_key = :k');
        foreach ($kv as $key => $value) {
            $update->execute([':k' => $key, ':v' => (string) $value]);
        }
        $pdo->commit();
        // Invalidate cache for the rest of this request
        settings_all(true);
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Returns true if at least one of the social URL settings is filled in.
 */
function settings_has_socials(): bool
{
    foreach (['facebook', 'instagram', 'youtube', 'x', 'tiktok'] as $k) {
        if (setting('social.' . $k) !== '') {
            return true;
        }
    }
    return false;
}
