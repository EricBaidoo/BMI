<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo = db_connect();
    
    // Create live_state table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS live_state (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Insert default values if they don't exist
    $pdo->exec("
        INSERT IGNORE INTO live_state (setting_key, setting_value) VALUES 
        ('current_prompt_html', ''),
        ('current_notes_html', '');
    ");
    
    echo "Live state migration successful.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
