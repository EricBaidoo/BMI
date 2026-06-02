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
<?php
$pageTitle = 'My Profile | BMI Admin';
require_once __DIR__ . '/includes/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">My Profile</h1>
            <p class="mt-1 text-slate-500">Update your account details and password.</p>
        </div>

        <div class="mt-6 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4">Account</h2>
            <dl class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-y-4 gap-x-6 text-sm">
                <div>
                    <dt class="text-slate-500 font-medium mb-1">Name</dt>
                    <dd class="font-semibold text-slate-900 text-base"><?php echo e($user['name']); ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-1">Email</dt>
                    <dd class="font-semibold text-slate-900 text-base"><?php echo e($user['email']); ?></dd>
                </div>
                <div>
                    <dt class="text-slate-500 font-medium mb-1">Role</dt>
                    <dd class="font-semibold text-slate-900 text-base capitalize inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200/50"><?php echo e($user['role']); ?></dd>
                </div>
            </dl>
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4">Change Password</h2>

            <?php if ($feedback !== ''): ?>
                <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <?php echo e($feedback); ?>
                </div>
            <?php endif; ?>
            <?php if ($error !== ''): ?>
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <?php echo e($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="mt-6 space-y-6 max-w-md" autocomplete="off">
                <?php echo csrf_field(); ?>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Current password</label>
                    <input type="password" name="current_password" required
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                           autocomplete="current-password">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">New password</label>
                    <input type="password" name="new_password" required minlength="10"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                           autocomplete="new-password">
                    <p class="mt-1.5 text-xs text-slate-500">At least 10 characters. Use a passphrase or password manager.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm new password</label>
                    <input type="password" name="confirm_password" required minlength="10"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                           autocomplete="new-password">
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-6 py-2.5 text-sm font-semibold transition-all shadow-md shadow-blue-500/30">Update password</button>
                </div>
            </form>
        </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
