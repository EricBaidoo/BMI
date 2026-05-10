<?php
$pageTitle = 'Events | Bridge Ministries International';
$pageDescription = 'Plan your week with upcoming services, outreach, and special gatherings at BMI.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$perPage = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$events = [];
$total = 0;
$eventsError = null;

try {
    $pdo = db_connect();
    $total = (int) $pdo->query('SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()')->fetchColumn();
    $stmt = $pdo->prepare(
        'SELECT id, title, description, event_date, event_time, venue, event_image
         FROM events
         WHERE event_date >= CURDATE()
         ORDER BY event_date ASC, event_time ASC, id DESC
         LIMIT :lim OFFSET :off'
    );
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll();
} catch (Throwable $e) {
    $eventsError = 'Events are temporarily unavailable.';
}

$totalPages = (int) ceil(max(1, $total) / $perPage);

include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip">Events</span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3">What Is Happening at BMI</h1>
        <p class="mt-4 text-lg muted-copy max-w-3xl">Plan your week with upcoming services, outreach opportunities, and special gatherings.</p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <?php if ($eventsError): ?>
        <div class="section-card"><?php echo e($eventsError); ?></div>
    <?php elseif (empty($events)): ?>
        <div class="section-card">No upcoming events have been published yet. Please check back soon.</div>
    <?php else: ?>
        <div class="grid md:grid-cols-2 gap-4">
            <?php foreach ($events as $event):
                $eventDate = date('M d, Y', strtotime((string) $event['event_date']));
                $eventTime = !empty($event['event_time']) ? date('g:i A', strtotime((string) $event['event_time'])) : null;
                $venue = trim((string) ($event['venue'] ?? ''));
            ?>
                <article class="rounded-xl border border-slate-200 overflow-hidden bg-white">
                    <?php if (!empty($event['event_image'])): ?>
                        <img src="<?php echo e($event['event_image']); ?>" alt="<?php echo e($event['title']); ?>" class="w-full h-48 object-cover" loading="lazy">
                    <?php endif; ?>
                    <div class="p-5">
                        <h2 class="font-semibold text-xl"><?php echo e($event['title']); ?></h2>
                        <p class="text-sm mt-2 muted-copy">
                            <span aria-hidden="true">📅</span> <?php echo e($eventDate); ?>
                            <?php if ($eventTime): ?>&middot; <span aria-hidden="true">⏰</span> <?php echo e($eventTime); ?><?php endif; ?>
                            <?php if ($venue !== ''): ?>&middot; <span aria-hidden="true">📍</span> <?php echo e($venue); ?><?php endif; ?>
                        </p>
                        <?php if (!empty($event['description'])): ?>
                            <p class="text-sm mt-3"><?php echo nl2br(e($event['description'])); ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="mt-8 flex items-center justify-center gap-2 text-sm" aria-label="Pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="px-3 py-2 rounded bg-slate-900 text-white"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="events.php?page=<?php echo $i; ?>" class="px-3 py-2 rounded border border-slate-200 hover:bg-slate-100"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

    <div class="section-card mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Do Not Miss an Event</h2>
            <p class="text-sm muted-copy mt-1">Get event reminders and ministry updates directly in your inbox.</p>
        </div>
        <a href="contact.php" class="secondary-action">Request Weekly Updates</a>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
