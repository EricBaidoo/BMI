<?php
/**
 * Deletes personal data the Privacy Policy says we don't keep (command line).
 * This also runs automatically once a day when staff sign in, so no cron job is required.
 *
 *  - Contact, prayer and visit messages older than MESSAGE_RETENTION_MONTHS (default 24).
 *    Newsletter sign-ups are kept until the person unsubscribes.
 *  - Sign-in attempt records older than 30 days.
 *
 * Usage: php database/purge.php [--dry-run]
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/maintenance.php';

$dryRun = in_array('--dry-run', $argv, true);
try {
    foreach (retention_purge($dryRun) as $label => $count) {
        printf("%s %d %s\n", $dryRun ? 'Would delete' : 'Deleted', $count, $label);
    }
} catch (Throwable $e) {
    log_exception($e, 'purge');
    fwrite(STDERR, 'Purge failed: ' . $e->getMessage() . "\n");
    exit(1);
}
