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

            $sermonImage = handle_image_upload_or_link($_FILES['sermon_image'] ?? null, $_POST['sermon_image_url'] ?? '', 'sermon');

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
<?php
$pageTitle = 'Manage Sermons | BMI Admin';
require_once __DIR__ . '/includes/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Manage Sermons</h1>
            <p class="mt-1 text-slate-500">Create and remove sermons displayed on the public website.</p>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($feedback); ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="mt-6 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4"><?php echo $editingSermon ? 'Edit Sermon' : 'Add New Sermon'; ?></h2>
            <form method="post" enctype="multipart/form-data" class="mt-6 grid md:grid-cols-2 gap-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="<?php echo $editingSermon ? 'edit' : 'add'; ?>">
                <?php if ($editingSermon): ?>
                    <input type="hidden" name="id" value="<?php echo (int) $editingSermon['id']; ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Title</label>
                    <input type="text" name="title" required maxlength="200" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editingSermon ? htmlspecialchars((string) $editingSermon['title']) : ''; ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Speaker</label>
                    <input type="text" name="speaker" required maxlength="120" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editingSermon ? htmlspecialchars((string) $editingSermon['speaker']) : ''; ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Date</label>
                    <input type="date" name="sermon_date" required class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editingSermon ? (string) $editingSermon['sermon_date'] : ''; ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Topic</label>
                    <input type="text" name="topic" maxlength="120" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editingSermon ? htmlspecialchars((string) $editingSermon['topic']) : ''; ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Media Type</label>
                    <select name="media_type" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                        <option value="audio" <?php echo $editingSermon && $editingSermon['media_type'] === 'audio' ? 'selected' : ''; ?>>Audio</option>
                        <option value="video" <?php echo $editingSermon && $editingSermon['media_type'] === 'video' ? 'selected' : ''; ?>>Video</option>
                        <option value="text" <?php echo $editingSermon && $editingSermon['media_type'] === 'text' ? 'selected' : ''; ?>>Text</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Media URL <span class="text-xs font-normal text-slate-500">(e.g., YouTube or Spotify link)</span></label>
                    <input type="url" name="media_url" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        placeholder="https://..." value="<?php echo $editingSermon ? htmlspecialchars((string) $editingSermon['media_url']) : ''; ?>">
                </div>

                <div class="md:col-span-2 p-5 border border-slate-200 rounded-lg bg-slate-50/50">
                    <label class="block text-sm font-semibold text-slate-700 mb-3">Sermon Image</label>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <span class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wider">Upload File (Max 5MB)</span>
                            <input type="file" name="sermon_image" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-white focus:bg-white transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wider">OR Paste Image URL</span>
                            <input type="url" name="sermon_image_url" placeholder="https://..." class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-white focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all text-sm">
                        </div>
                    </div>
                    <?php if ($editingSermon && $editingSermon['sermon_image']): ?>
                        <div class="mt-4 relative inline-block">
                            <span class="block text-xs font-medium text-slate-500 mb-1.5">Current Image:</span>
                            <img src="<?php echo strpos($editingSermon['sermon_image'], 'http') === 0 ? htmlspecialchars($editingSermon['sermon_image']) : '../' . htmlspecialchars($editingSermon['sermon_image']); ?>" alt="" class="h-24 w-32 object-cover rounded-lg border border-slate-200 shadow-sm">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Summary / Content</label>
                    <textarea name="content" rows="4" class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"><?php echo $editingSermon ? htmlspecialchars((string) $editingSermon['content']) : ''; ?></textarea>
                </div>

                <div class="md:col-span-2 pt-4 border-t border-slate-100 flex gap-3">
                    <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-6 py-2.5 text-sm font-semibold transition-all shadow-md shadow-blue-500/30">
                        <?php echo $editingSermon ? 'Update Sermon' : 'Add Sermon'; ?>
                    </button>
                    <?php if ($editingSermon): ?>
                        <a href="sermons.php" class="rounded-lg border border-slate-300 text-slate-700 px-6 py-2.5 text-sm font-semibold hover:bg-slate-50 transition-all text-center">Cancel</a>
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
                                <img src="<?php echo strpos($sermon['sermon_image'], 'http') === 0 ? htmlspecialchars((string) $sermon['sermon_image']) : '../' . htmlspecialchars((string) $sermon['sermon_image']); ?>" alt="<?php echo htmlspecialchars((string) $sermon['title']); ?>" class="w-full h-40 object-cover" loading="lazy">
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
                                <p class="mt-2 flex items-center gap-2">
                                    <?php if ($sermon['media_type'] === 'video'): ?>
                                        <span class="inline-block px-2 py-0.5 rounded text-[0.65rem] font-bold tracking-wider uppercase bg-red-100 text-red-700 border border-red-200">Video</span>
                                    <?php elseif ($sermon['media_type'] === 'audio'): ?>
                                        <span class="inline-block px-2 py-0.5 rounded text-[0.65rem] font-bold tracking-wider uppercase bg-emerald-100 text-emerald-700 border border-emerald-200">Audio</span>
                                    <?php else: ?>
                                        <span class="inline-block px-2 py-0.5 rounded text-[0.65rem] font-bold tracking-wider uppercase bg-slate-100 text-slate-700 border border-slate-200">Text</span>
                                    <?php endif; ?>
                                </p>
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
<?php require_once __DIR__ . '/includes/footer.php'; ?>
