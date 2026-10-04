<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/settings.php';

$user = auth_user();
$feedback = '';
$error = '';
$twoStepError = '';
$newRecoveryCodes = null;

$pdo = db_connect();
$loadAccount = function () use ($pdo, $user): array {
    $stmt = $pdo->prepare('SELECT totp_secret, totp_recovery, last_login_at, password_changed_at FROM users WHERE id = :id');
    $stmt->execute([':id' => $user['id']]);
    return $stmt->fetch() ?: [];
};
$account = $loadAccount();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = (string) ($_POST['action'] ?? 'change_password');
    try {
        if ($action === 'change_password') {
            auth_change_password(
                (string) ($_POST['current_password'] ?? ''),
                (string) ($_POST['new_password'] ?? ''),
                (string) ($_POST['confirm_password'] ?? '')
            );
            flash('profile', 'password_changed');
            header('Location: profile.php');
            exit;
        }

        if ($action === '2fa_start') {
            $_SESSION['totp_setup'] = totp_generate_secret();
        } elseif ($action === '2fa_cancel') {
            unset($_SESSION['totp_setup']);
        } elseif ($action === '2fa_confirm') {
            $secret = (string) ($_SESSION['totp_setup'] ?? '');
            if ($secret === '') {
                throw new RuntimeException('Setup expired. Please start again.');
            }
            $step = totp_verify($secret, (string) ($_POST['code'] ?? ''));
            if ($step === null) {
                throw new RuntimeException('That code is not correct. Make sure you scanned the code above and enter the newest 6-digit code.');
            }
            [$plain, $hashes] = totp_recovery_codes();
            $pdo->prepare('UPDATE users SET totp_secret = :s, totp_recovery = :r, totp_last_step = :st WHERE id = :id')
                ->execute([':s' => $secret, ':r' => json_encode($hashes), ':st' => $step, ':id' => $user['id']]);
            unset($_SESSION['totp_setup']);
            audit('2fa_enabled', 'user', $user['id'], 'Turned on two-step sign-in');
            $newRecoveryCodes = $plain;
            $feedback = 'Two-step sign-in is on. Save your recovery codes below.';
        } elseif ($action === '2fa_new_codes' || $action === '2fa_disable') {
            if (!auth_confirm_password((string) ($_POST['password'] ?? ''))) {
                throw new RuntimeException('Your password was not correct.');
            }
            if ($action === '2fa_new_codes') {
                [$plain, $hashes] = totp_recovery_codes();
                $pdo->prepare('UPDATE users SET totp_recovery = :r WHERE id = :id')->execute([':r' => json_encode($hashes), ':id' => $user['id']]);
                audit('2fa_codes_regenerated', 'user', $user['id'], 'Created new recovery codes');
                $newRecoveryCodes = $plain;
                $feedback = 'New recovery codes created. Your old codes no longer work.';
            } else {
                $pdo->prepare('UPDATE users SET totp_secret = NULL, totp_recovery = NULL, totp_last_step = NULL WHERE id = :id')->execute([':id' => $user['id']]);
                audit('2fa_disabled', 'user', $user['id'], 'Turned off two-step sign-in');
                $feedback = 'Two-step sign-in is off.';
            }
        }
    } catch (Throwable $e) {
        $msg = user_error_message($e);
        if ($action === 'change_password') {
            $error = $msg;
        } else {
            $twoStepError = $msg;
        }
    }
    $account = $loadAccount();
}

if (flash('profile') === 'password_changed') {
    $feedback = 'Password updated. Use your new password the next time you sign in.';
}

$twoStepOn = !empty($account['totp_secret']);
$recoveryLeft = count(json_decode((string) ($account['totp_recovery'] ?? ''), true) ?: []);
$setupSecret = (string) ($_SESSION['totp_setup'] ?? '');
$setupUri = $setupSecret !== '' ? totp_uri($setupSecret, $user['email'], setting('site.name', 'BMI') . ' Admin') : '';

