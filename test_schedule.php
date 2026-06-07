<?php
require 'includes/db.php';
$pdo = db_connect();
$v = $pdo->query('select setting_value from site_settings where setting_key="live.schedule"')->fetchColumn();
echo $v;
