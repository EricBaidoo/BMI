<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/uploads.php';

$feedback = '';
$error = '';
$editingSermon = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_check();
        $pdo = db_connect();
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'add' || $action === 'edit') {
            $title = trim((string) ($_POST['title'] ?? ''));
            $speaker = trim((string) ($_POST['speaker'] ?? ''));
            $sermonDate = trim((string) ($_POST['sermon_date'] ?? ''));
            $topic = trim((string) ($_POST['topic'] ?? ''));
            $mediaType = trim((string) ($_POST['media_type'] ?? 'audio'));
            $mediaUrl = trim((string) ($_POST['media_url'] ?? ''));
            $content = trim((string) ($_POST['content'] ?? ''));

            if (!in_array($mediaType, ['audio', 'video', 'text'], true)) {
                throw new RuntimeException('Invalid media type.');
            }
            if ($title === '' || $speaker === '' || $sermonDate === '') {
                throw new RuntimeException('Title, speaker, and date are required.');
            }
            if ($mediaUrl !== '' && !filter_var($mediaUrl, FILTER_VALIDATE_URL)) {
                throw new RuntimeException('Media URL is not valid.');
            }

            $sermonImage = upload_image($_FILES['sermon_image'] ?? null, 'sermon');

            if ($action === 'add') {
                $stmt = $pdo->prepare(
                    'INSERT INTO sermons (title, speaker, sermon_date, topic, media_type, media_url, content, sermon_image)
                     VALUES (:title, :speaker, :sermon_date, :topic, :media_type, :media_url, :content, :sermon_image)'
                );
                $stmt->execute([
                    ':title' => $title,
                    ':speaker' => $speaker,
                    ':sermon_date' => $sermonDate,
                    ':topic' => $topic !== '' ? $topic : null,
                    ':media_type' => $mediaType,
                    ':media_url' => $mediaUrl !== '' ? $mediaUrl : null,
                    ':content' => $content !== '' ? $content : null,
                    ':sermon_image' => $sermonImage,
                ]);
                header('Location: sermons.php?status=added');
                exit;
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid sermon ID.');
            }
            $existing = $pdo->prepare('SELECT sermon_image FROM sermons WHERE id = :id');
            $existing->execute([':id' => $id]);
            $row = $existing->fetch();
            if (!$row) {
                throw new RuntimeException('Sermon not found.');
            }

            $finalImage = $row['sermon_image'];
            if ($sermonImage !== null) {
                upload_delete($row['sermon_image']);
                $finalImage = $sermonImage;
            }

            $stmt = $pdo->prepare(
                'UPDATE sermons SET title = :title, speaker = :speaker, sermon_date = :sermon_date,
                    topic = :topic, media_type = :media_type, media_url = :media_url, content = :content,
                    sermon_image = :sermon_image WHERE id = :id'
            );
            $stmt->execute([
                ':id' => $id,
                ':title' => $title,
                ':speaker' => $speaker,
                ':sermon_date' => $sermonDate,
                ':topic' => $topic !== '' ? $topic : null,
                ':media_type' => $mediaType,
                ':media_url' => $mediaUrl !== '' ? $mediaUrl : null,
                ':content' => $content !== '' ? $content : null,
                ':sermon_image' => $finalImage,
            ]);
            header('Location: sermons.php?status=updated');
            exit;
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid sermon ID.');
            }
            $row = $pdo->prepare('SELECT sermon_image FROM sermons WHERE id = :id');
            $row->execute([':id' => $id]);
            $existing = $row->fetch();
            if ($existing) {
                upload_delete($existing['sermon_image']);
            }
            $pdo->prepare('DELETE FROM sermons WHERE id = :id')->execute([':id' => $id]);
            header('Location: sermons.php?status=deleted');
            exit;
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    if ($editId > 0) {
        try {
            $pdo = db_connect();
            $stmt = $pdo->prepare('SELECT * FROM sermons WHERE id = :id');
            $stmt->execute([':id' => $editId]);
            $editingSermon = $stmt->fetch();
        } catch (Throwable $e) {
            $error = 'Unable to load sermon: ' . $e->getMessage();
        }
    }
}

$flashMap = ['added' => 'Sermon added successfully.', 'updated' => 'Sermon updated successfully.', 'deleted' => 'Sermon deleted successfully.'];
$feedback = $flashMap[$_GET['status'] ?? ''] ?? '';

