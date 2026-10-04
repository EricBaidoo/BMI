<?php
/**
 * Versioned database migrations.
 *
 * Each file in database/migrations/ is named NNN_description.php and returns
 * function (PDO $pdo): void. Files run once, in order, and are recorded in the
 * schema_migrations table. Migrations are written to be safe if re-run.
 *
 * Usage (command line only):
 *   php database/run_migrations.php           apply pending migrations
 *   php database/run_migrations.php --status  list applied and pending migrations
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$pdo = db_connect();
$pdo->exec("CREATE TABLE IF NOT EXISTS schema_migrations (
    migration VARCHAR(190) NOT NULL PRIMARY KEY,
    applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$applied = $pdo->query('SELECT migration FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);
$files = glob(__DIR__ . '/migrations/[0-9][0-9][0-9]_*.php');
sort($files, SORT_STRING);

if (in_array('--status', $argv, true)) {
    foreach ($files as $file) {
        $name = basename($file, '.php');
        echo (in_array($name, $applied, true) ? '[applied] ' : '[pending] ') . $name . "\n";
    }
    exit(0);
}

$ran = 0;
foreach ($files as $file) {
    $name = basename($file, '.php');
    if (in_array($name, $applied, true)) {
        continue;
    }
    echo "== {$name}\n";
    $migration = require $file;
    if (!is_callable($migration)) {
        fwrite(STDERR, "{$name} does not return a function. Stopping.\n");
        exit(1);
    }
    try {
        $migration($pdo);
    } catch (Throwable $e) {
        fwrite(STDERR, "{$name} failed: " . $e->getMessage() . "\nNothing after this migration was run.\n");
        exit(1);
    }
    $pdo->prepare('INSERT INTO schema_migrations (migration) VALUES (?)')->execute([$name]);
    $ran++;
}

echo $ran === 0 ? "Database is up to date.\n" : "Applied {$ran} migration(s).\n";
