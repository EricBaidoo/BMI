<?php
$_POST = [];
$_FILES = [
    'setting_files' => [
        'name' => ['site_logo' => 'test.png'],
        'type' => ['site_logo' => 'image/png'],
        'tmp_name' => ['site_logo' => 'C:\\temp\\php123.tmp'],
        'error' => ['site_logo' => 0],
        'size' => ['site_logo' => 1234],
    ]
];

$key = 'site.logo';
$postKey = str_replace('.', '_', $key);

$actualKey = isset($_FILES['setting_files']['name'][$key]) ? $key : (isset($_FILES['setting_files']['name'][$postKey]) ? $postKey : null);
if ($actualKey && !empty($_FILES['setting_files']['name'][$actualKey])) {
    echo "SUCCESS: actualKey is $actualKey\n";
    $file = [
        'name' => $_FILES['setting_files']['name'][$actualKey],
        'type' => $_FILES['setting_files']['type'][$actualKey],
        'tmp_name' => $_FILES['setting_files']['tmp_name'][$actualKey],
        'error' => $_FILES['setting_files']['error'][$actualKey],
        'size' => $_FILES['setting_files']['size'][$actualKey],
    ];
    print_r($file);
} else {
    echo "FAILED: actualKey is null or empty\n";
}
