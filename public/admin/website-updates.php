<?php
/**
 * Website Updates: everything needed after a GitHub deploy, without server access.
 * Shows the deployed version, applies database updates (after an automatic backup),
 * downloads backups, and checks the server settings.
 */
require_once __DIR__ . '/../../includes/auth.php';
auth_require('system');

require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/settings.php';
require_once __DIR__ . '/../../includes/maintenance.php';

$feedback = '';
$error = '';
$output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = (string) ($_POST['action'] ?? '');
    try {
        if ($action === 'download_backup') {
            $b = create_backup();
            audit('backup', 'system', null, 'Downloaded a database backup');
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            header('Content-Type: application/gzip');
            header('Content-Disposition: attachment; filename="' . basename($b['file']) . '"');
            header('Content-Length: ' . filesize($b['file']));
            header('Cache-Control: no-store');
            readfile($b['file']);
            exit;
        }

        if ($action === 'apply') {
            // Always back up first; if that fails, change nothing.
            $b = create_backup();
            $result = migrations_apply();
            $output = $result['output'];
            if ($result['error'] !== '') {
                audit('update', 'system', null, 'Database update failed: ' . $result['error']);
                throw new RuntimeException('An update failed and the rest were not run: ' . $result['error']
                    . ' A backup was saved first (' . basename($b['file']) . ').');
            }
            audit('update', 'system', null, $result['applied'] ? 'Applied database updates: ' . implode(', ', $result['applied']) : 'Checked for database updates (none pending)');
            flash('updates', $result['applied'] ? 'Applied ' . count($result['applied']) . ' update(s). A backup was saved first.' : 'The database was already up to date.');
            header('Location: website-updates.php');
            exit;
        }
    } catch (Throwable $e) {
        $error = user_error_message($e, 'That did not work. Details are in the error log.');
    }
}

$flashMsg = flash('updates');
if ($flashMsg !== null) {
    $feedback = $flashMsg;
}

// ---- Deployed version (from the git checkout that the GitHub deploy creates) ----
$version = null;
$gitDir = APP_ROOT . '/.git';
if (is_file($gitDir . '/HEAD')) {
    $head = trim((string) file_get_contents($gitDir . '/HEAD'));
    $hash = '';
    $branch = '';
    if (str_starts_with($head, 'ref: ')) {
        $ref = substr($head, 5);
        $branch = basename($ref);
        if (is_file($gitDir . '/' . $ref)) {
            $hash = trim((string) file_get_contents($gitDir . '/' . $ref));
        } elseif (is_file($gitDir . '/packed-refs') && preg_match('/^([0-9a-f]{40}) ' . preg_quote($ref, '/') . '$/m', (string) file_get_contents($gitDir . '/packed-refs'), $m)) {
            $hash = $m[1];
        }
    } else {
        $hash = $head;
    }
    if ($hash !== '') {
        $version = ['hash' => substr($hash, 0, 7), 'branch' => $branch, 'when' => @filemtime($gitDir . '/HEAD') ?: null];
    }
}

// ---- Database updates ----
$pending = [];
try {
    $pending = array_keys(array_filter(migrations_status(), fn ($done) => !$done));
} catch (Throwable $e) {
    $error = $error ?: user_error_message($e, 'Could not read the database update status.');
}

// ---- Health checks: [label, status ok|warn|fail, detail] ----
$checks = [];
$add = function (string $label, string $status, string $detail) use (&$checks) {
    $checks[] = [$label, $status, $detail];
};
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
$secret = (string) env('APP_SECRET', '');
$mailTransport = strtolower((string) env('MAIL_TRANSPORT', env('MAIL_HOST', '') !== '' ? 'smtp' : 'mail'));

$add('Settings file (.env)', is_file(APP_ROOT . '/.env') ? 'ok' : 'fail',
    is_file(APP_ROOT . '/.env') ? 'Found.' : 'Missing. Create it next to the public folder using .env.example as the template.');
$add('Live mode', env('APP_ENV', '') === 'production' && env('APP_DEBUG', false) !== true ? 'ok' : 'warn',
    env('APP_DEBUG', false) === true ? 'APP_DEBUG is on: visitors could see error details. Set APP_DEBUG=false.' : (env('APP_ENV', '') === 'production' ? 'Production mode, error details hidden.' : 'Set APP_ENV=production on the live site.'));
$add('Secret key', strlen($secret) >= 32 && !str_contains($secret, 'replace') && $secret !== 'change-me' ? 'ok' : 'fail',
    strlen($secret) >= 32 ? 'Set.' : 'APP_SECRET is missing or a placeholder. Use a long random value.');
$add('Database account', strtolower((string) env('DB_USER', '')) === 'root' ? 'warn' : 'ok',
    strtolower((string) env('DB_USER', '')) === 'root' ? 'The site connects as "root". Use the database user created for this website.' : 'Uses its own database user.');
$add('Secure connection (HTTPS)', $https ? 'ok' : 'warn', $https ? 'This page was loaded over HTTPS.' : 'Not using HTTPS. Turn on the free SSL certificate in Hostinger.');
$add('Email sending', $mailTransport === 'smtp' && env('MAIL_HOST', '') !== '' && env('MAIL_USERNAME', '') !== '' ? 'ok' : 'warn',
    $mailTransport === 'smtp' && env('MAIL_HOST', '') !== '' ? 'SMTP via ' . env('MAIL_HOST', '') . '.' : ($mailTransport === 'log' ? 'MAIL_TRANSPORT=log: emails are only written to the log, not sent.' : 'Set MAIL_TRANSPORT=smtp and the MAIL_* details of a Hostinger mailbox so emails reliably arrive.'));