$pageTitle = 'My Profile | BMI Admin';
require_once __DIR__ . '/includes/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">My Profile</h1>
            <p class="mt-1 text-slate-500">Update your account details, password and sign-in security.</p>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo e($feedback); ?></div>
        <?php endif; ?>

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
                    <dd class="font-semibold text-slate-900 text-base inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200/50"><?php echo e(ROLE_LABELS[$user['role']] ?? $user['role']); ?></dd>
                </div>
            </dl>
        </div>

        <div id="two-step" class="mt-8 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <h2 class="text-xl font-bold text-slate-800">Two-step sign-in</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo $twoStepOn ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'; ?>"><?php echo $twoStepOn ? 'On' : 'Off'; ?></span>
            </div>
            <p class="mt-4 text-sm text-slate-600 max-w-2xl">After your password, you'll enter a 6-digit code from an authenticator app on your phone (Google Authenticator, Microsoft Authenticator or Authy). Someone who steals your password still can't sign in.</p>

            <?php if ($twoStepError !== ''): ?>
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo e($twoStepError); ?></div>
            <?php endif; ?>

            <?php if ($newRecoveryCodes): ?>
                <div class="mt-6 rounded-lg border border-amber-300 bg-amber-50 p-5">
                    <h3 class="font-bold text-amber-900">Save these recovery codes now</h3>
                    <p class="mt-1 text-sm text-amber-900">Each code works once, if you lose your phone. They won't be shown again. Store them in a password manager or print them.</p>
                    <ul class="mt-4 grid grid-cols-2 sm:grid-cols-5 gap-2 font-mono text-sm">
                        <?php foreach ($newRecoveryCodes as $code): ?>
                            <li class="bg-white border border-amber-200 rounded px-2 py-1.5 text-center"><?php echo e($code); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!$twoStepOn && $setupSecret === ''): ?>
                <form method="post" class="mt-6">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="2fa_start">
                    <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 text-sm font-semibold shadow-md shadow-blue-500/30">Set up two-step sign-in</button>
                </form>
            <?php elseif (!$twoStepOn): ?>
                <div class="mt-6 grid md:grid-cols-[auto_1fr] gap-8 items-start">
                    <div>
                        <div id="totp-qr" class="bg-white p-3 border border-slate-200 rounded-lg inline-block" data-uri="<?php echo e($setupUri); ?>"></div>
                    </div>
                    <div class="space-y-4 text-sm">
                        <ol class="list-decimal pl-5 space-y-2 text-slate-700">
                            <li>Open your authenticator app and add a new account.</li>
                            <li>Scan the code, or type this key: <code class="font-mono bg-slate-100 px-2 py-0.5 rounded break-all"><?php echo e(trim(chunk_split($setupSecret, 4, ' '))); ?></code></li>
                            <li>Enter the 6-digit code the app shows.</li>
                        </ol>
                        <form method="post" class="flex flex-wrap gap-3 items-end">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="2fa_confirm">
                            <div>
                                <label for="code" class="block text-sm font-semibold text-slate-700 mb-1.5">Code from the app</label>
                                <input type="text" id="code" name="code" required inputmode="numeric" autocomplete="one-time-code" maxlength="7" class="w-40 border border-slate-300 rounded-lg px-4 py-2.5 font-mono tracking-widest focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">
                            </div>
                            <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 text-sm font-semibold">Turn on</button>
                        </form>
                        <form method="post">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="2fa_cancel">
                            <button type="submit" class="text-slate-500 hover:text-slate-800 text-sm">Cancel setup</button>
                        </form>
                    </div>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
                <script>
                    (function () {
                        var box = document.getElementById('totp-qr');
                        if (window.QRCode && box) {
                            new QRCode(box, { text: box.dataset.uri, width: 180, height: 180, correctLevel: QRCode.CorrectLevel.M });
                        } else if (box) {
                            box.textContent = 'Type the key shown instead.';
                        }
                    })();
                </script>
            <?php else: ?>
                <p class="mt-4 text-sm text-slate-600">Recovery codes left: <strong><?php echo (int) $recoveryLeft; ?></strong></p>
                <div class="mt-6 grid md:grid-cols-2 gap-6">
                    <form method="post" class="space-y-3 border border-slate-200 rounded-lg p-4">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="2fa_new_codes">
                        <h3 class="font-semibold text-slate-800">Create new recovery codes</h3>
                        <label for="pw-codes" class="block text-sm text-slate-600">Confirm your password</label>
                        <input type="password" id="pw-codes" name="password" required autocomplete="current-password" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">
                        <button type="submit" class="rounded-lg bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Create new codes</button>
                    </form>
                    <form method="post" class="space-y-3 border border-red-200 rounded-lg p-4">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="2fa_disable">
                        <h3 class="font-semibold text-red-800">Turn off two-step sign-in</h3>
                        <label for="pw-off" class="block text-sm text-slate-600">Confirm your password</label>
                        <input type="password" id="pw-off" name="password" required autocomplete="current-password" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">
                        <button type="submit" class="rounded-lg bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-sm font-semibold">Turn off</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4">Change Password</h2>

            <?php if ($error !== ''): ?>
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form method="post" class="mt-6 space-y-6 max-w-md" autocomplete="off">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="change_password">

                <div>
                    <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Current password</label>
                    <input type="password" id="current_password" name="current_password" required
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                           autocomplete="current-password">
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-semibold text-slate-700 mb-1.5">New password</label>
                    <input type="password" id="new_password" name="new_password" required minlength="12"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                           autocomplete="new-password">
                    <p class="mt-1.5 text-xs text-slate-500">At least 12 characters, with letters and at least one number or symbol. A passphrase works well.</p>
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm new password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="12"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                           autocomplete="new-password">
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-6 py-2.5 text-sm font-semibold transition-all shadow-md shadow-blue-500/30">Update password</button>
                </div>
            </form>
        </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
