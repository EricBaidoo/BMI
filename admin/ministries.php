<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

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
            $description = trim((string) ($_POST['description'] ?? ''));
            $leader = trim((string) ($_POST['leader_name'] ?? ''));
            $schedule = trim((string) ($_POST['meeting_schedule'] ?? ''));

            if ($name === '') {
                throw new RuntimeException('Ministry name is required.');
            }

            if ($action === 'add') {
                $stmt = $pdo->prepare(
                    'INSERT INTO ministries (name, description, leader_name, meeting_schedule)
                     VALUES (:n, :d, :l, :s)'
                );
                $stmt->execute([
                    ':n' => $name,
                    ':d' => $description !== '' ? $description : null,
                    ':l' => $leader !== '' ? $leader : null,
                    ':s' => $schedule !== '' ? $schedule : null,
                ]);
                header('Location: ministries.php?status=added');
                exit;
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid ministry ID.');
            }
            $stmt = $pdo->prepare(
                'UPDATE ministries SET name = :n, description = :d, leader_name = :l, meeting_schedule = :s WHERE id = :id'
            );
            $stmt->execute([
                ':id' => $id,
                ':n' => $name,
                ':d' => $description !== '' ? $description : null,
                ':l' => $leader !== '' ? $leader : null,
                ':s' => $schedule !== '' ? $schedule : null,
            ]);
            header('Location: ministries.php?status=updated');
            exit;
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid ID.');
            }
            $pdo->prepare('DELETE FROM ministries WHERE id = :id')->execute([':id' => $id]);
            header('Location: ministries.php?status=deleted');
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
            $stmt = $pdo->prepare('SELECT * FROM ministries WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $editing = $stmt->fetch();
        } catch (Throwable $e) {
            $error = 'Unable to load ministry.';
        }
    }
}

$flashMap = ['added' => 'Ministry added.', 'updated' => 'Ministry updated.', 'deleted' => 'Ministry deleted.'];
$feedback = $flashMap[$_GET['status'] ?? ''] ?? '';

$ministries = [];
try {
    $pdo = db_connect();
    $ministries = $pdo->query('SELECT * FROM ministries ORDER BY name ASC')->fetchAll();
} catch (Throwable $e) {
    if ($error === '') {
        $error = 'Unable to load ministries.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Manage Ministries | BMI Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-3xl font-bold">Manage Ministries</h1>
                <p class="mt-2 text-slate-600">Add the ministry groups shown on the public Ministries page.</p>
            </div>
            <div class="flex gap-2">
                <a href="index.php" class="rounded bg-slate-200 px-4 py-2 text-sm">Dashboard</a>
                <a href="logout.php" class="rounded bg-slate-800 text-white px-4 py-2 text-sm">Sign out</a>
            </div>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($feedback); ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="mt-6 bg-white border border-slate-200 rounded p-5">
            <h2 class="text-xl font-semibold"><?php echo $editing ? 'Edit Ministry' : 'Add New Ministry'; ?></h2>
            <form method="post" class="mt-4 grid md:grid-cols-2 gap-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="<?php echo $editing ? 'edit' : 'add'; ?>">
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo (int) $editing['id']; ?>">
                <?php endif; ?>

                <label class="block text-sm">
                    <span class="font-medium">Name *</span>
                    <input type="text" name="name" required maxlength="150" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        value="<?php echo $editing ? htmlspecialchars((string) $editing['name']) : ''; ?>">
                </label>
                <label class="block text-sm">
                    <span class="font-medium">Leader</span>
                    <input type="text" name="leader_name" maxlength="120" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        value="<?php echo $editing ? htmlspecialchars((string) ($editing['leader_name'] ?? '')) : ''; ?>">
                </label>
                <label class="block text-sm md:col-span-2">
                    <span class="font-medium">Meeting schedule</span>
                    <input type="text" name="meeting_schedule" maxlength="150" placeholder="e.g. Saturdays, 4:00 PM" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        value="<?php echo $editing ? htmlspecialchars((string) ($editing['meeting_schedule'] ?? '')) : ''; ?>">
                </label>
                <label class="block text-sm md:col-span-2">
                    <span class="font-medium">Description</span>
                    <textarea name="description" rows="4" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"><?php echo $editing ? htmlspecialchars((string) ($editing['description'] ?? '')) : ''; ?></textarea>
                </label>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="rounded bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 text-sm font-semibold"><?php echo $editing ? 'Update' : 'Add'; ?></button>
                    <?php if ($editing): ?>
                        <a href="ministries.php" class="rounded bg-slate-400 hover:bg-slate-500 text-white px-4 py-2 text-sm font-semibold">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded p-5">
            <h2 class="text-xl font-semibold">Existing Ministries</h2>
            <?php if (empty($ministries)): ?>
                <p class="mt-3 text-sm text-slate-600">No ministries yet.</p>
            <?php else: ?>
                <table class="mt-4 w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 border-b">
                            <th class="py-2">Name</th>
                            <th class="py-2">Leader</th>
                            <th class="py-2">Schedule</th>
                            <th class="py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ministries as $m): ?>
                            <tr class="border-b last:border-0">
                                <td class="py-3 font-medium"><?php echo htmlspecialchars((string) $m['name']); ?></td>
                                <td class="py-3"><?php echo htmlspecialchars((string) ($m['leader_name'] ?? '')); ?></td>
                                <td class="py-3"><?php echo htmlspecialchars((string) ($m['meeting_schedule'] ?? '')); ?></td>
                                <td class="py-3 text-right">
                                    <a href="ministries.php?edit=<?php echo (int) $m['id']; ?>" class="text-blue-700 hover:underline mr-3">Edit</a>
                                    <form method="post" class="inline" onsubmit="return confirm('Delete this ministry?');">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $m['id']; ?>">
                                        <button type="submit" class="text-red-700 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
