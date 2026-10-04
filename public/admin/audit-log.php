<?php
require_once __DIR__ . '/../../includes/auth.php';
auth_require('audit');

require_once __DIR__ . '/../../includes/helpers.php';

$perPage = 50;
$page = max(1, (int) ($_GET['page'] ?? 1));
$filter = (string) ($_GET['filter'] ?? 'all');
$filters = [
    'all' => ['All activity', ''],
    'giving' => ['Giving details', "entity = 'giving'"],
    'security' => ['Sign-ins and security', "action IN ('login','login_failed','login_locked','logout','2fa_failed','2fa_enabled','2fa_disabled','2fa_reset','2fa_recovery_used','2fa_codes_regenerated','password_changed')"],
    'users' => ['Staff accounts', "entity = 'user' AND action IN ('create','update','delete')"],
    'content' => ['Content changes', "entity NOT IN ('user','giving')"],
];
if (!isset($filters[$filter])) {
    $filter = 'all';
}
$where = $filters[$filter][1] !== '' ? 'WHERE ' . $filters[$filter][1] : '';

$rows = [];
$total = 0;
$error = '';
try {
    $pdo = db_connect();
    $total = (int) $pdo->query("SELECT COUNT(*) FROM audit_log {$where}")->fetchColumn();
    $offset = ($page - 1) * $perPage;
    $rows = $pdo->query("SELECT * FROM audit_log {$where} ORDER BY id DESC LIMIT {$perPage} OFFSET {$offset}")->fetchAll();
} catch (Throwable $e) {
    $error = user_error_message($e, 'Unable to load the audit log.');
}
$pages = max(1, (int) ceil($total / $perPage));

$actionStyle = function (string $action): string {
    if (in_array($action, ['login_failed', 'login_locked', '2fa_failed', 'delete', '2fa_disabled', '2fa_reset'], true)) {
        return 'bg-red-100 text-red-800';
    }
    if (in_array($action, ['create', 'login', '2fa_enabled'], true)) {
        return 'bg-emerald-100 text-emerald-800';
    }
    return 'bg-slate-100 text-slate-700';
};

$pageTitle = 'Audit Log | BMI Admin';
require_once ADMIN_TEMPLATES . '/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Audit Log</h1>
            <p class="mt-1 text-slate-500">Every sign-in, account change and content change, with who made it and when. Entries cannot be edited or deleted from the admin panel.</p>
        </div>

        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo e($error); ?></div>
        <?php endif; ?>

        <nav class="flex flex-wrap gap-2 mb-6" aria-label="Filter">
            <?php foreach ($filters as $key => [$label]): ?>
                <a href="audit-log.php?filter=<?php echo e($key); ?>" class="px-3 py-1.5 rounded-full text-sm font-medium border <?php echo $filter === $key ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'; ?>"><?php echo e($label); ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b border-slate-200 bg-slate-50/50">
                        <th class="py-3 px-4 font-semibold whitespace-nowrap">When</th>
                        <th class="py-3 px-4 font-semibold">Who</th>
                        <th class="py-3 px-4 font-semibold">Action</th>
                        <th class="py-3 px-4 font-semibold">What happened</th>
                        <th class="py-3 px-4 font-semibold">IP address</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$rows): ?>
                    <tr><td colspan="5" class="py-8 px-4 text-center text-slate-500">No activity recorded yet.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $r): ?>
                    <?php $details = json_decode((string) $r['details'], true) ?: []; ?>
                    <tr class="border-b border-slate-100 align-top">
                        <td class="py-3 px-4 text-slate-600 whitespace-nowrap tabular-nums"><?php echo e(date('M j, Y H:i', strtotime($r['created_at']))); ?></td>
                        <td class="py-3 px-4 text-slate-700"><?php echo e($r['user_email'] ?? 'Unknown'); ?></td>
                        <td class="py-3 px-4"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold <?php echo $actionStyle($r['action']); ?>"><?php echo e(str_replace('_', ' ', $r['action'])); ?></span></td>
                        <td class="py-3 px-4 text-slate-800">
                            <?php echo e($r['summary']); ?>
                            <?php if (!empty($details['changes'])): ?>
                                <details class="mt-2">
                                    <summary class="cursor-pointer text-blue-600 text-xs font-medium">Show changes</summary>
                                    <dl class="mt-2 space-y-2 text-xs">
                                        <?php foreach ($details['changes'] as $field => $change): ?>
                                            <div class="border-l-2 border-slate-200 pl-3">
                                                <dt class="font-semibold text-slate-700"><?php echo e($field); ?></dt>
                                                <dd class="text-red-700 break-all">Before: <?php echo e(mb_strimwidth((string) ($change[0] ?? ''), 0, 300, '…') ?: '(empty)'); ?></dd>
                                                <dd class="text-emerald-700 break-all">After: <?php echo e(mb_strimwidth((string) ($change[1] ?? ''), 0, 300, '…') ?: '(empty)'); ?></dd>
                                            </div>
                                        <?php endforeach; ?>
                                    </dl>
                                </details>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-slate-500 tabular-nums"><?php echo e($r['ip'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pages > 1): ?>
            <div class="mt-6 flex items-center gap-3 text-sm">
                <?php if ($page > 1): ?><a class="text-blue-600 font-medium" href="audit-log.php?filter=<?php echo e($filter); ?>&page=<?php echo $page - 1; ?>">Newer</a><?php endif; ?>
                <span class="text-slate-500">Page <?php echo $page; ?> of <?php echo $pages; ?></span>
                <?php if ($page < $pages): ?><a class="text-blue-600 font-medium" href="audit-log.php?filter=<?php echo e($filter); ?>&page=<?php echo $page + 1; ?>">Older</a><?php endif; ?>
            </div>
        <?php endif; ?>
<?php require_once ADMIN_TEMPLATES . '/footer.php'; ?>
