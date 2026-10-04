<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/logger.php';
require_once __DIR__ . '/audit.php';
require_once __DIR__ . '/totp.php';

/** Sessions end after this much inactivity, and in any case this long after sign-in. */
const AUTH_IDLE_TIMEOUT = 30 * 60;
const AUTH_ABSOLUTE_TIMEOUT = 12 * 60 * 60;

/** Lockouts: failures allowed in the window, per account and per IP address. */
const AUTH_LOCK_WINDOW = 15 * 60;
const AUTH_MAX_FAILS_PER_ACCOUNT = 5;
const AUTH_MAX_FAILS_PER_IP = 20;

/**
 * What each staff role may do.
 *  content  sermons, events, blog, ministries, homepage, page text
 *  live     livestream control
 *  inbox    contact, prayer and visit messages
 *  settings site settings (except giving and analytics)
 *  giving   bank, mobile money and online giving details
 *  users    staff accounts
 *  audit    audit log
 */
const ROLE_CAPABILITIES = [
    'admin' => ['content', 'live', 'inbox', 'settings', 'giving', 'users', 'audit'],
    'editor' => ['content', 'live', 'inbox', 'settings'],
    'finance' => ['giving'],
];

const ROLE_LABELS = [
    'admin' => 'Administrator',
    'editor' => 'Editor',
    'finance' => 'Finance',
];

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_check(): bool
{
    return isset($_SESSION['user']['id']);
}

function auth_can(string $capability, ?array $user = null): bool
{
    $user = $user ?? auth_user();
    $role = (string) ($user['role'] ?? '');
    return in_array($capability, ROLE_CAPABILITIES[$role] ?? [], true);
}

/**
 * Require a signed-in, active user (and optionally a capability) for an admin page.
 * Enforces session timeouts and re-reads the account from the database on every request,
 * so a deactivated or demoted user loses access immediately.
 */
function auth_require(?string $capability = null): void
{
    if (!auth_check()) {
        auth_redirect_to_login();
    }

    $now = time();
    $idle = $now - (int) ($_SESSION['auth_last_activity'] ?? 0);
    $age = $now - (int) ($_SESSION['auth_login_time'] ?? 0);
    if ($idle > AUTH_IDLE_TIMEOUT || $age > AUTH_ABSOLUTE_TIMEOUT) {
        auth_logout();
        app_session_start();
        auth_redirect_to_login('timeout');
    }

    try {
        $stmt = db_connect()->prepare('SELECT id, name, email, role, is_active FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => (int) $_SESSION['user']['id']]);
        $row = $stmt->fetch();
    } catch (Throwable $e) {
        log_exception($e, 'auth_require');
        http_response_code(503);
        exit('The admin panel is temporarily unavailable. Please try again shortly.');
    }
    if (!$row || (int) $row['is_active'] !== 1) {
        auth_logout();
        app_session_start();
        auth_redirect_to_login('disabled');
    }
    $_SESSION['user']['name'] = $row['name'];
    $_SESSION['user']['email'] = $row['email'];
    $_SESSION['user']['role'] = $row['role'];
    $_SESSION['auth_last_activity'] = $now;

    if ($capability !== null && !auth_can($capability)) {
        auth_forbidden();
    }
}

function auth_redirect_to_login(string $reason = ''): void
{
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    $target = auth_safe_redirect(basename((string) parse_url($uri, PHP_URL_PATH)) . (($q = parse_url($uri, PHP_URL_QUERY)) ? '?' . $q : ''));
    $query = ['redirect' => $target];
    if ($reason !== '') {
        $query['reason'] = $reason;
    }
    header('Location: login.php?' . http_build_query($query));
    exit;
}

/** Only allow redirects to admin pages in this folder, e.g. "sermons.php?id=4". */
function auth_safe_redirect(string $target): string
{
    if (preg_match('/^[a-z0-9_-]+\.php(\?[A-Za-z0-9_\-=&%.]*)?$/i', $target) && !str_starts_with(strtolower($target), 'logout.php')) {
        return $target;
    }
    return 'index.php';
}

function auth_forbidden(): void
{
    http_response_code(403);
    $pageTitle = 'Access denied | BMI Admin';
    require __DIR__ . '/../admin/includes/header.php';
    echo '<div class="max-w-xl mt-10 bg-white border border-slate-200 rounded-xl p-8 shadow-sm">'
        . '<h1 class="text-xl font-bold text-slate-800">You don\'t have access to this page</h1>'
        . '<p class="mt-3 text-slate-600">Your role (' . htmlspecialchars(ROLE_LABELS[auth_user()['role'] ?? ''] ?? 'unknown') . ') does not include this area. '
        . 'If you need it, ask an administrator to change your role.</p>'
        . '<a href="index.php" class="inline-block mt-6 text-blue-600 font-medium hover:underline">Back to the dashboard</a></div>';
    require __DIR__ . '/../admin/includes/footer.php';
    exit;
}

function auth_client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
}

