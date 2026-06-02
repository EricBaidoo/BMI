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

$pdo = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_check();
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'save_settings') {
            $posted = $_POST['setting'] ?? [];
            if (is_array($posted)) {
                $stmt = $pdo->prepare('UPDATE site_settings SET setting_value = :v WHERE setting_key = :k');
                foreach ($posted as $k => $v) {
                    if (strpos($k, 'home.') === 0) {
                        $stmt->execute([':v' => trim((string)$v), ':k' => $k]);
                    }
                }
                
                // Handle settings images
                $imageSettings = ['home.hero_bg_image', 'home.founder_image', 'home.mission_bg', 'home.watch_bg'];
                foreach ($imageSettings as $imgKey) {
                    $imgUrl = $_POST['setting_url'][$imgKey] ?? '';
                    $uploaded = handle_image_upload_or_link($_FILES['setting_file'][$imgKey] ?? null, $imgUrl, 'home');
                    if ($uploaded !== null) {
                        $stmt->execute([':v' => $uploaded, ':k' => $imgKey]);
                    }
                }
                $feedback = 'Homepage settings saved successfully.';
            }
        } 
        elseif ($action === 'add_slide') {
            $title = trim((string)($_POST['title'] ?? ''));
            $subtitle = trim((string)($_POST['subtitle'] ?? ''));
            $btnText = trim((string)($_POST['button_text'] ?? ''));
            $btnUrl = trim((string)($_POST['button_url'] ?? ''));
            $sort = (int)($_POST['sort_order'] ?? 0);
            $bg = handle_image_upload_or_link($_FILES['bg_image'] ?? null, $_POST['bg_image_url'] ?? '', 'slide');
            
            $pdo->prepare("INSERT INTO hero_slides (title, subtitle, bg_image, button_text, button_url, sort_order) VALUES (?, ?, ?, ?, ?, ?)")
                ->execute([$title, $subtitle, $bg, $btnText, $btnUrl, $sort]);
            $feedback = 'Slide added.';
        }
        elseif ($action === 'delete_slide') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM hero_slides WHERE id = ?")->execute([$id]);
            $feedback = 'Slide deleted.';
        }
        elseif ($action === 'add_testimony') {
            $name = trim((string)($_POST['author_name'] ?? ''));
            $role = trim((string)($_POST['author_role'] ?? ''));
            $quote = trim((string)($_POST['quote'] ?? ''));
            $sort = (int)($_POST['sort_order'] ?? 0);
            $img = handle_image_upload_or_link($_FILES['image'] ?? null, $_POST['image_url'] ?? '', 'testimony');
            
            $pdo->prepare("INSERT INTO testimonies (author_name, author_role, quote, image_url, sort_order) VALUES (?, ?, ?, ?, ?)")
                ->execute([$name, $role, $quote, $img, $sort]);
            $feedback = 'Testimony added.';
        }
        elseif ($action === 'delete_testimony') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM testimonies WHERE id = ?")->execute([$id]);
            $feedback = 'Testimony deleted.';
        }
        elseif ($action === 'add_service') {
            $title = trim((string)($_POST['title'] ?? ''));
            $subtitle = trim((string)($_POST['subtitle'] ?? ''));
            $desc = trim((string)($_POST['description'] ?? ''));
            $time = trim((string)($_POST['time_info'] ?? ''));
            $color = trim((string)($_POST['theme_color'] ?? 'cyan'));
            $sort = (int)($_POST['sort_order'] ?? 0);
            $img = handle_image_upload_or_link($_FILES['image'] ?? null, $_POST['image_url'] ?? '', 'service');
            
            $pdo->prepare("INSERT INTO weekly_services (title, subtitle, description, time_info, image_url, theme_color, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)")
                ->execute([$title, $subtitle, $desc, $time, $img, $color, $sort]);
            $feedback = 'Service added.';
        }
        elseif ($action === 'delete_service') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM weekly_services WHERE id = ?")->execute([$id]);
            $feedback = 'Service deleted.';
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

