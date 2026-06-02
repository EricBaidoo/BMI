<?php
require_once __DIR__ . '/includes/db.php';
$pdo = db_connect();
$rows = $pdo->query('SELECT setting_key FROM site_settings WHERE setting_group LIKE "page_%"')->fetchAll(PDO::FETCH_COLUMN);
echo implode("\n", $rows);
