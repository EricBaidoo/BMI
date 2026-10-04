<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/logger.php';

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
        log_exception($e, 'settings');
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
    require_once __DIR__ . '/audit.php';

    $before = settings_all(true);
    $changes = [];
    foreach ($kv as $key => $value) {
        if ((string) ($before[$key] ?? '') !== (string) $value) {
            $changes[$key] = [(string) ($before[$key] ?? ''), (string) $value];
        }
    }
    if (!$changes) {
        return;
    }

    $pdo = db_connect();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value, setting_group) VALUES (:k, :v1, :g) ON DUPLICATE KEY UPDATE setting_value = :v2');
        foreach ($changes as $key => [, $value]) {
            $group = str_contains($key, '.') ? explode('.', $key)[0] : 'general';
            $stmt->execute([':k' => $key, ':v1' => $value, ':g' => $group, ':v2' => $value]);
        }
        $pdo->commit();
        // Invalidate cache for the rest of this request
        settings_all(true);
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    $givingChanges = array_filter($changes, fn ($k) => audit_is_giving_key($k), ARRAY_FILTER_USE_KEY);
    $keys = array_keys($changes);
    audit('update', $givingChanges ? 'giving' : 'setting', null,
        'Changed ' . count($keys) . ' setting(s): ' . implode(', ', array_slice($keys, 0, 6)) . (count($keys) > 6 ? '…' : ''),
        ['changes' => $changes]);
    audit_alert_giving_change($givingChanges);
}

/** Short-lived cache of the livestream prompt/notes served to viewers (see api/live_state.php). */
function live_state_cache_file(): string
{
    return sys_get_temp_dir() . '/bmi_live_state_' . md5(__DIR__) . '.json';
}

/**
 * Weekly service times from Settings → Service times, as [label => time] (empty entries skipped).
 * Settings store "Sundays · 8:45 AM"; the day becomes the label when no separate label exists.
 */
function service_times(): array
{
    $labels = [
        'service.sunday_worship' => 'Sunday Worship',
        'service.bible_study' => 'Bible Study',
        'service.prayer_service' => 'Prayer Service',
    ];
    $out = [];
    foreach ($labels as $key => $label) {
        $value = trim(setting($key));
        if ($value !== '') {
            $out[$label] = $value;
        }
    }
    return $out;
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

require_once __DIR__ . '/sanitize.php';
require_once __DIR__ . '/localtime.php';
