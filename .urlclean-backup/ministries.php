<?php
$pageTitle = 'Ministries | Bridge Ministries International';
$pageDescription = 'Find your community at Bridge Ministries International — youth, children, women, men, and serving teams.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$ministries = [];
try {
    $pdo = db_connect();
    $ministries = $pdo->query('SELECT * FROM ministries ORDER BY name ASC')->fetchAll();
} catch (Throwable $e) {
    $ministries = [];
}

include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip">Ministries</span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3">Find Your Community</h1>
        <p class="mt-4 text-lg muted-copy max-w-3xl">Every ministry at BMI is designed to help people grow in Christ, build relationships, and serve with purpose.</p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <?php if (empty($ministries)): ?>
        <div class="section-card">
            <p class="muted-copy">Ministries will be listed here soon. Please contact the church office to learn how to get involved.</p>
            <a href="contact.php" class="primary-action mt-4">Get Connected</a>
        </div>
    <?php else: ?>
        <div class="mt-1 grid md:grid-cols-2 gap-4">
            <?php foreach ($ministries as $m):
                $iconLetter = strtoupper(substr((string) $m['name'], 0, 1));
            ?>
                <div class="section-card icon-card" data-icon="<?php echo e($iconLetter); ?>">
                    <h2 class="font-semibold text-xl"><?php echo e($m['name']); ?></h2>
                    <?php if (!empty($m['leader_name'])): ?>
                        <p class="text-sm mt-2 muted-copy">Leader: <?php echo e($m['leader_name']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($m['meeting_schedule'])): ?>
                        <p class="text-sm muted-copy">Meeting: <?php echo e($m['meeting_schedule']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($m['description'])): ?>
                        <p class="text-sm mt-2"><?php echo e($m['description']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="section-card mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Take Your Next Step</h2>
            <p class="text-sm muted-copy mt-1">Not sure where to begin? We can help you find the right ministry fit.</p>
        </div>
        <a href="contact.php" class="primary-action">Get Connected</a>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
