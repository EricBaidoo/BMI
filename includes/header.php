<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/settings.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$siteName        = setting('site.name', $siteName);
$siteDescription = setting('site.description', $siteDescription);
$analyticsDomain = setting('analytics.plausible_domain', $analyticsDomain);

$pageTitle = $pageTitle ?? $siteName;
$pageDescription = $pageDescription ?? $siteDescription;
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');

$canonicalUrl = $siteUrl . '/' . ltrim($_SERVER['REQUEST_URI'] ?? '/', '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    
    <!-- OpenGraph SEO Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="og:type" content="<?php echo htmlspecialchars($ogType ?? 'website'); ?>">
    <?php if (!empty($ogImage)): ?>
        <meta property="og:image" content="<?php echo htmlspecialchars(strpos($ogImage, 'http') === 0 ? $ogImage : $siteUrl . '/' . ltrim($ogImage, '/')); ?>">
    <?php elseif (setting('site.logo')): ?>
        <meta property="og:image" content="<?php echo htmlspecialchars(strpos(setting('site.logo'), 'http') === 0 ? setting('site.logo') : $siteUrl . '/' . ltrim(setting('site.logo'), '/')); ?>">
    <?php endif; ?>
    <?php if (setting('site.favicon')): ?>
        <link rel="icon" href="<?php echo htmlspecialchars(setting('site.favicon')); ?>">
    <?php endif; ?>
    
    <!-- Google Fonts: Inter, Outfit, Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@400;600&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <!-- Preconnect for external assets -->
    <link rel="preconnect" href="https://images.unsplash.com">
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/styles.css') ?: time(); ?>">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Swup.js -->
    <script src="https://unpkg.com/swup@4"></script>

    <!-- GSAP Core -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <?php if (!empty($analyticsDomain)): ?>
        <script defer data-domain="<?php echo htmlspecialchars($analyticsDomain); ?>" src="https://plausible.io/js/script.js"></script>
    <?php endif; ?>
</head>
<body class="bg-brand-950 text-brand-300 font-sans antialiased flex flex-col min-h-screen overflow-x-hidden selection:bg-brand-600 selection:text-white">

<!-- Global Film Grain Overlay -->
<div class="film-grain"></div>

<!-- HEADER -->
<header id="site-header" class="fixed w-full top-0 z-50 transition-all duration-700 ease-out py-8 header-transparent group">
    <div class="max-w-[112.5rem] w-[90%] mx-auto relative z-50 flex justify-between items-center">
        <!-- Logo -->
        <a href="index.php" class="flex-shrink-0 flex items-center z-[100] gap-3">
            <?php 
                $logo = setting('site.logo');
                if (!$logo) {
                    $logo = 'assets/image/ui/bmi logo new.png';
                }
            ?>
            <img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars(setting('site.title')) ?>" class="h-10 w-auto object-contain">
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden xl:flex items-center gap-12">
            <?php
            $navLinks = [
                'about' => 'About Us',
                'ministries' => 'Ministries',
                'sermons' => 'Sermons',
                'events' => 'Events',
                'donate' => 'Give'
            ];
            foreach ($navLinks as $url => $label):
                $isActive = ($currentPage === $url || $currentPage === $url . '.php');
            ?>
            <a href="<?php echo $url; ?>" class="relative text-[0.65rem] font-sans font-bold uppercase tracking-widest-xl text-white/70 hover:text-white transition-colors py-2 nav-link-hover">
                <?php echo $label; ?>
                <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-0 h-px bg-accent transition-all duration-300 <?php echo $isActive ? 'w-full' : ''; ?>"></span>
            </a>
            <?php endforeach; ?>
        </nav>

        <!-- Right Actions -->
        <div class="flex items-center gap-8">
            <a href="livestream" class="hidden md:flex items-center gap-3 text-[0.65rem] font-bold text-white uppercase tracking-widest hover:text-accent transition-colors group/live">
                <div class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-accent group-hover/live:bg-white transition-colors"></span>
                </div>
                Live
            </a>
            
            <button id="mega-menu-trigger" class="flex items-center gap-4 text-white focus:outline-none group/menu xl:hidden">
                <span class="hidden md:block text-[0.65rem] font-bold uppercase tracking-widest-xl group-hover/menu:text-accent transition-colors">Menu</span>
                <div class="w-10 h-10 rounded-full border border-white/20 flex flex-col justify-center items-center gap-1.5 group-hover/menu:border-accent group-hover/menu:bg-accent/10 transition-all">
                    <span class="w-4 h-px bg-white group-hover/menu:w-5 transition-all"></span>
                    <span class="w-4 h-px bg-white group-hover/menu:w-3 transition-all"></span>
                </div>
            </button>
        </div>
    </div>
</header>

<!-- FULL SCREEN MEGA MENU -->
<div id="mega-menu" class="fixed inset-0 z-[60] bg-obsidian-950/98 backdrop-blur-3xl hidden opacity-0 flex-col justify-center">
    <!-- Close Button -->
    <button id="mega-menu-close" class="absolute top-8 right-[5%] w-12 h-12 rounded-full border border-white/10 flex flex-col justify-center items-center gap-0 hover:bg-white hover:text-black transition-all z-50">
        <span class="w-5 h-px bg-current rotate-45 translate-y-[1px]"></span>
        <span class="w-5 h-px bg-current -rotate-45 -translate-y-[1px]"></span>
    </button>

    <div class="max-w-[112.5rem] w-[90%] mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <!-- Links -->
        <div class="flex flex-col gap-6 lg:gap-8">
            <span class="text-accent text-[0.65rem] font-bold uppercase tracking-widest-xl mb-4">Navigation</span>
            <?php foreach ($navLinks as $url => $label): ?>
                <a href="<?php echo $url; ?>" class="mega-link font-display font-black text-5xl md:text-7xl text-white/50 hover:text-white uppercase tracking-tight transition-colors flex items-center gap-6 group w-fit">
                    <?php echo $label; ?>
                    <svg class="w-10 h-10 opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-500 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            <?php endforeach; ?>
        </div>
        
        <!-- Featured Card (Media/Sermon) -->
        <div class="hidden lg:block relative rounded-[3rem] overflow-hidden aspect-video bg-obsidian-900 border border-white/5 group hover:border-white/20 transition-all">
            <img src="https://images.unsplash.com/photo-1438283173091-5dbf5c5a3206?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-80 transition-opacity duration-700 group-hover:scale-105" alt="Latest Sermon">
            <div class="absolute inset-0 bg-gradient-to-t from-obsidian-950 to-transparent"></div>
            <div class="absolute bottom-10 left-10 right-10">
                <span class="text-accent text-xs font-bold uppercase tracking-widest mb-2 block">Latest Sermon</span>
                <h3 class="text-3xl font-display font-black text-white uppercase leading-none mb-6">Faith In The Fire</h3>
                <a href="sermons" class="inline-flex items-center gap-3 bg-white text-black px-6 py-3 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-accent hover:text-white transition-colors">
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    Watch Now
                </a>
            </div>
        </div>
    </div>
</div>

<main id="swup" class="flex-grow transition-fade pt-0">

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Mega Menu Logic
    const trigger = document.getElementById('mega-menu-trigger');
    const close = document.getElementById('mega-menu-close');
    const menu = document.getElementById('mega-menu');
    const links = document.querySelectorAll('.mega-link');
    
    let isMenuOpen = false;
    
    const openMenu = () => {
        isMenuOpen = true;
        menu.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        gsap.to(menu, { opacity: 1, duration: 0.5, ease: "power2.out" });
        gsap.fromTo(links, 
            { y: 50, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.7, stagger: 0.1, ease: "power3.out", delay: 0.2 }
        );
    };
    
    const closeMenu = () => {
        isMenuOpen = false;
        document.body.style.overflow = '';
        
        gsap.to(menu, { opacity: 0, duration: 0.5, ease: "power2.in", onComplete: () => {
            menu.classList.add('hidden');
        }});
    };
    
    trigger.addEventListener('click', openMenu);
    close.addEventListener('click', closeMenu);
    links.forEach(l => l.addEventListener('click', closeMenu));
});
</script>
