<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/helpers.php';

$feedback = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_check();
        $pdo = db_connect();
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $pdo->prepare('DELETE FROM messages WHERE id = :id')->execute([':id' => $id]);
                flash('messages', 'deleted');
                header('Location: messages.php');
                exit;
            }
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

if (flash('messages') === 'deleted') {
    $feedback = 'Message deleted.';
}

$messages = [];
try {
    $pdo = db_connect();
    $messages = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();
} catch (Throwable $e) {
    $error = 'Unable to load messages.';
}
?>
<?php
$pageTitle = 'Inbox | BMI Admin';
require_once __DIR__ . '/includes/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Inbox</h1>
            <p class="mt-1 text-slate-500">Contact messages and prayer requests submitted from the website.</p>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo e($feedback); ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo e($error); ?></div>
        <?php endif; ?>

        <div class="mt-8 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <?php if (empty($messages)): ?>
                <div class="p-8 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <p class="text-sm">No messages yet.</p>
                </div>
            <?php else: ?>
                <ul class="divide-y divide-slate-100">
                    <?php foreach ($messages as $m): ?>
                        <li class="p-6 hover:bg-slate-50/50 transition-colors">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <h3 class="font-bold text-slate-900 text-base"><?php echo e($m['full_name']); ?></h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide uppercase <?php echo $m['type'] === 'prayer' ? 'bg-purple-100 text-purple-800 border border-purple-200/50' : 'bg-slate-100 text-slate-700 border border-slate-200/50'; ?>">
                                            <?php echo e($m['type']); ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-blue-600 mt-1"><a href="mailto:<?php echo e($m['email']); ?>" class="hover:underline"><?php echo e($m['email']); ?></a></p>
                                    <?php if (!empty($m['subject'])): ?>
                                        <p class="text-sm text-slate-700 mt-2 font-medium">Subject: <?php echo e($m['subject']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col items-end gap-2 text-sm">
                                    <span class="text-slate-500 font-medium"><?php echo e(date('M d, Y g:i A', strtotime((string) $m['created_at']))); ?></span>
                                    <form method="post" onsubmit="return confirm('Delete this message?');">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $m['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium transition-colors">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-slate-700 whitespace-pre-line leading-relaxed bg-slate-50/50 rounded-lg p-4 border border-slate-100/50">
                                <?php echo e($m['message']); ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
