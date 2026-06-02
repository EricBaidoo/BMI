<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/helpers.php';

$feedback = '';
$error = '';

/**
 * Definition of every editable setting. Keep this in sync with database/migrate.php seeds.
 * Adding a new setting? Add it here AND in migrate.php, then run migrate.
 *
 * type: text | textarea | url | tel | email
 */
$schema = [
    'general' => [
        'label' => 'General',
        'fields' => [
            ['key' => 'site.name',         'label' => 'Site / Church name',  'type' => 'text'],
            ['key' => 'site.logo',         'label' => 'Main Logo (Header/Footer)', 'type' => 'image'],
            ['key' => 'site.favicon',      'label' => 'Favicon (Browser Tab)',     'type' => 'image'],
            ['key' => 'site.tagline',      'label' => 'Tagline',              'type' => 'text'],
            ['key' => 'site.description',  'label' => 'Short description (used in SEO meta + social cards)', 'type' => 'textarea'],
            ['key' => 'site.founded_year', 'label' => 'Founded year',         'type' => 'text'],
        ],
    ],
    'contact' => [
        'label' => 'Contact details',
        'fields' => [
            ['key' => 'contact.address',         'label' => 'Address',              'type' => 'textarea'],
            ['key' => 'contact.phone_primary',   'label' => 'Primary phone',        'type' => 'tel'],
            ['key' => 'contact.phone_secondary', 'label' => 'Secondary phone',      'type' => 'tel'],
            ['key' => 'contact.email_general',   'label' => 'General email',        'type' => 'email'],
            ['key' => 'contact.email_prayer',    'label' => 'Prayer requests email','type' => 'email'],
            ['key' => 'contact.email_giving',    'label' => 'Giving / finance email','type'=> 'email'],
            ['key' => 'contact.map_query',       'label' => 'Google Maps search query (used to embed map)', 'type' => 'text'],
        ],
    ],
    'service' => [
        'label' => 'Service times',
        'fields' => [
            ['key' => 'service.sunday_worship', 'label' => 'Sunday worship',  'type' => 'text'],
            ['key' => 'service.bible_study',    'label' => 'Bible study',     'type' => 'text'],
            ['key' => 'service.prayer_service', 'label' => 'Prayer service',  'type' => 'text'],
            ['key' => 'service.notes',          'label' => 'Notes',           'type' => 'textarea'],
        ],
    ],
    'social' => [
        'label' => 'Social media',
        'fields' => [
            ['key' => 'social.facebook',  'label' => 'Facebook URL',  'type' => 'url'],
            ['key' => 'social.instagram', 'label' => 'Instagram URL', 'type' => 'url'],
            ['key' => 'social.youtube',   'label' => 'YouTube URL',   'type' => 'url'],
            ['key' => 'social.x',         'label' => 'X / Twitter URL','type'=> 'url'],
            ['key' => 'social.tiktok',    'label' => 'TikTok URL',    'type' => 'url'],
        ],
    ],
    'giving' => [
        'label' => 'Giving',
        'fields' => [
            ['key' => 'giving.bank_name',           'label' => 'Bank name',          'type' => 'text'],
            ['key' => 'giving.bank_account_name',   'label' => 'Account name',       'type' => 'text'],
            ['key' => 'giving.bank_account_number', 'label' => 'Account number',     'type' => 'text'],
            ['key' => 'giving.bank_branch',         'label' => 'Branch',             'type' => 'text'],
            ['key' => 'giving.momo_mtn',            'label' => 'MTN MoMo number',    'type' => 'tel'],
            ['key' => 'giving.momo_vodafone',      'label' => 'Vodafone Cash number','type'=> 'tel'],
            ['key' => 'giving.momo_airteltigo',     'label' => 'AirtelTigo Money',   'type' => 'tel'],
            ['key' => 'giving.paystack_public_key', 'label' => 'Paystack public key (pk_live_… or pk_test_…)', 'type' => 'text'],
            ['key' => 'giving.currency',            'label' => 'Currency code (GHS, USD, …)','type'=> 'text'],
        ],
    ],
    'live' => [
        'label' => 'Livestream',
        'fields' => [
            ['key' => 'live.embed_url',           'label' => 'Live embed URL (YouTube/Facebook embed src)', 'type' => 'url'],
            ['key' => 'live.youtube_channel_url', 'label' => 'YouTube channel URL', 'type' => 'url'],
        ],
    ],
    'analytics' => [
        'label' => 'Analytics',
        'fields' => [
            ['key' => 'analytics.plausible_domain', 'label' => 'Plausible domain (e.g. bmiglobal.org)', 'type' => 'text'],
        ],
    ],
];