// Fetch all data
$slides = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC")->fetchAll();
$testimonies = $pdo->query("SELECT * FROM testimonies ORDER BY sort_order ASC, id ASC")->fetchAll();
$services = $pdo->query("SELECT * FROM weekly_services ORDER BY sort_order ASC, id ASC")->fetchAll();

$pageTitle = 'Homepage Manager | BMI Admin';
require_once __DIR__ . '/includes/header.php';

function render_text_setting($key, $label, $type = 'text') {
    $val = setting($key);
    $html = '<div class="mb-4"><label class="block text-sm font-semibold text-slate-700 mb-1.5">' . htmlspecialchars($label) . '</label>';
    if ($type === 'textarea') {
        $html .= '<textarea name="setting['.$key.']" rows="3" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white transition-all text-sm">'.htmlspecialchars($val).'</textarea>';
    } else {
        $html .= '<input type="'.$type.'" name="setting['.$key.']" value="'.htmlspecialchars($val).'" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white transition-all text-sm">';
    }
    $html .= '</div>';
    return $html;
}

function render_image_setting($key, $label) {
    $val = setting($key);
    $html = '<div class="mb-4 p-4 border border-slate-200 rounded-lg bg-slate-50/50">';
    $html .= '<label class="block text-sm font-semibold text-slate-700 mb-3">' . htmlspecialchars($label) . '</label>';
    $html .= '<div class="grid md:grid-cols-2 gap-4">';
    $html .= '<div><span class="block text-xs font-medium text-slate-500 mb-1.5">Upload File</span><input type="file" name="setting_file['.$key.']" accept="image/*" class="w-full text-sm"></div>';
    $html .= '<div><span class="block text-xs font-medium text-slate-500 mb-1.5">OR Paste URL</span><input type="url" name="setting_url['.$key.']" placeholder="https://" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-white text-sm"></div>';
    $html .= '</div>';
    if ($val) {
        $src = strpos($val, 'http') === 0 ? $val : '../' . $val;
        $html .= '<div class="mt-3"><img src="'.htmlspecialchars($src).'" class="h-20 w-auto rounded border border-slate-200"></div>';
    }
    $html .= '</div>';
    return $html;
}
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Homepage Manager</h1>
    <p class="mt-1 text-slate-500">Manage all dynamic sections and content of the public homepage.</p>
</div>

<?php if ($feedback !== ''): ?>
    <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($feedback); ?></div>
