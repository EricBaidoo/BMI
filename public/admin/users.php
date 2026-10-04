<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require('users');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/helpers.php';

$currentUser = auth_user();
$feedback = '';
$error = '';
$editing = null;

/** Number of active administrators other than $exceptId. */
$otherActiveAdmins = function (PDO $pdo, int $exceptId): int {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND is_active = 1 AND id != :id");
    $stmt->execute([':id' => $exceptId]);
    return (int) $stmt->fetchColumn();
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_check();
        $pdo = db_connect();
        $action = (string) ($_POST['action'] ?? '');

        // Account and password changes for other people need the administrator's own password.
        $needsConfirm = in_array($action, ['add', 'delete', 'reset_2fa'], true)
            || ($action === 'edit' && ((string) ($_POST['password'] ?? '') !== '' || (int) ($_POST['id'] ?? 0) !== (int) $currentUser['id']));
        if ($needsConfirm && !auth_confirm_password((string) ($_POST['confirm_password'] ?? ''))) {
            throw new RuntimeException('Enter your own password to confirm this change.');
        }

        if ($action === 'add' || $action === 'edit') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = strtolower(trim((string) ($_POST['email'] ?? '')));
            $role = (string) ($_POST['role'] ?? 'editor');
            $active = isset($_POST['is_active']) ? 1 : 0;
            $password = (string) ($_POST['password'] ?? '');

            if (!array_key_exists($role, ROLE_LABELS)) {
                throw new RuntimeException('Invalid role.');
            }
            if ($name === '' || $email === '') {
                throw new RuntimeException('Name and email are required.');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Invalid email address.');
            }
            if ($password !== '') {
                auth_validate_new_password($password);
            }

            if ($action === 'add') {
                if ($password === '') {
                    throw new RuntimeException('Password is required for new users.');
                }
                $check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :e');
                $check->execute([':e' => $email]);
                if ((int) $check->fetchColumn() > 0) {
                    throw new RuntimeException('Email is already registered.');
                }

                $pdo->prepare('INSERT INTO users (name, email, role, is_active, password_hash, password_changed_at) VALUES (:n, :e, :r, :a, :h, NOW())')
                    ->execute([':n' => $name, ':e' => $email, ':r' => $role, ':a' => $active, ':h' => password_hash($password, PASSWORD_DEFAULT)]);
                $newId = (int) $pdo->lastInsertId();
                audit('create', 'user', $newId, "Added {$email} as " . ROLE_LABELS[$role], ['role' => $role, 'active' => $active]);

                header('Location: users.php?status=added');
                exit;
            }

            $id = (int) ($_POST['id'] ?? 0);
            $before = $pdo->prepare('SELECT id, name, email, role, is_active FROM users WHERE id = :id');
            $before->execute([':id' => $id]);
            $old = $before->fetch();
            if (!$old) {
                throw new RuntimeException('User not found.');
            }

            if ($id === (int) $currentUser['id'] && (!$active || $role !== $old['role'])) {
                throw new RuntimeException('You cannot change your own role or deactivate yourself. Ask another administrator.');
            }
            if ($old['role'] === 'admin' && ($role !== 'admin' || !$active) && $otherActiveAdmins($pdo, $id) === 0) {
                throw new RuntimeException('This is the last active administrator. Make someone else an administrator first.');
            }

            $check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :e AND id != :id');
            $check->execute([':e' => $email, ':id' => $id]);
            if ((int) $check->fetchColumn() > 0) {
                throw new RuntimeException('Email is already registered by another user.');
            }

            $pdo->prepare('UPDATE users SET name = :n, email = :e, role = :r, is_active = :a WHERE id = :id')
                ->execute([':n' => $name, ':e' => $email, ':r' => $role, ':a' => $active, ':id' => $id]);
            if ($password !== '') {
                $pdo->prepare('UPDATE users SET password_hash = :h, password_changed_at = NOW() WHERE id = :id')
                    ->execute([':h' => password_hash($password, PASSWORD_DEFAULT), ':id' => $id]);
            }

            $changes = [];
            foreach (['name' => $name, 'email' => $email, 'role' => $role, 'is_active' => $active] as $field => $value) {
                if ((string) $old[$field] !== (string) $value) {
                    $changes[$field] = [$old[$field], $value];
                }
            }
            if ($password !== '') {
                $changes['password'] = ['(hidden)', '(changed)'];
            }
            if ($changes) {
                audit('update', 'user', $id, "Updated account {$email}", ['changes' => $changes]);
            }

            header('Location: users.php?status=updated');
            exit;
        }

        if ($action === 'delete' || $action === 'reset_2fa') {
            $id = (int) ($_POST['id'] ?? 0);
            $stmt = $pdo->prepare('SELECT id, email, role, is_active FROM users WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $target = $stmt->fetch();
            if (!$target) {
                throw new RuntimeException('User not found.');
            }

            if ($action === 'reset_2fa') {
                $pdo->prepare('UPDATE users SET totp_secret = NULL, totp_recovery = NULL, totp_last_step = NULL WHERE id = :id')->execute([':id' => $id]);
                audit('2fa_reset', 'user', $id, "Reset two-step sign-in for {$target['email']}");
                header('Location: users.php?status=reset_2fa');
                exit;
            }

            if ($id === (int) $currentUser['id']) {
                throw new RuntimeException('You cannot delete your own account.');
            }
            if ($target['role'] === 'admin' && $otherActiveAdmins($pdo, $id) === 0) {
                throw new RuntimeException('This is the last active administrator and cannot be deleted.');
            }
            $pdo->prepare('DELETE FROM users WHERE id = :id')->execute([':id' => $id]);
            audit('delete', 'user', $id, "Deleted account {$target['email']}", ['role' => $target['role']]);
            header('Location: users.php?status=deleted');
            exit;
        }
    } catch (Throwable $e) {
        $error = user_error_message($e);
    }
}

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    if ($id > 0) {
        try {
            $stmt = db_connect()->prepare('SELECT id, name, email, role, is_active, created_at FROM users WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $editing = $stmt->fetch() ?: null;
        } catch (Throwable $e) {
            $error = user_error_message($e, 'Unable to load user.');
        }
    }
}

