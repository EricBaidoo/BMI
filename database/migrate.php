<?php
/**
 * Database Migration Script
 * Run this once to update the database schema
 */

require_once __DIR__ . '/../includes/db.php';

try {
    $pdo = db_connect();
    
    // Check if event_image column exists
    $stmt = $pdo->query("DESCRIBE events");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    if (!in_array('event_image', $columns)) {
        // Add event_image column
        $pdo->exec("ALTER TABLE events ADD COLUMN event_image VARCHAR(255) DEFAULT NULL AFTER venue");
        echo "✓ Added event_image column to events table<br>";
    } else {
        echo "✓ event_image column already exists<br>";
    }
    
    // Check if sermon_image column exists
    $stmt = $pdo->query("DESCRIBE sermons");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    if (!in_array('sermon_image', $columns)) {
        // Add sermon_image column
        $pdo->exec("ALTER TABLE sermons ADD COLUMN sermon_image VARCHAR(255) DEFAULT NULL AFTER content");
        echo "✓ Added sermon_image column to sermons table<br>";
    } else {
        echo "✓ sermon_image column already exists<br>";
    }
    
    echo "<br>Migration completed successfully!";
    
} catch (Throwable $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
?>
