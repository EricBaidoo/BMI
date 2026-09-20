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

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Swup.js -->
    <script src="https://unpkg.com/swup@4"></script>

    <?php if (!empty($analyticsDomain)): ?>
        <script defer data-domain="<?php echo htmlspecialchars($analyticsDomain); ?>" src="https://plausible.io/js/script.js"></script>
    <?php endif; ?>
</head>
<body class="bg-brand-950 text-brand-300 font-sans antialiased flex flex-col min-h-screen overflow-x-hidden selection:bg-brand-600 selection:text-white">

<!-- Global Film Grain Overlay -->
<div class="film-grain"></div>

<!-- HEADER -->
<header 
    id="site-header" 
    class="fixed w-full top-0 z-50 transition-all duration-500 ease-out bg-transparent py-8">
    
    <!-- Permanent subtle gradient to protect text when header is transparent -->
    <div id="header-gradient" class="absolute inset-0 bg-gradient-to-b from-black/80 to-transparent pointer-events-none z-[-1] transition-opacity duration-500"></div>

    <div class="max-w-[112.5rem] w-[90%] mx-auto relative z-50">
        <div class="flex justify-between items-center transition-all duration-500" id="header-inner">
            
            <!-- Logo Area -->
            <div class="flex-shrink-0 flex items-center">
                <a href="./" class="flex items-center gap-4 group">
                    <img class="h-8 md:h-10 w-auto transform group-hover:scale-105 transition-transform duration-700 ease-out" src="<?php echo setting('site.logo') ? htmlspecialchars(setting('site.logo')) : 'assets/image/bmi%20logo%20new.png'; ?>" alt="BMI Logo" onerror="this.style.display='none';">
                    <span class="hidden md:block font-sans font-semibold tracking-[0.1em] text-xs md:text-sm text-white uppercase group-hover:text-brand-300 transition-colors">
                        Bridge Ministries
                    </span>
                </a>
            </div>

            <!-- Main Navigation -->
            <nav class="hidden xl:flex items-center justify-center gap-10">
                <?php
                $navLinks = [
                    'about' => 'About Us',
                    'ministries' => 'Ministries & Services',
                    'sermons' => 'Sermons',
                    'events' => 'Events',
                    'flagship-programs' => 'Flagship',
                    'contact' => 'Contact'
                ];
                foreach ($navLinks as $url => $label):
                    $isActive = ($currentPage === $url || $currentPage === $url . '.php');
                ?>
                <a href="<?php echo $url; ?>" class="nav-link relative text-[0.6875rem] font-medium uppercase tracking-[0.15em] transition-colors py-2 group">
                    <?php echo $label; ?>
                    <?php if ($isActive): ?>
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-white rounded-full shadow-[0_0_8px_rgba(255,255,255,0.8)]"></span>
                    <?php else: ?>
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-0 h-1 bg-white rounded-full transition-all duration-300 group-hover:w-1 opacity-0 group-hover:opacity-100"></span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </nav>
                
            <!-- Action Buttons -->
            <div class="hidden xl:flex flex-shrink-0 items-center gap-8">
                <a href="livestream" class="text-[0.6875rem] font-medium text-white hover:text-accent transition-colors flex items-center gap-2 uppercase tracking-[0.15em]">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full bg-accent opacity-75 rounded-full"></span>
                        <span class="relative inline-flex h-1.5 w-1.5 bg-accent rounded-full"></span>
                    </span>
                    Live
                </a>
                
                <a href="donate" class="btn-give px-8 py-3 rounded-none text-[0.6875rem] font-bold uppercase tracking-[0.15em] transition-transform hover:-translate-y-0.5">
                    Give
                </a>
            </div>

            <div class="xl:hidden flex items-center">
                <button id="mobile-menu-btn" class="text-white hover:text-brand-300 focus:outline-none transition-colors" aria-label="Toggle menu">
                    <svg id="icon-menu" style="display: block;" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="icon-close" style="display: none;" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Nav -->
    <div 
        id="mobile-nav-container"
        class="fixed inset-0 z-40 bg-brand-950/98 backdrop-blur-2xl xl:hidden flex-col justify-center items-center h-screen pt-20 transition-all duration-300 transform -translate-y-full opacity-0 pointer-events-none flex">
        
        <div class="flex flex-col gap-6 text-center w-full px-6">
            <?php foreach ($navLinks as $url => $label): ?>
            <a href="<?php echo $url; ?>" class="mobile-nav-link text-xl font-display font-medium text-white/70 hover:text-white transition-colors">
                <?php echo $label; ?>
            </a>
            <?php endforeach; ?>
            
            <div class="h-px w-12 bg-white/10 mx-auto my-4"></div>
            
            <a href="livestream" class="mobile-nav-link text-lg font-sans font-medium text-white hover:text-accent transition-colors flex items-center justify-center gap-3 uppercase tracking-widest">
                <span class="w-2 h-2 bg-accent rounded-full animate-pulse"></span>
                Watch Live
            </a>
            <a href="donate" class="mobile-nav-link mt-4 btn-give px-10 py-4 text-sm tracking-[0.2em]">Give Online</a>
        </div>
    </div>
</header>

<main id="swup" class="flex-grow transition-fade">

<script>
document.addEventListener('DOMContentLoaded', () => {
    const header = document.getElementById('site-header');
    const headerGradient = document.getElementById('header-gradient');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const iconMenu = document.getElementById('icon-menu');
    const iconClose = document.getElementById('icon-close');
    const mobileNavContainer = document.getElementById('mobile-nav-container');
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
    
    let isMenuOpen = false;

    // Scroll Handler
    function handleScroll() {
        if (window.scrollY > 20) {
            header.classList.remove('bg-transparent', 'py-8');
            header.classList.add('bg-[#050505]/80', 'backdrop-blur-xl', 'py-4', 'shadow-2xl', 'border-b', 'border-white/5');
            headerGradient.classList.add('opacity-0');
        } else {
            header.classList.add('bg-transparent', 'py-8');
            header.classList.remove('bg-[#050505]/80', 'backdrop-blur-xl', 'py-4', 'shadow-2xl', 'border-b', 'border-white/5');
            headerGradient.classList.remove('opacity-0');
        }
    }
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Initialize on load

    // Mobile Menu Toggle
    function toggleMenu() {
        isMenuOpen = !isMenuOpen;
        if (isMenuOpen) {
            iconMenu.style.display = 'none';
            iconClose.style.display = 'block';
            
            mobileNavContainer.classList.remove('-translate-y-full', 'opacity-0', 'pointer-events-none');
            mobileNavContainer.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
            document.body.style.overflow = 'hidden';
        } else {
            iconMenu.style.display = 'block';
            iconClose.style.display = 'none';
            
            mobileNavContainer.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
            mobileNavContainer.classList.add('-translate-y-full', 'opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
        }
    }

    mobileMenuBtn.addEventListener('click', toggleMenu);
    
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (isMenuOpen) toggleMenu();
        });
    });
});
</script>
