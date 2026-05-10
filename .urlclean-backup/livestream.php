<?php
$pageTitle = 'Livestream | Bridge Ministries International';
$pageDescription = 'Watch BMI services live and replay recent recordings.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/settings.php';

$liveEmbedUrl   = setting('live.embed_url', (string) env('LIVESTREAM_EMBED_URL', ''));
$svcSundayLive  = setting('service.sunday_worship');
$svcPrayerLive  = setting('service.prayer_service');
$youtubeChannel = setting('live.youtube_channel_url');

$recordings = [];
try {
    $pdo = db_connect();
    $stmt = $pdo->query(
        "SELECT id, title, sermon_date, media_url
         FROM sermons
         WHERE media_type = 'video' AND media_url IS NOT NULL AND media_url <> ''
         ORDER BY sermon_date DESC, id DESC
         LIMIT 8"
    );
    $recordings = $stmt->fetchAll();
} catch (Throwable $e) {
    $recordings = [];
}

include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip">Livestream</span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3">Worship with Us Online</h1>
        <p class="mt-4 text-lg muted-copy max-w-3xl">Join services live from anywhere and access recent recordings throughout the week.</p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="section-card">
        <h2 class="font-semibold text-xl mb-3">Live Service Player</h2>
        <div class="aspect-video w-full bg-slate-200 rounded-xl overflow-hidden flex items-center justify-center text-slate-500">
            <?php if ($liveEmbedUrl !== ''): ?>
                <iframe class="w-full h-full" src="<?php echo e($liveEmbedUrl); ?>" title="BMI Live Service" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            <?php else: ?>
                <p class="text-center px-4 text-sm">
                    Live stream will appear here once configured.
                    <?php if ($youtubeChannel !== ''): ?>
                        In the meantime, visit our <a class="text-teal-700 hover:underline" target="_blank" rel="noopener" href="<?php echo e($youtubeChannel); ?>">YouTube channel</a>.
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
        <p class="text-sm mt-3 muted-copy">
            Service Schedule:
            <?php
                $svc = array_filter([$svcSundayLive, $svcPrayerLive]);
                echo $svc ? e(implode(' · ', $svc)) . '.' : 'see our weekly service times.';
            ?>
        </p>
    </div>

    <div class="grid md:grid-cols-2 gap-4 mt-6">
        <div class="section-card">
            <h2 class="font-semibold text-xl">Past Recordings</h2>
            <?php if (empty($recordings)): ?>
                <p class="text-sm mt-3 muted-copy">Recordings will appear here as soon as we publish video sermons.</p>
            <?php else: ?>
                <ul class="mt-3 text-sm space-y-2">
                    <?php foreach ($recordings as $r): ?>
                        <li>
                            <a href="<?php echo e($r['media_url']); ?>" target="_blank" rel="noopener" class="text-teal-700 hover:underline">
                                <?php echo e($r['title']); ?> | <?php echo e(date('M d, Y', strtotime((string) $r['sermon_date']))); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a href="sermons.php" class="inline-block mt-4 text-sm font-semibold text-teal-700 hover:underline">Browse all sermons &rarr;</a>
            <?php endif; ?>
        </div>
        <div class="section-card">
            <h2 class="font-semibold text-xl">Need Prayer While Watching?</h2>
            <p class="text-sm mt-2 muted-copy">Our team is available to pray with you and respond to prayer requests.</p>
            <a href="contact.php" class="primary-action mt-4">Send Prayer Request</a>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
