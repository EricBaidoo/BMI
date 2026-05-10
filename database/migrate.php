<?php
/**
 * Database migration script — re-runnable.
 * Run from XAMPP shell or browser to apply incremental schema changes.
 *
 * Browser: http://localhost/BMI/database/migrate.php
 * CLI:     c:\xampp\php\php.exe database\migrate.php
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/settings.php';

$inCli = PHP_SAPI === 'cli';
$say = static function (string $line) use ($inCli): void {
    echo $inCli ? $line . "\n" : $line . "<br>\n";
};

try {
    $pdo = db_connect();

    // 1. Add image columns if missing
    $eventCols = $pdo->query('DESCRIBE events')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('event_image', $eventCols, true)) {
        $pdo->exec('ALTER TABLE events ADD COLUMN event_image VARCHAR(255) DEFAULT NULL AFTER venue');
        $say('+ Added events.event_image');
    } else {
        $say('= events.event_image already present');
    }

    $sermonCols = $pdo->query('DESCRIBE sermons')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('sermon_image', $sermonCols, true)) {
        $pdo->exec('ALTER TABLE sermons ADD COLUMN sermon_image VARCHAR(255) DEFAULT NULL AFTER content');
        $say('+ Added sermons.sermon_image');
    } else {
        $say('= sermons.sermon_image already present');
    }

    // 2. Create site_settings table
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS site_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(80) NOT NULL UNIQUE,
            setting_value LONGTEXT,
            setting_group VARCHAR(40) DEFAULT 'general',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_settings_group (setting_group)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $say('= site_settings table ensured');

    // 3. Seed defaults (only inserts missing keys; never overwrites existing values)
    $defaults = [
        // General
        ['site.name', 'Bridge Ministries International', 'general'],
        ['site.tagline', 'Glorify. Grow. Go.', 'general'],
        ['site.description', 'Bridge Ministries International is a Bible-believing church family in Accra, Ghana, helping people know Christ, grow in faith, and live on mission.', 'general'],
        ['site.founded_year', '2005', 'general'],

        // Contact
        ['contact.address', 'Behind Sel Fuel station, Anyaa-Awoshie Rd, Accra, Ghana', 'contact'],
        ['contact.phone_primary', '026 235 4383', 'contact'],
        ['contact.phone_secondary', '', 'contact'],
        ['contact.email_general', 'info@bmiglobal.org', 'contact'],
        ['contact.email_prayer', 'prayer@bmiglobal.org', 'contact'],
        ['contact.email_giving', 'giving@bmiglobal.org', 'contact'],
        ['contact.map_query', 'Anyaa-Awoshie Road, Accra, Ghana', 'contact'],

        // Service times
        ['service.sunday_worship', 'Sundays · 8:45 AM', 'service'],
        ['service.bible_study', 'Wednesdays · 6:00 PM', 'service'],
        ['service.prayer_service', 'Fridays · 5:30 PM', 'service'],
        ['service.notes', 'All are welcome. Childcare available during the Sunday service.', 'service'],

        // Social
        ['social.facebook', '', 'social'],
        ['social.instagram', '', 'social'],
        ['social.youtube', '', 'social'],
        ['social.x', '', 'social'],
        ['social.tiktok', '', 'social'],

        // Giving
        ['giving.bank_name', '', 'giving'],
        ['giving.bank_account_name', '', 'giving'],
        ['giving.bank_account_number', '', 'giving'],
        ['giving.bank_branch', '', 'giving'],
        ['giving.momo_mtn', '', 'giving'],
        ['giving.momo_vodafone', '', 'giving'],
        ['giving.momo_airteltigo', '', 'giving'],
        ['giving.paystack_public_key', '', 'giving'],
        ['giving.currency', 'GHS', 'giving'],

        // Livestream
        ['live.embed_url', '', 'live'],
        ['live.youtube_channel_url', '', 'live'],

        // Analytics
        ['analytics.plausible_domain', '', 'analytics'],
    ];

    $insert = $pdo->prepare(
        'INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_group) VALUES (:k, :v, :g)'
    );
    $added = 0;
    foreach ($defaults as [$k, $v, $g]) {
        $insert->execute([':k' => $k, ':v' => $v, ':g' => $g]);
        if ($insert->rowCount() > 0) {
            $added++;
        }
    }
    $say('+ Seeded ' . $added . ' new default setting(s); existing values preserved.');

    $say('');
    $say('Migration complete.');
} catch (Throwable $e) {
    $say('ERROR: ' . $e->getMessage());
    exit(1);
}
