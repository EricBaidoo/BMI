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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Inbox | BMI Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-3xl font-bold">Inbox</h1>
                <p class="mt-2 text-slate-600">Contact messages and prayer requests submitted from the website.</p>
            </div>
            <div class="flex gap-2">
                <a href="index.php" class="rounded bg-slate-200 px-4 py-2 text-sm">Dashboard</a>
                <a href="logout.php" class="rounded bg-slate-800 text-white px-4 py-2 text-sm">Sign out</a>
            </div>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo e($feedback); ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo e($error); ?></div>
        <?php endif; ?>

        <div class="mt-6 bg-white border border-slate-200 rounded">
            <?php if (empty($messages)): ?>
                <p class="p-5 text-sm text-slate-600">No messages yet.</p>
            <?php else: ?>
                <ul class="divide-y divide-slate-200">
                    <?php foreach ($messages as $m): ?>
                        <li class="p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold"><?php echo e($m['full_name']); ?>
                                        <span class="ml-2 inline-block rounded px-2 py-0.5 text-xs <?php echo $m['type'] === 'prayer' ? 'bg-purple-100 text-purple-800' : 'bg-slate-200 text-slate-700'; ?>">
                                            <?php echo ucfirst($m['type']); ?>
                                        </span>
                                    </p>
                                    <p class="text-sm text-slate-600"><a href="mailto:<?php echo e($m['email']); ?>" class="hover:underline"><?php echo e($m['email']); ?></a></p>
                                    <?php if (!empty($m['subject'])): ?>
                                        <p class="text-sm mt-1"><strong>Subject:</strong> <?php echo e($m['subject']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-start gap-3 text-sm">
                                    <span class="text-slate-500"><?php echo e(date('M d, Y H:i', strtotime((string) $m['created_at']))); ?></span>
                                    <form method="post" onsubmit="return confirm('Delete this message?');">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $m['id']; ?>">
                                        <button type="submit" class="text-red-700 hover:underline">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <p class="mt-3 text-sm whitespace-pre-line text-slate-700"><?php echo e($m['message']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
