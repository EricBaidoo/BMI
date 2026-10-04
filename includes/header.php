<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/settings.php';

// No session here: pages with forms (contact, visit, livestream) start one themselves via csrf.php.
// Pages without a session can be cached by browsers and a CDN.
if (session_status() !== PHP_SESSION_ACTIVE && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && !headers_sent()) {
    header('Cache-Control: public, max-age=0, s-maxage=300, stale-while-revalidate=60');
}

$siteName        = setting('site.name', $siteName);
$siteDescription = setting('site.description', $siteDescription);
$analyticsDomain = setting('analytics.plausible_domain', $analyticsDomain);

$pageTitle = $pageTitle ?? $siteName;
$pageDescription = $pageDescription ?? $siteDescription;
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');

// Canonical URL: the clean public address of this page, without tracking or UI parameters.
$basePath = rtrim((string) parse_url($siteUrl, PHP_URL_PATH), '/');
$requestPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
    $requestPath = substr($requestPath, strlen($basePath));
}
$requestPath = preg_replace('~\.php$~', '', preg_replace('~/index(\.php)?$~', '/', $requestPath));
parse_str((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY), $requestQuery);
$canonicalQuery = array_intersect_key($requestQuery, array_flip(['id', 'slug', 'page', 'topic']));
$canonicalUrl = $canonicalUrl ?? ($siteUrl . '/' . ltrim($requestPath, '/') . ($canonicalQuery ? '?' . http_build_query($canonicalQuery) : ''));

$absoluteUrl = function (string $path) use ($siteUrl): string {
    return preg_match('~^https?://~i', $path) ? $path : $siteUrl . '/' . ltrim($path, '/');
};
$shareImage = !empty($ogImage) ? $ogImage : setting('site.logo');

// Organisation details for search engines (every page). Pages can add more via $structuredData.
$socialProfiles = array_values(array_filter(array_map(fn ($l) => safe_url($l['url'] ?? ''), json_decode(setting('social.links', '[]'), true) ?: [])));
$churchData = array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Church',
    'name' => $siteName,
    'url' => $siteUrl . '/',
    'description' => $siteDescription,
    'logo' => setting('site.logo') ? $absoluteUrl(setting('site.logo')) : null,
    'telephone' => setting('contact.phone_primary') ?: null,
    'email' => setting('contact.email_general') ?: null,
    'address' => setting('contact.address') ? ['@type' => 'PostalAddress', 'streetAddress' => setting('contact.address'), 'addressCountry' => 'GH'] : null,
    'sameAs' => $socialProfiles ?: null,
]);
$structuredData = array_merge([$churchData], $structuredData ?? []);

// Latest sermon for the menu's featured card.
$menuSermon = null;
try {
    $menuSermon = db_connect()->query("SELECT id, title, sermon_image FROM sermons ORDER BY sermon_date DESC, id DESC LIMIT 1")->fetch() ?: null;
} catch (Throwable $e) {
    log_exception($e, 'header latest sermon');
}

