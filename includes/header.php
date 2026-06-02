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
    <?php if (setting('site.favicon')): ?>
        <link rel="icon" href="<?php echo htmlspecialchars(setting('site.favicon')); ?>">
    <?php endif; ?>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Preconnect for external assets -->
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="preconnect" href="https://source.unsplash.com">
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/styles.css') ?: time(); ?>">

    <!-- AOS Animation CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <style>
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            animation: marquee 20s linear infinite;
        }
        
        /* Global Typography Alignments */
        p { text-align: justify !important; }
        
        @media (max-width: 767px) {
            h1, h2 { text-align: center !important; }
            /* Center section intro labels on mobile */
            .inline-flex.items-center.px-4.py-2.bg-white { 
                display: flex !important; 
                margin-left: auto !important; 
                margin-right: auto !important; 
                width: max-content !important;
            }
        }
    </style>

    <?php if (!empty($analyticsDomain)): ?>
        <script defer data-domain="<?php echo htmlspecialchars($analyticsDomain); ?>" src="https://plausible.io/js/script.js"></script>
    <?php endif; ?>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</head>
<body class="bg-slate-900 text-slate-300 font-sans antialiased flex flex-col min-h-screen">

<!-- HEADER -->
<header id="site-header" class="fixed w-full top-0 z-50 transition-all duration-500 border-b border-white/5 bg-slate-950/70 backdrop-blur-xl supports-[backdrop-filter]:bg-slate-950/40">
    <!-- Top subtle accent line -->
    <div class="h-[0.125rem] w-full bg-gradient-to-r from-transparent via-[#c49a45]/50 to-transparent"></div>
    
    <div class="w-[90%] max-w-[112.5rem] mx-auto">
        <div class="flex justify-between items-center h-20 md:h-24 transition-all duration-500 gap-8" id="header-inner">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center group cursor-pointer">
                <a href="./" class="flex items-center gap-3">
                    <div class="relative">
                        <div class="absolute inset-0 bg-[#c49a45] blur-md opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
                        <img class="h-10 md:h-12 w-auto relative z-10 transform group-hover:scale-105 transition-transform duration-500" src="<?php echo setting('site.logo') ? htmlspecialchars(setting('site.logo')) : 'assets/image/bmi%20logo%20new.png'; ?>" alt="BMI Logo" onerror="this.style.display='none';">
                    </div>
                    <span class="font-display font-black text-xl lg:text-2xl tracking-tight text-white group-hover:text-slate-200 transition-colors whitespace-nowrap"><?php echo htmlspecialchars($siteName ?? 'Bridge Ministries'); ?></span>
                </a>
            </div>

            <!-- Desktop Nav Links (Centered) -->
            <nav class="hidden xl:flex items-center justify-center flex-1 gap-10 2xl:gap-14">
                <a href="about" class="relative text-[0.875rem] 2xl:text-[0.9375rem] font-bold text-slate-300 hover:text-white transition-colors group py-2 whitespace-nowrap">
                    About Us
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="ministries" class="relative text-[0.875rem] 2xl:text-[0.9375rem] font-bold text-slate-300 hover:text-white transition-colors group py-2 whitespace-nowrap">
                    Ministries
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="sermons" class="relative text-[0.875rem] 2xl:text-[0.9375rem] font-bold text-slate-300 hover:text-white transition-colors group py-2 whitespace-nowrap">
                    Sermons
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="events" class="relative text-[0.875rem] 2xl:text-[0.9375rem] font-bold text-slate-300 hover:text-white transition-colors group py-2 whitespace-nowrap">
                    Events
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="flagship-programs" class="relative text-[0.875rem] 2xl:text-[0.9375rem] font-bold text-slate-300 hover:text-white transition-colors group py-2 whitespace-nowrap">
                    Flagship Programs
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="contact" class="relative text-[0.875rem] 2xl:text-[0.9375rem] font-bold text-slate-300 hover:text-white transition-colors group py-2 whitespace-nowrap">
                    Contact
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-full"></span>
                </a>
            </nav>
                
            <!-- Action Buttons (Right) -->
            <div class="hidden xl:flex flex-shrink-0 items-center gap-8">
                <a href="livestream" class="relative text-[0.75rem] 2xl:text-[0.8125rem] font-black text-white hover:text-red-400 transition-colors flex items-center gap-3 uppercase tracking-widest group whitespace-nowrap">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full bg-red-500 opacity-75"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 bg-red-500"></span>
                    </span>
                    Watch Live
                </a>
                
                <div class="flex items-center gap-4 border-l border-white/10 pl-8">
                    <a href="visit" class="relative text-[0.75rem] 2xl:text-[0.8125rem] font-bold text-white hover:text-slate-900 border border-white/20 hover:bg-white px-6 py-3.5 transition-all duration-300 uppercase tracking-widest whitespace-nowrap">
                        Plan a Visit
                    </a>
                    <a href="donate" class="relative group overflow-hidden bg-gradient-to-r from-[#c49a45] to-[#d4ac57] text-white font-bold uppercase tracking-widest text-[0.75rem] 2xl:text-[0.8125rem] px-6 py-3.5 shadow-[0_0_1.25rem_rgba(196,154,69,0.3)] hover:shadow-[0_0_1.875rem_rgba(196,154,69,0.6)] transition-all duration-300 hover:-translate-y-0.5 whitespace-nowrap">
                        <span class="relative z-10">Give Online</span>
                        <div class="absolute inset-0 h-full w-full scale-0 transition-all duration-300 group-hover:scale-100 group-hover:bg-white/20 z-0"></div>
                    </a>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden flex items-center">
                <button id="mobile-menu-btn" class="p-2 -mr-2 text-slate-400 hover:text-white focus:outline-none transition-colors" aria-label="Toggle menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Nav -->
    <div id="mobile-menu" class="hidden lg:hidden bg-slate-900 border-t border-slate-800 shadow-xl absolute top-full left-0 w-full">
        <div class="px-4 py-6 space-y-4">
            <a href="about" class="block text-slate-300 hover:text-white font-medium px-2 py-2 rounded-md transition-colors">About Us</a>
            <a href="ministries" class="block text-slate-300 hover:text-white font-medium px-2 py-2 rounded-md transition-colors">Ministries</a>
            <a href="sermons" class="block text-slate-300 hover:text-white font-medium px-2 py-2 rounded-md transition-colors">Sermons</a>
            <a href="events" class="block text-slate-300 hover:text-white font-medium px-2 py-2 rounded-md transition-colors">Events</a>
            <a href="flagship-programs" class="block text-slate-300 hover:text-white font-medium px-2 py-2 rounded-md transition-colors">Flagship Programs</a>
            <a href="contact" class="block text-slate-300 hover:text-white font-medium px-2 py-2 rounded-md transition-colors">Contact</a>
            <a href="livestream" class="flex items-center gap-2 text-slate-300 hover:text-white font-medium px-2 py-2 rounded-md transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Watch Live
            </a>
            <div class="pt-4 pb-2 space-y-3">
                <a href="visit" class="block w-full text-center border border-slate-700 text-white hover:bg-slate-800 text-sm font-bold px-8 py-3.5 rounded-sm transition-all">Plan a Visit</a>
                <a href="donate" class="block w-full text-center bg-[#c49a45] text-white hover:bg-[#d4ac57] text-sm font-bold px-8 py-3.5 rounded-sm transition-all shadow-md">Give Online</a>
            </div>
        </div>
    </div>
</header>

<main class="flex-grow">
