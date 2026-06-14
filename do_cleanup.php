<?php
$files = [
    'check.php', 'check_db.php', 'cleanup.php', 'copy_images.php', 'create_admin.php',
    'migrate_social.php', 'redesign.js', 'redesign_script.php', 'replace.php', 'run_migrate.php',
    'seed_flagships.php', 'temp.php', 'test_array.php', 'test_config.php', 'test_date.php',
    'test_db.php', 'test_dots.php', 'test_gd.php', 'test_keys.php', 'test_keys2.php',
    'test_post.php', 'test_schedule.php', 'test_submit.php', 'upgrade_db.php',
    'admin/debug.php', 'admin/files_dump.txt', 'admin/test_post.php', 'admin/test_sim.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        unlink($path);
    }
}

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                    rrmdir($dir. DIRECTORY_SEPARATOR .$object);
                else
                    unlink($dir. DIRECTORY_SEPARATOR .$object);
            }
        }
        rmdir($dir);
    }
}

rrmdir(__DIR__ . '/.urlclean-backup');
echo "Cleanup completed successfully.";
