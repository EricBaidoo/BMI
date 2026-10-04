<?php
/**
 * Database updates, backups and data-retention clean-up.
 *
 * Used both from the command line (database/*.php) and from Admin → Website Updates,
 * so the site can be maintained without server access: deploy by pushing to GitHub,
 * then press "Apply updates" in the admin.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/logger.php';

/** Folder that holds backups: BACKUP_DIR, or a folder next to (never inside) the website. */
function backup_dir(): string
{
    $root = realpath(APP_ROOT) ?: APP_ROOT;
    $dir = rtrim((string) env('BACKUP_DIR', dirname($root) . '/bmi-backups'), '/\\');
    if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
        throw new RuntimeException("Cannot create the backup folder {$dir}.");
    }
    $real = realpath($dir) ?: $dir;
    if (str_starts_with(strtolower(str_replace('\\', '/', $real) . '/'), strtolower(str_replace('\\', '/', $root) . '/'))) {
        throw new RuntimeException("The backup folder ({$real}) is inside the website, where it could be downloaded. Set BACKUP_DIR to a folder outside it.");
    }
    return $real;
}

/**
 * Writes a gzipped SQL dump of every table and removes backups older than BACKUP_KEEP_DAYS.
 * @return array{file: string, tables: int, rows: int, removed: int}
 */
function create_backup(): array
{
    $pdo = db_connect();
    $dir = backup_dir();
    $keepDays = max(1, (int) env('BACKUP_KEEP_DAYS', 30));
    $db = (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
    $file = $dir . DIRECTORY_SEPARATOR . $db . '_' . date('Y-m-d_His') . '.sql.gz';
    $gz = gzopen($file, 'wb6');
    if (!$gz) {
        throw new RuntimeException("Cannot write {$file}");
    }

    gzwrite($gz, "-- Backup of `{$db}` taken " . date('c') . "\nSET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n");
    $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_type = 'BASE TABLE' ORDER BY table_name")->fetchAll(PDO::FETCH_COLUMN);
    $rowsTotal = 0;
    foreach ($tables as $table) {
        $create = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_NUM)[1];
        gzwrite($gz, "DROP TABLE IF EXISTS `{$table}`;\n{$create};\n\n");
        $batch = [];
        foreach ($pdo->query("SELECT * FROM `{$table}`", PDO::FETCH_NUM) as $row) {
            $batch[] = '(' . implode(',', array_map(fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v), $row)) . ')';
            if (count($batch) === 200) {
                gzwrite($gz, "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $batch) . ";\n");
                $rowsTotal += 200;
                $batch = [];
            }
        }
        if ($batch) {
            gzwrite($gz, "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $batch) . ";\n");
            $rowsTotal += count($batch);
        }
        gzwrite($gz, "\n");
    }
    gzwrite($gz, "SET FOREIGN_KEY_CHECKS=1;\n");
    gzclose($gz);
    @chmod($file, 0600);

    $removed = 0;
    foreach (glob($dir . DIRECTORY_SEPARATOR . '*.sql.gz') ?: [] as $old) {
        if (filemtime($old) < time() - $keepDays * 86400 && @unlink($old)) {
            $removed++;
        }
    }
    return ['file' => $file, 'tables' => count($tables), 'rows' => $rowsTotal, 'removed' => $removed];
}

/** @return array<string, bool> migration name => applied */
function migrations_status(): array
{
    $pdo = db_connect();
    $pdo->exec("CREATE TABLE IF NOT EXISTS schema_migrations (
        migration VARCHAR(190) NOT NULL PRIMARY KEY,
        applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $applied = $pdo->query('SELECT migration FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);
    $files = glob(APP_ROOT . '/database/migrations/[0-9][0-9][0-9]_*.php') ?: [];
    sort($files, SORT_STRING);
    $status = [];
    foreach ($files as $file) {
        $name = basename($file, '.php');
        $status[$name] = in_array($name, $applied, true);
    }
    return $status;
}

/**
 * Runs pending migrations in order; stops at the first failure.
 * @return array{applied: list<string>, output: string, error: string}
 */
function migrations_apply(): array
{
    $pdo = db_connect();
    $applied = [];
    $error = '';
    ob_start();
    foreach (migrations_status() as $name => $done) {
        if ($done) {
            continue;
        }
        echo "== {$name}\n";
        $migration = require APP_ROOT . '/database/migrations/' . $name . '.php';
        if (!is_callable($migration)) {
            $error = "{$name} does not return a function.";
            break;
        }
        try {
            $migration($pdo);
        } catch (Throwable $e) {
            log_exception($e, "migration {$name}");
            $error = "{$name} failed: " . $e->getMessage();
            break;
        }
        $pdo->prepare('INSERT INTO schema_migrations (migration) VALUES (?)')->execute([$name]);
        $applied[] = $name;
    }
    return ['applied' => $applied, 'output' => (string) ob_get_clean(), 'error' => $error];
}

/**
 * Deletes personal data past its retention period (see the Privacy Policy).
 * @return array<string, int> what => rows deleted (or that would be deleted)
 */
function retention_purge(bool $dryRun = false): array
{
    $pdo = db_connect();
    $months = max(1, (int) env('MESSAGE_RETENTION_MONTHS', 24));
    $rules = [
        "messages older than {$months} months" => ['messages', "type <> 'newsletter' AND created_at < (NOW() - INTERVAL {$months} MONTH)"],
        'sign-in attempt records older than 30 days' => ['login_attempts', 'created_at < (NOW() - INTERVAL 30 DAY)'],
    ];
    $result = [];
    foreach ($rules as $label => [$table, $condition]) {
        $count = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}` WHERE {$condition}")->fetchColumn();
        if (!$dryRun && $count > 0) {
            $pdo->exec("DELETE FROM `{$table}` WHERE {$condition}");
        }
        $result[$label] = $count;
    }
    if (!$dryRun) {
        $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_group) VALUES ('system.last_purge', NOW(), 'system')
                       ON DUPLICATE KEY UPDATE setting_value = NOW()")->execute();
    }
    return $result;
}

/**
 * Runs the retention clean-up at most once a day, so no cron job is needed.
 * Called when staff sign in; failures are logged and never block the sign-in.
 */
function maybe_run_daily_purge(): void
{
    try {
        // Compared in MySQL, which wrote the timestamp, so time zones can't disagree.
        $due = (bool) db_connect()->query(
            "SELECT COALESCE(MAX(CAST(setting_value AS DATETIME)) < NOW() - INTERVAL 1 DAY, 1)
             FROM site_settings WHERE setting_key = 'system.last_purge'"
        )->fetchColumn();
    } catch (Throwable $e) {
        log_exception($e, 'daily purge check');
        return;
    }
    if (!$due) {
        return;
    }
    try {
        retention_purge();
    } catch (Throwable $e) {
        log_exception($e, 'daily purge');
    }
}
