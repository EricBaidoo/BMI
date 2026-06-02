<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo = db_connect();
    
    $email = 'admin@bmi.org';
    $password = 'BMIAdmin2026!';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :e');
    $stmt->execute([':e' => $email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $update = $pdo->prepare('UPDATE users SET password_hash = :p WHERE email = :e');
        $update->execute([':p' => $hash, ':e' => $email]);
        echo "<h1>Admin account reset successfully!</h1>";
    } else {
        $insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (:n, :e, :p, "admin")');
        $insert->execute([':n' => 'Super Admin', ':e' => $email, ':p' => $hash]);
        echo "<h1>Admin account created successfully!</h1>";
    }
    
    echo "<p><strong>Login URL:</strong> <a href='admin/login.php'>http://localhost/BMI/admin/login.php</a></p>";
    echo "<p><strong>Email:</strong> admin@bmi.org</p>";
    echo "<p><strong>Password:</strong> BMIAdmin2026!</p>";
    echo "<p><small>Note: Please delete this file (create_admin.php) after you log in for security purposes.</small></p>";
    
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}