$flashMap = [
    'added' => 'User added.',
    'updated' => 'User updated.',
    'deleted' => 'User deleted.',
    'reset_2fa' => 'Two-step sign-in was reset. The user can sign in with their password and set it up again.',
];
$feedback = $flashMap[$_GET['status'] ?? ''] ?? '';

$users = [];
try {
    $users = db_connect()->query('SELECT id, name, email, role, is_active, created_at, last_login_at, totp_secret IS NOT NULL AS has_2fa FROM users ORDER BY is_active DESC, name ASC')->fetchAll();
} catch (Throwable $e) {
    $error = $error ?: user_error_message($e, 'Unable to load users.');
}

$roleBadge = ['admin' => 'bg-blue-100 text-blue-800', 'editor' => 'bg-slate-200 text-slate-700', 'finance' => 'bg-amber-100 text-amber-800'];

$pageTitle = 'Manage Users | BMI Admin';
require_once __DIR__ . '/includes/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Manage Users</h1>
            <p class="mt-1 text-slate-500">Add staff, choose what each person can change, and turn accounts off when someone leaves.</p>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo e($feedback); ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo e($error); ?></div>
        <?php endif; ?>

        <div class="mt-6 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4"><?php echo $editing ? 'Edit User' : 'Add New User'; ?></h2>
            <form method="post" class="mt-6 grid md:grid-cols-2 gap-6" autocomplete="off">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="<?php echo $editing ? 'edit' : 'add'; ?>">
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo (int) $editing['id']; ?>">
                <?php endif; ?>

                <div>
                    <label for="u-name" class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name *</label>
                    <input type="text" id="u-name" name="name" required maxlength="100" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editing ? e($editing['name']) : ''; ?>">
                </div>

                <div>
                    <label for="u-email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address *</label>
                    <input type="email" id="u-email" name="email" required maxlength="150" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editing ? e($editing['email']) : ''; ?>">
                </div>

                <div>
                    <label for="u-role" class="block text-sm font-semibold text-slate-700 mb-1.5">Role *</label>
                    <select id="u-role" name="role" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                        <?php $selectedRole = $editing['role'] ?? 'editor'; ?>
                        <option value="editor" <?php echo $selectedRole === 'editor' ? 'selected' : ''; ?>>Editor: sermons, events, blog, pages, livestream, inbox</option>
                        <option value="finance" <?php echo $selectedRole === 'finance' ? 'selected' : ''; ?>>Finance: giving details only</option>
                        <option value="admin" <?php echo $selectedRole === 'admin' ? 'selected' : ''; ?>>Administrator: everything, including users and audit log</option>
                    </select>
                </div>

                <div>
                    <label for="u-password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Password <?php echo $editing ? '(leave blank to keep current)' : '*'; ?>
                    </label>
                    <input type="password" id="u-password" name="password" <?php echo $editing ? '' : 'required'; ?> minlength="12" autocomplete="new-password" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                    <p class="mt-1.5 text-xs text-slate-500">At least 12 characters, with letters and a number or symbol.</p>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="u-active" name="is_active" value="1" class="w-4 h-4" <?php echo !$editing || (int) $editing['is_active'] === 1 ? 'checked' : ''; ?>>
                    <label for="u-active" class="text-sm font-semibold text-slate-700">Account is active (untick to block sign-in without deleting)</label>
                </div>

                <div>
                    <label for="u-confirm" class="block text-sm font-semibold text-slate-700 mb-1.5">Your password (to confirm)</label>
                    <input type="password" id="u-confirm" name="confirm_password" autocomplete="current-password" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                    <p class="mt-1.5 text-xs text-slate-500">Needed when adding staff, changing someone else's account or setting a password.</p>
                </div>

                <div class="md:col-span-2 pt-4 border-t border-slate-100 flex gap-3">
                    <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-6 py-2.5 text-sm font-semibold transition-all shadow-md shadow-blue-500/30"><?php echo $editing ? 'Update User' : 'Add User'; ?></button>
                    <?php if ($editing): ?>
                        <a href="users.php" class="rounded-lg border border-slate-300 text-slate-700 px-6 py-2.5 text-sm font-semibold hover:bg-slate-50 transition-all text-center">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4">All Users</h2>
            <?php if (empty($users)): ?>
                <p class="mt-4 text-sm text-slate-600">No users found.</p>
            <?php else: ?>
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500 border-b border-slate-200 bg-slate-50/50">
                                <th class="py-3 px-4 font-semibold">Name</th>
                                <th class="py-3 px-4 font-semibold">Email</th>
                                <th class="py-3 px-4 font-semibold">Role</th>
                                <th class="py-3 px-4 font-semibold">Two-step</th>
                                <th class="py-3 px-4 font-semibold">Last sign-in</th>
                                <th class="py-3 px-4 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors last:border-0 <?php echo (int) $u['is_active'] === 1 ? '' : 'opacity-60'; ?>">
                                <td class="py-3 px-4 font-medium text-slate-900">
                                    <?php echo e($u['name']); ?>
                                    <?php if ((int) $u['is_active'] !== 1): ?><span class="ml-2 text-xs font-semibold text-red-700">Inactive</span><?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-slate-600"><?php echo e($u['email']); ?></td>
                                <td class="py-3 px-4"><span class="inline-block rounded-full px-3 py-1 text-xs font-semibold <?php echo $roleBadge[$u['role']] ?? ''; ?>"><?php echo e(ROLE_LABELS[$u['role']] ?? $u['role']); ?></span></td>
                                <td class="py-3 px-4"><?php echo (int) $u['has_2fa'] === 1 ? '<span class="text-emerald-700 font-semibold">On</span>' : '<span class="text-amber-700 font-semibold">Off</span>'; ?></td>
                                <td class="py-3 px-4 text-slate-500"><?php echo $u['last_login_at'] ? e(date('M j, Y H:i', strtotime($u['last_login_at']))) : 'Never'; ?></td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <a href="users.php?edit=<?php echo (int) $u['id']; ?>" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                    <?php if ((int) $u['id'] !== (int) $currentUser['id']): ?>
                                        <details class="inline-block ml-4 text-left align-top">
                                            <summary class="cursor-pointer text-slate-600 hover:text-slate-900 font-medium">More</summary>
                                            <div class="absolute z-10 mt-2 w-72 right-8 bg-white border border-slate-200 rounded-lg shadow-lg p-4 space-y-4">
                                                <?php if ((int) $u['has_2fa'] === 1): ?>
                                                <form method="post" class="space-y-2">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="action" value="reset_2fa">
                                                    <input type="hidden" name="id" value="<?php echo (int) $u['id']; ?>">
                                                    <label class="block text-xs text-slate-600">Reset two-step sign-in (lost phone). Your password:</label>
                                                    <input type="password" name="confirm_password" required class="w-full border border-slate-300 rounded px-3 py-1.5">
                                                    <button type="submit" class="text-amber-700 hover:text-amber-900 font-semibold">Reset two-step</button>
                                                </form>
                                                <?php endif; ?>
                                                <form method="post" class="space-y-2">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo (int) $u['id']; ?>">
                                                    <label class="block text-xs text-slate-600">Delete permanently. Prefer making the account inactive. Your password:</label>
                                                    <input type="password" name="confirm_password" required class="w-full border border-slate-300 rounded px-3 py-1.5">
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete user</button>
                                                </form>
                                            </div>
                                        </details>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
