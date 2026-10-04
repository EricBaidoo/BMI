<?php
/**
 * Versioned database updates (command line). On the live site you can instead use
 * Admin → Website Updates → "Apply updates", which runs exactly the same code.
 *
 * Each file in database/migrations/ is named NNN_description.php and returns
 * function (PDO $pdo): void. Files run once, in order, and are recorded in the
 * schema_migrations table. Migrations are written to be safe if re-run.
 *
 * Usage:
 *   php database/run_migrations.php           apply pending migrations
 *   php database/run_migrations.php --status  list applied and pending migrations
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/maintenance.php';

if (in_array('--status', $argv, true)) {
    foreach (migrations_status() as $name => $done) {
        echo ($done ? '[applied] ' : '[pending] ') . $name . "\n";
    }
    exit(0);
}

$result = migrations_apply();
echo $result['output'];
if ($result['error'] !== '') {
    fwrite(STDERR, $result['error'] . "\nNothing after this migration was run.\n");
    exit(1);
}
echo $result['applied'] === [] ? "Database is up to date.\n" : 'Applied ' . count($result['applied']) . " migration(s).\n";
