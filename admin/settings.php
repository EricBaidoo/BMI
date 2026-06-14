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
            ['key' => 'social.links', 'label' => 'Social Media Links', 'type' => 'social_repeater'],
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
            ['key' => 'live.schedule',            'label' => 'Upcoming Schedule', 'type' => 'schedule_repeater'],
            ['key' => 'live.past_services',       'label' => 'Past Service Replays', 'type' => 'past_services_repeater'],
            ['key' => 'live.offline_thumbnail',   'label' => 'Offline Thumbnail Image', 'type' => 'image'],
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
        
        foreach ($schema as $groupKey => $group) {
            foreach ($group['fields'] as $f) {
                $key = $f['key'];
                $postKey = str_replace('.', '_', $key);
                $postedKey = array_key_exists($key, $posted) ? $key : (array_key_exists($postKey, $posted) ? $postKey : null);
                
                if ($f['type'] === 'social_repeater') {
                        // If it's a repeater but no elements were posted (e.g. all removed), it won't be in $posted.
                        // We check if we are currently saving this group to determine if we should clear it.
                        if (isset($_POST['active_group']) && $_POST['active_group'] === $groupKey) {
                            $links = [];
                            if ($postedKey && is_array($posted[$postedKey])) {
                                foreach ($posted[$postedKey] as $link) {
                                    $links[] = [
                                        'name' => trim((string)($link['name'] ?? '')),
                                        'url' => trim((string)($link['url'] ?? '')),
                                        'icon' => trim((string)($link['icon'] ?? ''))
                                    ];
                                }
                            }
                            $update[$key] = json_encode($links);
                        }
                    } else if ($f['type'] === 'schedule_repeater') {
                        if (isset($_POST['active_group']) && $_POST['active_group'] === $groupKey) {
                            $events = [];
                            if ($postedKey && is_array($posted[$postedKey])) {
                                foreach ($posted[$postedKey] as $event) {
                                    $events[] = [
                                        'type' => trim((string)($event['type'] ?? 'weekly')),
                                        'day' => trim((string)($event['day'] ?? 'Sunday')),
                                        'name' => trim((string)($event['name'] ?? '')),
                                        'date' => trim((string)($event['date'] ?? '')),
                                        'time' => trim((string)($event['time'] ?? '')),
                                        'last_ended' => trim((string)($event['last_ended'] ?? ''))
                                    ];
                                }
                            }
                            $update[$key] = json_encode($events);
                        }
                    } else if ($f['type'] === 'past_services_repeater') {
                        if (isset($_POST['active_group']) && $_POST['active_group'] === $groupKey) {
                            $replays = [];
                            if ($postedKey && is_array($posted[$postedKey])) {
                                foreach ($posted[$postedKey] as $replay) {
                                    $replays[] = [
                                        'title' => trim((string)($replay['title'] ?? '')),
                                        'url' => trim((string)($replay['url'] ?? '')),
                                        'date' => trim((string)($replay['date'] ?? ''))
                                    ];
                                }
                            }
                            // Sort by date descending
                            usort($replays, function($a, $b) {
                                return strtotime($b['date']) <=> strtotime($a['date']);
                            });
                            $update[$key] = json_encode($replays);
                        }
                    } else if ($f['type'] === 'image') {
                        if (isset($_POST['active_group']) && $_POST['active_group'] === $groupKey) {
                            $fileInputName = 'setting_file_' . $postKey;
                            if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
                                $tmpName = $_FILES[$fileInputName]['tmp_name'];
                                $fileName = basename($_FILES[$fileInputName]['name']);
                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                if (in_array($ext, $allowed)) {
                                    $uploadDir = __DIR__ . '/../uploads/';
                                    if (!is_dir($uploadDir)) {
                                        mkdir($uploadDir, 0755, true);
                                    }
                                    $newName = uniqid('thumb_') . '.' . $ext;
                                    if (move_uploaded_file($tmpName, $uploadDir . $newName)) {
                                        $update[$key] = 'uploads/' . $newName;
                                    }
                                }
                            } else if (isset($posted[$postedKey])) {
                                // Fallback to URL if typed manually
                                $update[$key] = trim((string) $posted[$postedKey]);
                            }
                        }
                    } else if ($postedKey) {
                        $val = trim((string) $posted[$postedKey]);
                        
                        // Automatically convert YouTube standard links to Embed links
                        if ($key === 'live.embed_url' && $val !== '') {
                            if (strpos($val, 'youtube.com/watch') !== false) {
                                parse_str(parse_url($val, PHP_URL_QUERY), $query);
                                if (isset($query['v'])) {
                                    $val = 'https://www.youtube.com/embed/' . $query['v'];
                                }
                            } else if (strpos($val, 'youtu.be/') !== false) {
                                $path = parse_url($val, PHP_URL_PATH);
                                $val = 'https://www.youtube.com/embed/' . ltrim((string)$path, '/');
                            }
                        }
                        
                        $update[$key] = $val;
                        
                        // Handle auto-archiving
                        if ($key === 'live.embed_url' && isset($_POST['end_and_archive']) && $_POST['end_and_archive'] === '1') {
                            $oldEmbedUrl = setting('live.embed_url', '');
                            if ($oldEmbedUrl !== '') {
                                $replayTitle = 'Livestream Replay - ' . date('F j, Y');
                                
                                // Look through the submitted schedule to find the service that just ended
                                $schedKey = 'live.schedule';
                                if (isset($posted[$schedKey]) && is_array($posted[$schedKey])) {
                                    foreach ($posted[$schedKey] as $index => $event) {
                                        $type = trim((string)($event['type'] ?? 'date'));
                                        $time = trim((string)($event['time'] ?? ''));
                                        if (empty($time)) continue;
                                        
                                        if ($type === 'weekly') {
                                            $day = trim((string)($event['day'] ?? 'Sunday'));
                                            $isToday = (date('l') === $day);
                                            $eventDate = $isToday ? date('Y-m-d') : date('Y-m-d', strtotime("next $day"));
                                        } else {
                                            $eventDate = trim((string)($event['date'] ?? ''));
                                        }
                                        
                                        if (empty($eventDate)) continue;
                                        $eventTime = strtotime($eventDate . ' ' . $time);
                                        
                                        if (date('Y-m-d', $eventTime) === date('Y-m-d')) {
                                            $replayTitle = trim((string)($event['name'] ?? $replayTitle));
                                            if ($type === 'date') {
                                                unset($posted[$schedKey][$index]); // Auto-remove from schedule
                                            } else {
                                                // For weekly events, tag it as ended today so the frontend hides it immediately
                                                $posted[$schedKey][$index]['last_ended'] = date('Y-m-d');
                                            }
                                            break;
                                        }
                                    }
                                }
                                
                                $newPast = [
                                    'title' => $replayTitle,
                                    'url' => $oldEmbedUrl,
                                    'date' => date('Y-m-d')
                                ];
                                
                                $pastKey = 'live.past_services';
                                if (!isset($posted[$pastKey]) || !is_array($posted[$pastKey])) {
                                    $existingPast = json_decode(setting('live.past_services', '[]'), true) ?: [];
                                    $posted[$pastKey] = $existingPast;
                                }
                                
                                array_unshift($posted[$pastKey], $newPast);
                            }
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
                <input type="hidden" name="end_and_archive" id="end_and_archive_input" value="0">
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
                                <div class="flex flex-col gap-3">
                                    <?php if ($val): ?>
                                        <div class="relative w-48 h-28 rounded-lg overflow-hidden border border-slate-200 bg-slate-100">
                                            <img src="<?php echo rtrim((string)$siteUrl, '/'); ?>/<?php echo htmlspecialchars($val); ?>" class="w-full h-full object-cover">
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex flex-col gap-2">
                                        <input type="file" name="setting_file_<?php echo e(str_replace('.', '_', $field['key'])); ?>" accept="image/*" class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors">
                                        <input type="hidden" name="setting[<?php echo e($field['key']); ?>]" value="<?php echo e($val); ?>">
                                        <p class="text-xs text-slate-500">Upload a new image to replace the current one.</p>
                                    </div>
                                </div>
                            <?php elseif ($field['type'] === 'social_repeater'): ?>
                                <?php $links = json_decode($val, true) ?: []; ?>
                                <div id="social-repeater" class="space-y-4">
                                    <?php foreach($links as $index => $link): ?>
                                        <div class="flex flex-col gap-4 items-start border border-slate-200 p-4 rounded-lg bg-white shadow-sm relative group/social">
                                            <div class="flex justify-between w-full items-center mb-1">
                                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Platform Entry</span>
                                                <button type="button" onclick="this.closest('.group\\/social').remove()" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition-colors" aria-label="Remove">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </div>
                                            <div class="w-full space-y-3">
                                                <input type="text" name="setting[<?php echo e($field['key']); ?>][<?php echo $index; ?>][name]" value="<?php echo e($link['name'] ?? ''); ?>" placeholder="Platform Name (e.g. LinkedIn)" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                                <input type="url" name="setting[<?php echo e($field['key']); ?>][<?php echo $index; ?>][url]" value="<?php echo e($link['url'] ?? ''); ?>" placeholder="URL" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                                
                                                <div class="flex flex-col md:flex-row gap-2 items-start md:items-center mt-2">
                                                    <select onchange="if(this.value) { this.nextElementSibling.value = this.value; this.value=''; }" class="w-full md:w-1/3 border border-slate-300 rounded-lg px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 outline-none transition-all text-sm font-semibold text-slate-700">
                                                        <option value="">-- Quick Select Icon --</option>
                                                        <option value='<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'>Facebook</option>
                                                        <option value='<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>'>Instagram</option>
                                                        <option value='<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>'>LinkedIn</option>
                                                        <option value='<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>'>YouTube</option>
                                                        <option value='<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/></svg>'>X (Twitter)</option>
                                                        <option value='<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93v7.2c0 1.61-.39 3.23-1.18 4.61-1.22 2.14-3.4 3.56-5.83 3.94-2.38.38-4.95-.12-6.9-1.57-2.16-1.61-3.37-4.14-3.14-6.84.19-2.31 1.34-4.49 3.21-5.88 1.84-1.37 4.2-1.83 6.44-1.25V12.5c-1.36-.18-2.77-.07-4.04.54-1.25.6-2.22 1.63-2.68 2.89-.5 1.36-.45 2.92.14 4.24.63 1.41 1.85 2.47 3.33 2.87 1.51.4 3.14.22 4.49-.51 1.25-.68 2.15-1.84 2.52-3.19.16-.58.24-1.18.25-1.78l-.01-17.54h.01z"/></svg>'>TikTok</option>
                                                        <option value='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>'>Generic Link Icon</option>
                                                    </select>
                                                    <textarea name="setting[<?php echo e($field['key']); ?>][<?php echo $index; ?>][icon]" rows="2" placeholder="SVG Icon Code" class="w-full md:w-2/3 border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none font-mono text-xs transition-all"><?php echo e($link['icon'] ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" onclick="addSocialLink('<?php echo e($field['key']); ?>')" class="mt-4 flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-lg transition-colors text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Add Social Media
                                </button>
                                
                                <template id="social-template-<?php echo e(str_replace('.', '_', $field['key'])); ?>">
                                    <div class="flex flex-col gap-4 items-start border border-slate-200 p-4 rounded-lg bg-white shadow-sm relative group/social">
                                        <div class="flex justify-between w-full items-center mb-1">
                                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Platform Entry</span>
                                            <button type="button" onclick="this.closest('.group\\/social').remove()" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition-colors" aria-label="Remove">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                        <div class="w-full space-y-3">
                                            <input type="text" name="setting[<?php echo e($field['key']); ?>][__INDEX__][name]" value="" placeholder="Platform Name (e.g. LinkedIn)" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                            <input type="url" name="setting[<?php echo e($field['key']); ?>][__INDEX__][url]" value="" placeholder="URL" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                            
                                            <div class="flex flex-col md:flex-row gap-2 items-start md:items-center mt-2">
                                                <select onchange="if(this.value) { this.nextElementSibling.value = this.value; this.value=''; }" class="w-full md:w-1/3 border border-slate-300 rounded-lg px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 outline-none transition-all text-sm font-semibold text-slate-700">
                                                    <option value="">-- Quick Select Icon --</option>
                                                    <option value='<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>'>Facebook</option>
                                                    <option value='<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>'>Instagram</option>
                                                    <option value='<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>'>LinkedIn</option>
                                                    <option value='<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>'>YouTube</option>
                                                    <option value='<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/></svg>'>X (Twitter)</option>
                                                    <option value='<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93v7.2c0 1.61-.39 3.23-1.18 4.61-1.22 2.14-3.4 3.56-5.83 3.94-2.38.38-4.95-.12-6.9-1.57-2.16-1.61-3.37-4.14-3.14-6.84.19-2.31 1.34-4.49 3.21-5.88 1.84-1.37 4.2-1.83 6.44-1.25V12.5c-1.36-.18-2.77-.07-4.04.54-1.25.6-2.22 1.63-2.68 2.89-.5 1.36-.45 2.92.14 4.24.63 1.41 1.85 2.47 3.33 2.87 1.51.4 3.14.22 4.49-.51 1.25-.68 2.15-1.84 2.52-3.19.16-.58.24-1.18.25-1.78l-.01-17.54h.01z"/></svg>'>TikTok</option>
                                                    <option value='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>'>Generic Link Icon</option>
                                                </select>
                                                <textarea name="setting[<?php echo e($field['key']); ?>][__INDEX__][icon]" rows="2" placeholder="SVG Icon Code" class="w-full md:w-2/3 border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none font-mono text-xs transition-all"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                
                                <script>
                                    function addSocialLink(key) {
                                        const safeKey = key.replace('.', '_');
                                        const template = document.getElementById('social-template-' + safeKey);
                                        const container = document.getElementById('social-repeater');
                                        const index = Date.now();
                                        const html = template.innerHTML.replace(/__INDEX__/g, index);
                                        container.insertAdjacentHTML('beforeend', html);
                                    }
                                </script>
                            <?php elseif ($field['type'] === 'schedule_repeater'): ?>
                                <?php $events = json_decode($val, true) ?: []; ?>
                                <div id="schedule-repeater" class="space-y-4">
                                    <?php foreach($events as $index => $event): ?>
                                        <div class="flex flex-col gap-4 items-start border border-slate-200 p-4 rounded-lg bg-white shadow-sm relative group/schedule">
                                            <div class="flex justify-between w-full items-center mb-1">
                                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Event Entry</span>
                                                <button type="button" onclick="this.closest('.group\\/schedule').remove()" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition-colors" aria-label="Remove">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </div>
                                            <div class="w-full grid grid-cols-1 md:grid-cols-4 gap-3">
                                                <input type="text" name="setting[<?php echo e($field['key']); ?>][<?php echo $index; ?>][name]" value="<?php echo e($event['name'] ?? ''); ?>" placeholder="Event Name (e.g. Celebration Service)" class="md:col-span-4 w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                                
                                                <div class="md:col-span-2">
                                                    <select name="setting[<?php echo e($field['key']); ?>][<?php echo $index; ?>][type]" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all" onchange="toggleScheduleType(this)">
                                                        <option value="weekly" <?php echo ($event['type'] ?? 'weekly') === 'weekly' ? 'selected' : ''; ?>>Weekly Recurring</option>
                                                        <option value="date" <?php echo ($event['type'] ?? 'weekly') === 'date' ? 'selected' : ''; ?>>Specific Date</option>
                                                    </select>
                                                </div>

                                                <div class="schedule-day-container <?php echo ($event['type'] ?? 'weekly') === 'weekly' ? 'block' : 'hidden'; ?> md:col-span-1">
                                                    <select name="setting[<?php echo e($field['key']); ?>][<?php echo $index; ?>][day]" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                                        <?php
                                                        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                                        foreach ($days as $day) {
                                                            $selected = ($event['day'] ?? 'Sunday') === $day ? 'selected' : '';
                                                            echo "<option value=\"$day\" $selected>$day</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="schedule-date-container <?php echo ($event['type'] ?? 'weekly') === 'date' ? 'block' : 'hidden'; ?> md:col-span-1">
                                                    <input type="date" name="setting[<?php echo e($field['key']); ?>][<?php echo $index; ?>][date]" value="<?php echo e($event['date'] ?? ''); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                                </div>

                                                <div class="md:col-span-1">
                                                    <input type="time" name="setting[<?php echo e($field['key']); ?>][<?php echo $index; ?>][time]" value="<?php echo e($event['time'] ?? ''); ?>" required class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" onclick="addScheduleEvent('<?php echo e($field['key']); ?>')" class="mt-4 flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-lg transition-colors text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Add Scheduled Event
                                </button>
                                
                                <template id="schedule-template-<?php echo e(str_replace('.', '_', $field['key'])); ?>">
                                    <div class="flex flex-col gap-4 items-start border border-slate-200 p-4 rounded-lg bg-white shadow-sm relative group/schedule">
                                        <div class="flex justify-between w-full items-center mb-1">
                                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Event Entry</span>
                                            <button type="button" onclick="this.closest('.group\\/schedule').remove()" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition-colors" aria-label="Remove">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                        <div class="w-full grid grid-cols-1 md:grid-cols-4 gap-3">
                                            <input type="text" name="setting[<?php echo e($field['key']); ?>][__INDEX__][name]" value="" placeholder="Event Name (e.g. Celebration Service)" class="md:col-span-4 w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                            
                                            <div class="md:col-span-2">
                                                <select name="setting[<?php echo e($field['key']); ?>][__INDEX__][type]" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all" onchange="toggleScheduleType(this)">
                                                    <option value="weekly" selected>Weekly Recurring</option>
                                                    <option value="date">Specific Date</option>
                                                </select>
                                            </div>

                                            <div class="schedule-day-container block md:col-span-1">
                                                <select name="setting[<?php echo e($field['key']); ?>][__INDEX__][day]" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                                    <?php
                                                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                                    foreach ($days as $day) {
                                                        echo "<option value=\"$day\">$day</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="schedule-date-container hidden md:col-span-1">
                                                <input type="date" name="setting[<?php echo e($field['key']); ?>][__INDEX__][date]" value="" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                            </div>

                                            <div class="md:col-span-1">
                                                <input type="time" name="setting[<?php echo e($field['key']); ?>][__INDEX__][time]" value="" required class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                
                                <script>
                                    function toggleScheduleType(select) {
                                        const container = select.closest('.group\\/schedule');
                                        const isWeekly = select.value === 'weekly';
                                        container.querySelector('.schedule-day-container').classList.toggle('hidden', !isWeekly);
                                        container.querySelector('.schedule-day-container').classList.toggle('block', isWeekly);
                                        container.querySelector('.schedule-date-container').classList.toggle('hidden', isWeekly);
                                        container.querySelector('.schedule-date-container').classList.toggle('block', !isWeekly);
                                    }

                                    function addScheduleEvent(key) {
                                        const safeKey = key.replace('.', '_');
                                        const template = document.getElementById('schedule-template-' + safeKey);
                                        const container = document.getElementById('schedule-repeater');
                                        const index = Date.now();
                                        const html = template.innerHTML.replace(/__INDEX__/g, index);
                                        container.insertAdjacentHTML('beforeend', html);
                                    }
                                </script>
                            <?php elseif ($field['type'] === 'past_services_repeater'): ?>
                                <?php 
                                    $replays = json_decode($val, true) ?: []; 
                                    $postedKey = str_replace('.', '_', $field['key']);
                                ?>
                                <div class="overflow-x-auto border border-slate-200 rounded-lg shadow-sm">
                                    <table class="w-full text-left border-collapse">
                                        <thead class="bg-slate-50 border-b border-slate-200">
                                            <tr>
                                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-40">Date</th>
                                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-1/3">Service Title</th>
                                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Video URL</th>
                                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right w-16"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="repeater_<?php echo $postedKey; ?>" class="divide-y divide-slate-100 bg-white">
                                            <?php foreach ($replays as $index => $replay): ?>
                                                <tr class="group hover:bg-slate-50 transition-colors">
                                                    <td class="p-3 align-top">
                                                        <input type="date" name="setting[<?php echo e($field['key']); ?>][<?php echo $index; ?>][date]" value="<?php echo htmlspecialchars($replay['date'] ?? ''); ?>" class="w-full border border-slate-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-transparent group-hover:bg-white group-hover:border-slate-300 transition-colors" required>
                                                    </td>
                                                    <td class="p-3 align-top">
                                                        <input type="text" name="setting[<?php echo e($field['key']); ?>][<?php echo $index; ?>][title]" value="<?php echo htmlspecialchars($replay['title'] ?? ''); ?>" class="w-full border border-slate-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-transparent group-hover:bg-white group-hover:border-slate-300 transition-colors" required>
                                                    </td>
                                                    <td class="p-3 align-top">
                                                        <input type="url" name="setting[<?php echo e($field['key']); ?>][<?php echo $index; ?>][url]" value="<?php echo htmlspecialchars($replay['url'] ?? ''); ?>" class="w-full border border-slate-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-transparent group-hover:bg-white group-hover:border-slate-300 transition-colors" required placeholder="https://">
                                                    </td>
                                                    <td class="p-3 align-top text-right">
                                                        <button type="button" onclick="this.closest('tr').remove()" class="p-2 text-slate-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors mt-0.5" title="Remove Replay">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" onclick="addPastService('<?php echo $postedKey; ?>')" class="mt-4 px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold rounded-lg border border-slate-200 transition-colors flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Past Service Replay
                                </button>
                                <template id="replay-template-<?php echo $postedKey; ?>">
                                    <tr class="group hover:bg-slate-50 transition-colors">
                                        <td class="p-3 align-top">
                                            <input type="date" name="setting_repeater[<?php echo $postedKey; ?>][__INDEX__][date]" value="" class="w-full border border-slate-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-transparent group-hover:bg-white group-hover:border-slate-300 transition-colors" required>
                                        </td>
                                        <td class="p-3 align-top">
                                            <input type="text" name="setting_repeater[<?php echo $postedKey; ?>][__INDEX__][title]" value="" class="w-full border border-slate-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-transparent group-hover:bg-white group-hover:border-slate-300 transition-colors" required>
                                        </td>
                                        <td class="p-3 align-top">
                                            <input type="url" name="setting_repeater[<?php echo $postedKey; ?>][__INDEX__][url]" value="" class="w-full border border-slate-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-transparent group-hover:bg-white group-hover:border-slate-300 transition-colors" required placeholder="https://">
                                        </td>
                                        <td class="p-3 align-top text-right">
                                            <button type="button" onclick="this.closest('tr').remove()" class="p-2 text-slate-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors mt-0.5" title="Remove Replay">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                
                                <script>
                                    function addPastService(key) {
                                        const template = document.getElementById('replay-template-' + key);
                                        const container = document.getElementById('repeater_' + key);
                                        const index = Date.now();
                                        const html = template.innerHTML.replace(/__INDEX__/g, index);
                                        container.insertAdjacentHTML('afterbegin', html);
                                    }
                                </script>
                            <?php else: ?>
                                <div class="flex flex-col gap-2">
                                    <input type="<?php echo e($field['type']); ?>" name="setting[<?php echo e($field['key']); ?>]"
                                           id="input_<?php echo e(str_replace('.', '_', $field['key'])); ?>"
                                           value="<?php echo e($val); ?>"
                                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                                           <?php if ($field['type'] === 'url') echo 'placeholder="https://"'; ?>
                                    >
                                    <?php if ($field['key'] === 'live.embed_url' && $val !== ''): ?>
                                        <button type="button" onclick="endAndArchiveStream()" class="self-start mt-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded-lg border border-red-200 shadow-sm transition-colors flex items-center gap-2 text-sm">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h12v12H6z"/></svg>
                                            End Stream & Archive
                                        </button>
                                        <script>
                                            function endAndArchiveStream() {
                                                if(confirm('Are you sure you want to end the stream? This will automatically clear the URL and save the replay to your Past Services.')) {
                                                    document.getElementById('input_live_embed_url').value = '';
                                                    document.getElementById('end_and_archive_input').value = '1';
                                                    document.getElementById('input_live_embed_url').closest('form').submit();
                                                }
                                            }
                                        </script>
                                    <?php endif; ?>
                                </div>
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
