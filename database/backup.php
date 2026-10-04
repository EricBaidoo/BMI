<?php
/**
 * Database backup (command line). The same backup runs automatically before
 * Admin → Website Updates applies changes, and can be downloaded there.
 *
 * Writes a gzipped SQL dump to BACKUP_DIR (default: a "bmi-backups" folder next to the
 * website, never inside it) and deletes backups older than BACKUP_KEEP_DAYS (default 30).
 *
 * Usage:   php database/backup.php
 * Restore: import the .sql.gz file in phpMyAdmin, or: gunzip -c FILE | mysql -u USER -p DATABASE
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/maintenance.php';

try {
    $b = create_backup();
    printf("Backed up %d tables (%d rows) to %s (%s KB). Removed %d old backup(s).\n",
        $b['tables'], $b['rows'], $b['file'], number_format(filesize($b['file']) / 1024, 1), $b['removed']);
} catch (Throwable $e) {
    log_exception($e, 'backup');
    fwrite(STDERR, 'Backup failed: ' . $e->getMessage() . "\n");
    exit(1);
}
