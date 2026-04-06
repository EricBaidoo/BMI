<?php
$pageTitle = 'Sermons | Bridge Ministries International';
require_once __DIR__ . '/includes/db.php';

$sermons = [];
$sermonsError = null;

try {
    $pdo = db_connect();
    $stmt = $pdo->query(
        "SELECT id, title, speaker, sermon_date, topic, media_type, media_url, content
         FROM sermons
         ORDER BY sermon_date DESC, id DESC"
    );
    $sermons = $stmt->fetchAll();
} catch (Throwable $e) {
    $sermonsError = 'Sermons are temporarily unavailable.';
}

include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip">Sermons</span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3">Watch, Listen, and Grow</h1>
        <p class="mt-4 text-lg muted-copy max-w-3xl">Browse messages by date, speaker, and topic. Whether you missed a service or want to revisit a teaching, start here.</p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="section-card">
        <h2 class="text-xl font-semibold">Latest Messages</h2>
        <div class="mt-4 grid md:grid-cols-2 gap-4">
            <?php if ($sermonsError): ?>
                <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 p-4 md:col-span-2">
                    <?php echo htmlspecialchars($sermonsError); ?>
                </div>
            <?php elseif (empty($sermons)): ?>
                <div class="rounded-xl border border-slate-200 p-4 md:col-span-2">
                    No sermons have been published yet.
                </div>
            <?php else: ?>
                <?php foreach ($sermons as $sermon): ?>
                    <article class="rounded-xl border border-slate-200 p-4">
                        <h3 class="font-semibold"><?php echo htmlspecialchars((string) $sermon['title']); ?></h3>
                        <div class="detail-row">
                            <span>Speaker: <?php echo htmlspecialchars((string) $sermon['speaker']); ?></span>
                            <span>Date: <?php echo htmlspecialchars(date('Y-m-d', strtotime((string) $sermon['sermon_date']))); ?></span>
                            <?php if (!empty($sermon['topic'])): ?>
                                <span>Topic: <?php echo htmlspecialchars((string) $sermon['topic']); ?></span>
                            <?php endif; ?>
                            <span>Type: <?php echo htmlspecialchars(strtoupper((string) $sermon['media_type'])); ?></span>
                        </div>
                        <?php if (!empty($sermon['content'])): ?>
                            <p class="text-sm mt-2 muted-copy"><?php echo htmlspecialchars((string) $sermon['content']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($sermon['media_url'])): ?>
                            <a href="<?php echo htmlspecialchars((string) $sermon['media_url']); ?>" target="_blank" rel="noopener" class="inline-block mt-3 text-teal-700 text-sm font-semibold">Open Media</a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="section-card mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Never Miss a Message</h2>
            <p class="text-sm muted-copy mt-1">Join us in person or online each week.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="livestream.php" class="primary-action">Watch Live</a>
            <a href="contact.php" class="secondary-action">Plan Your Visit</a>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