<?php endif; ?>
<?php if ($error !== ''): ?>
    <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="grid lg:grid-cols-3 gap-8 mt-6">

    <!-- Column 1 & 2: Main Settings -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Hero Slides -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Hero Slides</h2>
            
            <div class="space-y-4 mb-6">
                <?php foreach ($slides as $slide): ?>
                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-lg">
                        <div class="flex items-center gap-4">
                            <?php if ($slide['bg_image']): ?>
                                <img src="<?php echo strpos($slide['bg_image'], 'http') === 0 ? htmlspecialchars($slide['bg_image']) : '../' . htmlspecialchars($slide['bg_image']); ?>" class="w-16 h-12 object-cover rounded">
                            <?php endif; ?>
                            <div>
                                <p class="font-bold text-slate-800 text-sm"><?php echo htmlspecialchars(strip_tags($slide['title'])); ?></p>
                                <p class="text-xs text-slate-500">Order: <?php echo (int)$slide['sort_order']; ?></p>
                            </div>
                        </div>
                        <form method="post" onsubmit="return confirm('Delete slide?');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="delete_slide">
                            <input type="hidden" name="id" value="<?php echo (int)$slide['id']; ?>">
                            <button class="text-red-600 hover:text-red-800 text-sm font-semibold">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <form method="post" enctype="multipart/form-data" class="bg-slate-50 p-4 border border-slate-200 rounded-lg">
                <h3 class="font-bold text-sm text-slate-700 mb-4">Add New Slide</h3>
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="add_slide">
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Title (HTML allowed)</label>
                        <input type="text" name="title" required class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Subtitle</label>
                        <input type="text" name="subtitle" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Button Text</label>
                        <input type="text" name="button_text" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Button URL</label>
                        <input type="text" name="button_url" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Upload Background</label>
                        <input type="file" name="bg_image" class="text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">OR Image URL</label>
                        <input type="url" name="bg_image_url" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    </div>
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-blue-700">Add Slide</button>
            </form>
        </div>

        <!-- Weekly Services -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Weekly Services</h2>
            
            <div class="grid sm:grid-cols-2 gap-4 mb-6">
                <?php foreach ($services as $svc): ?>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg relative">
                        <form method="post" onsubmit="return confirm('Delete service?');" class="absolute top-2 right-2">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="delete_service">
                            <input type="hidden" name="id" value="<?php echo (int)$svc['id']; ?>">
                            <button class="text-red-600 hover:text-red-800 bg-white rounded-full p-1 shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </form>
                        <p class="font-bold text-slate-800 text-sm"><?php echo htmlspecialchars($svc['title']); ?></p>
                        <p class="text-xs text-slate-500 mb-2"><?php echo htmlspecialchars($svc['time_info']); ?></p>
                        <?php if ($svc['image_url']): ?>
                            <img src="<?php echo strpos($svc['image_url'], 'http') === 0 ? htmlspecialchars($svc['image_url']) : '../' . htmlspecialchars($svc['image_url']); ?>" class="w-full h-24 object-cover rounded">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <form method="post" enctype="multipart/form-data" class="bg-slate-50 p-4 border border-slate-200 rounded-lg">
                <h3 class="font-bold text-sm text-slate-700 mb-4">Add New Service</h3>
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="add_service">
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Title</label>
                        <input type="text" name="title" required class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Subtitle</label>
                        <input type="text" name="subtitle" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Description</label>
                        <textarea name="description" class="w-full border border-slate-300 rounded px-3 py-2 text-sm" rows="2"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Time Info</label>
                        <input type="text" name="time_info" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Theme Color</label>
                        <select name="theme_color" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                            <option value="cyan">Cyan</option>
                            <option value="purple">Purple</option>
                            <option value="orange">Orange</option>
                            <option value="teal">Teal</option>
                        </select>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Upload Flyer</label>
                        <input type="file" name="image" class="text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">OR Image URL</label>
                        <input type="url" name="image_url" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    </div>
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-blue-700">Add Service</button>
            </form>
        </div>
        
        <!-- Text Sections Settings -->
        <form method="post" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Homepage Text Sections</h2>
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="save_settings">
            
            <div class="space-y-6">
                <!-- Founder -->
                <div class="p-4 border border-slate-200 rounded-lg">
                    <h3 class="font-bold text-slate-800 mb-4">Meet The Founder</h3>
                    <?php 
                        echo render_text_setting('home.founder_title', 'Title (HTML allowed)');
                        echo render_text_setting('home.founder_bio1', 'Bio Paragraph 1', 'textarea');
                        echo render_text_setting('home.founder_bio2', 'Bio Paragraph 2', 'textarea');
                        echo render_image_setting('home.founder_image', 'Founder Image');
                    ?>
                </div>

                <!-- Mission -->
                <div class="p-4 border border-slate-200 rounded-lg">
                    <h3 class="font-bold text-slate-800 mb-4">Mission Section</h3>
                    <?php 
                        echo render_text_setting('home.mission_title', 'Title (HTML allowed)');
                        echo render_text_setting('home.mission_text', 'Description text', 'textarea');
                        echo render_text_setting('home.mission_box1_title', 'Box 1 Title');
                        echo render_text_setting('home.mission_box1_desc', 'Box 1 Description');
                        echo render_text_setting('home.mission_box2_title', 'Box 2 Title');
                        echo render_text_setting('home.mission_box2_desc', 'Box 2 Description');
                        echo render_image_setting('home.mission_bg', 'Background Image');
                    ?>
                </div>

                <!-- Watch Online -->
                <div class="p-4 border border-slate-200 rounded-lg">
                    <h3 class="font-bold text-slate-800 mb-4">Watch Online Section</h3>
                    <?php 
                        echo render_text_setting('home.watch_title', 'Title (HTML allowed)');
                        echo render_text_setting('home.watch_subtitle', 'Subtitle', 'textarea');
                        echo render_image_setting('home.watch_bg', 'Background Image');
                    ?>
                </div>

                <!-- Counters -->
                <div class="p-4 border border-slate-200 rounded-lg grid md:grid-cols-2 gap-4">
                    <h3 class="font-bold text-slate-800 md:col-span-2">Counters</h3>
                    <?php 
                        echo render_text_setting('home.counter1_number', 'Counter 1 Number');
                        echo render_text_setting('home.counter1_label', 'Counter 1 Label');
                        echo render_text_setting('home.counter2_number', 'Counter 2 Number');
                        echo render_text_setting('home.counter2_label', 'Counter 2 Label');
                    ?>
                </div>

                <!-- Marquee -->
                <div class="p-4 border border-slate-200 rounded-lg">
                    <h3 class="font-bold text-slate-800 mb-4">Scrolling Ticker (Marquee)</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <?php 
                            echo render_text_setting('home.marquee_text1', 'Text 1');
                            echo render_text_setting('home.marquee_text2', 'Text 2');
                            echo render_text_setting('home.marquee_text3', 'Text 3');
                            echo render_text_setting('home.marquee_text4', 'Text 4');
                            echo render_text_setting('home.marquee_text5', 'Text 5');
                        ?>
                    </div>
                </div>

                <button class="bg-emerald-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md hover:bg-emerald-700 w-full md:w-auto">Save Settings</button>
            </div>
        </form>

    </div>

    <!-- Column 3: Side Items -->
    <div class="space-y-8">
        
        <!-- Testimonies -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Testimonies</h2>
            
            <div class="space-y-4 mb-6">
                <?php foreach ($testimonies as $test): ?>
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg relative">
                        <form method="post" onsubmit="return confirm('Delete testimony?');" class="absolute top-2 right-2">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="delete_testimony">
                            <input type="hidden" name="id" value="<?php echo (int)$test['id']; ?>">
                            <button class="text-red-600 hover:text-red-800 bg-white rounded-full p-1 shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </form>
                        <div class="flex items-center gap-3 mb-2">
                            <?php if ($test['image_url']): ?>
                                <img src="<?php echo strpos($test['image_url'], 'http') === 0 ? htmlspecialchars($test['image_url']) : '../' . htmlspecialchars($test['image_url']); ?>" class="w-10 h-10 object-cover rounded-full">
                            <?php endif; ?>
                            <div>
                                <p class="font-bold text-slate-800 text-sm"><?php echo htmlspecialchars($test['author_name']); ?></p>
                                <p class="text-xs text-slate-500"><?php echo htmlspecialchars($test['author_role']); ?></p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-600 italic">"<?php echo htmlspecialchars(substr($test['quote'], 0, 80)); ?>..."</p>
                    </div>
                <?php endforeach; ?>
            </div>

            <form method="post" enctype="multipart/form-data" class="bg-slate-50 p-4 border border-slate-200 rounded-lg">
                <h3 class="font-bold text-sm text-slate-700 mb-4">Add Testimony</h3>
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="add_testimony">
                <div class="space-y-3 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Author Name</label>
                        <input type="text" name="author_name" required class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Role (e.g. Member)</label>
                        <input type="text" name="author_role" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Quote</label>
                        <textarea name="quote" required class="w-full border border-slate-300 rounded px-3 py-2 text-sm" rows="3"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Upload Photo</label>
                        <input type="file" name="image" class="text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">OR Photo URL</label>
                        <input type="url" name="image_url" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                    </div>
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-blue-700 w-full">Add Testimony</button>
            </form>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
