<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/helpers.php';

$feedback = '';
$error = '';
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_check();
        $pdo = db_connect();
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'add' || $action === 'edit') {
            $title = trim((string) ($_POST['title'] ?? ''));
            $content = trim((string) ($_POST['content'] ?? ''));
            $category = (string) ($_POST['category'] ?? 'blog');
            $publish = isset($_POST['publish']) && $_POST['publish'] === '1';

            if (!in_array($category, ['blog', 'announcement', 'devotional'], true)) {
                throw new RuntimeException('Invalid category.');
            }
            if ($title === '' || $content === '') {
                throw new RuntimeException('Title and content are required.');
            }

            $slug = slugify($title);

            if ($action === 'add') {
                // Ensure slug uniqueness
                $check = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE slug = :s');
                $base = $slug; $i = 2;
                while (true) {
                    $check->execute([':s' => $slug]);
                    if ((int) $check->fetchColumn() === 0) {
                        break;
                    }
                    $slug = $base . '-' . $i++;
                }

                $stmt = $pdo->prepare(
                    'INSERT INTO posts (title, slug, content, category, published_at)
                     VALUES (:t, :s, :c, :cat, :p)'
                );
                $stmt->execute([
                    ':t' => $title,
                    ':s' => $slug,
                    ':c' => $content,
                    ':cat' => $category,
                    ':p' => $publish ? date('Y-m-d H:i:s') : null,
                ]);
                header('Location: posts.php?status=added');
                exit;
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid post ID.');
            }
            // Keep existing slug to preserve URLs
            $existing = $pdo->prepare('SELECT slug, published_at FROM posts WHERE id = :id');
            $existing->execute([':id' => $id]);
            $row = $existing->fetch();
            if (!$row) {
                throw new RuntimeException('Post not found.');
            }
            $publishedAt = $row['published_at'];
            if ($publish && !$publishedAt) {
                $publishedAt = date('Y-m-d H:i:s');
            } elseif (!$publish) {
                $publishedAt = null;
            }

            $stmt = $pdo->prepare(
                'UPDATE posts SET title = :t, content = :c, category = :cat, published_at = :p WHERE id = :id'
            );
            $stmt->execute([
                ':id' => $id,
                ':t' => $title,
                ':c' => $content,
                ':cat' => $category,
                ':p' => $publishedAt,
            ]);
            header('Location: posts.php?status=updated');
            exit;
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid ID.');
            }
            $pdo->prepare('DELETE FROM posts WHERE id = :id')->execute([':id' => $id]);
            header('Location: posts.php?status=deleted');
            exit;
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    if ($id > 0) {
        try {
            $pdo = db_connect();
            $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $editing = $stmt->fetch();
        } catch (Throwable $e) {
            $error = 'Unable to load post.';
        }
    }
}

$flashMap = ['added' => 'Post added.', 'updated' => 'Post updated.', 'deleted' => 'Post deleted.'];
$feedback = $flashMap[$_GET['status'] ?? ''] ?? '';

$posts = [];
try {
    $pdo = db_connect();
    $posts = $pdo->query('SELECT * FROM posts ORDER BY COALESCE(published_at, created_at) DESC')->fetchAll();
} catch (Throwable $e) {
    if ($error === '') {
        $error = 'Unable to load posts.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Manage Posts | BMI Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-3xl font-bold">Manage Posts</h1>
                <p class="mt-2 text-slate-600">Blog, announcements, and devotionals shown on the public Good News page.</p>
            </div>
            <div class="flex gap-2">
                <a href="index.php" class="rounded bg-slate-200 px-4 py-2 text-sm">Dashboard</a>
                <a href="logout.php" class="rounded bg-slate-800 text-white px-4 py-2 text-sm">Sign out</a>
            </div>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo e($feedback); ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo e($error); ?></div>
        <?php endif; ?>

        <div class="mt-6 bg-white border border-slate-200 rounded p-5">
            <h2 class="text-xl font-semibold"><?php echo $editing ? 'Edit Post' : 'Add New Post'; ?></h2>
            <form method="post" class="mt-4 grid md:grid-cols-2 gap-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="<?php echo $editing ? 'edit' : 'add'; ?>">
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo (int) $editing['id']; ?>">
                <?php endif; ?>

                <label class="block text-sm md:col-span-2">
                    <span class="font-medium">Title *</span>
                    <input type="text" name="title" required maxlength="200" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"
                        value="<?php echo $editing ? e($editing['title']) : ''; ?>">
                </label>

                <label class="block text-sm">
                    <span class="font-medium">Category</span>
                    <select name="category" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                        <?php foreach (['blog' => 'Blog', 'announcement' => 'Announcement', 'devotional' => 'Devotional'] as $val => $label): ?>
                            <option value="<?php echo $val; ?>" <?php echo $editing && $editing['category'] === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="flex items-center gap-2 text-sm pt-6">
                    <input type="checkbox" name="publish" value="1" <?php echo (!$editing || $editing['published_at']) ? 'checked' : ''; ?> class="rounded">
                    <span>Publish (visible on website)</span>
                </label>

                <label class="block text-sm md:col-span-2">
                    <span class="font-medium">Content *</span>
                    <textarea name="content" rows="10" required class="mt-1 w-full border border-slate-300 rounded px-3 py-2"><?php echo $editing ? e($editing['content']) : ''; ?></textarea>
                    <p class="mt-1 text-xs text-slate-500">Plain text or basic HTML. Line breaks are preserved.</p>
                </label>

                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="rounded bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 text-sm font-semibold"><?php echo $editing ? 'Update' : 'Add'; ?></button>
                    <?php if ($editing): ?>
                        <a href="posts.php" class="rounded bg-slate-400 hover:bg-slate-500 text-white px-4 py-2 text-sm font-semibold">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded p-5">
            <h2 class="text-xl font-semibold">All Posts</h2>
            <?php if (empty($posts)): ?>
                <p class="mt-3 text-sm text-slate-600">No posts yet.</p>
            <?php else: ?>
                <table class="mt-4 w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 border-b">
                            <th class="py-2">Title</th>
                            <th class="py-2">Category</th>
                            <th class="py-2">Status</th>
                            <th class="py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $p): ?>
                            <tr class="border-b last:border-0">
                                <td class="py-3 font-medium"><?php echo e($p['title']); ?></td>
                                <td class="py-3 capitalize"><?php echo e($p['category']); ?></td>
                                <td class="py-3">
                                    <?php if ($p['published_at']): ?>
                                        <span class="inline-block rounded bg-emerald-100 text-emerald-800 px-2 py-0.5 text-xs">Published</span>
                                    <?php else: ?>
                                        <span class="inline-block rounded bg-slate-200 text-slate-700 px-2 py-0.5 text-xs">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-right">
                                    <a href="posts.php?edit=<?php echo (int) $p['id']; ?>" class="text-blue-700 hover:underline mr-3">Edit</a>
                                    <form method="post" class="inline" onsubmit="return confirm('Delete this post?');">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
                                        <button type="submit" class="text-red-700 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
