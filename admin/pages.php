<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/uploads.php';

$feedback = '';
$error = '';

/**
 * Definition of every editable page section.
 */
$schema = [

    'page_about' => [
        'label' => 'About Us',
        'fields' => [
            ['key' => 'about.hero_bg_image', 'label' => 'Hero Background Image', 'type' => 'image'],
            ['key' => 'about.history_text1', 'label' => 'History Text (Paragraph 1)', 'type' => 'textarea'],
            ['key' => 'about.history_text2', 'label' => 'History Text (Paragraph 2)', 'type' => 'textarea'],
            ['key' => 'about.vision_text', 'label' => 'Vision Text', 'type' => 'textarea'],
            ['key' => 'about.founder_name', 'label' => 'Founder Name', 'type' => 'text'],
            ['key' => 'about.founder_bio1', 'label' => 'Founder Bio (Paragraph 1)', 'type' => 'textarea'],
            ['key' => 'about.founder_bio2', 'label' => 'Founder Bio (Paragraph 2)', 'type' => 'textarea'],
            ['key' => 'about.founder_image', 'label' => 'Founder Image', 'type' => 'image'],
        ],
    ],
    'page_visit' => [
        'label' => 'Plan a Visit',
        'fields' => [
            ['key' => 'visit.hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'visit.hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'visit.hero_bg_image', 'label' => 'Hero Background Image', 'type' => 'image'],
            ['key' => 'visit.expect_text', 'label' => 'What to Expect Text', 'type' => 'textarea'],
            ['key' => 'visit.church_image', 'label' => 'Church Building Image', 'type' => 'image'],
        ],
    ],
    'page_beliefs' => [
        'label' => 'Beliefs',
        'fields' => [
            ['key' => 'beliefs.hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'beliefs.hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'beliefs.hero_bg_image', 'label' => 'Hero Background Image', 'type' => 'image'],
            ['key' => 'beliefs.intro_text', 'label' => 'Intro Text', 'type' => 'textarea'],
            ['key' => 'beliefs.note_text', 'label' => 'Note Before You Read', 'type' => 'textarea'],
        ],
    ],
    'page_ministries' => [
        'label' => 'Ministries',
        'fields' => [
            ['key' => 'ministries.hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'ministries.hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'ministries.hero_bg_image', 'label' => 'Hero Background Image', 'type' => 'image'],
        ],
    ],
    'page_sermons' => [
        'label' => 'Sermons',
        'fields' => [
            ['key' => 'sermons.hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'sermons.hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'sermons.hero_bg_image', 'label' => 'Hero Background Image', 'type' => 'image'],
        ],
    ],
    'page_events' => [
        'label' => 'Events',
        'fields' => [
            ['key' => 'events.hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'events.hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'events.hero_bg_image', 'label' => 'Hero Background Image', 'type' => 'image'],
        ],
    ],
    'page_flagship' => [
        'label' => 'Flagship Programs',
        'fields' => [
            ['key' => 'flagship.hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'flagship.hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'flagship.hero_bg_image', 'label' => 'Hero Background Image', 'type' => 'image'],
        ],
    ],
    'page_contact' => [
        'label' => 'Contact Us',
        'fields' => [
            ['key' => 'contact.hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'contact.hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'contact.hero_bg_image', 'label' => 'Hero Background Image', 'type' => 'image'],
        ],
    ],
    'page_live' => [
        'label' => 'Watch Live',
        'fields' => [
            ['key' => 'live.embed_url', 'label' => 'Live Stream Embed URL (YouTube/Facebook)', 'type' => 'text'],
        ],
    ],
    'page_donate' => [
        'label' => 'Give Online',
        'fields' => [
            ['key' => 'donate.hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'donate.hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'donate.hero_bg_image', 'label' => 'Hero Background Image', 'type' => 'image'],
            ['key' => 'donate.bank_details', 'label' => 'Bank Account Details', 'type' => 'textarea'],
            ['key' => 'donate.momo_details', 'label' => 'Mobile Money Details', 'type' => 'textarea'],
        ],
    ],
];

