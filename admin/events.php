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
            $eventType = trim((string) ($_POST['event_type'] ?? 'special'));
            $description = trim((string) ($_POST['description'] ?? ''));
            $eventDate = trim((string) ($_POST['event_date'] ?? ''));
            $endDate = trim((string) ($_POST['end_date'] ?? ''));
            $eventTime = trim((string) ($_POST['event_time'] ?? ''));
            $venue = trim((string) ($_POST['venue'] ?? ''));

            if ($title === '' || $eventDate === '') {
                throw new RuntimeException('Title and date are required.');
            }

            $eventImage = handle_image_upload_or_link($_FILES['event_image'] ?? null, $_POST['event_image_url'] ?? '', 'event');

            if ($action === 'add') {
                $stmt = $pdo->prepare(
                    'INSERT INTO events (title, event_type, description, event_date, end_date, event_time, venue, event_image)
                     VALUES (:title, :event_type, :description, :event_date, :end_date, :event_time, :venue, :event_image)'
                );
                $stmt->execute([
                    ':title' => $title,
                    ':event_type' => $eventType,
                    ':description' => $description !== '' ? $description : null,
                    ':event_date' => $eventDate,
                    ':end_date' => $endDate !== '' ? $endDate : null,
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
                'UPDATE events SET title = :title, event_type = :event_type, description = :description, event_date = :event_date,
                    end_date = :end_date, event_time = :event_time, venue = :venue, event_image = :event_image WHERE id = :id'
            );
            $stmt->execute([
                ':id' => $id,
                ':title' => $title,
                ':event_type' => $eventType,
                ':description' => $description !== '' ? $description : null,
                ':event_date' => $eventDate,
                ':end_date' => $endDate !== '' ? $endDate : null,
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
        'SELECT id, title, event_type, description, event_date, end_date, event_time, venue, event_image
         FROM events ORDER BY event_date DESC, event_time DESC, id DESC'
    )->fetchAll();
} catch (Throwable $e) {
    if ($error === '') {
        $error = 'Unable to load events.';
    }
}
?>
<?php
$pageTitle = 'Manage Events | BMI Admin';
require_once __DIR__ . '/includes/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Manage Events</h1>
            <p class="mt-1 text-slate-500">Create and remove events that appear on the public website.</p>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($feedback); ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="mt-6 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4"><?php echo $editingEvent ? 'Edit Event' : 'Add New Event'; ?></h2>
            <form method="post" enctype="multipart/form-data" class="mt-6 grid md:grid-cols-2 gap-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="<?php echo $editingEvent ? 'edit' : 'add'; ?>">
                <?php if ($editingEvent): ?>
                    <input type="hidden" name="id" value="<?php echo (int) $editingEvent['id']; ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Title *</label>
                    <input type="text" name="title" required maxlength="200" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editingEvent ? htmlspecialchars((string) $editingEvent['title']) : ''; ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Event Type</label>
                    <select name="event_type" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                        <option value="special" <?php echo ($editingEvent && ($editingEvent['event_type'] ?? '') === 'special') ? 'selected' : ''; ?>>Special Event</option>
                        <option value="flagship" <?php echo ($editingEvent && ($editingEvent['event_type'] ?? '') === 'flagship') ? 'selected' : ''; ?>>Flagship Program</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Start Date *</label>
                    <input type="date" name="event_date" required class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editingEvent ? htmlspecialchars((string) $editingEvent['event_date']) : ''; ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">End Date (Optional)</label>
                    <input type="date" name="end_date" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editingEvent && $editingEvent['end_date'] ? htmlspecialchars((string) $editingEvent['end_date']) : ''; ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Time</label>
                    <input type="time" name="event_time" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editingEvent && $editingEvent['event_time'] ? htmlspecialchars((string) $editingEvent['event_time']) : ''; ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Venue</label>
                    <input type="text" name="venue" maxlength="200" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editingEvent ? htmlspecialchars((string) ($editingEvent['venue'] ?? '')) : ''; ?>">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Description</label>
                    <textarea name="description" rows="4" class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"><?php echo $editingEvent ? htmlspecialchars((string) ($editingEvent['description'] ?? '')) : ''; ?></textarea>
                </div>

                <div class="md:col-span-2 p-5 border border-slate-200 rounded-lg bg-slate-50/50">
                    <label class="block text-sm font-semibold text-slate-700 mb-3">Event Image</label>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <span class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wider">Upload File (Max 5MB)</span>
                            <input type="file" name="event_image" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-white focus:bg-white transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wider">OR Paste Image URL</span>
                            <input type="url" name="event_image_url" placeholder="https://..." class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-white focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all text-sm">
                        </div>
                    </div>
                    <?php if ($editingEvent && $editingEvent['event_image']): ?>
                        <div class="mt-4 relative inline-block">
                            <span class="block text-xs font-medium text-slate-500 mb-1.5">Current Image:</span>
                            <img src="<?php echo strpos($editingEvent['event_image'], 'http') === 0 ? htmlspecialchars($editingEvent['event_image']) : '../' . htmlspecialchars($editingEvent['event_image']); ?>" alt="" class="h-24 w-32 object-cover rounded-lg border border-slate-200 shadow-sm">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="md:col-span-2 pt-4 border-t border-slate-100 flex gap-3">
                    <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-6 py-2.5 text-sm font-semibold transition-all shadow-md shadow-blue-500/30">
                        <?php echo $editingEvent ? 'Update Event' : 'Add Event'; ?>
                    </button>
                    <?php if ($editingEvent): ?>
                        <a href="events.php" class="rounded-lg border border-slate-300 text-slate-700 px-6 py-2.5 text-sm font-semibold hover:bg-slate-50 transition-all text-center">Cancel</a>
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
                                <img src="<?php echo strpos($event['event_image'], 'http') === 0 ? htmlspecialchars($event['event_image']) : '../' . htmlspecialchars($event['event_image']); ?>" alt="<?php echo htmlspecialchars((string) $event['title']); ?>" class="w-full h-40 object-cover" loading="lazy">
                            <?php else: ?>
                                <div class="w-full h-40 bg-slate-200 flex items-center justify-center text-slate-500">No Image</div>
                            <?php endif; ?>
                            <div class="p-4">
                                <h3 class="font-semibold text-slate-900 flex items-center justify-between">
                                    <?php echo htmlspecialchars((string) $event['title']); ?>
                                    <span class="text-[0.6rem] uppercase tracking-widest px-2 py-0.5 rounded-full border <?php echo $event['event_type'] === 'flagship' ? 'border-amber-300 text-amber-700 bg-amber-50' : 'border-slate-300 text-slate-500 bg-slate-50'; ?>">
                                        <?php echo htmlspecialchars((string) $event['event_type']); ?>
                                    </span>
                                </h3>
                                <p class="text-sm text-slate-600 mt-1">
                                    <strong><?php echo htmlspecialchars(date('M d, Y', strtotime((string) $event['event_date']))); ?></strong>
                                    <?php if ($event['end_date']): ?>
                                        - <strong><?php echo htmlspecialchars(date('M d, Y', strtotime((string) $event['end_date']))); ?></strong>
                                    <?php endif; ?>
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
<?php require_once __DIR__ . '/includes/footer.php'; ?>
