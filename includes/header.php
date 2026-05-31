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
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">

    <?php if (!empty($analyticsDomain)): ?>
        <script defer data-domain="<?php echo htmlspecialchars($analyticsDomain); ?>" src="https://plausible.io/js/script.js"></script>
    <?php endif; ?>
</head>
<body class="bg-slate-900 text-slate-300 font-sans antialiased flex flex-col min-h-screen">

<!-- HEADER -->
<header id="site-header" class="fixed w-full top-0 z-50 transition-all duration-300 bg-slate-900/95 border-b border-slate-800 shadow-sm backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20 md:h-24 transition-all duration-300" id="header-inner">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="./" class="flex items-center gap-3">
                    <img class="h-10 md:h-12 w-auto" src="assets/image/bmi%20logo%20new.png" alt="BMI Logo" onerror="this.style.display='none';">
                    <span class="font-display font-bold text-xl md:text-2xl tracking-tight text-white">Bridge Ministries</span>
                </a>
            </div>

            <!-- Desktop Nav -->
            <nav class="hidden lg:flex items-center" style="gap: 1.5rem;">
                <a href="about" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">About Us</a>
                <a href="ministries" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Ministries</a>
                <a href="sermons" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Sermons</a>
                <a href="events" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Events</a>
                <a href="flagship-programs" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Flagship Programs</a>
                <a href="contact" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Contact</a>
                <a href="livestream" class="text-sm font-medium text-slate-400 hover:text-white transition-colors flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Watch Live
                </a>
                
                <div class="flex items-center gap-3 ml-2 xl:ml-4">
                    <a href="visit" class="text-sm font-bold text-white hover:text-[#c49a45] transition-colors">
                        Plan a Visit
                    </a>
                    <a href="donate" class="bg-[#c49a45] text-white hover:bg-[#d4ac57] text-sm font-bold px-6 py-3 rounded-sm transition-all shadow-md">
                        Give Online
                    </a>
                </div>
            </nav>

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
