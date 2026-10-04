<?php
/**
 * 005 Church locations (branches in Ghana, the USA and elsewhere) for the Locations page.
 * Seeds the Accra headquarters from the existing contact and service-time settings.
 */
return function (PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS locations (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        country CHAR(2) NOT NULL DEFAULT 'GH',
        city VARCHAR(120) NULL,
        address TEXT NULL,
        service_times TEXT NULL,
        timezone VARCHAR(64) NOT NULL DEFAULT 'Africa/Accra',
        phone VARCHAR(40) NULL,
        email VARCHAR(150) NULL,
        map_query VARCHAR(255) NULL,
        location_image VARCHAR(255) NULL,
        sort_order INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        KEY idx_locations_order (sort_order, name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    if ((int) $pdo->query('SELECT COUNT(*) FROM locations')->fetchColumn() > 0) {
        return;
    }
    $settings = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'contact.%' OR setting_key LIKE 'service.%'")->fetchAll(PDO::FETCH_KEY_PAIR);
    $times = array_filter([
        ($settings['service.sunday_worship'] ?? '') !== '' ? 'Sunday Worship: ' . $settings['service.sunday_worship'] : '',
        ($settings['service.bible_study'] ?? '') !== '' ? 'Bible Study: ' . $settings['service.bible_study'] : '',
        ($settings['service.prayer_service'] ?? '') !== '' ? 'Prayer Service: ' . $settings['service.prayer_service'] : '',
    ]);
    $pdo->prepare('INSERT INTO locations (name, country, city, address, service_times, timezone, phone, email, map_query, sort_order)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
        ->execute([
            'Accra (Headquarters)', 'GH', 'Accra',
            $settings['contact.address'] ?? null,
            $times ? implode("\n", $times) : null,
            'Africa/Accra',
            $settings['contact.phone_primary'] ?? null,
            $settings['contact.email_general'] ?? null,
            $settings['contact.map_query'] ?? null,
            0,
        ]);
    echo "Seeded Accra headquarters location\n";
};
