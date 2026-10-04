<?php
$pageTitle = 'Give | Bridge Ministries International';
$pageDescription = 'Give to Bridge Ministries International from Ghana (Mobile Money, bank transfer, card) or the United States (card, bank, Zelle, check).';

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/settings.php';

/** An https link from settings, or ''. */
$httpsSetting = function (string ...$values): string {
    foreach ($values as $v) {
        $v = trim($v);
        if (preg_match('#^https://\S+$#i', $v)) {
            return $v;
        }
    }
    return '';
};
$lines = fn (array $pairs) => implode("\n", array_filter(array_map(fn ($label, $key) => setting($key) !== '' ? $label . setting($key) : '', array_keys($pairs), $pairs)));

// ---- Ghana (GH₵) ----
$ghOnline = $httpsSetting(setting('giving.gh_online_url'), (string) env('CHMS_PAYMENT_URL', ''), setting('donate.chms_url'));
$ghBank = $lines(['Bank: ' => 'giving.bank_name', 'Account name: ' => 'giving.bank_account_name', 'Account number: ' => 'giving.bank_account_number', 'Branch: ' => 'giving.bank_branch']) ?: trim(setting('donate.bank_details'));
$ghMomo = $lines(['MTN MoMo: ' => 'giving.momo_mtn', 'Telecel Cash: ' => 'giving.momo_vodafone', 'AirtelTigo Money: ' => 'giving.momo_airteltigo']) ?: trim(setting('donate.momo_details'));

// ---- United States (US$) ----
$usOnline = $httpsSetting(setting('giving.us_online_url'));
$usZelle = trim(setting('giving.us_zelle'));
$usCheck = trim(setting('giving.us_check_address'));
$usPayable = trim(setting('giving.us_check_payable'));
$usEntity = trim(setting('giving.us_entity_name'));
$usEin = trim(setting('giving.us_ein'));

$icons = [
    'card' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
    'bank' => 'M3 21h18M5 21V10m4 11V10m6 11V10m4 11V10M2 10l10-6 10 6',
    'phone' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
    'mail' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
];

$countries = [
    'GH' => ['label' => 'Ghana', 'currency' => 'GH₵', 'methods' => array_values(array_filter([
        $ghOnline !== '' ? ['icon' => 'card', 'title' => 'Give Online', 'text' => 'Give in cedis with Mobile Money or a card through our secure payment page. You can give once or regularly.', 'button' => ['Give online in GH₵', $ghOnline]] : null,
        $ghMomo !== '' ? ['icon' => 'phone', 'title' => 'Mobile Money', 'details' => $ghMomo] : null,
        $ghBank !== '' ? ['icon' => 'bank', 'title' => 'Bank Transfer', 'details' => $ghBank] : null,
    ]))],
    'US' => ['label' => 'United States', 'currency' => 'US$', 'methods' => array_values(array_filter([
        $usOnline !== '' ? ['icon' => 'card', 'title' => 'Give Online', 'text' => 'Give in dollars by card, Apple Pay, Google Pay or US bank account, once or every month. A receipt is emailed to you.', 'button' => ['Give online in US$', $usOnline]] : null,
        $usZelle !== '' ? ['icon' => 'phone', 'title' => 'Zelle', 'details' => "Send to: {$usZelle}\nAdd your name and \"Offering\" or \"Tithe\" in the memo."] : null,
        $usCheck !== '' ? ['icon' => 'mail', 'title' => 'By Check', 'details' => ($usPayable !== '' ? "Payable to: {$usPayable}\n\n" : '') . "Mail to:\n{$usCheck}"] : null,
    ]))],
];
$anyMethod = (bool) array_filter($countries, fn ($c) => $c['methods'] !== []);

// Default tab: the visitor's country when known (via Cloudflare), else Ghana; the browser may refine it.
$defaultCountry = client_country() === 'US' ? 'US' : 'GH';

include __DIR__ . '/../includes/header.php';

