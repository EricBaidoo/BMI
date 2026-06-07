<?php
require 'includes/db.php';
$pdo = db_connect();

// 1. Reset schedule to have "try" event
$pdo->query("UPDATE site_settings SET setting_value='[{\"type\":\"date\",\"day\":\"Sunday\",\"name\":\"try\",\"date\":\"2026-06-02\",\"time\":\"10:30\"}]' WHERE setting_key='live.schedule'");

$pdo->query("UPDATE site_settings SET setting_value='https://youtube.com/embed/test' WHERE setting_key='live.embed_url'");

// 2. Mock POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['active_group'] = 'live';
$_POST['end_and_archive'] = '1';
$_POST['setting'] = [
    'live_embed_url' => '',
    'live_schedule' => [
        ['type' => 'date', 'day' => 'Sunday', 'name' => 'try', 'date' => '2026-06-02', 'time' => '10:30']
    ]
];

// Include auth bypass
function auth_check() { return true; }

// Run it
ob_start();
include 'admin/settings.php';
$out = ob_get_clean();

// Check DB
$newSched = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key='live.schedule'")->fetchColumn();
echo "New Schedule: " . $newSched;
