<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$feedback = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = db_connect();

        if (isset($_POST['action']) && $_POST['action'] === 'add') {
            $title = trim((string) ($_POST['title'] ?? ''));
            $speaker = trim((string) ($_POST['speaker'] ?? ''));
            $sermonDate = trim((string) ($_POST['sermon_date'] ?? ''));
            $topic = trim((string) ($_POST['topic'] ?? ''));
            $mediaType = trim((string) ($_POST['media_type'] ?? 'audio'));
            $mediaUrl = trim((string) ($_POST['media_url'] ?? ''));
            $content = trim((string) ($_POST['content'] ?? ''));

            $allowedTypes = ['audio', 'video', 'text'];
            if (!in_array($mediaType, $allowedTypes, true)) {
                throw new RuntimeException('Invalid media type selected.');
            }

            if ($title === '' || $speaker === '' || $sermonDate === '') {
                throw new RuntimeException('Title, speaker, and date are required.');
            }

            $stmt = $pdo->prepare(
                'INSERT INTO sermons (title, speaker, sermon_date, topic, media_type, media_url, content)
                 VALUES (:title, :speaker, :sermon_date, :topic, :media_type, :media_url, :content)'
            );
            $stmt->execute([
                ':title' => $title,
                ':speaker' => $speaker,
                ':sermon_date' => $sermonDate,
                ':topic' => $topic !== '' ? $topic : null,
                ':media_type' => $mediaType,
                ':media_url' => $mediaUrl !== '' ? $mediaUrl : null,
                ':content' => $content !== '' ? $content : null,
            ]);

            header('Location: sermons.php?status=added');
            exit;
        }

        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid sermon ID.');
            }

            $stmt = $pdo->prepare('DELETE FROM sermons WHERE id = :id');
            $stmt->execute([':id' => $id]);

            header('Location: sermons.php?status=deleted');
            exit;
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

if (isset($_GET['status']) && $_GET['status'] === 'added') {
    $feedback = 'Sermon added successfully.';
}
if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
    $feedback = 'Sermon deleted successfully.';
}

$sermons = [];
try {
    $pdo = db_connect();
    $stmt = $pdo->query(
        'SELECT id, title, speaker, sermon_date, topic, media_type, media_url, content
         FROM sermons
         ORDER BY sermon_date DESC, id DESC'
    );
    $sermons = $stmt->fetchAll();
} catch (Throwable $e) {
    if ($error === '') {
        $error = 'Unable to load sermons: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <a href="index.php" class="inline-flex items-center rounded bg-slate-800 text-white px-4 py-2 text-sm">Back to Dashboard</a>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($feedback); ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="mt-6 bg-white border border-slate-200 rounded p-5">
            <h2 class="text-xl font-semibold">Add New Sermon</h2>
            <form method="post" class="mt-4 grid md:grid-cols-2 gap-4">
                <input type="hidden" name="action" value="add">

                <label class="block text-sm">
                    <span class="font-medium">Title</span>
                    <input type="text" name="title" required class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Speaker</span>
                    <input type="text" name="speaker" required class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Date</span>
                    <input type="date" name="sermon_date" required class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Topic</span>
                    <input type="text" name="topic" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Media Type</span>
                    <select name="media_type" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                        <option value="audio">Audio</option>
                        <option value="video">Video</option>
                        <option value="text">Text</option>
                    </select>
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Media URL</span>
                    <input type="url" name="media_url" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="https://...">
                </label>

                <label class="block text-sm md:col-span-2">
                    <span class="font-medium">Summary / Content</span>
                    <textarea name="content" rows="4" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"></textarea>
                </label>

                <div class="md:col-span-2">
                    <button type="submit" class="rounded bg-blue-700 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-800">Add Sermon</button>
                </div>
            </form>
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded p-5">
            <h2 class="text-xl font-semibold">Existing Sermons</h2>

            <?php if (empty($sermons)): ?>
                <p class="mt-3 text-sm text-slate-600">No sermons found.</p>
            <?php else: ?>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="text-left border-b border-slate-200">
                                <th class="py-2 pr-3">Title</th>
                                <th class="py-2 pr-3">Speaker</th>
                                <th class="py-2 pr-3">Date</th>
                                <th class="py-2 pr-3">Type</th>
                                <th class="py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sermons as $sermon): ?>
                                <tr class="border-b border-slate-100 align-top">
                                    <td class="py-3 pr-3">
                                        <p class="font-semibold"><?php echo htmlspecialchars((string) $sermon['title']); ?></p>
                                        <?php if (!empty($sermon['topic'])): ?>
                                            <p class="text-slate-600 mt-1">Topic: <?php echo htmlspecialchars((string) $sermon['topic']); ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 pr-3"><?php echo htmlspecialchars((string) $sermon['speaker']); ?></td>
                                    <td class="py-3 pr-3"><?php echo htmlspecialchars(date('M d, Y', strtotime((string) $sermon['sermon_date']))); ?></td>
                                    <td class="py-3 pr-3"><?php echo htmlspecialchars(strtoupper((string) $sermon['media_type'])); ?></td>
                                    <td class="py-3">
                                        <form method="post" onsubmit="return confirm('Delete this sermon?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int) $sermon['id']; ?>">
                                            <button type="submit" class="rounded border border-red-300 text-red-700 px-3 py-1 hover:bg-red-50">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