function auth_is_locked(string $email): bool
{
    try {
        // Time arithmetic stays in MySQL: PHP and MySQL time zones can differ.
        $pdo = db_connect();
        $window = (int) AUTH_LOCK_WINDOW;
        $byEmail = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE email = :e AND success = 0 AND created_at > (NOW() - INTERVAL {$window} SECOND)");
        $byEmail->execute([':e' => $email]);
        $byIp = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip = :ip AND success = 0 AND created_at > (NOW() - INTERVAL {$window} SECOND)");
        $byIp->execute([':ip' => auth_client_ip()]);
        return (int) $byEmail->fetchColumn() >= AUTH_MAX_FAILS_PER_ACCOUNT
            || (int) $byIp->fetchColumn() >= AUTH_MAX_FAILS_PER_IP;
    } catch (Throwable $e) {
        log_exception($e, 'auth_is_locked');
        return true; // fail closed
    }
}

function auth_record_attempt(string $email, bool $success): void
{
    try {
        db_connect()->prepare('INSERT INTO login_attempts (email, ip, success) VALUES (:e, :ip, :s)')
            ->execute([':e' => mb_substr($email, 0, 150), ':ip' => auth_client_ip(), ':s' => $success ? 1 : 0]);
        if (random_int(1, 50) === 1) {
            db_connect()->exec('DELETE FROM login_attempts WHERE created_at < (NOW() - INTERVAL 30 DAY)');
        }
    } catch (Throwable $e) {
        log_exception($e, 'auth_record_attempt');
    }
}

/**
 * First sign-in step. Returns:
 *  'ok'      signed in
 *  '2fa'     password correct; a code from the authenticator app is needed next
 *  'locked'  too many recent failures
 *  'invalid' wrong email/password or inactive account
 */
