<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/uploads.php';

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

            $ministryImage = handle_image_upload_or_link($_FILES['ministry_image'] ?? null, $_POST['ministry_image_url'] ?? '', 'ministry');

            if ($action === 'add') {
                $stmt = $pdo->prepare(
                    'INSERT INTO ministries (name, description, leader_name, meeting_schedule, ministry_image)
                     VALUES (:n, :d, :l, :s, :i)'
                );
                $stmt->execute([
                    ':n' => $name,
                    ':d' => $description !== '' ? $description : null,
                    ':l' => $leader !== '' ? $leader : null,
                    ':s' => $schedule !== '' ? $schedule : null,
                    ':i' => $ministryImage,
                ]);
                header('Location: ministries.php?status=added');
                exit;
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid ministry ID.');
            }

            $existing = $pdo->prepare('SELECT ministry_image FROM ministries WHERE id = :id');
            $existing->execute([':id' => $id]);
            $row = $existing->fetch();
            if (!$row) {
                throw new RuntimeException('Ministry not found.');
            }

            $finalImage = $row['ministry_image'];
            if ($ministryImage !== null) {
                upload_delete($row['ministry_image']);
                $finalImage = $ministryImage;
            }

            $stmt = $pdo->prepare(
                'UPDATE ministries SET name = :n, description = :d, leader_name = :l, meeting_schedule = :s, ministry_image = :i WHERE id = :id'
            );
            $stmt->execute([
                ':id' => $id,
                ':n' => $name,
                ':d' => $description !== '' ? $description : null,
                ':l' => $leader !== '' ? $leader : null,
                ':s' => $schedule !== '' ? $schedule : null,
                ':i' => $finalImage,
            ]);
            header('Location: ministries.php?status=updated');
            exit;
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid ID.');
            }
            $row = $pdo->prepare('SELECT ministry_image FROM ministries WHERE id = :id');
            $row->execute([':id' => $id]);
            $existing = $row->fetch();
            if ($existing) {
                upload_delete($existing['ministry_image']);
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
<?php
$pageTitle = 'Manage Ministries | BMI Admin';
require_once __DIR__ . '/includes/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Manage Ministries</h1>
            <p class="mt-1 text-slate-500">Add the ministry groups shown on the public Ministries page.</p>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($feedback); ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="mt-6 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4"><?php echo $editing ? 'Edit Ministry' : 'Add New Ministry'; ?></h2>
            <form method="post" class="mt-6 grid md:grid-cols-2 gap-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="<?php echo $editing ? 'edit' : 'add'; ?>">
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo (int) $editing['id']; ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Name *</label>
                    <input type="text" name="name" required maxlength="150" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editing ? htmlspecialchars((string) $editing['name']) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Leader</label>
                    <input type="text" name="leader_name" maxlength="120" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editing ? htmlspecialchars((string) ($editing['leader_name'] ?? '')) : ''; ?>">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Meeting schedule</label>
                    <input type="text" name="meeting_schedule" maxlength="150" placeholder="e.g. Saturdays, 4:00 PM" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editing ? htmlspecialchars((string) ($editing['meeting_schedule'] ?? '')) : ''; ?>">
                </div>

                <div class="md:col-span-2 p-5 border border-slate-200 rounded-lg bg-slate-50/50">
                    <label class="block text-sm font-semibold text-slate-700 mb-3">Ministry Logo / Flyer</label>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <span class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wider">Upload File (Max 5MB)</span>
                            <input type="file" name="ministry_image" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-white focus:bg-white transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wider">OR Paste Image URL</span>
                            <input type="url" name="ministry_image_url" placeholder="https://..." class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-white focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all text-sm">
                        </div>
                    </div>
                    <?php if ($editing && ($editing['ministry_image'] ?? '')): ?>
                        <div class="mt-4 relative inline-block">
                            <span class="block text-xs font-medium text-slate-500 mb-1.5">Current Image:</span>
                            <img src="<?php echo strpos($editing['ministry_image'], 'http') === 0 ? htmlspecialchars($editing['ministry_image']) : '../' . htmlspecialchars($editing['ministry_image']); ?>" alt="" class="h-24 w-32 object-cover rounded-lg border border-slate-200 shadow-sm">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Description</label>
                    <textarea name="description" rows="4" class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"><?php echo $editing ? htmlspecialchars((string) ($editing['description'] ?? '')) : ''; ?></textarea>
                </div>
                <div class="md:col-span-2 pt-4 border-t border-slate-100 flex gap-3">
                    <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-6 py-2.5 text-sm font-semibold transition-all shadow-md shadow-blue-500/30"><?php echo $editing ? 'Update' : 'Add'; ?></button>
                    <?php if ($editing): ?>
                        <a href="ministries.php" class="rounded-lg border border-slate-300 text-slate-700 px-6 py-2.5 text-sm font-semibold hover:bg-slate-50 transition-all text-center">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4">Existing Ministries</h2>
            <?php if (empty($ministries)): ?>
                <p class="mt-4 text-sm text-slate-600">No ministries yet.</p>
            <?php else: ?>
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500 border-b border-slate-200 bg-slate-50/50">
                                <th class="py-3 px-4 font-semibold rounded-tl-lg w-16">Logo</th>
                                <th class="py-3 px-4 font-semibold">Name</th>
                                <th class="py-3 px-4 font-semibold">Leader</th>
                                <th class="py-3 px-4 font-semibold">Schedule</th>
                                <th class="py-3 px-4 font-semibold text-right rounded-tr-lg">Actions</th>
                            </tr>
                        </thead>
                    <tbody>
                        <?php foreach ($ministries as $m): ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors last:border-0">
                                <td class="py-3 px-4">
                                    <?php if ($m['ministry_image'] ?? ''): ?>
                                        <img src="<?php echo strpos($m['ministry_image'], 'http') === 0 ? htmlspecialchars($m['ministry_image']) : '../' . htmlspecialchars($m['ministry_image']); ?>" class="w-10 h-10 object-cover rounded-full border border-slate-200" alt="">
                                    <?php else: ?>
                                        <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 text-xs border border-slate-200">No</div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 font-medium text-slate-900"><?php echo htmlspecialchars((string) $m['name']); ?></td>
                                <td class="py-3 px-4 text-slate-600"><?php echo htmlspecialchars((string) ($m['leader_name'] ?? '')); ?></td>
                                <td class="py-3 px-4 text-slate-600"><?php echo htmlspecialchars((string) ($m['meeting_schedule'] ?? '')); ?></td>
                                <td class="py-3 px-4 text-right">
                                    <a href="ministries.php?edit=<?php echo (int) $m['id']; ?>" class="text-blue-600 hover:text-blue-800 font-medium mr-4">Edit</a>
                                    <form method="post" class="inline" onsubmit="return confirm('Delete this ministry?');">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $m['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
