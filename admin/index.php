<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

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

$user = auth_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Admin Dashboard | Bridge Ministries International</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <h1 class="text-lg font-bold">BMI Admin</h1>
            <div class="flex items-center gap-3 text-sm">
                <span class="text-slate-600">Signed in as <?php echo htmlspecialchars($user['name'] ?? $user['email']); ?></span>
                <a href="profile.php" class="rounded border border-slate-300 px-3 py-1.5 hover:bg-slate-50">My Profile</a>
                <a href="logout.php" class="rounded bg-slate-800 text-white px-3 py-1.5 hover:bg-slate-900">Sign out</a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto py-10 px-4">
        <h2 class="text-3xl font-bold">Dashboard</h2>
        <p class="mt-2 text-slate-600">Manage content, events, ministries, posts, and messages.</p>

        <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="sermons.php" class="bg-white rounded-lg border border-slate-200 p-5 hover:shadow-md transition">
                <p class="text-sm text-slate-500">Sermons</p>
                <p class="text-2xl font-bold mt-1"><?php echo $counts['sermons']; ?></p>
                <p class="text-sm text-blue-700 mt-2">Manage &rarr;</p>
            </a>
            <a href="events.php" class="bg-white rounded-lg border border-slate-200 p-5 hover:shadow-md transition">
                <p class="text-sm text-slate-500">Events</p>
                <p class="text-2xl font-bold mt-1"><?php echo $counts['events']; ?></p>
                <p class="text-sm text-blue-700 mt-2">Manage &rarr;</p>
            </a>
            <a href="ministries.php" class="bg-white rounded-lg border border-slate-200 p-5 hover:shadow-md transition">
                <p class="text-sm text-slate-500">Ministries</p>
                <p class="text-2xl font-bold mt-1"><?php echo $counts['ministries']; ?></p>
                <p class="text-sm text-blue-700 mt-2">Manage &rarr;</p>
            </a>
            <a href="posts.php" class="bg-white rounded-lg border border-slate-200 p-5 hover:shadow-md transition">
                <p class="text-sm text-slate-500">Blog Posts</p>
                <p class="text-2xl font-bold mt-1"><?php echo $counts['posts']; ?></p>
                <p class="text-sm text-blue-700 mt-2">Manage &rarr;</p>
            </a>
            <a href="messages.php" class="bg-white rounded-lg border border-slate-200 p-5 hover:shadow-md transition">
                <p class="text-sm text-slate-500">Inbox / Prayer Requests</p>
                <p class="text-2xl font-bold mt-1"><?php echo $counts['messages']; ?></p>
                <p class="text-sm text-blue-700 mt-2">View &rarr;</p>
            </a>
            <a href="settings.php" class="bg-white rounded-lg border border-slate-200 p-5 hover:shadow-md transition">
                <p class="text-sm text-slate-500">Site Settings</p>
                <p class="text-2xl font-bold mt-1">Edit</p>
                <p class="text-sm text-blue-700 mt-2">Address, phone, socials, giving &rarr;</p>
            </a>
            <a href="../index.php" class="bg-white rounded-lg border border-slate-200 p-5 hover:shadow-md transition">
                <p class="text-sm text-slate-500">Public site</p>
                <p class="text-2xl font-bold mt-1">View</p>
                <p class="text-sm text-blue-700 mt-2">Open website &rarr;</p>
            </a>
        </div>
    </main>
</body>
</html>
