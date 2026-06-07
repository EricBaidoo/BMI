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
<header id="site-header" class="fixed w-full top-0 z-50 transition-all duration-500 border-b border-white/10 bg-slate-950/80 backdrop-blur-lg">
    <!-- Top subtle accent line -->
    <div class="h-1 w-full bg-gradient-to-r from-transparent via-[#c49a45] to-transparent opacity-70"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-24" id="header-inner">
            
            <!-- Logo Area (Left) -->
            <div class="flex-shrink-0 flex items-center">
                <a href="./" class="flex items-center gap-4 group">
                    <div class="relative flex items-center justify-center">
                        <div class="absolute inset-0 bg-[#c49a45] blur-lg opacity-20 group-hover:opacity-40 transition-opacity duration-500 rounded-full"></div>
                        <img class="h-12 w-auto relative z-10 transform group-hover:scale-105 transition-transform duration-500" src="<?php echo setting('site.logo') ? htmlspecialchars(setting('site.logo')) : 'assets/image/bmi%20logo%20new.png'; ?>" alt="BMI Logo" onerror="this.style.display='none';">
                    </div>
                    <span class="font-display font-bold text-xl tracking-wide text-white group-hover:text-[#c49a45] transition-colors whitespace-nowrap">
                        <?php echo htmlspecialchars($siteName ?? 'Bridge Ministries'); ?>
                    </span>
                </a>
            </div>

            <!-- Main Navigation (Center) -->
            <nav class="hidden xl:flex items-center justify-center gap-8">
                <?php
                $navLinks = [
                    'about' => 'About Us',
                    'ministries' => 'Ministries',
                    'sermons' => 'Sermons',
                    'events' => 'Events',
                    'flagship-programs' => 'Flagship',
                    'contact' => 'Contact'
                ];
                foreach ($navLinks as $url => $label):
                    $isActive = ($currentPage === $url || $currentPage === $url . '.php');
                ?>
                <a href="<?php echo $url; ?>" class="relative text-base font-medium transition-colors py-2 <?php echo $isActive ? 'text-[#c49a45]' : 'text-slate-300 hover:text-white group'; ?>">
                    <?php echo $label; ?>
                    <span class="absolute bottom-0 left-0 h-[2px] bg-[#c49a45] transition-all duration-300 <?php echo $isActive ? 'w-full' : 'w-0 group-hover:w-full'; ?>"></span>
                </a>
                <?php endforeach; ?>
            </nav>
                
            <!-- Action Buttons (Right) -->
            <div class="hidden xl:flex flex-shrink-0 items-center gap-5">
                <a href="livestream" class="text-base font-medium text-slate-300 hover:text-[#c49a45] transition-colors flex items-center gap-2 group whitespace-nowrap">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full bg-red-500 opacity-75 rounded-full"></span>
                        <span class="relative inline-flex h-2 w-2 bg-red-500 rounded-full"></span>
                    </span>
                    Live
                </a>
                
                <div class="h-6 w-px bg-white/20"></div>
               
                
                <a href="donate" class="bg-[#c49a45] hover:bg-[#b0883b] text-white font-medium text-base px-6 py-2.5 rounded shadow-[0_0_15px_rgba(196,154,69,0.3)] hover:shadow-[0_0_20px_rgba(196,154,69,0.5)] transition-all duration-300 transform hover:-translate-y-0.5 whitespace-nowrap">
                    Give
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="xl:hidden flex items-center pl-4">
                <button id="mobile-menu-btn" class="p-2 text-slate-300 hover:text-white focus:outline-none transition-colors rounded-md hover:bg-white/5" aria-label="Toggle menu">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Nav -->
    <div id="mobile-menu" class="hidden xl:hidden bg-slate-950/95 backdrop-blur-xl border-t border-white/10 absolute top-full left-0 w-full shadow-2xl">
        <div class="px-6 py-6 flex flex-col gap-4">
            <?php
            foreach ($navLinks as $url => $label):
                $isActive = ($currentPage === $url || $currentPage === $url . '.php');
            ?>
            <a href="<?php echo $url; ?>" class="text-lg font-medium transition-colors <?php echo $isActive ? 'text-[#c49a45]' : 'text-slate-300 hover:text-white'; ?>">
                <?php echo $label; ?>
            </a>
            <?php endforeach; ?>
            
            <div class="h-px w-full bg-white/10 my-2"></div>
            
            <a href="livestream" class="flex items-center gap-3 text-lg font-medium text-slate-300 hover:text-white transition-colors">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full bg-red-500 opacity-75 rounded-full"></span>
                    <span class="relative inline-flex h-3 w-3 bg-red-500 rounded-full"></span>
                </span>
                Watch Live
            </a>
            <a href="visit" class="text-lg font-medium text-slate-300 hover:text-white transition-colors">Plan a Visit</a>
            <a href="donate" class="inline-block text-center mt-2 bg-[#c49a45] text-white font-medium text-lg px-6 py-3 rounded shadow-lg transition-all">Give Online</a>
        </div>
    </div>
</header>

<main class="flex-grow">
