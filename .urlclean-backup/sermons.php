<?php
$pageTitle = 'Sermons | Bridge Ministries International';
$pageDescription = 'Browse messages by date, speaker, and topic from Bridge Ministries International.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$perPage = 12;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$sermons = [];
$total = 0;
$sermonsError = null;

try {
    $pdo = db_connect();
    $total = (int) $pdo->query('SELECT COUNT(*) FROM sermons')->fetchColumn();
    $stmt = $pdo->prepare(
        'SELECT id, title, speaker, sermon_date, topic, media_type, media_url, content, sermon_image
         FROM sermons
         ORDER BY sermon_date DESC, id DESC
         LIMIT :lim OFFSET :off'
    );
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $sermons = $stmt->fetchAll();
} catch (Throwable $e) {
    $sermonsError = 'Sermons are temporarily unavailable.';
}

$totalPages = (int) ceil(max(1, $total) / $perPage);

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
    <?php if ($sermonsError): ?>
        <div class="section-card"><?php echo e($sermonsError); ?></div>
    <?php elseif (empty($sermons)): ?>
        <div class="section-card">No sermons have been published yet. Please check back soon.</div>
    <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($sermons as $sermon): ?>
                <article class="rounded-xl border border-slate-200 overflow-hidden bg-white">
                    <?php if (!empty($sermon['sermon_image'])): ?>
                        <img src="<?php echo e($sermon['sermon_image']); ?>" alt="<?php echo e($sermon['title']); ?>" class="w-full h-44 object-cover" loading="lazy">
                    <?php endif; ?>
                    <div class="p-4">
                        <h3 class="font-semibold text-slate-900"><?php echo e($sermon['title']); ?></h3>
                        <p class="mt-1 text-sm text-slate-600">
                            <?php echo e($sermon['speaker']); ?> &middot;
                            <?php echo e(date('M d, Y', strtotime((string) $sermon['sermon_date']))); ?>
                        </p>
                        <?php if (!empty($sermon['topic'])): ?>
                            <p class="mt-2 text-sm">Topic: <?php echo e($sermon['topic']); ?></p>
                        <?php endif; ?>
                        <p class="mt-1 text-xs text-slate-500"><?php echo e(strtoupper($sermon['media_type'])); ?></p>
                        <?php if (!empty($sermon['content'])): ?>
                            <p class="mt-3 text-sm text-slate-700"><?php echo e(excerpt($sermon['content'], 25)); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($sermon['media_url'])): ?>
                            <a href="<?php echo e($sermon['media_url']); ?>" target="_blank" rel="noopener" class="inline-block mt-3 text-teal-700 text-sm font-semibold hover:underline">Open Media &rarr;</a>
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
                        <a href="sermons.php?page=<?php echo $i; ?>" class="px-3 py-2 rounded border border-slate-200 hover:bg-slate-100"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

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
