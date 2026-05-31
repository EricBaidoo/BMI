<?php
require_once __DIR__ . '/includes/db.php';
$pdo = db_connect();
$cols = $pdo->query("SHOW COLUMNS FROM events")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cols);