// Build the whitelist of allowed keys — anything not on this list is ignored on save.
$allowedKeys = [];
foreach ($schema as $group) {
    foreach ($group['fields'] as $f) {
        $allowedKeys[] = $f['key'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents(__DIR__ . '/files_dump.txt', print_r($_FILES, true) . "\n" . print_r($_POST, true));
    try {
        csrf_check();
        require_once __DIR__ . '/../includes/uploads.php';
        
        $posted = $_POST['setting'] ?? [];
        if (!is_array($posted)) {
            throw new RuntimeException('Invalid form payload.');
        }
        $update = [];
        
        foreach ($schema as $group) {
            foreach ($group['fields'] as $f) {
                $key = $f['key'];
                $postKey = str_replace('.', '_', $key);
                if ($f['type'] === 'image') {
                    $actualKey = isset($_FILES['setting_files']['name'][$key]) ? $key : (isset($_FILES['setting_files']['name'][$postKey]) ? $postKey : null);
                    if ($actualKey && !empty($_FILES['setting_files']['name'][$actualKey])) {
                        $file = [
                            'name' => $_FILES['setting_files']['name'][$actualKey],
                            'type' => $_FILES['setting_files']['type'][$actualKey],
                            'tmp_name' => $_FILES['setting_files']['tmp_name'][$actualKey],
                            'error' => $_FILES['setting_files']['error'][$actualKey],
                            'size' => $_FILES['setting_files']['size'][$actualKey],
                        ];
                        if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                            $update[$key] = upload_image($file, 'site');
                        }
                    }
                } else {
                    if (array_key_exists($key, $posted)) {
                        $update[$key] = trim((string) $posted[$key]);
                    } elseif (array_key_exists($postKey, $posted)) {
                        $update[$key] = trim((string) $posted[$postKey]);
                    }
                }
            }
        }
        settings_save($update);
        flash('settings', 'saved');
        $group = isset($_POST['active_group']) && isset($schema[$_POST['active_group']]) ? $_POST['active_group'] : 'general';
        header('Location: settings.php?group=' . urlencode($group));
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

if (flash('settings') === 'saved') {
    $feedback = 'Settings saved.';
}

$values = settings_all(true);
$activeGroup = isset($_GET['group']) && isset($schema[$_GET['group']]) ? $_GET['group'] : 'general';
?>
<?php
$pageTitle = 'Site Settings | BMI Admin';
require_once __DIR__ . '/includes/header.php';
?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Site Settings</h1>
            <p class="mt-1 text-slate-500">Update site-wide content. Changes take effect immediately on the public site.</p>
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
                // Render every group as hidden inputs so we don't lose values on tab switch — only the active group is visible.
                foreach ($schema as $groupKey => $group):
                    foreach ($group['fields'] as $field):
                        $val = $values[$field['key']] ?? '';
                        $visible = $groupKey === $activeGroup;
                ?>
                    <?php if ($visible): ?>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5"><?php echo e($field['label']); ?></label>
                            <?php if ($field['type'] === 'textarea'): ?>
                                <textarea name="setting[<?php echo e($field['key']); ?>]" rows="3" class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"><?php echo e($val); ?></textarea>
                            <?php elseif ($field['type'] === 'image'): ?>
                                <div class="flex items-center gap-4">
                                    <?php if ($val): ?>
                                        <div class="h-12 w-auto bg-slate-100 rounded border border-slate-200 overflow-hidden flex items-center justify-center p-1">
                                            <img src="/BMI/<?php echo e($val); ?>" class="max-h-full max-w-full object-contain" alt="">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="setting_files[<?php echo e(str_replace('.', '_', $field['key'])); ?>]" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                            <?php else: ?>
                                <input type="<?php echo e($field['type']); ?>" name="setting[<?php echo e($field['key']); ?>]"
                                       value="<?php echo e($val); ?>"
                                       class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                            <?php endif; ?>
                            <span class="mt-1.5 block text-xs text-slate-400 font-mono tracking-tight"><?php echo e($field['key']); ?></span>
                        </div>
                    <?php else: ?>
                        <?php if ($field['type'] !== 'image'): ?>
                            <input type="hidden" name="setting[<?php echo e($field['key']); ?>]" value="<?php echo e($val); ?>">
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; endforeach; ?>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-6 py-2.5 text-sm font-semibold transition-all shadow-md shadow-blue-500/30">Save changes</button>
                </div>
            </form>
        </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
