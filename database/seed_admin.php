<?php
/**
 * One-time admin account bootstrap.
 *
 * Usage:  php database/seed_admin.php "Admin Name" admin@example.com "StrongPasswordHere"
 *
 * Refuses to run if any users already exist (use the admin UI to add more from then on).
 */

require_once __DIR__ . '/../includes/db.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script must be run from the command line.\n");
}

if ($argc < 4) {
    fwrite(STDERR, "Usage: php database/seed_admin.php \"Name\" email@example.com \"Password\"\n");
    exit(1);
}

[$_, $name, $email, $password] = $argv;
$name = trim($name);
$email = strtolower(trim($email));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Invalid email.\n");
    exit(1);
}
if (strlen($password) < 10) {
    fwrite(STDERR, "Password must be at least 10 characters.\n");
    exit(1);
}

$pdo = db_connect();
$count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
if ($count > 0) {
    fwrite(STDERR, "Refusing: users table is not empty. Add new users via the admin UI.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (:n, :e, :p, "admin")');
$stmt->execute([':n' => $name, ':e' => $email, ':p' => $hash]);

echo "Admin user created: $email\n";
