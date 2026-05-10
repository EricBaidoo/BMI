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
    try {
        csrf_check();
        $posted = $_POST['setting'] ?? [];
        if (!is_array($posted)) {
            throw new RuntimeException('Invalid form payload.');
        }
        $update = [];
        foreach ($allowedKeys as $key) {
            // PHP converts dots in $_POST keys to underscores at the top level — but keys
            // inside an array (setting[...]) are preserved verbatim.
            if (array_key_exists($key, $posted)) {
                $update[$key] = trim((string) $posted[$key]);
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Site Settings | BMI Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-3xl font-bold">Site Settings</h1>
                <p class="mt-2 text-slate-600">Update site-wide content. Changes take effect immediately on the public site.</p>
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

        <div class="mt-6 grid md:grid-cols-[220px_1fr] gap-6 items-start">
            <nav class="bg-white border border-slate-200 rounded p-2 sticky top-4">
                <ul class="text-sm">
                    <?php foreach ($schema as $key => $group): ?>
                        <li>
                            <a href="?group=<?php echo e($key); ?>"
                               class="block rounded px-3 py-2 <?php echo $key === $activeGroup ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-100'; ?>">
                                <?php echo e($group['label']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <form method="post" class="bg-white border border-slate-200 rounded p-6 space-y-5">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="active_group" value="<?php echo e($activeGroup); ?>">
                <h2 class="text-xl font-semibold border-b border-slate-200 pb-3"><?php echo e($schema[$activeGroup]['label']); ?></h2>

                <?php
                // Render every group as hidden inputs so we don't lose values on tab switch — only the active group is visible.
                foreach ($schema as $groupKey => $group):
                    foreach ($group['fields'] as $field):
                        $val = $values[$field['key']] ?? '';
                        $visible = $groupKey === $activeGroup;
                ?>
                    <?php if ($visible): ?>
                        <label class="block text-sm">
                            <span class="font-medium"><?php echo e($field['label']); ?></span>
                            <?php if ($field['type'] === 'textarea'): ?>
                                <textarea name="setting[<?php echo e($field['key']); ?>]" rows="3" class="mt-1 w-full border border-slate-300 rounded px-3 py-2"><?php echo e($val); ?></textarea>
                            <?php else: ?>
                                <input type="<?php echo e($field['type']); ?>" name="setting[<?php echo e($field['key']); ?>]"
                                       value="<?php echo e($val); ?>"
                                       class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                            <?php endif; ?>
                            <span class="mt-1 block text-xs text-slate-400"><code><?php echo e($field['key']); ?></code></span>
                        </label>
                    <?php else: ?>
                        <input type="hidden" name="setting[<?php echo e($field['key']); ?>]" value="<?php echo e($val); ?>">
                    <?php endif; ?>
                <?php endforeach; endforeach; ?>

                <div class="pt-2">
                    <button type="submit" class="rounded bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 text-sm font-semibold">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
