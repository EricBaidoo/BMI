<?php
/**
 * Adds the 'visit' message type so Plan a Visit submissions land in the admin Inbox.
 * Safe to run more than once. Run from the command line: php database/migrate_visit_requests.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$pdo = db_connect();
$type = (string) $pdo->query("SELECT COLUMN_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'messages' AND column_name = 'type'")->fetchColumn();

if (str_contains($type, "'visit'")) {
    echo "messages.type already includes 'visit'\n";
} else {
    $pdo->exec("ALTER TABLE messages MODIFY type ENUM('contact','prayer','visit') NOT NULL DEFAULT 'contact'");
    echo "Added 'visit' to messages.type\n";
}
