<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_check(): bool
{
    return isset($_SESSION['user']['id']);
}

function auth_require(): void
{
    if (!auth_check()) {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? 'index.php');
        header('Location: login.php?redirect=' . $redirect);
        exit;
    }
}

function auth_attempt(string $email, string $password): bool
{
    $email = strtolower(trim($email));
    if ($email === '' || $password === '') {
        return false;
    }

    if (auth_throttle_locked($email)) {
        return false;
    }

    try {
        $pdo = db_connect();
        $stmt = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            auth_throttle_record_failure($email);
            return false;
        }

        if (password_needs_rehash((string) $user['password_hash'], PASSWORD_DEFAULT)) {
            $rehash = password_hash($password, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
            $upd->execute([':hash' => $rehash, ':id' => $user['id']]);
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'] ?? 'admin',
        ];
        auth_throttle_clear($email);
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * Verify current user's password against the DB and replace it with a new one.
 * Throws RuntimeException with a user-safe message on validation failure.
 */
function auth_change_password(string $currentPassword, string $newPassword, string $confirmPassword): void
{
    $user = auth_user();
    if (!$user) {
        throw new RuntimeException('You must be signed in to change your password.');
    }

    if ($newPassword !== $confirmPassword) {
        throw new RuntimeException('New password and confirmation do not match.');
    }
    if (strlen($newPassword) < 10) {
        throw new RuntimeException('New password must be at least 10 characters.');
    }
    if ($newPassword === $currentPassword) {
        throw new RuntimeException('New password must be different from your current password.');
    }

    $pdo = db_connect();
    $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $user['id']]);
    $row = $stmt->fetch();
    if (!$row || !password_verify($currentPassword, (string) $row['password_hash'])) {
        throw new RuntimeException('Current password is incorrect.');
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $upd = $pdo->prepare('UPDATE users SET password_hash = :h WHERE id = :id');
    $upd->execute([':h' => $newHash, ':id' => $user['id']]);

    session_regenerate_id(true);
}

function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

/**
 * Simple in-session login throttle: 5 failures locks for 10 minutes per email.
 */
function auth_throttle_locked(string $email): bool
{
    $key = 'auth_fail_' . md5($email);
    $entry = $_SESSION[$key] ?? null;
    if (!$entry) {
        return false;
    }
    if ($entry['count'] >= 5 && (time() - $entry['last']) < 600) {
        return true;
    }
    if ((time() - $entry['last']) >= 600) {
        unset($_SESSION[$key]);
    }
    return false;
}

function auth_throttle_record_failure(string $email): void
{
    $key = 'auth_fail_' . md5($email);
    $entry = $_SESSION[$key] ?? ['count' => 0, 'last' => 0];
    $entry['count']++;
    $entry['last'] = time();
    $_SESSION[$key] = $entry;
}

function auth_throttle_clear(string $email): void
{
    unset($_SESSION['auth_fail_' . md5($email)]);
}