$add('Error alerts', filter_var((string) env('ALERT_EMAIL', ''), FILTER_VALIDATE_EMAIL) ? 'ok' : 'warn',
    env('ALERT_EMAIL', '') !== '' ? 'Sent to ' . env('ALERT_EMAIL', '') . '.' : 'Set ALERT_EMAIL to be told when the site has an error.');
$logsOk = (is_dir(APP_ROOT . '/logs') || @mkdir(APP_ROOT . '/logs', 0750, true)) && is_writable(APP_ROOT . '/logs');
$add('Error log folder', $logsOk ? 'ok' : 'fail', $logsOk ? 'Writable.' : 'The logs folder is not writable, so errors cannot be recorded.');
try {
    $dir = backup_dir();
    $add('Backup folder', is_writable($dir) ? 'ok' : 'fail', is_writable($dir) ? 'Backups are saved outside the website.' : "{$dir} is not writable.");
} catch (Throwable $e) {
    $add('Backup folder', 'fail', $e->getMessage());
}
$add('Database updates', $pending ? 'warn' : 'ok', $pending ? count($pending) . ' waiting. Press "Apply updates" below.' : 'Up to date.');
$lastPurge = setting('system.last_purge');
$add('Old data clean-up', $lastPurge !== '' ? 'ok' : 'warn', $lastPurge !== '' ? 'Last ran ' . $lastPurge . ' (runs daily when staff sign in).' : 'Has not run yet; it runs automatically the next time staff sign in.');
$add('PHP version', version_compare(PHP_VERSION, '8.1.0', '>=') ? 'ok' : 'fail', 'PHP ' . PHP_VERSION . (version_compare(PHP_VERSION, '8.1.0', '>=') ? '.' : ': needs 8.1 or newer (change it in Hostinger → PHP Configuration).'));

$badge = ['ok' => ['OK', 'bg-emerald-100 text-emerald-800'], 'warn' => ['Check', 'bg-amber-100 text-amber-800'], 'fail' => ['Fix', 'bg-red-100 text-red-800']];

$pageTitle = 'Website Updates | BMI Admin';
require_once ADMIN_TEMPLATES . '/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Website Updates</h1>
            <p class="mt-1 text-slate-500">After new code is pushed to GitHub and deployed, check the version here and apply any database updates.</p>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm" role="status"><?php echo e($feedback); ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm" role="alert"><?php echo e($error); ?></div>
        <?php endif; ?>

        <div class="mt-6 grid lg:grid-cols-2 gap-6">
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800">Deployed version</h2>
                <?php if ($version): ?>
                    <p class="mt-3 text-slate-700">Code version <code class="font-mono bg-slate-100 px-2 py-0.5 rounded"><?php echo e($version['hash']); ?></code><?php echo $version['branch'] !== '' ? ' on <strong>' . e($version['branch']) . '</strong>' : ''; ?></p>
                    <?php if ($version['when']): ?><p class="mt-1 text-sm text-slate-500">Last deployed <?php echo e(date('j M Y, H:i', (int) $version['when'])); ?> (<?php echo e(date('T')); ?>)</p><?php endif; ?>
                    <p class="mt-3 text-sm text-slate-500">Compare this with the latest commit on GitHub to confirm the deploy worked.</p>
                <?php else: ?>
                    <p class="mt-3 text-slate-600">Version unknown (the site was not deployed from Git).</p>
                <?php endif; ?>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800">Database updates</h2>
                <?php if ($pending): ?>
                    <p class="mt-3 text-slate-700"><?php echo count($pending); ?> update(s) waiting:</p>
                    <ul class="mt-2 text-sm font-mono text-slate-600 list-disc pl-5"><?php foreach ($pending as $p): ?><li><?php echo e($p); ?></li><?php endforeach; ?></ul>
                <?php else: ?>
                    <p class="mt-3 text-emerald-700 font-medium">The database is up to date.</p>
                <?php endif; ?>
                <div class="mt-5 flex flex-wrap gap-3">
                    <form method="post" onsubmit="return confirm('A backup is taken first, then the updates are applied. Continue?');">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="apply">
                        <button type="submit" class="rounded-lg <?php echo $pending ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'border border-slate-300 text-slate-700 hover:bg-slate-50'; ?> px-5 py-2.5 text-sm font-semibold"><?php echo $pending ? 'Back up and apply updates' : 'Check again'; ?></button>
                    </form>
                    <form method="post">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="download_backup">
                        <button type="submit" class="rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 px-5 py-2.5 text-sm font-semibold">Download a backup</button>
                    </form>
                </div>
                <?php if ($output !== ''): ?>
                    <pre class="mt-4 text-xs bg-slate-50 border border-slate-200 rounded p-3 overflow-x-auto"><?php echo e($output); ?></pre>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-6 bg-white border border-slate-200 rounded-xl shadow-sm overflow-x-auto">
            <h2 class="text-lg font-bold text-slate-800 px-6 pt-6">Site health</h2>
            <p class="px-6 mt-1 text-sm text-slate-500">These come from the server's .env settings file. Anything marked "Fix" or "Check" should be sorted before announcing the site.</p>
            <table class="w-full text-sm mt-4">
                <tbody>
                <?php foreach ($checks as [$label, $status, $detail]): ?>
                    <tr class="border-t border-slate-100">
                        <td class="py-3 px-6 font-medium text-slate-800 whitespace-nowrap"><?php echo e($label); ?></td>
                        <td class="py-3 px-2"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold <?php echo $badge[$status][1]; ?>"><?php echo $badge[$status][0]; ?></span></td>
                        <td class="py-3 px-6 text-slate-600"><?php echo e($detail); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
<?php require_once ADMIN_TEMPLATES . '/footer.php'; ?>
