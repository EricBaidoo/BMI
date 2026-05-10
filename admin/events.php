<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/uploads.php';

$feedback = '';
$error = '';
$editingEvent = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_check();
        $pdo = db_connect();
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'add' || $action === 'edit') {
            $title = trim((string) ($_POST['title'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $eventDate = trim((string) ($_POST['event_date'] ?? ''));
            $eventTime = trim((string) ($_POST['event_time'] ?? ''));
            $venue = trim((string) ($_POST['venue'] ?? ''));

            if ($title === '' || $eventDate === '') {
                throw new RuntimeException('Title and date are required.');
            }

            $eventImage = upload_image($_FILES['event_image'] ?? null, 'event');

            if ($action === 'add') {
                $stmt = $pdo->prepare(
                    'INSERT INTO events (title, description, event_date, event_time, venue, event_image)
                     VALUES (:title, :description, :event_date, :event_time, :venue, :event_image)'
                );
                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description !== '' ? $description : null,
                    ':event_date' => $eventDate,
                    ':event_time' => $eventTime !== '' ? $eventTime : null,
                    ':venue' => $venue !== '' ? $venue : null,
                    ':event_image' => $eventImage,
                ]);
                header('Location: events.php?status=added');
                exit;
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid event ID.');
            }
            $existing = $pdo->prepare('SELECT event_image FROM events WHERE id = :id');
            $existing->execute([':id' => $id]);
            $row = $existing->fetch();
            if (!$row) {
                throw new RuntimeException('Event not found.');
            }

            $finalImage = $row['event_image'];
            if ($eventImage !== null) {
                upload_delete($row['event_image']);
                $finalImage = $eventImage;
            }

            $stmt = $pdo->prepare(
                'UPDATE events SET title = :title, description = :description, event_date = :event_date,
                    event_time = :event_time, venue = :venue, event_image = :event_image WHERE id = :id'
            );
            $stmt->execute([
                ':id' => $id,
                ':title' => $title,
                ':description' => $description !== '' ? $description : null,
                ':event_date' => $eventDate,
                ':event_time' => $eventTime !== '' ? $eventTime : null,
                ':venue' => $venue !== '' ? $venue : null,
                ':event_image' => $finalImage,
            ]);
            header('Location: events.php?status=updated');
            exit;
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid event ID.');
            }
            $row = $pdo->prepare('SELECT event_image FROM events WHERE id = :id');
            $row->execute([':id' => $id]);
            $existing = $row->fetch();
            if ($existing) {
                upload_delete($existing['event_image']);
            }
            $pdo->prepare('DELETE FROM events WHERE id = :id')->execute([':id' => $id]);
            header('Location: events.php?status=deleted');
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
            $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
            $stmt->execute([':id' => $editId]);
            $editingEvent = $stmt->fetch();
        } catch (Throwable $e) {
            $error = 'Unable to load event: ' . $e->getMessage();
        }
    }
}

$flashMap = ['added' => 'Event added successfully.', 'updated' => 'Event updated successfully.', 'deleted' => 'Event deleted successfully.'];
$feedback = $flashMap[$_GET['status'] ?? ''] ?? '';