$navLinks = [
    'about' => 'About Us',
    'ministries' => 'Ministries',
    'sermons' => 'Sermons',
    'events' => 'Events',
    'donate' => 'Give',
];
$menuLinks = [
    'visit' => "I'm New",
    'livestream' => 'Watch Live',
    'about' => 'About Us',
    'beliefs' => 'Our Beliefs',
    'ministries' => 'Ministries',
    'sermons' => 'Sermons',
    'events' => 'Events',
    'blog' => 'Blog',
    'donate' => 'Give',
    'contact' => 'Contact',
];
$isLiveNow = setting('live.is_streaming_now') === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <?php if (!empty($noIndex)): ?>
        <meta name="robots" content="noindex">
    <?php endif; ?>

    <!-- Animations only when the visitor has not asked for reduced motion; undone if GSAP never loads. -->
    <script>
        (function (d) {
            if (!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
                d.classList.add('js-anim');
                setTimeout(function () { if (typeof window.gsap === 'undefined') d.classList.remove('js-anim'); }, 4000);
            }
        })(document.documentElement);
    </script>

    <!-- Social sharing -->
    <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="og:type" content="<?php echo htmlspecialchars($ogType ?? 'website'); ?>">
    <?php if ($shareImage): ?>
        <meta property="og:image" content="<?php echo htmlspecialchars($absoluteUrl($shareImage)); ?>">
        <meta name="twitter:image" content="<?php echo htmlspecialchars($absoluteUrl($shareImage)); ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <?php if (setting('site.favicon')): ?>
        <link rel="icon" href="<?php echo setting_url('site.favicon'); ?>">
    <?php endif; ?>

    <?php foreach ($structuredData as $item): ?>
    <script type="application/ld+json"><?php echo json_encode($item, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?></script>
    <?php endforeach; ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@400;600&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/styles.css') ?: time(); ?>">

    <!-- Swiper CSS (pinned version) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11.1.14/swiper-bundle.min.css">

    <!-- GSAP Core -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <?php if (!empty($analyticsDomain)): ?>
        <script defer data-domain="<?php echo htmlspecialchars($analyticsDomain); ?>" src="https://plausible.io/js/script.js"></script>
    <?php endif; ?>
</head>
<body class="bg-brand-950 text-brand-300 font-sans antialiased flex flex-col min-h-screen overflow-x-hidden selection:bg-brand-600 selection:text-white">

<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[200] focus:bg-white focus:text-black focus:px-5 focus:py-3 focus:rounded-full focus:font-bold">Skip to main content</a>

<!-- Global Film Grain Overlay -->
<div class="film-grain" aria-hidden="true"></div>

<!-- HEADER -->
<header id="site-header" class="fixed w-full top-0 z-50 transition-all duration-700 ease-out py-8 header-transparent group">
    <div class="max-w-[112.5rem] w-[90%] mx-auto relative z-50 flex justify-between items-center gap-6">
        <!-- Logo -->
        <a href="./" class="flex-shrink-0 flex items-center z-[100] gap-3">
            <?php
                $logo = setting('site.logo');
                if (!$logo) {
                    $logo = 'assets/image/ui/bmi logo new.png';
                }
            ?>
            <img src="<?= htmlspecialchars(safe_url($logo)) ?>" alt="<?= htmlspecialchars($siteName) ?> home" class="h-10 w-auto object-contain">
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden xl:flex items-center gap-10" aria-label="Main">
            <?php foreach ($navLinks as $url => $label):
                $isActive = ($currentPage === $url . '.php');
            ?>
            <a href="<?php echo $url; ?>" class="relative text-xs font-sans font-bold uppercase tracking-widest-xl text-white/80 hover:text-white transition-colors py-2 nav-link-hover" <?php echo $isActive ? 'aria-current="page"' : ''; ?>>
                <?php echo $label; ?>
                <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-0 h-px bg-accent transition-all duration-300 <?php echo $isActive ? 'w-full' : ''; ?>" aria-hidden="true"></span>
            </a>
            <?php endforeach; ?>
        </nav>

        <!-- Right Actions -->
        <div class="flex items-center gap-5 md:gap-8">
            <a href="livestream" class="flex items-center gap-3 text-xs font-bold text-white uppercase tracking-widest hover:text-accent transition-colors group/live" aria-label="<?php echo $isLiveNow ? 'Watch live now' : 'Watch online'; ?>">
                <span class="relative flex h-2 w-2" aria-hidden="true">
                    <?php if ($isLiveNow): ?><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-75"></span><?php endif; ?>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-accent group-hover/live:bg-white transition-colors"></span>
                </span>
                <span class="hidden sm:inline"><?php echo $isLiveNow ? 'Live now' : 'Live'; ?></span>
            </a>

            <a href="visit" class="hidden md:inline-flex items-center rounded-full bg-white text-black hover:bg-accent hover:text-white px-5 py-2.5 text-xs font-bold uppercase tracking-widest transition-colors">Plan a Visit</a>

            <button type="button" id="mega-menu-trigger" class="flex items-center gap-4 text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-accent rounded-full group/menu" aria-label="Open menu" aria-expanded="false" aria-controls="mega-menu">
                <span class="hidden md:block text-xs font-bold uppercase tracking-widest-xl group-hover/menu:text-accent transition-colors" aria-hidden="true">Menu</span>
                <span class="w-10 h-10 rounded-full border border-white/20 flex flex-col justify-center items-center gap-1.5 group-hover/menu:border-accent group-hover/menu:bg-accent/10 transition-all" aria-hidden="true">
                    <span class="w-4 h-px bg-white group-hover/menu:w-5 transition-all"></span>
                    <span class="w-4 h-px bg-white group-hover/menu:w-3 transition-all"></span>
                </span>
            </button>
        </div>
    </div>
</header>

<!-- FULL SCREEN MENU -->
<div id="mega-menu" class="fixed inset-0 z-[60] bg-obsidian-950/98 backdrop-blur-3xl hidden opacity-0 flex-col justify-center overflow-y-auto" role="dialog" aria-modal="true" aria-label="Site menu">
    <!-- Close Button -->
    <button type="button" id="mega-menu-close" class="absolute top-8 right-[5%] w-12 h-12 rounded-full border border-white/10 flex flex-col justify-center items-center gap-0 text-white hover:bg-white hover:text-black transition-all z-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent" aria-label="Close menu">
        <span class="w-5 h-px bg-current rotate-45 translate-y-[1px]" aria-hidden="true"></span>
        <span class="w-5 h-px bg-current -rotate-45 -translate-y-[1px]" aria-hidden="true"></span>
    </button>

    <div class="max-w-[112.5rem] w-[90%] mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center py-24">
        <!-- Links -->
        <nav aria-label="Site">
            <span class="block text-accent text-xs font-bold uppercase tracking-widest-xl mb-8">Navigation</span>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-4 lg:gap-y-6">
                <?php foreach ($menuLinks as $url => $label): ?>
                <li>
                    <a href="<?php echo $url; ?>" class="mega-link font-display font-black text-3xl md:text-5xl text-white/60 hover:text-white uppercase tracking-tight transition-colors flex items-center gap-4 group w-fit" <?php echo $currentPage === $url . '.php' ? 'aria-current="page"' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                        <svg class="w-8 h-8 opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-500 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <?php if ($menuSermon): ?>
        <!-- Featured Card (latest sermon) -->
        <div class="hidden lg:block relative rounded-[3rem] overflow-hidden aspect-video bg-obsidian-900 border border-white/5 group hover:border-white/20 transition-all">
            <?php if (!empty($menuSermon['sermon_image'])): ?>
                <img src="<?= htmlspecialchars(safe_url($menuSermon['sermon_image'])) ?>" loading="lazy" decoding="async" class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-80 transition-opacity duration-700 group-hover:scale-105" alt="">
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-obsidian-950 to-transparent"></div>
            <div class="absolute bottom-10 left-10 right-10">
                <span class="text-accent text-xs font-bold uppercase tracking-widest mb-2 block">Latest Sermon</span>
                <h2 class="text-3xl font-display font-black text-white uppercase leading-none mb-6"><?= htmlspecialchars($menuSermon['title']) ?></h2>
                <a href="sermon?id=<?= (int) $menuSermon['id'] ?>" class="inline-flex items-center gap-3 bg-white text-black px-6 py-3 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-accent hover:text-white transition-colors">
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                    Watch Now
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<main id="main-content" tabindex="-1" class="flex-grow pt-0 focus:outline-none">

<script>
document.addEventListener('DOMContentLoaded', () => {
    const trigger = document.getElementById('mega-menu-trigger');
    const close = document.getElementById('mega-menu-close');
    const menu = document.getElementById('mega-menu');
    const links = menu.querySelectorAll('.mega-link');
    const animate = typeof gsap !== 'undefined' && !document.documentElement.classList.contains('reduce-motion')
        && !(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
    let isMenuOpen = false;

    const focusable = () => Array.from(menu.querySelectorAll('a[href], button')).filter(el => el.offsetParent !== null);

    const openMenu = () => {
        isMenuOpen = true;
        menu.classList.remove('hidden');
        menu.classList.add('flex');
        document.body.style.overflow = 'hidden';
        trigger.setAttribute('aria-expanded', 'true');
        if (animate) {
            gsap.to(menu, { opacity: 1, duration: 0.5, ease: "power2.out" });
            gsap.fromTo(links, { y: 50, opacity: 0 }, { y: 0, opacity: 1, duration: 0.7, stagger: 0.06, ease: "power3.out", delay: 0.2 });
        } else {
            menu.style.opacity = 1;
        }
        close.focus();
    };

    const closeMenu = () => {
        if (!isMenuOpen) return;
        isMenuOpen = false;
        document.body.style.overflow = '';
        trigger.setAttribute('aria-expanded', 'false');
        const done = () => { menu.classList.add('hidden'); menu.classList.remove('flex'); };
        if (animate) {
            gsap.to(menu, { opacity: 0, duration: 0.4, ease: "power2.in", onComplete: done });
        } else {
            menu.style.opacity = 0;
            done();
        }
        trigger.focus();
    };

    trigger.addEventListener('click', openMenu);
    close.addEventListener('click', closeMenu);
    links.forEach(l => l.addEventListener('click', closeMenu));

    // Escape closes the menu; Tab stays inside it while open.
    document.addEventListener('keydown', (e) => {
        if (!isMenuOpen) return;
        if (e.key === 'Escape') {
            closeMenu();
        } else if (e.key === 'Tab') {
            const items = focusable();
            if (!items.length) return;
            const first = items[0], last = items[items.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        }
    });
});
</script>
