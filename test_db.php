<?php
require_once __DIR__ . '/includes/db.php';
$pdo = db_connect();
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'home.%'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
