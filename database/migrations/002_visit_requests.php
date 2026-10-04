<?php
/**
 * 002 Adds the 'visit' message type so Plan a Visit submissions land in the admin Inbox.
 */
return function (PDO $pdo): void {
    $type = (string) $pdo->query("SELECT COLUMN_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'messages' AND column_name = 'type'")->fetchColumn();
    if (!str_contains($type, "'visit'")) {
        $pdo->exec("ALTER TABLE messages MODIFY type ENUM('contact','prayer','visit') NOT NULL DEFAULT 'contact'");
        echo "Added 'visit' to messages.type\n";
    }
};
