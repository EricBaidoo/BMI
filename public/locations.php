<?php
$pageTitle = 'Locations | Bridge Ministries International';
$pageDescription = 'Find a Bridge Ministries International church near you in Ghana and the United States, or join us online from anywhere.';

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/settings.php';

$countryNames = ['GH' => 'Ghana', 'US' => 'United States', 'GB' => 'United Kingdom', 'CA' => 'Canada', 'NG' => 'Nigeria', 'ZZ' => ''];

$locations = [];
$locationsError = '';
try {
    $locations = db_connect()->query('SELECT * FROM locations ORDER BY sort_order ASC, name ASC')->fetchAll();
} catch (Throwable $e) {
    log_exception($e, 'locations');
    $locationsError = 'Our locations are temporarily unavailable. Please check back shortly.';
}

// Group by country, keeping Ghana first, then the USA, then the rest in order of appearance.
$byCountry = [];
foreach ($locations as $loc) {
    $byCountry[$loc['country']][] = $loc;
}
uksort($byCountry, fn ($a, $b) => (array_search($a, ['GH', 'US'], true) === false ? 9 : array_search($a, ['GH', 'US'], true))
    <=> (array_search($b, ['GH', 'US'], true) === false ? 9 : array_search($b, ['GH', 'US'], true)));

// Each location as a Church in search results.
$structuredData = [];
foreach ($locations as $loc) {
    $structuredData[] = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Church',
        'name' => setting('site.name', 'Bridge Ministries International') . ' — ' . $loc['name'],
        'url' => $siteUrl . '/locations#location-' . (int) $loc['id'],
        'telephone' => $loc['phone'] ? phone_tel((string) $loc['phone'], (string) $loc['country']) : null,
        'email' => $loc['email'] ?: null,
        'address' => $loc['address'] ? array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => (string) $loc['address'],
            'addressLocality' => $loc['city'] ?: null,
            'addressCountry' => $loc['country'] !== 'ZZ' ? $loc['country'] : null,
        ]) : null,
    ]);
}

include __DIR__ . '/../includes/header.php';
?>

<!-- HERO SECTION -->
<section class="relative pt-32 pb-20 md:pt-48 md:pb-28 bg-[#000000] overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0B] via-[#000000] to-[#0A0A0B]"></div>
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10 text-center">
        <span class="block text-white/60 font-bold text-sm tracking-widest uppercase mb-6">One Church, Many Places</span>
        <h1 class="text-4xl md:text-7xl font-display font-black text-white mb-6 tracking-normal leading-tight">
            Find a <i class="text-amber-500 font-light">Location</i>
        </h1>
        <p class="text-lg md:text-xl text-white/50 max-w-3xl mx-auto font-light leading-relaxed">
            Worship with us in Ghana, in the United States, or online from wherever you are.
            Service times are shown in each branch's local time, with your own time alongside.
        </p>
    </div>
</section>

<section class="py-20 md:py-28 bg-[#0A0A0B] relative">
    <div class="w-[90%] max-w-[112.5rem] mx-auto space-y-20">

        <?php if ($locationsError !== ''): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-300 p-8 rounded-[2rem] text-center font-bold"><?php echo e($locationsError); ?></div>
        <?php endif; ?>

        <?php foreach ($byCountry as $country => $countryLocations): ?>
        <div>
            <?php if (($countryNames[$country] ?? '') !== ''): ?>
                <h2 class="text-3xl md:text-5xl font-display font-black text-white uppercase tracking-normal mb-10"><?php echo e($countryNames[$country]); ?></h2>
            <?php endif; ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                <?php foreach ($countryLocations as $loc):
                    $tel = $loc['phone'] ? phone_tel((string) $loc['phone'], (string) $loc['country']) : '';
                    $map = trim((string) ($loc['map_query'] ?: $loc['address']));
                    $image = safe_url((string) $loc['location_image']);
                ?>
                <article id="location-<?php echo (int) $loc['id']; ?>" class="bg-white/[0.03] border border-white/10 rounded-[2rem] overflow-hidden flex flex-col scroll-mt-32">
                    <?php if ($image !== ''): ?>
                        <img src="<?php echo e($image); ?>" alt="" loading="lazy" decoding="async" class="w-full h-52 object-cover">
                    <?php endif; ?>
                    <div class="p-8 flex flex-col gap-6 flex-1">
                        <div>
                            <h3 class="text-2xl font-display font-black text-white uppercase leading-tight"><?php echo e($loc['name']); ?></h3>
                            <?php if ($loc['city']): ?><p class="text-amber-500 text-sm font-bold uppercase tracking-widest mt-2"><?php echo e($loc['city']); ?></p><?php endif; ?>
                        </div>

                        <?php if (trim((string) $loc['service_times']) !== ''): ?>
                        <ul class="space-y-2 text-neutral-300">
                            <?php foreach (preg_split('/\R/', trim((string) $loc['service_times'])) as $line): ?>
                                <?php if (trim($line) !== ''): ?><li><?php echo time_with_local(trim($line), (string) $loc['timezone']); ?></li><?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>

                        <?php if ($loc['address']): ?>
                            <p class="text-neutral-400 whitespace-pre-line"><?php echo e($loc['address']); ?></p>
                        <?php endif; ?>

                        <div class="mt-auto flex flex-wrap gap-3 pt-2">
                            <?php if ($map !== ''): ?>
                                <a href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo rawurlencode($map); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-full bg-white text-black hover:bg-amber-500 px-5 py-2.5 text-xs font-bold uppercase tracking-widest transition-colors">Directions</a>
                            <?php endif; ?>
                            <?php if ($tel !== ''): ?>
                                <a href="tel:<?php echo e($tel); ?>" class="inline-flex items-center rounded-full border border-white/20 text-white hover:bg-white hover:text-black px-5 py-2.5 text-xs font-bold uppercase tracking-widest transition-colors">Call <?php echo e($loc['phone']); ?></a>
                            <?php endif; ?>
                            <?php if ($loc['email']): ?>
                                <a href="mailto:<?php echo e($loc['email']); ?>" class="inline-flex items-center rounded-full border border-white/20 text-white hover:bg-white hover:text-black px-5 py-2.5 text-xs font-bold uppercase tracking-widest transition-colors">Email</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Online campus -->
        <div class="rounded-[2rem] border border-amber-500/30 bg-gradient-to-r from-amber-500/10 to-transparent p-8 md:p-12 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div>
                <h2 class="text-3xl md:text-4xl font-display font-black text-white uppercase">Online, Anywhere</h2>
                <p class="text-neutral-300 mt-3 max-w-2xl">No branch near you? Join every Sunday service live online, follow along with sermon notes, and catch up on past services whenever it suits your time zone.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="livestream" class="inline-flex items-center rounded-full bg-amber-500 text-black hover:bg-white px-6 py-3 text-xs font-bold uppercase tracking-widest transition-colors">Watch Live</a>
                <a href="contact" class="inline-flex items-center rounded-full border border-white/20 text-white hover:bg-white hover:text-black px-6 py-3 text-xs font-bold uppercase tracking-widest transition-colors">Start a fellowship near you</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