// Build the whitelist of allowed keys
$allowedKeys = [];
foreach ($schema as $group) {
    foreach ($group['fields'] as $f) {
        $allowedKeys[] = $f['key'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_check();
        $posted = $_POST['setting'] ?? [];
        if (!is_array($posted)) {
            throw new RuntimeException('Invalid form payload.');
        }
        $update = [];
        foreach ($schema as $groupKey => $group) {
            foreach ($group['fields'] as $f) {
                $k = $f['key'];
                if ($f['type'] === 'image') {
                    if (isset($_POST['active_group']) && $_POST['active_group'] === $groupKey) {
                        $fileKey = str_replace('.', '_', $k);
                        $imgUrl = $_POST['setting_url'][$k] ?? ($_POST['setting_url'][$fileKey] ?? '');
                        $existingImage = setting($k);
                        
                        $actualKey = isset($_FILES['setting_file']['name'][$k]) ? $k : (isset($_FILES['setting_file']['name'][$fileKey]) ? $fileKey : null);
                        
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
                        $update[$k] = handle_image_upload_or_link($file, $imgUrl, $existingImage);
                    } else {
                        if (array_key_exists($k, $posted)) {
                            $update[$k] = trim((string) $posted[$k]);
                        }
                    }
                } else {
                    $postKey = str_replace('.', '_', $k);
                    if (array_key_exists($k, $posted)) {
                        $update[$k] = trim((string) $posted[$k]);
                    } elseif (array_key_exists($postKey, $posted)) {
                        $update[$k] = trim((string) $posted[$postKey]);
                    }
                }
            }
        }
        settings_save($update);
        flash('pages', 'saved');
        $group = isset($_POST['active_group']) && isset($schema[$_POST['active_group']]) ? $_POST['active_group'] : 'page_about';
        header('Location: pages.php?group=' . urlencode($group));
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

if (flash('pages') === 'saved') {
    $feedback = 'Page content saved.';
}

$values = settings_all(true);
$activeGroup = isset($_GET['group']) && isset($schema[$_GET['group']]) ? $_GET['group'] : 'page_about';
?>
<?php
$pageTitle = 'Page Content | BMI Admin';
require_once __DIR__ . '/includes/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Page Content</h1>
            <p class="mt-1 text-slate-500">Edit the text and content of your static pages.</p>
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
                    <?php foreach ($schema as $key => $group): ?>
                        <li>
                            <a href="?group=<?php echo e($key); ?>"
                               class="block rounded-lg px-3 py-2.5 transition-colors <?php echo $key === $activeGroup ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-100/50 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                                <?php echo e($group['label']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <form method="post" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-xl p-6 md:p-8 space-y-6 shadow-sm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="active_group" value="<?php echo e($activeGroup); ?>">
                <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4"><?php echo e($schema[$activeGroup]['label']); ?></h2>

                <?php
                foreach ($schema as $groupKey => $group):
                    foreach ($group['fields'] as $field):
                        $val = $values[$field['key']] ?? '';
                        $visible = $groupKey === $activeGroup;
                ?>
                    <?php if ($visible): ?>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5"><?php echo e($field['label']); ?></label>
                            <?php if ($field['type'] === 'textarea'): ?>
                                <textarea name="setting[<?php echo e($field['key']); ?>]" rows="4" class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"><?php echo e($val); ?></textarea>
                            <?php elseif ($field['type'] === 'image'): ?>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <span class="block text-xs font-medium text-slate-500 mb-1.5">Upload File</span>
                                        <input type="file" name="setting_file[<?php echo e(str_replace('.', '_', $field['key'])); ?>]" accept="image/*" class="w-full text-sm">
                                    </div>
                                    <div>
                                        <span class="block text-xs font-medium text-slate-500 mb-1.5">OR Paste URL</span>
                                        <input type="url" name="setting_url[<?php echo e($field['key']); ?>]" placeholder="https://" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-white text-sm" value="<?php echo filter_var($val, FILTER_VALIDATE_URL) ? e($val) : ''; ?>">
                                    </div>
                                </div>
                                <?php if ($val): ?>
                                    <div class="mt-3 flex items-center gap-4">
                                        <span class="text-sm font-semibold text-slate-700">Current Image:</span>
                                        <img src="<?php echo strpos($val, 'http') === 0 ? e($val) : '../' . e($val); ?>" class="h-16 w-auto rounded border border-slate-200 object-cover">
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <input type="<?php echo e($field['type']); ?>" name="setting[<?php echo e($field['key']); ?>]"
                                       value="<?php echo e($val); ?>"
                                       class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="setting[<?php echo e($field['key']); ?>]" value="<?php echo e($val); ?>">
                    <?php endif; ?>
                <?php endforeach; endforeach; ?>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-6 py-2.5 text-sm font-semibold transition-all shadow-md shadow-blue-500/30">Save changes</button>
                </div>
            </form>
        </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
