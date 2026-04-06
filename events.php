<?php
$pageTitle = 'Events | Bridge Ministries International';
require_once __DIR__ . '/includes/db.php';

$events = [];
$eventsError = null;

try {
    $pdo = db_connect();
    $stmt = $pdo->query(
        "SELECT id, title, description, event_date, event_time, venue
         FROM events
         ORDER BY event_date ASC, event_time ASC, id DESC"
    );
    $events = $stmt->fetchAll();
} catch (Throwable $e) {
    $eventsError = 'Events are temporarily unavailable.';
}

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
        <div class="section-card"><?php echo htmlspecialchars($eventsError); ?></div>
    <?php endif; ?>

    <div class="space-y-4">
        <?php if (empty($events) && !$eventsError): ?>
            <div class="section-card">No events have been published yet. Please check back soon.</div>
        <?php endif; ?>

        <?php foreach ($events as $event): ?>
            <?php
                $eventDate = date('M d, Y', strtotime((string) $event['event_date']));
                $eventTime = !empty($event['event_time']) ? date('g:i A', strtotime((string) $event['event_time'])) : null;
                $venue = trim((string) ($event['venue'] ?? ''));
                $meta = ['Date: ' . $eventDate];
                if ($eventTime) {
                    $meta[] = 'Time: ' . $eventTime;
                }
                if ($venue !== '') {
                    $meta[] = 'Venue: ' . $venue;
                }
            ?>
            <article class="section-card icon-card" data-icon="E">
                <h2 class="font-semibold text-xl"><?php echo htmlspecialchars((string) $event['title']); ?></h2>
                <p class="text-sm mt-3 muted-copy"><?php echo htmlspecialchars(implode(' | ', $meta)); ?></p>
                <?php if (!empty($event['description'])): ?>
                    <p class="text-sm mt-2"><?php echo nl2br(htmlspecialchars((string) $event['description'])); ?></p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="section-card mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Do Not Miss an Event</h2>
            <p class="text-sm muted-copy mt-1">Get event reminders and ministry updates directly in your inbox.</p>
        </div>
        <a href="contact.php" class="secondary-action">Request Weekly Updates</a>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
