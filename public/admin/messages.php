<?php
require_once __DIR__ . '/../../includes/auth.php';
auth_require('inbox');

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/helpers.php';

$feedback = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_check();
        $pdo = db_connect();
        $action = (string) ($_POST['action'] ?? '');

        // Privacy requests: delete everything one person has sent (contact, prayer, visit, newsletter).
        if ($action === 'delete_email') {
            $email = strtolower(trim((string) ($_POST['email'] ?? '')));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Enter a valid email address.');
            }
            $stmt = $pdo->prepare('DELETE FROM messages WHERE LOWER(email) = :e');
            $stmt->execute([':e' => $email]);
            $deleted = $stmt->rowCount();
            // The address itself is not written to the audit log, so the deletion is complete.
            audit('delete', 'message', null, "Privacy request: deleted {$deleted} message(s) from one sender");
            flash('messages', "Deleted {$deleted} message(s) from that address.");
            header('Location: messages.php');
            exit;
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $pdo->prepare('DELETE FROM messages WHERE id = :id')->execute([':id' => $id]);
                audit('delete', 'message', $id, 'Deleted inbox message #' . $id);
                flash('messages', 'deleted');
                header('Location: messages.php');
                exit;
            }
        }
    } catch (Throwable $e) {
        $error = user_error_message($e);
    }
}

$flashMessage = flash('messages');
if ($flashMessage !== null) {
    $feedback = $flashMessage === 'deleted' ? 'Message deleted.' : $flashMessage;
}

$lookup = strtolower(trim((string) ($_GET['lookup'] ?? '')));
if ($lookup !== '' && !filter_var($lookup, FILTER_VALIDATE_EMAIL)) {
    $error = $error ?: 'Enter a full email address to look someone up.';
    $lookup = '';
}

$messages = [];
try {
    $pdo = db_connect();
    if ($lookup !== '') {
        $stmt = $pdo->prepare('SELECT * FROM messages WHERE LOWER(email) = :e ORDER BY created_at DESC');
        $stmt->execute([':e' => $lookup]);
        $messages = $stmt->fetchAll();
        $totalPages = 1;
    } else {
        $page = max(1, (int)($_GET['p'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;
        $total = $pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn();
        $totalPages = max(1, ceil($total / $limit));

        $messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT $limit OFFSET $offset")->fetchAll();
    }
} catch (Throwable $e) {
    $error = user_error_message($e, 'Unable to load messages.');
}
?>
<?php
$pageTitle = 'Inbox | BMI Admin';
require_once ADMIN_TEMPLATES . '/header.php';
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

        <div class="mt-6 bg-white border border-slate-200 rounded-xl shadow-sm p-5">
            <form method="get" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[16rem]">
                    <label for="lookup" class="block text-sm font-semibold text-slate-700 mb-1.5">Find everything from one person <span class="font-normal text-slate-500">(for privacy requests)</span></label>
                    <input type="email" id="lookup" name="lookup" value="<?php echo e($lookup); ?>" placeholder="name@example.com" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">
                </div>
                <button type="submit" class="rounded-lg bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 text-sm font-semibold">Find</button>
                <?php if ($lookup !== ''): ?><a href="messages.php" class="px-3 py-2.5 text-sm text-slate-600 hover:text-slate-900">Show all messages</a><?php endif; ?>
            </form>
            <?php if ($lookup !== ''): ?>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-lg bg-slate-50 border border-slate-200 px-4 py-3 text-sm">
                    <span><strong><?php echo count($messages); ?></strong> message(s) from <?php echo e($lookup); ?>. To answer a request for a copy, reply with the messages below.</span>
                    <?php if ($messages): ?>
                    <form method="post" onsubmit="return confirm('Permanently delete all <?php echo count($messages); ?> message(s) from this address?');">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="delete_email">
                        <input type="hidden" name="email" value="<?php echo e($lookup); ?>">
                        <button type="submit" class="rounded-lg bg-red-600 hover:bg-red-700 text-white px-4 py-2 font-semibold">Delete all from this person</button>
                    </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide uppercase <?php echo ['prayer' => 'bg-purple-100 text-purple-800 border border-purple-200/50', 'visit' => 'bg-emerald-100 text-emerald-800 border border-emerald-200/50', 'newsletter' => 'bg-sky-100 text-sky-800 border border-sky-200/50'][$m['type']] ?? 'bg-slate-100 text-slate-700 border border-slate-200/50'; ?>">
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
            <?php if (!empty($totalPages) && $totalPages > 1): ?>
                <div class="p-6 border-t border-slate-100 flex justify-center gap-2">
                    <?php if ($page > 1): ?>
                        <a href="?p=<?php echo $page - 1; ?>" class="px-4 py-2 border rounded hover:bg-slate-50 text-sm font-medium">Previous</a>
                    <?php endif; ?>
                    <span class="px-4 py-2 text-sm font-medium text-slate-500">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    <?php if ($page < $totalPages): ?>
                        <a href="?p=<?php echo $page + 1; ?>" class="px-4 py-2 border rounded hover:bg-slate-50 text-sm font-medium">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
<?php require_once ADMIN_TEMPLATES . '/footer.php'; ?>