render_hero_cinematic([
    'title' => setting('donate.hero_title', '<span class="italic font-light">Give</span> Online'),
    'subtitle' => setting('donate.hero_subtitle', 'Partnership'),
    'bg_image' => setting('donate.hero_bg_image', 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?q=80&w=1920&auto=format&fit=crop'),
    'button_text' => 'Ways to Give',
    'button_url' => '#ways-to-give',
]);
?>

<!-- WAYS TO GIVE SECTION -->
<section id="ways-to-give" class="py-24 md:py-32 bg-obsidian-900 relative overflow-hidden scroll-mt-24">
    <div class="absolute top-0 right-0 w-[50rem] h-[50rem] bg-accent-glow blur-[150px] rounded-full mix-blend-screen pointer-events-none z-0"></div>

    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <h2 class="text-4xl md:text-5xl lg:text-7xl font-display font-black text-white uppercase tracking-normal mb-6 leading-[1.0]">Ways to <i class="text-accent font-light">Give</i></h2>
            <p class="text-neutral-400 text-lg">Choose where you are giving from.</p>
        </div>

        <?php if (!$anyMethod): ?>
        <div class="max-w-2xl mx-auto text-center bg-obsidian-800/40 p-10 lg:p-12 rounded-[2.5rem] border border-white/5">
            <p class="text-neutral-300 font-sans font-medium leading-relaxed mb-8">Giving details are being set up. To give today, please contact our finance team and we will share the church's official account details with you.</p>
            <?php render_button_primary(['text' => 'Contact Finance Team', 'url' => 'contact?subject=Giving', 'style' => 'light']); ?>
        </div>
        <?php else: ?>

        <div id="give-tabs" class="hidden justify-center mb-14" role="tablist" aria-label="Country">
            <div class="inline-flex rounded-full border border-white/10 bg-white/5 p-1.5">
                <?php foreach ($countries as $code => $c): ?>
                    <button type="button" role="tab" id="tab-give-<?php echo $code; ?>" aria-controls="give-<?php echo $code; ?>" data-country="<?php echo $code; ?>"
                            class="give-tab rounded-full px-6 md:px-8 py-3 text-xs md:text-sm font-bold uppercase tracking-widest text-white/60 hover:text-white transition-colors aria-selected:bg-white aria-selected:text-black">
                        <?php echo e($c['label']); ?> <span class="opacity-70">(<?php echo e($c['currency']); ?>)</span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <?php foreach ($countries as $code => $c): ?>
        <div id="give-<?php echo $code; ?>" class="give-panel mb-16 last:mb-0" role="tabpanel" aria-labelledby="tab-give-<?php echo $code; ?>" data-country="<?php echo $code; ?>">
            <h3 class="give-panel-heading text-2xl md:text-3xl font-display font-black uppercase text-white text-center mb-10">Giving from <?php echo e($c['label']); ?> (<?php echo e($c['currency']); ?>)</h3>

            <?php if (!$c['methods']): ?>
                <div class="max-w-2xl mx-auto text-center bg-obsidian-800/40 p-10 rounded-[2.5rem] border border-white/5">
                    <p class="text-neutral-300 leading-relaxed mb-8">Giving from <?php echo e($c['label']); ?> is being set up. Please contact our finance team and we'll help you give.</p>
                    <?php render_button_primary(['text' => 'Contact Finance Team', 'url' => 'contact?subject=Giving', 'style' => 'light']); ?>
                </div>
            <?php else: ?>
            <div class="grid grid-cols-1 <?= ['', 'max-w-xl mx-auto', 'md:grid-cols-2 max-w-5xl mx-auto', 'md:grid-cols-3'][count($c['methods'])] ?> gap-8">
                <?php foreach ($c['methods'] as $m): ?>
                <div class="bg-obsidian-800/40 p-10 lg:p-12 rounded-[2.5rem] shadow-glass border border-white/5 text-center flex flex-col">
                    <div class="w-20 h-20 rounded-[1.5rem] bg-white/5 border border-white/10 text-neutral-300 flex items-center justify-center mx-auto mb-8" aria-hidden="true">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?php echo $icons[$m['icon']]; ?>"/></svg>
                    </div>
                    <h4 class="text-3xl font-display font-black uppercase text-white mb-6 tracking-normal leading-none"><?php echo e($m['title']); ?></h4>
                    <?php if (!empty($m['text'])): ?>
                        <p class="text-neutral-400 font-medium leading-relaxed mb-8 flex-grow"><?php echo e($m['text']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($m['details'])): ?>
                        <div class="bg-obsidian-950/50 border border-white/5 rounded-2xl p-6 flex-grow text-left">
                            <p class="text-neutral-300 font-medium leading-loose whitespace-pre-wrap text-sm select-all"><?php echo e($m['details']); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($m['button'])): ?>
                        <div><?php render_button_primary(['text' => $m['button'][0], 'url' => $m['button'][1], 'style' => 'light']); ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($code === 'US' && $c['methods']): ?>
                <p class="max-w-3xl mx-auto text-center text-sm text-neutral-400 mt-10 leading-relaxed">
                    <?php if ($usEntity !== '' && $usEin !== ''): ?>
                        Gifts from the United States are received by <?php echo e($usEntity); ?>, a registered 501(c)(3) nonprofit (EIN <?php echo e($usEin); ?>), and are tax-deductible to the extent allowed by law. Please keep your receipt for your records.
                    <?php else: ?>
                        Please contact our finance team about receipts for your records.
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <script>
        (function () {
            var tabs = document.getElementById('give-tabs');
            var buttons = tabs.querySelectorAll('.give-tab');
            var panels = document.querySelectorAll('.give-panel');
            var stored = null;
            try { stored = localStorage.getItem('bmi-give-country'); } catch (e) {}
            var guess = '<?php echo $defaultCountry; ?>';
            try {
                var tz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
                if (guess !== 'US' && /^(America\/|US\/|Pacific\/Honolulu)/.test(tz)) guess = 'US';
            } catch (e) {}
            function show(code, remember) {
                buttons.forEach(function (b) {
                    var on = b.dataset.country === code;
                    b.setAttribute('aria-selected', on ? 'true' : 'false');
                    b.tabIndex = on ? 0 : -1;
                });
                panels.forEach(function (p) {
                    p.hidden = p.dataset.country !== code;
                    p.querySelector('.give-panel-heading').classList.add('sr-only');
                });
                if (remember) { try { localStorage.setItem('bmi-give-country', code); } catch (e) {} }
            }
            tabs.classList.remove('hidden');
            tabs.classList.add('flex');
            buttons.forEach(function (b, i) {
                b.addEventListener('click', function () { show(b.dataset.country, true); });
                b.addEventListener('keydown', function (e) {
                    if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
                    var next = buttons[(i + (e.key === 'ArrowRight' ? 1 : buttons.length - 1)) % buttons.length];
                    show(next.dataset.country, true);
                    next.focus();
                });
            });
            show(stored === 'GH' || stored === 'US' ? stored : guess, false);
        })();
        </script>
        <?php endif; ?>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-24 bg-obsidian-950 relative overflow-hidden border-t border-white/5 gs-reveal-section">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-r from-accent/10 to-blue-600/10 opacity-50"></div>
    </div>
    
    <div class="max-w-[112.5rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10 gs-reveal-up w-[90%]">
        <div class="bg-obsidian-800/50 backdrop-blur-xl border border-white/10 p-12 md:p-20 rounded-[3rem] flex flex-col md:flex-row items-center justify-between gap-12 text-center md:text-left shadow-glass relative overflow-hidden">
            <!-- Glass Accents -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-accent/20 blur-[100px] rounded-full"></div>
            
            <div class="relative z-10">
                <h2 class="text-3xl md:text-5xl font-display font-black tracking-normal uppercase text-white mb-6">Have Questions About Giving?</h2>
                <p class="text-neutral-400 font-medium text-lg max-w-xl leading-relaxed">Our finance team is here to help with annual giving statements, asset transfers, and general inquiries.</p>
            </div>
            
            <div class="relative z-10 flex-shrink-0">
                <?php render_button_primary(['text' => 'Contact Finance Team', 'url' => 'contact?subject=Giving', 'style' => 'outline']); ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
