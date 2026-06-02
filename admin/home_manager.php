<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/uploads.php';

$feedback = '';
$error = '';

$activeTab = $_GET['tab'] ?? 'slides';
$validTabs = ['slides', 'founder', 'mission', 'testimonies', 'services'];
if (!in_array($activeTab, $validTabs, true)) {
    $activeTab = 'slides';
}

$pdo = db_connect();

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_check();
        $action = (string) ($_POST['action'] ?? 'save_settings');
        
        // --- Handle Settings (Founder, Mission & Marquee) ---
        if ($action === 'save_settings') {
            $posted = $_POST['setting'] ?? [];
            if (!is_array($posted)) {
                throw new RuntimeException('Invalid form payload.');
            }
            $update = [];
            
            // Allow setting images for founder
            if ($activeTab === 'founder') {
                $k = 'home.founder_image';
                $postKey = 'home_founder_image';
                $actualKey = isset($_FILES['setting_file']['name'][$k]) ? $k : (isset($_FILES['setting_file']['name'][$postKey]) ? $postKey : null);
                
                $file = null;
                if ($actualKey && !empty($_FILES['setting_file']['name'][$actualKey])) {
                    $file = [
                        'name' => $_FILES['setting_file']['name'][$actualKey],
                        'type' => $_FILES['setting_file']['type'][$actualKey],
                        'tmp_name' => $_FILES['setting_file']['tmp_name'][$actualKey],
                        'error' => $_FILES['setting_file']['error'][$actualKey],
                        'size' => $_FILES['setting_file']['size'][$actualKey],
                    ];
                }
                $imgUrl = $_POST['setting_url'][$k] ?? ($_POST['setting_url'][$postKey] ?? '');
                $existingImage = setting($k);
                $update[$k] = handle_image_upload_or_link($file, $imgUrl, $existingImage);
            }
            
            // Loop through posted texts
            foreach ($posted as $k => $v) {
                // Ensure we only update home.* settings
                if (strpos($k, 'home.') === 0) {
                    $update[$k] = trim((string) $v);
                }
            }
            
            if (!empty($update)) {
                settings_save($update);
            }
            header('Location: home_manager.php?tab=' . urlencode($activeTab) . '&status=saved');
            exit;
        }

        // --- Handle Hero Slides CRUD ---
        if ($action === 'save_slide' && $activeTab === 'slides') {
            $id = (int) ($_POST['id'] ?? 0);
            $title = trim((string) ($_POST['title'] ?? ''));
            $subtitle = trim((string) ($_POST['subtitle'] ?? ''));
            $buttonText = trim((string) ($_POST['button_text'] ?? ''));
            $buttonUrl = trim((string) ($_POST['button_url'] ?? ''));
            $sortOrder = (int) ($_POST['sort_order'] ?? 0);
            
            $bgImage = handle_image_upload_or_link($_FILES['bg_image'] ?? null, $_POST['bg_image_url'] ?? '', $_POST['existing_bg_image'] ?? '');
            
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE hero_slides SET title = :title, subtitle = :subtitle, button_text = :button_text, button_url = :button_url, sort_order = :sort_order, bg_image = :bg_image WHERE id = :id');
                $stmt->execute([
                    ':id' => $id, ':title' => $title, ':subtitle' => $subtitle, 
                    ':button_text' => $buttonText, ':button_url' => $buttonUrl, 
                    ':sort_order' => $sortOrder, ':bg_image' => $bgImage
                ]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO hero_slides (title, subtitle, button_text, button_url, sort_order, bg_image) VALUES (:title, :subtitle, :button_text, :button_url, :sort_order, :bg_image)');
                $stmt->execute([
                    ':title' => $title, ':subtitle' => $subtitle, 
                    ':button_text' => $buttonText, ':button_url' => $buttonUrl, 
                    ':sort_order' => $sortOrder, ':bg_image' => $bgImage
                ]);
            }
            header('Location: home_manager.php?tab=slides&status=saved');
            exit;
        }
        
        if ($action === 'delete_slide' && $activeTab === 'slides') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $pdo->prepare('DELETE FROM hero_slides WHERE id = :id')->execute([':id' => $id]);
            }
            header('Location: home_manager.php?tab=slides&status=deleted');
            exit;
        }
        
        // --- Handle Testimonies CRUD ---
        if ($action === 'save_testimony' && $activeTab === 'testimonies') {
            $id = (int) ($_POST['id'] ?? 0);
            $authorName = trim((string) ($_POST['author_name'] ?? ''));
            $authorRole = trim((string) ($_POST['author_role'] ?? ''));
            $quote = trim((string) ($_POST['quote'] ?? ''));
            $sortOrder = (int) ($_POST['sort_order'] ?? 0);
            
            if ($authorName === '' || $quote === '') {
                throw new RuntimeException('Author Name and Quote are required.');
            }
            
            $imageUrl = handle_image_upload_or_link($_FILES['image_url'] ?? null, $_POST['image_url_url'] ?? '', $_POST['existing_image_url'] ?? '');
            
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE testimonies SET author_name = :aname, author_role = :arole, quote = :quote, sort_order = :sort_order, image_url = :image_url WHERE id = :id');
                $stmt->execute([
                    ':id' => $id, ':aname' => $authorName, ':arole' => $authorRole, 
                    ':quote' => $quote, ':sort_order' => $sortOrder, ':image_url' => $imageUrl
                ]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO testimonies (author_name, author_role, quote, sort_order, image_url) VALUES (:aname, :arole, :quote, :sort_order, :image_url)');
                $stmt->execute([
                    ':aname' => $authorName, ':arole' => $authorRole, 
                    ':quote' => $quote, ':sort_order' => $sortOrder, ':image_url' => $imageUrl
                ]);
            }
            header('Location: home_manager.php?tab=testimonies&status=saved');
            exit;
        }
        
        if ($action === 'delete_testimony' && $activeTab === 'testimonies') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $pdo->prepare('DELETE FROM testimonies WHERE id = :id')->execute([':id' => $id]);
            }
            header('Location: home_manager.php?tab=testimonies&status=deleted');
            exit;
        }

        // --- Handle Weekly Services CRUD ---
        if ($action === 'save_service' && $activeTab === 'services') {
            $id = (int) ($_POST['id'] ?? 0);
            $title = trim((string) ($_POST['title'] ?? ''));
            $subtitle = trim((string) ($_POST['subtitle'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $timeInfo = trim((string) ($_POST['time_info'] ?? ''));
            $themeColor = trim((string) ($_POST['theme_color'] ?? 'cyan'));
            $sortOrder = (int) ($_POST['sort_order'] ?? 0);

            if ($title === '') {
                throw new RuntimeException('Service title is required.');
            }

            $imageUrl = handle_image_upload_or_link($_FILES['image_url'] ?? null, $_POST['image_url_url'] ?? '', $_POST['existing_image_url'] ?? '');

            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE weekly_services SET title = :title, subtitle = :subtitle, description = :description, time_info = :time_info, image_url = :image_url, theme_color = :theme_color, sort_order = :sort_order WHERE id = :id');
                $stmt->execute([
                    ':id' => $id, ':title' => $title, ':subtitle' => $subtitle,
                    ':description' => $description, ':time_info' => $timeInfo,
                    ':image_url' => $imageUrl, ':theme_color' => $themeColor, ':sort_order' => $sortOrder
                ]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO weekly_services (title, subtitle, description, time_info, image_url, theme_color, sort_order) VALUES (:title, :subtitle, :description, :time_info, :image_url, :theme_color, :sort_order)');
                $stmt->execute([
                    ':title' => $title, ':subtitle' => $subtitle,
                    ':description' => $description, ':time_info' => $timeInfo,
                    ':image_url' => $imageUrl, ':theme_color' => $themeColor, ':sort_order' => $sortOrder
                ]);
            }
            header('Location: home_manager.php?tab=services&status=saved');
            exit;
        }

        if ($action === 'delete_service' && $activeTab === 'services') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $pdo->prepare('DELETE FROM weekly_services WHERE id = :id')->execute([':id' => $id]);
            }
            header('Location: home_manager.php?tab=services&status=deleted');
            exit;
        }
        
    } catch (Throwable $ex) {
        $error = $ex->getMessage();
    }
}

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'saved') $feedback = 'Changes saved successfully.';
    if ($_GET['status'] === 'deleted') $feedback = 'Item deleted successfully.';
}

