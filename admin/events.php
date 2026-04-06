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
            $description = trim((string) ($_POST['description'] ?? ''));
            $eventDate = trim((string) ($_POST['event_date'] ?? ''));
            $eventTime = trim((string) ($_POST['event_time'] ?? ''));
            $venue = trim((string) ($_POST['venue'] ?? ''));

            if ($title === '' || $eventDate === '') {
                throw new RuntimeException('Title and date are required.');
            }

            $stmt = $pdo->prepare(
                'INSERT INTO events (title, description, event_date, event_time, venue) VALUES (:title, :description, :event_date, :event_time, :venue)'
            );
            $stmt->execute([
                ':title' => $title,
                ':description' => $description !== '' ? $description : null,
                ':event_date' => $eventDate,
                ':event_time' => $eventTime !== '' ? $eventTime : null,
                ':venue' => $venue !== '' ? $venue : null,
            ]);

            header('Location: events.php?status=added');
            exit;
        }

        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid event ID.');
            }

            $stmt = $pdo->prepare('DELETE FROM events WHERE id = :id');
            $stmt->execute([':id' => $id]);

            header('Location: events.php?status=deleted');
            exit;
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

if (isset($_GET['status']) && $_GET['status'] === 'added') {
    $feedback = 'Event added successfully.';
}
if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
    $feedback = 'Event deleted successfully.';
}

$events = [];
try {
    $pdo = db_connect();
    $stmt = $pdo->query(
        'SELECT id, title, description, event_date, event_time, venue, created_at
         FROM events
         ORDER BY event_date DESC, event_time DESC, id DESC'
    );
    $events = $stmt->fetchAll();
} catch (Throwable $e) {
    if ($error === '') {
        $error = 'Unable to load events: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <a href="index.php" class="inline-flex items-center rounded bg-slate-800 text-white px-4 py-2 text-sm">Back to Dashboard</a>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($feedback); ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="mt-6 bg-white border border-slate-200 rounded p-5">
            <h2 class="text-xl font-semibold">Add New Event</h2>
            <form method="post" class="mt-4 grid md:grid-cols-2 gap-4">
                <input type="hidden" name="action" value="add">

                <label class="block text-sm">
                    <span class="font-medium">Title</span>
                    <input type="text" name="title" required class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Venue</span>
                    <input type="text" name="venue" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Date</span>
                    <input type="date" name="event_date" required class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Time</span>
                    <input type="time" name="event_time" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                </label>

                <label class="block text-sm md:col-span-2">
                    <span class="font-medium">Description</span>
                    <textarea name="description" rows="4" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"></textarea>
                </label>

                <div class="md:col-span-2">
                    <button type="submit" class="rounded bg-blue-700 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-800">Add Event</button>
                </div>
            </form>
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded p-5">
            <h2 class="text-xl font-semibold">Existing Events</h2>

            <?php if (empty($events)): ?>
                <p class="mt-3 text-sm text-slate-600">No events found.</p>
            <?php else: ?>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="text-left border-b border-slate-200">
                                <th class="py-2 pr-3">Title</th>
                                <th class="py-2 pr-3">Date</th>
                                <th class="py-2 pr-3">Time</th>
                                <th class="py-2 pr-3">Venue</th>
                                <th class="py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                                <tr class="border-b border-slate-100 align-top">
                                    <td class="py-3 pr-3">
                                        <p class="font-semibold"><?php echo htmlspecialchars((string) $event['title']); ?></p>
                                        <?php if (!empty($event['description'])): ?>
                                            <p class="text-slate-600 mt-1"><?php echo htmlspecialchars((string) $event['description']); ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 pr-3"><?php echo htmlspecialchars(date('M d, Y', strtotime((string) $event['event_date']))); ?></td>
                                    <td class="py-3 pr-3"><?php echo !empty($event['event_time']) ? htmlspecialchars(date('g:i A', strtotime((string) $event['event_time']))) : '-'; ?></td>
                                    <td class="py-3 pr-3"><?php echo !empty($event['venue']) ? htmlspecialchars((string) $event['venue']) : '-'; ?></td>
                                    <td class="py-3">
                                        <form method="post" onsubmit="return confirm('Delete this event?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int) $event['id']; ?>">
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
