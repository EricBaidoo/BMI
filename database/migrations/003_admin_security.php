<?php
/**
 * 003 Admin security (Phase 2):
 *  - Staff roles: admin, editor, finance
 *  - Account status, last login, two-factor sign-in fields
 *  - login_attempts table for database-backed lockouts
 *  - audit_log table
 */
return function (PDO $pdo): void {
    $columns = $pdo->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_COLUMN);

    $pdo->exec("ALTER TABLE users MODIFY role ENUM('admin','editor','finance') NOT NULL DEFAULT 'editor'");

    $add = [
        'is_active' => "TINYINT(1) NOT NULL DEFAULT 1 AFTER role",
        'last_login_at' => "DATETIME NULL",
        'password_changed_at' => "DATETIME NULL",
        'totp_secret' => "VARCHAR(64) NULL",
        'totp_recovery' => "TEXT NULL",
        'totp_last_step' => "BIGINT NULL",
    ];
    foreach ($add as $name => $def) {
        if (!in_array($name, $columns, true)) {
            $pdo->exec("ALTER TABLE users ADD COLUMN `$name` $def");
            echo "Added users.$name\n";
        }
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(150) NOT NULL,
        ip VARCHAR(45) NOT NULL,
        success TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        KEY idx_login_email_time (email, created_at),
        KEY idx_login_ip_time (ip, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS audit_log (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        user_email VARCHAR(150) NULL,
        action VARCHAR(40) NOT NULL,
        entity VARCHAR(40) NOT NULL,
        entity_id VARCHAR(64) NULL,
        summary VARCHAR(255) NOT NULL DEFAULT '',
        details MEDIUMTEXT NULL,
        ip VARCHAR(45) NULL,
        user_agent VARCHAR(255) NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        KEY idx_audit_time (created_at),
        KEY idx_audit_entity (entity, entity_id),
        KEY idx_audit_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "login_attempts and audit_log tables ready\n";
};
