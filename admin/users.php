<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

$currentUser = auth_user();
if (($currentUser['role'] ?? 'admin') !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    die('Only administrators can manage users.');
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/helpers.php';

$feedback = '';
$error = '';
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_check();
        $pdo = db_connect();
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'add' || $action === 'edit') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = strtolower(trim((string) ($_POST['email'] ?? '')));
            $role = (string) ($_POST['role'] ?? 'admin');
            $password = (string) ($_POST['password'] ?? '');

            if (!in_array($role, ['admin', 'editor'], true)) {
                throw new RuntimeException('Invalid role.');
            }
            if ($name === '' || $email === '') {
                throw new RuntimeException('Name and email are required.');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Invalid email address.');
            }

            if ($action === 'add') {
                if ($password === '') {
                    throw new RuntimeException('Password is required for new users.');
                }
                if (strlen($password) < 10) {
                    throw new RuntimeException('Password must be at least 10 characters.');
                }

                $check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :e');
                $check->execute([':e' => $email]);
                if ((int) $check->fetchColumn() > 0) {
                    throw new RuntimeException('Email is already registered.');
                }

                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (name, email, role, password_hash) VALUES (:n, :e, :r, :h)');
                $stmt->execute([':n' => $name, ':e' => $email, ':r' => $role, ':h' => $hash]);

                header('Location: users.php?status=added');
                exit;
            }

            // Edit
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid user ID.');
            }

            // Prevent changing own role or email if there is a risk, but we'll allow it.
            // Check email uniqueness ignoring self
            $check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :e AND id != :id');
            $check->execute([':e' => $email, ':id' => $id]);
            if ((int) $check->fetchColumn() > 0) {
                throw new RuntimeException('Email is already registered by another user.');
            }

            if ($password !== '') {
                if (strlen($password) < 10) {
                    throw new RuntimeException('Password must be at least 10 characters.');
                }
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE users SET name = :n, email = :e, role = :r, password_hash = :h WHERE id = :id');
                $stmt->execute([':n' => $name, ':e' => $email, ':r' => $role, ':h' => $hash, ':id' => $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE users SET name = :n, email = :e, role = :r WHERE id = :id');
                $stmt->execute([':n' => $name, ':e' => $email, ':r' => $role, ':id' => $id]);
            }

            // If user updated themselves, refresh session
            if ($id === (int)$currentUser['id']) {
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['role'] = $role;
            }

            header('Location: users.php?status=updated');
            exit;
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid ID.');
            }
            if ($id === (int)$currentUser['id']) {
                throw new RuntimeException('You cannot delete your own account.');
            }
            
            $pdo->prepare('DELETE FROM users WHERE id = :id')->execute([':id' => $id]);
            header('Location: users.php?status=deleted');
            exit;
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    if ($id > 0) {
        try {
            $pdo = db_connect();
            $stmt = $pdo->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $editing = $stmt->fetch();
        } catch (Throwable $e) {
            $error = 'Unable to load user.';
        }
    }
}

$flashMap = ['added' => 'User added successfully.', 'updated' => 'User updated successfully.', 'deleted' => 'User deleted successfully.'];
$feedback = $flashMap[$_GET['status'] ?? ''] ?? '';

$users = [];
try {
    $pdo = db_connect();
    $users = $pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY name ASC')->fetchAll();
} catch (Throwable $e) {
    if ($error === '') {
        $error = 'Unable to load users.';
    }
}
?>
<?php
$pageTitle = 'Manage Users | BMI Admin';
require_once __DIR__ . '/includes/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Manage Users</h1>
            <p class="mt-1 text-slate-500">Add, edit, or remove administrators and editors for the website.</p>
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
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name *</label>
                    <input type="text" name="name" required maxlength="100" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editing ? e($editing['name']) : ''; ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address *</label>
                    <input type="email" name="email" required maxlength="150" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editing ? e($editing['email']) : ''; ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role *</label>
                    <select name="role" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                        <option value="admin" <?php echo $editing && $editing['role'] === 'admin' ? 'selected' : ''; ?>>Admin (Full Access)</option>
                        <option value="editor" <?php echo $editing && $editing['role'] === 'editor' ? 'selected' : ''; ?>>Editor (Content Only)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Password <?php echo $editing ? '(Leave blank to keep current)' : '*'; ?>
                    </label>
                    <input type="password" name="password" <?php echo $editing ? '' : 'required'; ?> minlength="10" autocomplete="new-password" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
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
                                <th class="py-3 px-4 font-semibold rounded-tl-lg">Name</th>
                                <th class="py-3 px-4 font-semibold">Email</th>
                                <th class="py-3 px-4 font-semibold">Role</th>
                                <th class="py-3 px-4 font-semibold">Joined</th>
                                <th class="py-3 px-4 font-semibold text-right rounded-tr-lg">Actions</th>
                            </tr>
                        </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors last:border-0">
                                <td class="py-3 px-4 font-medium text-slate-900 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-300 font-bold border border-slate-700 text-xs">
                                        <?php echo strtoupper(substr($u['name'] ?? $u['email'], 0, 1)); ?>
                                    </div>
                                    <?php echo e($u['name']); ?>
                                </td>
                                <td class="py-3 px-4 text-slate-600"><?php echo e($u['email']); ?></td>
                                <td class="py-3 px-4">
                                    <?php if ($u['role'] === 'admin'): ?>
                                        <span class="inline-block rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-xs font-semibold">Admin</span>
                                    <?php else: ?>
                                        <span class="inline-block rounded-full bg-slate-200 text-slate-700 px-3 py-1 text-xs font-semibold">Editor</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-slate-500"><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                                <td class="py-3 px-4 text-right">
                                    <a href="users.php?edit=<?php echo (int) $u['id']; ?>" class="text-blue-600 hover:text-blue-800 font-medium mr-4">Edit</a>
                                    <?php if ((int)$u['id'] !== (int)$currentUser['id']): ?>
                                        <form method="post" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int) $u['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-slate-400 font-medium cursor-not-allowed" title="You cannot delete yourself">Delete</span>
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