$pageTitle = 'Homepage Manager | BMI Admin';
require_once __DIR__ . '/includes/header.php';
?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Homepage Manager</h1>
    <p class="mt-1 text-slate-500">Manage all dynamic content sections of your public homepage.</p>
</div>

<?php if ($feedback !== ''): ?>
    <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo e($feedback); ?></div>
<?php endif; ?>
<?php if ($error !== ''): ?>
    <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo e($error); ?></div>
<?php endif; ?>

<div class="mt-6 grid md:grid-cols-[220px_1fr] gap-6 items-start">
    <nav class="bg-white border border-slate-200 rounded-xl p-2 sticky top-4 shadow-sm">
        <ul class="text-sm space-y-1">
            <li>
                <a href="?tab=slides" class="block rounded-lg px-3 py-2.5 transition-colors <?php echo $activeTab === 'slides' ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-100/50 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    Hero Slides
                </a>
            </li>
            <li>
                <a href="?tab=founder" class="block rounded-lg px-3 py-2.5 transition-colors <?php echo $activeTab === 'founder' ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-100/50 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    Founder Section
                </a>
            </li>
            <li>
                <a href="?tab=mission" class="block rounded-lg px-3 py-2.5 transition-colors <?php echo $activeTab === 'mission' ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-100/50 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    Mission & Marquee
                </a>
            </li>
            <li>
                <a href="?tab=testimonies" class="block rounded-lg px-3 py-2.5 transition-colors <?php echo $activeTab === 'testimonies' ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-100/50 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    Testimonies
                </a>
            </li>
            <li>
                <a href="?tab=services" class="block rounded-lg px-3 py-2.5 transition-colors <?php echo $activeTab === 'services' ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-100/50 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    Weekly Services
                </a>
            </li>
        </ul>
    </nav>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 md:p-8">
        <?php if ($activeTab === 'slides'): 
            $slides = $pdo->query('SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC')->fetchAll();
            $editId = (int) ($_GET['edit_slide'] ?? 0);
            $editing = null;
            if ($editId > 0) {
                $stmt = $pdo->prepare('SELECT * FROM hero_slides WHERE id = :id');
                $stmt->execute([':id' => $editId]);
                $editing = $stmt->fetch();
            }
        ?>
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4 mb-6">
                <?php echo $editing ? 'Edit Slide' : 'Hero Slides'; ?>
            </h2>
            
            <form method="post" enctype="multipart/form-data" class="space-y-6 mb-12 bg-slate-50/50 p-6 rounded-xl border border-slate-100">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="save_slide">
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo $editing['id']; ?>">
                    <input type="hidden" name="existing_bg_image" value="<?php echo e($editing['bg_image']); ?>">
                <?php endif; ?>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Title (HTML allowed)</label>
                        <input type="text" name="title" value="<?php echo e($editing['title'] ?? ''); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all" placeholder="E.g. Impact the <span class='text-[#c49a45]'>World.</span>">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Subtitle</label>
                        <input type="text" name="subtitle" value="<?php echo e($editing['subtitle'] ?? ''); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Button Text</label>
                        <input type="text" name="button_text" value="<?php echo e($editing['button_text'] ?? ''); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Button URL</label>
                        <input type="text" name="button_url" value="<?php echo e($editing['button_url'] ?? ''); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order" value="<?php echo e($editing['sort_order'] ?? '0'); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Background Image</label>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs font-medium text-slate-500 mb-1.5">Upload File</span>
                                <input type="file" name="bg_image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-slate-500 mb-1.5">OR Paste URL</span>
                                <input type="text" name="bg_image_url" placeholder="https://..." class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <?php if ($editing): ?>
                        <a href="home_manager.php?tab=slides" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold transition-colors">Cancel</a>
                    <?php endif; ?>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold transition-colors shadow-sm">
                        <?php echo $editing ? 'Update Slide' : 'Add Slide'; ?>
                    </button>
                </div>
            </form>

            <div class="space-y-4">
                <?php foreach ($slides as $slide): ?>
                    <div class="flex items-center gap-4 bg-white border border-slate-200 rounded-lg p-4 shadow-sm hover:shadow transition-shadow">
                        <?php if ($slide['bg_image']): ?>
                            <img src="<?php echo strpos($slide['bg_image'], 'http') === 0 ? htmlspecialchars($slide['bg_image']) : '/BMI/' . htmlspecialchars($slide['bg_image']); ?>" class="w-24 h-16 object-cover rounded" alt="">
                        <?php else: ?>
                            <div class="w-24 h-16 bg-slate-100 rounded border border-slate-200 flex items-center justify-center text-xs text-slate-400">No Image</div>
                        <?php endif; ?>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-800 text-lg truncate"><?php echo e(strip_tags($slide['title'])); ?></h3>
                            <p class="text-sm text-slate-500 truncate"><?php echo e($slide['subtitle']); ?></p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <a href="?tab=slides&edit_slide=<?php echo $slide['id']; ?>" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form method="post" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this slide?');">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="delete_slide">
                                <input type="hidden" name="id" value="<?php echo $slide['id']; ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php elseif ($activeTab === 'founder'): ?>
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4 mb-6">Founder Section</h2>
            <form method="post" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="save_settings">
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Founder Title (HTML Allowed)</label>
                        <textarea name="setting[home.founder_title]" rows="3" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"><?php echo e(setting('home.founder_title')); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Founder Biography Paragraph 1</label>
                        <textarea name="setting[home.founder_bio1]" rows="3" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"><?php echo e(setting('home.founder_bio1')); ?></textarea>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Founder Biography Quote (Highlight Box)</label>
                        <textarea name="setting[home.founder_bio2]" rows="2" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"><?php echo e(setting('home.founder_bio2')); ?></textarea>
                    </div>
                    
                    <div class="col-span-2 pt-4 border-t border-slate-100">
                        <label class="block text-sm font-semibold text-slate-700 mb-4">Founder Portrait Image</label>
                        <div class="flex flex-col gap-4">
                            <?php $val = setting('home.founder_image'); if ($val): ?>
                                <div class="w-full max-w-sm bg-slate-100 rounded border border-slate-200 overflow-hidden">
                                    <img src="<?php echo strpos($val, 'http') === 0 ? htmlspecialchars($val) : '/BMI/' . htmlspecialchars($val); ?>" class="w-full h-auto max-h-[15rem] object-cover" alt="">
                                </div>
                            <?php endif; ?>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <span class="block text-xs font-medium text-slate-500 mb-1.5">Upload New Image</span>
                                    <input type="file" name="setting_file[home_founder_image]" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                                <div>
                                    <span class="block text-xs font-medium text-slate-500 mb-1.5">OR Paste Image URL</span>
                                    <input type="text" name="setting_url[home.founder_image]" placeholder="https://..." class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-slate-200 text-right">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold shadow-md hover:shadow-lg transition-all">Save Changes</button>
                </div>
            </form>

        <?php elseif ($activeTab === 'mission'): ?>
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4 mb-6">Mission & Marquee Section</h2>
            <form method="post" class="space-y-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="save_settings">
                
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Scrolling Marquee Text</h3>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php for ($i=1; $i<=5; $i++): ?>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Marquee Item <?php echo $i; ?></label>
                                <input type="text" name="setting[home.marquee_text<?php echo $i; ?>]" value="<?php echo e(setting('home.marquee_text'.$i)); ?>" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-slate-50 outline-none">
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Our Mission Text</h3>
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Mission Title</label>
                            <input type="text" name="setting[home.mission_title]" value="<?php echo e(setting('home.mission_title')); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Mission Main Text</label>
                            <textarea name="setting[home.mission_text]" rows="2" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 outline-none"><?php echo e(setting('home.mission_text')); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-3 gap-4">
                        <?php for ($i=1; $i<=3; $i++): ?>
                            <div class="p-4 border border-slate-200 rounded bg-slate-50">
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Box <?php echo $i; ?> Title</label>
                                <input type="text" name="setting[home.mission_box<?php echo $i; ?>_title]" value="<?php echo e(setting('home.mission_box'.$i.'_title')); ?>" class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm outline-none mb-3">
                                
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Box <?php echo $i; ?> Description</label>
                                <textarea name="setting[home.mission_box<?php echo $i; ?>_desc]" rows="2" class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm outline-none"><?php echo e(setting('home.mission_box'.$i.'_desc')); ?></textarea>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-slate-200 text-right">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold shadow-md hover:shadow-lg transition-all">Save Changes</button>
                </div>
            </form>

        <?php elseif ($activeTab === 'testimonies'): 
            $testimonies = $pdo->query('SELECT * FROM testimonies ORDER BY sort_order ASC, id ASC')->fetchAll();
            $editId = (int) ($_GET['edit_testimony'] ?? 0);
            $editing = null;
            if ($editId > 0) {
                $stmt = $pdo->prepare('SELECT * FROM testimonies WHERE id = :id');
                $stmt->execute([':id' => $editId]);
                $editing = $stmt->fetch();
            }
        ?>
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4 mb-6">
                <?php echo $editing ? 'Edit Testimony' : 'Testimonies'; ?>
            </h2>
            
            <form method="post" enctype="multipart/form-data" class="space-y-6 mb-12 bg-slate-50/50 p-6 rounded-xl border border-slate-100">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="save_testimony">
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo $editing['id']; ?>">
                    <input type="hidden" name="existing_image_url" value="<?php echo e($editing['image_url']); ?>">
                <?php endif; ?>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Author Name *</label>
                        <input type="text" name="author_name" required value="<?php echo e($editing['author_name'] ?? ''); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Author Role / Position</label>
                        <input type="text" name="author_role" value="<?php echo e($editing['author_role'] ?? ''); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 outline-none">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Testimony Quote *</label>
                        <textarea name="quote" required rows="3" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 outline-none"><?php echo e($editing['quote'] ?? ''); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order" value="<?php echo e($editing['sort_order'] ?? '0'); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 outline-none">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Author Avatar Image</label>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs font-medium text-slate-500 mb-1.5">Upload File</span>
                                <input type="file" name="image_url" accept="image/*" class="w-full text-sm text-slate-500">
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-slate-500 mb-1.5">OR Paste URL</span>
                                <input type="text" name="image_url_url" placeholder="https://..." class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 outline-none">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <?php if ($editing): ?>
                        <a href="home_manager.php?tab=testimonies" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold">Cancel</a>
                    <?php endif; ?>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold">
                        <?php echo $editing ? 'Update Testimony' : 'Add Testimony'; ?>
                    </button>
                </div>
            </form>

            <div class="grid md:grid-cols-2 gap-4">
                <?php foreach ($testimonies as $t): ?>
                    <div class="flex gap-4 bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
                        <img src="<?php echo strpos($t['image_url'], 'http') === 0 ? htmlspecialchars($t['image_url']) : '/BMI/' . htmlspecialchars($t['image_url']); ?>" class="w-12 h-12 rounded-full object-cover shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-800 text-sm"><?php echo e($t['author_name']); ?></h3>
                            <p class="text-xs text-slate-400 mb-2"><?php echo e($t['author_role']); ?></p>
                            <p class="text-sm text-slate-600 italic truncate">"<?php echo e($t['quote']); ?>"</p>
                        </div>
                        <div class="flex flex-col gap-2 shrink-0">
                            <a href="?tab=testimonies&edit_testimony=<?php echo $t['id']; ?>" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form method="post" onsubmit="return confirm('Delete testimony?');">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="delete_testimony">
                                <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($activeTab === 'services'):
            $services = $pdo->query('SELECT * FROM weekly_services ORDER BY sort_order ASC, id ASC')->fetchAll();
            $editId = (int) ($_GET['edit_service'] ?? 0);
            $editing = null;
            if ($editId > 0) {
                $stmt = $pdo->prepare('SELECT * FROM weekly_services WHERE id = :id');
                $stmt->execute([':id' => $editId]);
                $editing = $stmt->fetch();
            }
        ?>
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4 mb-6">
                <?php echo $editing ? 'Edit Service' : 'Weekly Services'; ?>
            </h2>

            <form method="post" enctype="multipart/form-data" class="space-y-6 mb-12 bg-slate-50/50 p-6 rounded-xl border border-slate-100">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="save_service">
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo $editing['id']; ?>">
                    <input type="hidden" name="existing_image_url" value="<?php echo e($editing['image_url'] ?? ''); ?>">
                <?php endif; ?>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Service Title *</label>
                        <input type="text" name="title" required value="<?php echo e($editing['title'] ?? ''); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all" placeholder="E.g. RESTORERS">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Subtitle</label>
                        <input type="text" name="subtitle" value="<?php echo e($editing['subtitle'] ?? ''); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all" placeholder="E.g. Celebration Service">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Description</label>
                        <textarea name="description" rows="3" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"><?php echo e($editing['description'] ?? ''); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Time Info</label>
                        <input type="text" name="time_info" value="<?php echo e($editing['time_info'] ?? ''); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all" placeholder="E.g. Sunday 8:45 AM">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Theme Color</label>
                        <select name="theme_color" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                            <?php
                            $colors = ['cyan' => 'Cyan', 'purple' => 'Purple', 'orange' => 'Orange', 'teal' => 'Teal', 'blue' => 'Blue', 'red' => 'Red', 'green' => 'Green', 'yellow' => 'Yellow', 'pink' => 'Pink'];
                            foreach ($colors as $val => $label):
                            ?>
                                <option value="<?php echo $val; ?>" <?php echo ($editing['theme_color'] ?? 'cyan') === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order" value="<?php echo e($editing['sort_order'] ?? '0'); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Service Flyer / Image</label>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs font-medium text-slate-500 mb-1.5">Upload File</span>
                                <input type="file" name="image_url" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-slate-500 mb-1.5">OR Paste URL</span>
                                <input type="text" name="image_url_url" placeholder="https://..." class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <?php if ($editing): ?>
                        <a href="home_manager.php?tab=services" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold transition-colors">Cancel</a>
                    <?php endif; ?>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold transition-colors shadow-sm">
                        <?php echo $editing ? 'Update Service' : 'Add Service'; ?>
                    </button>
                </div>
            </form>

            <div class="space-y-4">
                <?php foreach ($services as $svc): ?>
                    <div class="flex items-center gap-4 bg-white border border-slate-200 rounded-lg p-4 shadow-sm hover:shadow transition-shadow">
                        <?php if (!empty($svc['image_url'])): ?>
                            <img src="<?php echo strpos($svc['image_url'], 'http') === 0 ? htmlspecialchars($svc['image_url']) : '/BMI/' . htmlspecialchars($svc['image_url']); ?>" class="w-20 h-20 object-cover rounded-lg" alt="">
                        <?php else: ?>
                            <div class="w-20 h-20 bg-slate-100 rounded-lg border border-slate-200 flex items-center justify-center text-xs text-slate-400">No Image</div>
                        <?php endif; ?>

                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-800 text-lg"><?php echo e($svc['title']); ?></h3>
                            <p class="text-sm text-slate-500"><?php echo e($svc['subtitle']); ?> · <?php echo e($svc['time_info']); ?></p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-600"><?php echo e($svc['theme_color']); ?></span>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <a href="?tab=services&edit_service=<?php echo $svc['id']; ?>" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form method="post" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="delete_service">
                                <input type="hidden" name="id" value="<?php echo $svc['id']; ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
