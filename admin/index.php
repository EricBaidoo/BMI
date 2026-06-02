<?php
$pageTitle = 'Dashboard | BMI Admin';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$counts = [
    'sermons' => 0,
    'events' => 0,
    'ministries' => 0,
    'posts' => 0,
    'messages' => 0,
];
try {
    $pdo = db_connect();
    foreach (array_keys($counts) as $table) {
        $counts[$table] = (int) $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    }
} catch (Throwable $e) {
    // ignore — dashboard still renders
}
?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800">Welcome back, <?php echo htmlspecialchars($user['name'] ?? 'Admin'); ?>!</h1>
    <p class="text-slate-500 mt-1">Here is what's happening with your church website today.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    
    <!-- Quick Stat Cards -->
    <a href="messages.php" class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-blue-300 transition-all group relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <?php echo render_icon('inbox'); ?>
        </div>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <?php echo render_icon('inbox'); ?>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">New Messages</p>
                <p class="text-2xl font-bold text-slate-800 mt-0.5"><?php echo $counts['messages']; ?></p>
            </div>
        </div>
    </a>

    <a href="sermons.php" class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:indigo-300 transition-all group relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <?php echo render_icon('video-camera'); ?>
        </div>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <?php echo render_icon('video-camera'); ?>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Total Sermons</p>
                <p class="text-2xl font-bold text-slate-800 mt-0.5"><?php echo $counts['sermons']; ?></p>
            </div>
        </div>
    </a>

    <a href="events.php" class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all group relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <?php echo render_icon('calendar'); ?>
        </div>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <?php echo render_icon('calendar'); ?>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Upcoming Events</p>
                <p class="text-2xl font-bold text-slate-800 mt-0.5"><?php echo $counts['events']; ?></p>
            </div>
        </div>
    </a>

    <a href="posts.php" class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-amber-300 transition-all group relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <?php echo render_icon('newspaper'); ?>
        </div>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                <?php echo render_icon('newspaper'); ?>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Blog Posts</p>
                <p class="text-2xl font-bold text-slate-800 mt-0.5"><?php echo $counts['posts']; ?></p>
            </div>
        </div>
    </a>
</div>

<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800">Quick Actions</h2>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4">
            <a href="sermons.php" class="flex flex-col items-center justify-center p-4 rounded-lg border border-slate-100 hover:border-blue-200 hover:bg-blue-50 text-slate-600 hover:text-blue-700 transition-colors text-center group">
                <div class="w-10 h-10 rounded-full bg-slate-50 group-hover:bg-white flex items-center justify-center mb-2 text-slate-400 group-hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-sm font-medium">Add Sermon</span>
            </a>
            <a href="events.php" class="flex flex-col items-center justify-center p-4 rounded-lg border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 transition-colors text-center group">
                <div class="w-10 h-10 rounded-full bg-slate-50 group-hover:bg-white flex items-center justify-center mb-2 text-slate-400 group-hover:text-emerald-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-sm font-medium">Create Event</span>
            </a>
            <a href="posts.php" class="flex flex-col items-center justify-center p-4 rounded-lg border border-slate-100 hover:border-amber-200 hover:bg-amber-50 text-slate-600 hover:text-amber-700 transition-colors text-center group">
                <div class="w-10 h-10 rounded-full bg-slate-50 group-hover:bg-white flex items-center justify-center mb-2 text-slate-400 group-hover:text-amber-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-sm font-medium">Write Post</span>
            </a>
            <a href="pages.php" class="flex flex-col items-center justify-center p-4 rounded-lg border border-slate-100 hover:border-purple-200 hover:bg-purple-50 text-slate-600 hover:text-purple-700 transition-colors text-center group">
                <div class="w-10 h-10 rounded-full bg-slate-50 group-hover:bg-white flex items-center justify-center mb-2 text-slate-400 group-hover:text-purple-600">
                    <?php echo render_icon('document-text'); ?>
                </div>
                <span class="text-sm font-medium">Edit Pages</span>
            </a>
        </div>
    </div>

    <!-- System Info -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800">System Information</h2>
        </div>
        <div class="p-6 flex-1 flex flex-col justify-center">
            <ul class="space-y-4">
                <li class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Platform</span>
                    <span class="font-medium text-slate-800">BMI CMS v2.0</span>
                </li>
                <li class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">PHP Version</span>
                    <span class="font-medium text-slate-800"><?php echo phpversion(); ?></span>
                </li>
                <li class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Database</span>
                    <span class="font-medium text-slate-800">MySQL (Connected)</span>
                </li>
                <li class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Server Time</span>
                    <span class="font-medium text-slate-800"><?php echo date('Y-m-d H:i:s'); ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
