<?php
/**
 * 001 Audit fixes (2026-10-03): weekly_services columns, live_state table, event slugs,
 * image paths moved into assets/image subfolders.
 */
return function (PDO $pdo): void {
    require_once dirname(__DIR__, 2) . '/includes/paths.php';
    $root = PUBLIC_DIR;
    $hasColumn = function (string $table, string $column) use ($pdo): bool {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
        $stmt->execute([$table, $column]);
        return (bool) $stmt->fetchColumn();
    };

    // 1. weekly_services columns
    $columns = [
        'leader_name'      => 'VARCHAR(255) NULL',
        'long_description' => 'TEXT NULL',
        'gallery_image_1'  => 'VARCHAR(255) NULL',
        'gallery_image_2'  => 'VARCHAR(255) NULL',
        'gallery_image_3'  => 'VARCHAR(255) NULL',
        'show_on_homepage' => 'TINYINT(1) NOT NULL DEFAULT 1',
    ];
    foreach ($columns as $name => $def) {
        if (!$hasColumn('weekly_services', $name)) {
            $pdo->exec("ALTER TABLE weekly_services ADD `$name` $def");
            echo "Added weekly_services.$name\n";
        }
    }

    // 2. live_state table
    $pdo->exec("CREATE TABLE IF NOT EXISTS live_state (
        setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
        setting_value MEDIUMTEXT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "live_state table ready\n";

    // 3. Event slugs
    $exists = $pdo->prepare('SELECT COUNT(*) FROM events WHERE slug = ?');
    $update = $pdo->prepare('UPDATE events SET slug = ? WHERE id = ?');
    foreach ($pdo->query("SELECT id, title FROM events WHERE slug IS NULL OR slug = ''") as $row) {
        $base = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($row['title'])), '-') ?: 'event';
        $slug = $base;
        for ($i = 2; $exists->execute([$slug]) && $exists->fetchColumn() > 0; $i++) {
            $slug = "$base-$i";
        }
        $update->execute([$slug, $row['id']]);
        echo "Event #{$row['id']} slug -> $slug\n";
    }

    // 4. Image paths whose files moved into a subfolder of assets/image
    $imageColumns = [
        'events' => 'event_image', 'sermons' => 'sermon_image', 'posts' => 'post_image',
        'ministries' => 'ministry_image', 'hero_slides' => 'bg_image',
        'testimonies' => 'image_url', 'weekly_services' => 'image_url',
    ];
    $index = [];
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator("$root/assets/image", FilesystemIterator::SKIP_DOTS)) as $file) {
        $index[strtolower($file->getFilename())][] = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
    }
    foreach ($imageColumns as $table => $col) {
        $fix = $pdo->prepare("UPDATE `$table` SET `$col` = ? WHERE id = ?");
        foreach ($pdo->query("SELECT id, `$col` AS v FROM `$table` WHERE `$col` <> ''") as $row) {
            $path = $row['v'];
            if (preg_match('#^https?://#', $path) || file_exists($root . '/' . ltrim($path, '/'))) {
                continue;
            }
            $matches = $index[strtolower(basename($path))] ?? [];
            if (count($matches) === 1) {
                $fix->execute([$matches[0], $row['id']]);
                echo "$table#{$row['id']}: $path -> {$matches[0]}\n";
            } else {
                echo "$table#{$row['id']}: $path STILL MISSING (" . count($matches) . " candidates)\n";
            }
        }
    }
};