function auth_attempt(string $email, string $password): string
{
    $email = strtolower(trim($email));
    if ($email === '' || $password === '') {
        return 'invalid';
    }
    if (auth_is_locked($email)) {
        audit('login_locked', 'user', null, "Sign-in blocked by lockout for {$email}", [], ['email' => $email]);
        return 'locked';
    }

    try {
        $stmt = db_connect()->prepare('SELECT id, name, email, password_hash, role, is_active, totp_secret FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
    } catch (Throwable $e) {
        log_exception($e, 'auth_attempt');
        return 'invalid';
    }

    // Verify against a dummy hash when the account doesn't exist, so timing doesn't reveal valid emails.
    $hash = $user ? (string) $user['password_hash'] : '$2y$10$1Fm1iFIgzqPFBWvUw29XoutqATbB.ejkEz3PoEgeFaD5ZmTCEORiq';
    $valid = password_verify($password, $hash) && $user && (int) $user['is_active'] === 1;

    if (!$valid) {
        auth_record_attempt($email, false);
        audit('login_failed', 'user', $user['id'] ?? null, "Failed sign-in for {$email}", [], ['email' => $email]);
        return 'invalid';
    }

    if (password_needs_rehash((string) $user['password_hash'], PASSWORD_DEFAULT)) {
        db_connect()->prepare('UPDATE users SET password_hash = :h WHERE id = :id')
            ->execute([':h' => password_hash($password, PASSWORD_DEFAULT), ':id' => $user['id']]);
    }

    if (!empty($user['totp_secret'])) {
        session_regenerate_id(true);
        $_SESSION['auth_pending'] = ['id' => (int) $user['id'], 'email' => $user['email'], 'time' => time(), 'tries' => 0];
        return '2fa';
    }

    auth_complete_login($user);
    return 'ok';
}

/** Second sign-in step: a 6-digit app code or a one-time recovery code. Returns 'ok', 'invalid' or 'expired'. */
function auth_verify_second_factor(string $code): string
{
    $pending = $_SESSION['auth_pending'] ?? null;
    if (!$pending || time() - (int) $pending['time'] > 300 || (int) $pending['tries'] >= 5) {
        unset($_SESSION['auth_pending']);
        return 'expired';
    }
    $_SESSION['auth_pending']['tries']++;

    $pdo = db_connect();
    $stmt = $pdo->prepare('SELECT id, name, email, role, is_active, totp_secret, totp_recovery, totp_last_step FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $pending['id']]);
    $user = $stmt->fetch();
    if (!$user || (int) $user['is_active'] !== 1 || empty($user['totp_secret'])) {
        unset($_SESSION['auth_pending']);
        return 'expired';
    }

    $code = strtoupper(trim($code));
    $step = totp_verify((string) $user['totp_secret'], $code, $user['totp_last_step'] !== null ? (int) $user['totp_last_step'] : null);
    if ($step !== null) {
        $pdo->prepare('UPDATE users SET totp_last_step = :s WHERE id = :id')->execute([':s' => $step, ':id' => $user['id']]);
        auth_complete_login($user);
        return 'ok';
    }

    $recovery = json_decode((string) $user['totp_recovery'], true) ?: [];
    $plain = str_replace(['-', ' '], '', $code);
    foreach ($recovery as $i => $hash) {
        if (strlen($plain) === 8 && password_verify($plain, $hash)) {
            unset($recovery[$i]);
            $pdo->prepare('UPDATE users SET totp_recovery = :r WHERE id = :id')->execute([':r' => json_encode(array_values($recovery)), ':id' => $user['id']]);
            auth_complete_login($user);
            audit('2fa_recovery_used', 'user', $user['id'], 'Signed in with a recovery code (' . count($recovery) . ' left)');
            return 'ok';
        }
    }

    auth_record_attempt((string) $user['email'], false);
    audit('2fa_failed', 'user', $user['id'], 'Wrong two-factor code for ' . $user['email'], [], ['id' => $user['id'], 'email' => $user['email']]);
    return 'invalid';
}

function auth_complete_login(array $user): void
{
    unset($_SESSION['auth_pending']);
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
    $_SESSION['auth_login_time'] = time();
    $_SESSION['auth_last_activity'] = time();
    unset($_SESSION['csrf_token']);

    auth_record_attempt((string) $user['email'], true);
    try {
        db_connect()->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id')->execute([':id' => $user['id']]);
    } catch (Throwable $e) {
        log_exception($e, 'auth_complete_login');
    }
    audit('login', 'user', $user['id'], 'Signed in' . (!empty($user['totp_secret']) ? ' with two-factor' : ''));
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
    auth_validate_new_password($newPassword);
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

    $pdo->prepare('UPDATE users SET password_hash = :h, password_changed_at = NOW() WHERE id = :id')
        ->execute([':h' => password_hash($newPassword, PASSWORD_DEFAULT), ':id' => $user['id']]);

    session_regenerate_id(true);
    audit('password_changed', 'user', $user['id'], 'Changed own password');
}

function auth_validate_new_password(string $password): void
{
    if (strlen($password) < 12) {
        throw new RuntimeException('Passwords must be at least 12 characters.');
    }
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[^A-Za-z]/', $password)) {
        throw new RuntimeException('Passwords must contain letters and at least one number or symbol.');
    }
}

/** Confirms the signed-in user's password (used before sensitive changes). */
function auth_confirm_password(string $password): bool
{
    $user = auth_user();
    if (!$user || $password === '') {
        return false;
    }
    $stmt = db_connect()->prepare('SELECT password_hash FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $user['id']]);
    return password_verify($password, (string) $stmt->fetchColumn());
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
