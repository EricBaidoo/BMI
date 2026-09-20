<?php
require_once 'includes/db.php';
try {
    $pdo = db_connect();
    
    // Add column
    try {
        $pdo->exec("ALTER TABLE weekly_services ADD COLUMN show_on_homepage TINYINT(1) DEFAULT 0");
    } catch (Exception $e) {}

    // Set the original 4 services to show on homepage
    // RESTORERS, REPAIRERS, SWITCH ON, BUILDERS
    $pdo->exec("UPDATE weekly_services SET show_on_homepage = 1 WHERE title IN ('RESTORERS — Celebration Service', 'REPAIRERS — Cell Meetings', 'SWITCH ON — Youth Service', 'BUILDERS — Leadership Meeting', 'RESTORERS', 'REPAIRERS', 'SWITCH ON', 'BUILDERS')");

    echo "Database updated.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
