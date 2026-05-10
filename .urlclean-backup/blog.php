<?php
$pageTitle = 'Good News | Bridge Ministries International';
$pageDescription = 'News, announcements, and devotionals from Bridge Ministries International.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$slug = trim((string) ($_GET['post'] ?? ''));
$single = null;
$posts = [];

try {
    $pdo = db_connect();

    if ($slug !== '') {
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE slug = :s AND published_at IS NOT NULL LIMIT 1');
        $stmt->execute([':s' => $slug]);
        $single = $stmt->fetch();
    }

    if (!$single) {
        $posts = $pdo->query(
            'SELECT id, title, slug, content, category, published_at
             FROM posts
             WHERE published_at IS NOT NULL
             ORDER BY published_at DESC
             LIMIT 30'
        )->fetchAll();
    } else {
        $pageTitle = $single['title'] . ' | Bridge Ministries International';
        $pageDescription = excerpt($single['content'], 30);
    }
} catch (Throwable $e) {
    $posts = [];
}

include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip"><?php echo $single ? ucfirst((string) $single['category']) : 'Good News'; ?></span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3"><?php echo $single ? e($single['title']) : 'Church News and Devotionals'; ?></h1>
        <?php if (!$single): ?>
            <p class="mt-4 text-lg muted-copy max-w-3xl">Stay informed about updates, events, and encouragement for your daily walk with Christ.</p>
        <?php else: ?>
            <p class="mt-4 text-sm muted-copy">Posted on <?php echo e(date('F j, Y', strtotime((string) $single['published_at']))); ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <?php if ($single): ?>
        <article class="section-card">
            <div class="prose max-w-none text-slate-800">
                <?php echo nl2br(e($single['content'])); ?>
            </div>
            <p class="mt-6 text-sm"><a href="blog.php" class="text-teal-700 hover:underline">&larr; Back to all posts</a></p>
        </article>
    <?php elseif (empty($posts)): ?>
        <div class="section-card">
            <p class="muted-copy">No posts yet. Please check back soon for updates and devotionals.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($posts as $p): ?>
                <article class="section-card">
                    <span class="tag-chip"><?php echo e(ucfirst($p['category'])); ?></span>
                    <h2 class="font-semibold text-xl mt-3"><a href="blog.php?post=<?php echo e($p['slug']); ?>" class="hover:underline"><?php echo e($p['title']); ?></a></h2>
                    <p class="text-sm mt-2 muted-copy">Posted on <?php echo e(date('Y-m-d', strtotime((string) $p['published_at']))); ?></p>
                    <p class="text-sm mt-2"><?php echo e(excerpt($p['content'], 40)); ?></p>
                    <a href="blog.php?post=<?php echo e($p['slug']); ?>" class="inline-block mt-3 text-teal-700 text-sm font-semibold hover:underline">Read article &rarr;</a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="section-card mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Get New Posts by Email</h2>
            <p class="text-sm muted-copy mt-1">Subscribe to updates and never miss church news.</p>
        </div>
        <a href="contact.php" class="secondary-action">Subscribe</a>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