$sermons = [];
try {
    $pdo = db_connect();
    $sermons = $pdo->query(
        'SELECT id, title, speaker, sermon_date, topic, media_type, media_url, content, sermon_image
         FROM sermons ORDER BY sermon_date DESC, id DESC'
    )->fetchAll();
} catch (Throwable $e) {
    if ($error === '') {
        $error = 'Unable to load sermons.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Manage Sermons | BMI Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold">Manage Sermons</h1>
                <p class="mt-2 text-slate-600">Create and remove sermons displayed on the public website.</p>
            </div>
            <div class="flex gap-2">
                <a href="index.php" class="rounded bg-slate-200 text-slate-800 px-4 py-2 text-sm">Dashboard</a>
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
            <h2 class="text-xl font-semibold"><?php echo $editingSermon ? 'Edit Sermon' : 'Add New Sermon'; ?></h2>
            <form method="post" enctype="multipart/form-data" class="mt-4 grid md:grid-cols-2 gap-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="<?php echo $editingSermon ? 'edit' : 'add'; ?>">
                <?php if ($editingSermon): ?>
                    <input type="hidden" name="id" value="<?php echo (int) $editingSermon['id']; ?>">
                <?php endif; ?>

                <label class="block text-sm">
                    <span class="font-medium">Title</span>
                    <input type="text" name="title" required maxlength="200" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        value="<?php echo $editingSermon ? htmlspecialchars((string) $editingSermon['title']) : ''; ?>">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Speaker</span>
                    <input type="text" name="speaker" required maxlength="120" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        value="<?php echo $editingSermon ? htmlspecialchars((string) $editingSermon['speaker']) : ''; ?>">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Date</span>
                    <input type="date" name="sermon_date" required class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        value="<?php echo $editingSermon ? (string) $editingSermon['sermon_date'] : ''; ?>">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Topic</span>
                    <input type="text" name="topic" maxlength="120" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        value="<?php echo $editingSermon ? htmlspecialchars((string) $editingSermon['topic']) : ''; ?>">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Media Type</span>
                    <select name="media_type" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                        <option value="audio" <?php echo $editingSermon && $editingSermon['media_type'] === 'audio' ? 'selected' : ''; ?>>Audio</option>
                        <option value="video" <?php echo $editingSermon && $editingSermon['media_type'] === 'video' ? 'selected' : ''; ?>>Video</option>
                        <option value="text" <?php echo $editingSermon && $editingSermon['media_type'] === 'text' ? 'selected' : ''; ?>>Text</option>
                    </select>
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Media URL</span>
                    <input type="url" name="media_url" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        placeholder="https://..." value="<?php echo $editingSermon ? htmlspecialchars((string) $editingSermon['media_url']) : ''; ?>">
                </label>

                <label class="block text-sm md:col-span-2">
                    <span class="font-medium">Sermon Image</span>
                    <input type="file" name="sermon_image" accept="image/jpeg,image/png,image/gif,image/webp" class="mt-1 w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    <p class="mt-1 text-xs text-slate-600">JPG, PNG, GIF, WebP. Max 5MB.</p>
                    <?php if ($editingSermon && $editingSermon['sermon_image']): ?>
                        <img src="../<?php echo htmlspecialchars((string) $editingSermon['sermon_image']); ?>" alt="" class="mt-2 h-20 w-20 object-cover rounded">
                    <?php endif; ?>
                </label>

                <label class="block text-sm md:col-span-2">
                    <span class="font-medium">Summary / Content</span>
                    <textarea name="content" rows="4" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"><?php echo $editingSermon ? htmlspecialchars((string) $editingSermon['content']) : ''; ?></textarea>
                </label>

                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="rounded bg-blue-700 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-800"><?php echo $editingSermon ? 'Update Sermon' : 'Add Sermon'; ?></button>
                    <?php if ($editingSermon): ?>
                        <a href="sermons.php" class="rounded bg-slate-400 text-white px-4 py-2 text-sm font-semibold hover:bg-slate-500">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded p-5">
            <h2 class="text-xl font-semibold">Existing Sermons</h2>
            <?php if (empty($sermons)): ?>
                <p class="mt-3 text-sm text-slate-600">No sermons found.</p>
            <?php else: ?>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($sermons as $sermon): ?>
                        <div class="border border-slate-200 rounded overflow-hidden hover:shadow-lg transition-shadow">
                            <?php if ($sermon['sermon_image']): ?>
                                <img src="../<?php echo htmlspecialchars((string) $sermon['sermon_image']); ?>" alt="<?php echo htmlspecialchars((string) $sermon['title']); ?>" class="w-full h-40 object-cover" loading="lazy">
                            <?php else: ?>
                                <div class="w-full h-40 bg-slate-200 flex items-center justify-center text-slate-400">No image</div>
                            <?php endif; ?>
                            <div class="p-4">
                                <h3 class="font-semibold text-slate-900"><?php echo htmlspecialchars((string) $sermon['title']); ?></h3>
                                <p class="mt-1 text-sm text-slate-600">
                                    <span class="font-medium"><?php echo htmlspecialchars((string) $sermon['speaker']); ?></span> &middot;
                                    <?php echo htmlspecialchars(date('M d, Y', strtotime((string) $sermon['sermon_date']))); ?>
                                </p>
                                <?php if (!empty($sermon['topic'])): ?>
                                    <p class="mt-2 text-sm text-slate-700">Topic: <?php echo htmlspecialchars((string) $sermon['topic']); ?></p>
                                <?php endif; ?>
                                <p class="mt-1 text-xs text-slate-500">Type: <?php echo htmlspecialchars(strtoupper((string) $sermon['media_type'])); ?></p>
                                <div class="mt-4 flex gap-2">
                                    <a href="sermons.php?edit=<?php echo (int) $sermon['id']; ?>" class="flex-1 rounded border border-blue-300 text-blue-700 text-center px-3 py-2 text-sm hover:bg-blue-50 font-medium">Edit</a>
                                    <form method="post" onsubmit="return confirm('Delete this sermon?');" class="flex-1">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $sermon['id']; ?>">
                                        <button type="submit" class="w-full rounded border border-red-300 text-red-700 px-3 py-2 text-sm hover:bg-red-50 font-medium">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
