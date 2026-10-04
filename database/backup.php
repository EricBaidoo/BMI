<?php
/**
 * Database backup (command line only).
 *
 * Writes a gzipped SQL dump of every table to BACKUP_DIR (from .env), which must be
 * outside the website folder, and deletes backups older than BACKUP_KEEP_DAYS (default 30).
 *
 * Usage:   php database/backup.php
 * Hosting: schedule it nightly as a cron job, e.g.
 *          15 2 * * *  php /home/USER/public_html/database/backup.php
 * Restore: gunzip -c backup.sql.gz | mysql -u USER -p DATABASE
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/logger.php';

$root = realpath(dirname(__DIR__));
$dir = rtrim((string) env('BACKUP_DIR', dirname($root) . '/bmi-backups'), '/\\');
$keepDays = max(1, (int) env('BACKUP_KEEP_DAYS', 30));

if (!is_dir($dir) && !mkdir($dir, 0700, true) && !is_dir($dir)) {
    fwrite(STDERR, "Cannot create backup folder {$dir}\n");
    exit(1);
}
$realDir = realpath($dir);
if (str_starts_with(strtolower($realDir . DIRECTORY_SEPARATOR), strtolower($root . DIRECTORY_SEPARATOR))) {
    fwrite(STDERR, "BACKUP_DIR ({$realDir}) is inside the website folder, where it could be downloaded. Choose a folder outside {$root}.\n");
    exit(1);
}

try {
    $pdo = db_connect();
    $db = (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
    $file = $realDir . DIRECTORY_SEPARATOR . $db . '_' . date('Y-m-d_His') . '.sql.gz';
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

        $stmt = $pdo->query("SELECT * FROM `{$table}`", PDO::FETCH_NUM);
        $batch = [];
        foreach ($stmt as $row) {
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
    foreach (glob($realDir . DIRECTORY_SEPARATOR . '*.sql.gz') as $old) {
        if (filemtime($old) < time() - $keepDays * 86400) {
            @unlink($old) && $removed++;
        }
    }

    printf("Backed up %d tables (%d rows) to %s (%s KB). Removed %d backup(s) older than %d days.\n",
        count($tables), $rowsTotal, $file, number_format(filesize($file) / 1024, 1), $removed, $keepDays);
} catch (Throwable $e) {
    log_exception($e, 'backup');
    fwrite(STDERR, 'Backup failed: ' . $e->getMessage() . "\n");
    exit(1);
}
