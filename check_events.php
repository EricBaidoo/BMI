<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo = db_connect();
    
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM events');
    $total = $stmt->fetch();
    echo "Total events: " . $total['total'] . "\n";
    
    $stmt = $pdo->query('SELECT COUNT(*) as upcoming FROM events WHERE event_date >= CURDATE()');
    $upcoming = $stmt->fetch();
    echo "Upcoming events: " . $upcoming['upcoming'] . "\n";
    
    $stmt = $pdo->query('SELECT id, title, event_date FROM events ORDER BY event_date DESC');
    $events = $stmt->fetchAll();
    echo "\nAll events:\n";
    foreach ($events as $event) {
        echo "- " . $event['title'] . " (" . $event['event_date'] . ")\n";
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}
?>
