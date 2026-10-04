<?php
/**
 * Deletes personal data the Privacy Policy says we don't keep (command line only).
 *
 *  - Contact, prayer and visit messages older than MESSAGE_RETENTION_MONTHS (default 24).
 *    Newsletter sign-ups are kept until the person unsubscribes.
 *  - Sign-in attempt records older than 30 days.
 *
 * Usage:   php database/purge.php [--dry-run]
 * Hosting: run nightly after the backup, e.g.
 *          15 2 * * *  php /path/to/BMI/database/backup.php && php /path/to/BMI/database/purge.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/logger.php';

$dryRun = in_array('--dry-run', $argv, true);
$months = max(1, (int) env('MESSAGE_RETENTION_MONTHS', 24));

try {
    $pdo = db_connect();
    $where = [
        'messages' => ["type <> 'newsletter' AND created_at < (NOW() - INTERVAL {$months} MONTH)", "messages older than {$months} months"],
        'login_attempts' => ['created_at < (NOW() - INTERVAL 30 DAY)', 'sign-in attempt records older than 30 days'],
    ];
    foreach ($where as $table => [$condition, $label]) {
        $count = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}` WHERE {$condition}")->fetchColumn();
        if (!$dryRun && $count > 0) {
            $pdo->exec("DELETE FROM `{$table}` WHERE {$condition}");
        }
        printf("%s %d %s\n", $dryRun ? 'Would delete' : 'Deleted', $count, $label);
    }
    if (!$dryRun) {
        log_message('info', 'Retention purge ran');
    }
} catch (Throwable $e) {
    log_exception($e, 'purge');
    fwrite(STDERR, 'Purge failed: ' . $e->getMessage() . "\n");
    exit(1);
}
