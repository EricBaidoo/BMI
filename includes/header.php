<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';
$pageTitle = $pageTitle ?? $siteName;
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$isHomePage = $currentPage === 'index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="site-body text-slate-800">
<a href="#main-content" class="skip-link">Skip to main content</a>
<header class="site-header <?php echo $isHomePage ? 'home-overlay-header' : ''; ?>">
    <div class="max-w-6xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between md:hidden">
            <a href="index.php" class="brand-mark text-base"><?php echo htmlspecialchars($siteName); ?></a>
            <button id="mobileMenuButton" type="button" class="menu-button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="mobileMenu">
                Menu
            </button>
        </div>

        <div class="hidden md:grid md:grid-cols-[1fr_auto_1fr] md:items-center md:gap-5">
            <nav class="flex items-center gap-2 text-sm font-semibold header-split-nav" aria-label="Primary navigation left">
                <a href="index.php" class="nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Home</a>
                <a href="about.php" class="nav-link <?php echo $currentPage === 'about.php' ? 'active' : ''; ?>">About</a>
                <a href="events.php" class="nav-link <?php echo $currentPage === 'events.php' ? 'active' : ''; ?>">Events</a>
                <a href="sermons.php" class="nav-link <?php echo $currentPage === 'sermons.php' ? 'active' : ''; ?>">Sermons</a>
            </nav>

            <a href="index.php" class="wordmark" aria-label="Bridge Ministries International Home">
                <svg class="wordmark-logo" viewBox="0 0 120 42" aria-hidden="true" focusable="false">
                    <path d="M60 5v30" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    <path d="M48 16h24" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    <path d="M12 34c7-12 13-18 19-24 6 6 12 12 18 24" fill="none" stroke="currentColor" stroke-width="2"/>
                    <path d="M71 34c6-11 12-17 17-22 6 5 11 11 18 22" fill="none" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span class="wordmark-top">Bridge Ministries</span>
                <span class="wordmark-sub">International</span>
            </a>

            <nav class="flex items-center justify-end gap-2 text-sm font-semibold header-split-nav" aria-label="Primary navigation right">
                <a href="contact.php" class="nav-link <?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">Contact</a>
                <a href="donate.php" class="nav-link <?php echo $currentPage === 'donate.php' ? 'active' : ''; ?>">Give</a>
                <a href="blog.php" class="nav-link <?php echo $currentPage === 'blog.php' ? 'active' : ''; ?>">Good News</a>
            </nav>
        </div>
    </div>

    <nav id="mobileMenu" class="mobile-menu hidden md:hidden" aria-label="Mobile navigation">
        <div class="max-w-6xl mx-auto px-4 pb-4 grid grid-cols-2 gap-2 text-sm">
            <a href="index.php" class="nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="about.php" class="nav-link <?php echo $currentPage === 'about.php' ? 'active' : ''; ?>">About</a>
            <a href="ministries.php" class="nav-link <?php echo $currentPage === 'ministries.php' ? 'active' : ''; ?>">Ministries</a>
            <a href="events.php" class="nav-link <?php echo $currentPage === 'events.php' ? 'active' : ''; ?>">Events</a>
            <a href="sermons.php" class="nav-link <?php echo $currentPage === 'sermons.php' ? 'active' : ''; ?>">Sermons</a>
            <a href="livestream.php" class="nav-link <?php echo $currentPage === 'livestream.php' ? 'active' : ''; ?>">Live</a>
            <a href="contact.php" class="nav-link <?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">Contact</a>
            <a href="donate.php" class="primary-action text-center col-span-2">Give</a>
        </div>
    </nav>
</header>
<main id="main-content">
