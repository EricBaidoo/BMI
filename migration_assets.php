<?php
require 'includes/db.php';

$pdo = db_connect();

$directories = [
    'assets/image/events',
    'assets/image/sermons',
    'assets/image/hero',
    'assets/image/staff',
    'assets/image/ministries',
    'assets/image/ui',
    'assets/image/uncategorized'
];

foreach ($directories as $dir) {
    if (!is_dir(__DIR__ . '/' . $dir)) {
        mkdir(__DIR__ . '/' . $dir, 0777, true);
        echo "Created $dir\n";
    }
}

function move_and_update($pdo, $table, $column, $id_col, $category) {
    $stmt = $pdo->query("SELECT $id_col, $column FROM $table WHERE $column IS NOT NULL AND $column != ''");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row[$id_col];
        $path = $row[$column];
        
        // Skip URLs or already categorized
        if (strpos($path, 'http') === 0 || strpos($path, "assets/image/$category/") === 0 || strpos($path, "assets/video/") === 0) {
            continue;
        }

        // If it starts with assets/image/
        if (strpos($path, 'assets/image/') === 0) {
            $filename = basename($path);
            $new_relative_path = "assets/image/$category/" . $filename;
            $old_full_path = __DIR__ . '/' . $path;
            $new_full_path = __DIR__ . '/' . $new_relative_path;

            if (file_exists($old_full_path)) {
                rename($old_full_path, $new_full_path);
                // Update DB
                $pdo->prepare("UPDATE $table SET $column = ? WHERE $id_col = ?")->execute([$new_relative_path, $id]);
                echo "Moved $filename to $category and updated $table.\n";
            } else if (file_exists($new_full_path)) {
                 $pdo->prepare("UPDATE $table SET $column = ? WHERE $id_col = ?")->execute([$new_relative_path, $id]);
                 echo "Updated $table for $filename (file already moved).\n";
            }
        }
    }
}

move_and_update($pdo, 'events', 'event_image', 'id', 'events');
move_and_update($pdo, 'sermons', 'sermon_image', 'id', 'sermons');
move_and_update($pdo, 'ministries', 'ministry_image', 'id', 'ministries');
move_and_update($pdo, 'hero_slides', 'bg_image', 'id', 'hero');
move_and_update($pdo, 'testimonies', 'image_url', 'id', 'staff'); // Assuming testimonies avatars as staff/people

// Handle settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_value LIKE 'assets/image/%'");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $key = $row['setting_key'];
    $path = $row['setting_value'];
    
    // Determine category based on key
    $category = 'ui';
    if (strpos($key, 'hero_bg') !== false) $category = 'hero';
    if (strpos($key, 'founder') !== false) $category = 'staff';
    
    if (strpos($path, "assets/image/$category/") !== 0) {
        $filename = basename($path);
        $new_relative_path = "assets/image/$category/" . $filename;
        $old_full_path = __DIR__ . '/' . $path;
        $new_full_path = __DIR__ . '/' . $new_relative_path;

        if (file_exists($old_full_path)) {
            rename($old_full_path, $new_full_path);
            $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?")->execute([$new_relative_path, $key]);
            echo "Moved $filename to $category and updated setting $key.\n";
        } else if (file_exists($new_full_path)) {
            $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?")->execute([$new_relative_path, $key]);
            echo "Updated setting $key for $filename (file already moved).\n";
        }
    }
}

// Move remaining files in assets/image to uncategorized if they are files
$files = glob(__DIR__ . '/assets/image/*');
foreach ($files as $file) {
    if (is_file($file)) {
        $filename = basename($file);
        
        // Categorize based on prefix if possible
        $dest = 'uncategorized';
        if (strpos($filename, 'event_') === 0) $dest = 'events';
        else if (strpos($filename, 'sermon_') === 0) $dest = 'sermons';
        else if (strpos($filename, 'site_') === 0) $dest = 'ui';
        else if (strpos($filename, 'logo') !== false) $dest = 'ui';
        
        rename($file, __DIR__ . "/assets/image/$dest/" . $filename);
        echo "Moved orphaned file $filename to $dest<br>\n";
    }
}

echo "Migration complete.\n";
