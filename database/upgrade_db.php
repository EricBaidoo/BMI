<?php
require_once __DIR__ . '/../includes/db.php';

try {
    $pdo = db_connect();

    // Add slug, event_type, and end_date columns
    $sql = "
        ALTER TABLE events
        ADD COLUMN slug VARCHAR(220) UNIQUE AFTER title,
        ADD COLUMN event_type ENUM('flagship', 'special') DEFAULT 'special' AFTER description,
        ADD COLUMN end_date DATE DEFAULT NULL AFTER event_date;
    ";
    
    $pdo->exec($sql);
    echo "Successfully updated events table schema!\n";
    
    // Add an index on event_type
    $pdo->exec("CREATE INDEX idx_events_type ON events(event_type);");
    echo "Added index for event_type.\n";

    // Seed some test data!
    $pdo->exec("TRUNCATE TABLE events");
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
        'https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=1200&auto=format&fit=crop'
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
        'https://images.unsplash.com/photo-1528605248644-14dd04022da1?q=80&w=1200&auto=format&fit=crop'
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
        'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?q=80&w=1200&auto=format&fit=crop'
    ]);

    echo "Successfully seeded test events data!\n";

} catch (Throwable $e) {
    // If columns already exist, it will throw an error, which is fine for idempotency
    echo "Error or Already Migrated: " . $e->getMessage() . "\n";
}
