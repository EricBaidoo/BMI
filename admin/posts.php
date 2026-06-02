<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/uploads.php';

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

                // Handle image upload
                $postImage = handle_image_upload_or_link('post_image_file', 'post_image_url');

                $stmt = $pdo->prepare(
                    'INSERT INTO posts (title, slug, content, category, published_at, post_image)
                     VALUES (:t, :s, :c, :cat, :p, :img)'
                );
                $stmt->execute([
                    ':t' => $title,
                    ':s' => $slug,
                    ':c' => $content,
                    ':cat' => $category,
                    ':p' => $publish ? date('Y-m-d H:i:s') : null,
                    ':img' => $postImage,
                ]);
                header('Location: posts.php?status=added');
                exit;
            }

            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid post ID.');
            }
            // Keep existing slug to preserve URLs
            $existing = $pdo->prepare('SELECT slug, published_at, post_image FROM posts WHERE id = :id');
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

            // Handle image upload
            $postImage = handle_image_upload_or_link('post_image_file', 'post_image_url', $row['post_image']);

            $stmt = $pdo->prepare(
                'UPDATE posts SET title = :t, content = :c, category = :cat, published_at = :p, post_image = :img WHERE id = :id'
            );
            $stmt->execute([
                ':id' => $id,
                ':t' => $title,
                ':c' => $content,
                ':cat' => $category,
                ':p' => $publishedAt,
                ':img' => $postImage,
            ]);
            header('Location: posts.php?status=updated');
            exit;
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Invalid ID.');
            }
            $existing = $pdo->prepare('SELECT post_image FROM posts WHERE id = :id');
            $existing->execute([':id' => $id]);
            $row = $existing->fetch();
            
            $pdo->prepare('DELETE FROM posts WHERE id = :id')->execute([':id' => $id]);
            
            if ($row && $row['post_image']) {
                upload_delete($row['post_image']);
            }
            
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
<?php
$pageTitle = 'Manage Posts | BMI Admin';
require_once __DIR__ . '/includes/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Manage Posts</h1>
            <p class="mt-1 text-slate-500">Blog, announcements, and devotionals shown on the public Good News page.</p>
        </div>

        <?php if ($feedback !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo e($feedback); ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo e($error); ?></div>
        <?php endif; ?>

        <div class="mt-6 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4"><?php echo $editing ? 'Edit Post' : 'Add New Post'; ?></h2>
            <form method="post" enctype="multipart/form-data" class="mt-6 grid md:grid-cols-2 gap-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="<?php echo $editing ? 'edit' : 'add'; ?>">
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo (int) $editing['id']; ?>">
                <?php endif; ?>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Title *</label>
                    <input type="text" name="title" required maxlength="200" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                        value="<?php echo $editing ? e($editing['title']) : ''; ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Category</label>
                    <select name="category" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                        <?php foreach (['blog' => 'Blog', 'announcement' => 'Announcement', 'devotional' => 'Devotional'] as $val => $label): ?>
                            <option value="<?php echo $val; ?>" <?php echo $editing && $editing['category'] === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="md:col-span-2 grid md:grid-cols-2 gap-6 p-4 border border-slate-200 rounded-lg bg-slate-50">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Image Upload (Local File)</label>
                        <input type="file" name="post_image_file" accept="image/*" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all text-sm">
                        <p class="mt-1 text-xs text-slate-500">Max size: 5MB. Formats: JPG, PNG, GIF.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">OR Image URL (Remote Link)</label>
                        <input type="url" name="post_image_url" placeholder="https://example.com/image.jpg" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all text-sm" value="<?php echo $editing && filter_var($editing['post_image'] ?? '', FILTER_VALIDATE_URL) ? e($editing['post_image']) : ''; ?>">
                    </div>
                    <?php if ($editing && !empty($editing['post_image'])): ?>
                        <div class="md:col-span-2 flex items-center gap-4 mt-2">
                            <span class="text-sm font-semibold text-slate-700">Current Image:</span>
                            <img src="<?php echo e($editing['post_image']); ?>" alt="Current image" class="h-16 w-16 object-cover rounded border border-slate-300">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex items-center pt-8">
                    <label class="flex items-center gap-3 text-sm cursor-pointer group">
                        <input type="checkbox" name="publish" value="1" <?php echo (!$editing || $editing['published_at']) ? 'checked' : ''; ?> class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition-all cursor-pointer">
                        <span class="font-medium text-slate-700 group-hover:text-slate-900 transition-colors">Publish (visible on website)</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Content *</label>
                    <textarea name="content" rows="10" required class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all font-mono text-sm"><?php echo $editing ? e($editing['content']) : ''; ?></textarea>
                    <p class="mt-1.5 text-xs text-slate-500">Plain text or basic HTML. Line breaks are preserved.</p>
                </div>

                <div class="md:col-span-2 pt-4 border-t border-slate-100 flex gap-3">
                    <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-6 py-2.5 text-sm font-semibold transition-all shadow-md shadow-blue-500/30"><?php echo $editing ? 'Update' : 'Add'; ?></button>
                    <?php if ($editing): ?>
                        <a href="posts.php" class="rounded-lg border border-slate-300 text-slate-700 px-6 py-2.5 text-sm font-semibold hover:bg-slate-50 transition-all text-center">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4">All Posts</h2>
            <?php if (empty($posts)): ?>
                <p class="mt-4 text-sm text-slate-600">No posts yet.</p>
            <?php else: ?>
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500 border-b border-slate-200 bg-slate-50/50">
                                <th class="py-3 px-4 font-semibold rounded-tl-lg">Title</th>
                                <th class="py-3 px-4 font-semibold">Category</th>
                                <th class="py-3 px-4 font-semibold">Status</th>
                                <th class="py-3 px-4 font-semibold text-right rounded-tr-lg">Actions</th>
                            </tr>
                        </thead>
                    <tbody>
                        <?php foreach ($posts as $p): ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors last:border-0">
                                <td class="py-3 px-4 font-medium text-slate-900"><?php echo e($p['title']); ?></td>
                                <td class="py-3 px-4 capitalize text-slate-600"><?php echo e($p['category']); ?></td>
                                <td class="py-3 px-4">
                                    <?php if ($p['published_at']): ?>
                                        <span class="inline-block rounded-full bg-emerald-100 text-emerald-800 px-3 py-1 text-xs font-semibold">Published</span>
                                    <?php else: ?>
                                        <span class="inline-block rounded-full bg-slate-200 text-slate-700 px-3 py-1 text-xs font-semibold">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="posts.php?edit=<?php echo (int) $p['id']; ?>" class="text-blue-600 hover:text-blue-800 font-medium mr-4">Edit</a>
                                    <form method="post" class="inline" onsubmit="return confirm('Delete this post?');">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