$events = [];
try {
    $pdo = db_connect();
    $events = $pdo->query(
        'SELECT id, title, description, event_date, event_time, venue, event_image
         FROM events ORDER BY event_date DESC, event_time DESC, id DESC'
    )->fetchAll();
} catch (Throwable $e) {
    if ($error === '') {
        $error = 'Unable to load events.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Manage Events | BMI Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold">Manage Events</h1>
                <p class="mt-2 text-slate-600">Create and remove events that appear on the public website.</p>
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
            <h2 class="text-xl font-semibold"><?php echo $editingEvent ? 'Edit Event' : 'Add New Event'; ?></h2>
            <form method="post" enctype="multipart/form-data" class="mt-4 grid md:grid-cols-2 gap-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="<?php echo $editingEvent ? 'edit' : 'add'; ?>">
                <?php if ($editingEvent): ?>
                    <input type="hidden" name="id" value="<?php echo (int) $editingEvent['id']; ?>">
                <?php endif; ?>

                <label class="block text-sm">
                    <span class="font-medium">Title *</span>
                    <input type="text" name="title" required maxlength="200" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        value="<?php echo $editingEvent ? htmlspecialchars((string) $editingEvent['title']) : ''; ?>">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Venue</span>
                    <input type="text" name="venue" maxlength="200" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        value="<?php echo $editingEvent ? htmlspecialchars((string) ($editingEvent['venue'] ?? '')) : ''; ?>">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Date *</span>
                    <input type="date" name="event_date" required class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        value="<?php echo $editingEvent ? htmlspecialchars((string) $editingEvent['event_date']) : ''; ?>">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Time</span>
                    <input type="time" name="event_time" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        value="<?php echo $editingEvent && $editingEvent['event_time'] ? htmlspecialchars((string) $editingEvent['event_time']) : ''; ?>">
                </label>

                <label class="block text-sm md:col-span-2">
                    <span class="font-medium">Description</span>
                    <textarea name="description" rows="4" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"><?php echo $editingEvent ? htmlspecialchars((string) ($editingEvent['description'] ?? '')) : ''; ?></textarea>
                </label>

                <label class="block text-sm md:col-span-2">
                    <span class="font-medium">Event Image (JPG, PNG, GIF, WebP. Max 5MB)</span>
                    <input type="file" name="event_image" accept="image/jpeg,image/png,image/gif,image/webp" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                    <?php if ($editingEvent && $editingEvent['event_image']): ?>
                        <img src="../<?php echo htmlspecialchars((string) $editingEvent['event_image']); ?>" alt="" class="mt-2 h-20 w-20 object-cover rounded">
                    <?php endif; ?>
                </label>

                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="rounded bg-blue-700 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-800">
                        <?php echo $editingEvent ? 'Update Event' : 'Add Event'; ?>
                    </button>
                    <?php if ($editingEvent): ?>
                        <a href="events.php" class="rounded border border-slate-300 text-slate-700 px-4 py-2 text-sm font-semibold hover:bg-slate-50">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded p-5">
            <h2 class="text-xl font-semibold">Existing Events</h2>
            <?php if (empty($events)): ?>
                <p class="mt-3 text-sm text-slate-600">No events found.</p>
            <?php else: ?>
                <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($events as $event): ?>
                        <div class="border border-slate-200 rounded overflow-hidden hover:shadow-lg transition-shadow">
                            <?php if ($event['event_image']): ?>
                                <img src="../<?php echo htmlspecialchars($event['event_image']); ?>" alt="<?php echo htmlspecialchars((string) $event['title']); ?>" class="w-full h-40 object-cover" loading="lazy">
                            <?php else: ?>
                                <div class="w-full h-40 bg-slate-200 flex items-center justify-center text-slate-500">No Image</div>
                            <?php endif; ?>
                            <div class="p-4">
                                <h3 class="font-semibold text-slate-900"><?php echo htmlspecialchars((string) $event['title']); ?></h3>
                                <p class="text-sm text-slate-600 mt-1">
                                    <strong><?php echo htmlspecialchars(date('M d, Y', strtotime((string) $event['event_date']))); ?></strong>
                                    <?php if ($event['event_time']): ?>
                                        at <?php echo htmlspecialchars(date('g:i A', strtotime((string) $event['event_time']))); ?>
                                    <?php endif; ?>
                                </p>
                                <?php if ($event['venue']): ?>
                                    <p class="text-sm text-slate-600"><?php echo htmlspecialchars((string) $event['venue']); ?></p>
                                <?php endif; ?>
                                <?php if ($event['description']): ?>
                                    <p class="text-sm text-slate-600 mt-2"><?php echo htmlspecialchars(substr((string) $event['description'], 0, 100)); ?>&hellip;</p>
                                <?php endif; ?>
                                <div class="mt-4 flex gap-2">
                                    <a href="events.php?edit=<?php echo (int) $event['id']; ?>" class="flex-1 rounded border border-blue-300 text-blue-700 px-3 py-1 text-sm text-center hover:bg-blue-50 font-semibold">Edit</a>
                                    <form method="post" class="flex-1" onsubmit="return confirm('Delete this event?');">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $event['id']; ?>">
                                        <button type="submit" class="w-full rounded border border-red-300 text-red-700 px-3 py-1 text-sm hover:bg-red-50 font-semibold">Delete</button>
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
