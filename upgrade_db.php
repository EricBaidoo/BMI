<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo = db_connect();

    // Drop and recreate the events table to ensure clean schema
    $pdo->exec("DROP TABLE IF EXISTS events;");
    
    $sql = "
    CREATE TABLE events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        slug VARCHAR(220) UNIQUE,
        description TEXT,
        event_type ENUM('flagship', 'special') DEFAULT 'special',
        event_date DATE NOT NULL,
        end_date DATE DEFAULT NULL,
        event_time TIME,
        venue VARCHAR(200),
        event_image VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_events_date (event_date),
        INDEX idx_events_type (event_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "Successfully recreated events table schema!\n";
    


    // Seed some test data!
    $stmt = $pdo->prepare("INSERT INTO events (title, slug, description, event_type, event_date, end_date, venue, event_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Seed Flagship
    $stmt->execute([
        'Global Dominion Conference 2026',
        'global-dominion-conference-2026',
        'Join us for 4 days of explosive power, impartation, and divine alignment with world-renowned guest speakers.',
        'flagship',
        '2026-08-12',
        '2026-08-16',
        'BMI Main Auditorium',
        'https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=2000&auto=format&fit=crop'
    ]);
    
    // Seed Special Event 1
    $stmt->execute([
        'Youth Worship Night',
        'youth-worship-night',
        'An unplugged night of acoustic worship and intense prayer for all youth.',
        'special',
        '2026-06-15',
        null,
        'Youth Chapel',
        'https://images.unsplash.com/photo-1528605248644-14dd04022da1?q=80&w=2000&auto=format&fit=crop'
    ]);
    
    // Seed Special Event 2
    $stmt->execute([
        'Marriage Enrichment Seminar',
        'marriage-enrichment-seminar',
        'Discover biblical principles for building a lasting and joyous marriage.',
        'special',
        '2026-07-20',
        null,
        'Fellowship Hall',
        'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?q=80&w=2000&auto=format&fit=crop'
    ]);

    echo "Successfully seeded test events data!\n";

} catch (Throwable $e) {
    // If columns already exist, it will throw an error, which is fine for idempotency
    echo "Error or Already Migrated: " . $e->getMessage() . "\n";
}
