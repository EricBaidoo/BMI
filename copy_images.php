<?php
$source_dir = 'C:\\Users\\ERIC BAIDOO\\.gemini\\antigravity-ide\\brain\\cd5c12c5-522e-49cf-964a-28983eccd3b8\\';
$target_dir = __DIR__ . '/assets/image/';

$files = glob($source_dir . 'flyer_*.png');

echo "<h2>Copying Flyer Images...</h2>";

if (empty($files)) {
    echo "<p>No flyer images found in the source directory.</p>";
} else {
    foreach ($files as $file) {
        // We only care about the prefix (e.g. flyer_restorers) to overwrite old ones
        $basename = basename($file);
        
        // Find if it's restorers, repairers, etc.
        if (strpos($basename, 'flyer_restorers') !== false) {
            $new_name = 'flyer_restorers.png';
        } elseif (strpos($basename, 'flyer_repairers') !== false) {
            $new_name = 'flyer_repairers.png';
        } elseif (strpos($basename, 'flyer_switchon') !== false) {
            $new_name = 'flyer_switchon.png';
        } elseif (strpos($basename, 'flyer_builders') !== false) {
            $new_name = 'flyer_builders.png';
        } else {
            $new_name = $basename;
        }

        $dest = $target_dir . $new_name;
        
        if (copy($file, $dest)) {
            echo "<p style='color: green;'>Successfully copied: <strong>$new_name</strong></p>";
        } else {
            echo "<p style='color: red;'>Failed to copy: $basename</p>";
        }
    }
}

echo "<h3>Done! You can now safely delete this copy_images.php file.</h3>";
?>
