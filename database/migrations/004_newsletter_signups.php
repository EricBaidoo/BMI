<?php
/**
 * 004 Adds the 'newsletter' message type so footer sign-ups appear in the admin Inbox.
 */
return function (PDO $pdo): void {
    $type = (string) $pdo->query("SELECT COLUMN_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'messages' AND column_name = 'type'")->fetchColumn();
    if (!str_contains($type, "'newsletter'")) {
        $pdo->exec("ALTER TABLE messages MODIFY type ENUM('contact','prayer','visit','newsletter') NOT NULL DEFAULT 'contact'");
        echo "Added 'newsletter' to messages.type\n";
    }
};
