<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/helpers.php';

$user = auth_user();
$feedback = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_check();
        auth_change_password(
            (string) ($_POST['current_password'] ?? ''),
            (string) ($_POST['new_password'] ?? ''),
            (string) ($_POST['confirm_password'] ?? '')
        );
        flash('profile', 'password_changed');
        header('Location: profile.php');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

if (flash('profile') === 'password_changed') {
    $feedback = 'Password updated. Use your new password the next time you sign in.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>My Profile | BMI Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="max-w-3xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-3xl font-bold">My Profile</h1>
                <p class="mt-2 text-slate-600">Update your account details and password.</p>
            </div>
            <div class="flex gap-2">
                <a href="index.php" class="rounded bg-slate-200 px-4 py-2 text-sm">Dashboard</a>
                <a href="logout.php" class="rounded bg-slate-800 text-white px-4 py-2 text-sm">Sign out</a>
            </div>
        </div>

        <div class="mt-6 bg-white border border-slate-200 rounded p-5">
            <h2 class="text-xl font-semibold">Account</h2>
            <dl class="mt-4 grid grid-cols-3 gap-y-2 text-sm">
                <dt class="text-slate-500">Name</dt>
                <dd class="col-span-2 font-medium"><?php echo e($user['name']); ?></dd>
                <dt class="text-slate-500">Email</dt>
                <dd class="col-span-2 font-medium"><?php echo e($user['email']); ?></dd>
                <dt class="text-slate-500">Role</dt>
                <dd class="col-span-2 font-medium capitalize"><?php echo e($user['role']); ?></dd>
            </dl>
        </div>

        <div class="mt-6 bg-white border border-slate-200 rounded p-5">
            <h2 class="text-xl font-semibold">Change Password</h2>

            <?php if ($feedback !== ''): ?>
                <div class="mt-4 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo e($feedback); ?></div>
            <?php endif; ?>
            <?php if ($error !== ''): ?>
                <div class="mt-4 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form method="post" class="mt-4 space-y-4" autocomplete="off">
                <?php echo csrf_field(); ?>

                <label class="block text-sm">
                    <span class="font-medium">Current password</span>
                    <input type="password" name="current_password" required
                           class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                           autocomplete="current-password">
                </label>
                <label class="block text-sm">
                    <span class="font-medium">New password</span>
                    <input type="password" name="new_password" required minlength="10"
                           class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                           autocomplete="new-password">
                    <p class="mt-1 text-xs text-slate-500">At least 10 characters. Use a passphrase or password manager.</p>
                </label>
                <label class="block text-sm">
                    <span class="font-medium">Confirm new password</span>
                    <input type="password" name="confirm_password" required minlength="10"
                           class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                           autocomplete="new-password">
                </label>

                <button type="submit" class="rounded bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 text-sm font-semibold">Update password</button>
            </form>
        </div>
    </div>
</body>
</html>
