<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/settings.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Settings-driven values (override file-level defaults)
$siteName        = setting('site.name', $siteName);
$siteDescription = setting('site.description', $siteDescription);
$analyticsDomain = setting('analytics.plausible_domain', $analyticsDomain);

$pageTitle = $pageTitle ?? $siteName;
$pageDescription = $pageDescription ?? $siteDescription;
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$isHomePage = $currentPage === 'index.php';

$canonicalUrl = $siteUrl . '/' . ltrim($_SERVER['REQUEST_URI'] ?? '/', '/');
$ogImage = $siteUrl . '/assets/image/bmi%20logo%20new.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage); ?>">

    <link rel="icon" type="image/png" href="assets/image/bmi%20logo%20new.png">
    <link rel="shortcut icon" type="image/png" href="assets/image/bmi%20logo%20new.png">
    <link rel="apple-touch-icon" href="assets/image/bmi%20logo%20new.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo urlencode((string) filemtime(__DIR__ . '/../assets/css/styles.css')); ?>">

    <?php if (!empty($analyticsDomain)): ?>
        <script defer data-domain="<?php echo htmlspecialchars($analyticsDomain); ?>" src="https://plausible.io/js/script.js"></script>
    <?php endif; ?>

    <?php if ($isHomePage): ?>
    <script type="application/ld+json">
    <?php
        $sameAs = array_values(array_filter([
            setting('social.facebook'),
            setting('social.instagram'),
            setting('social.youtube'),
            setting('social.x'),
            setting('social.tiktok'),
        ]));
        echo json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Church',
            'name' => $siteName,
            'description' => $siteDescription,
            'url' => $siteUrl,
            'logo' => $ogImage,
            'telephone' => setting('contact.phone_primary'),
            'email' => setting('contact.email_general'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => setting('contact.address'),
                'addressCountry' => 'GH',
            ],
            'sameAs' => $sameAs,
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    ?>
    </script>
    <?php endif; ?>
</head>
<body class="site-body text-slate-800">
<a href="#main-content" class="skip-link">Skip to main content</a>
<header class="site-header <?php echo $isHomePage ? 'home-overlay-header' : ''; ?>">
    <div class="max-w-6xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between md:hidden">
            <a href="index.php" class="mobile-brand" aria-label="Bridge Ministries International Home">
                <img class="mobile-logo-img" src="assets/image/bmi%20logo%20new.png" alt="Bridge Ministries International logo">
            </a>
            <button id="mobileMenuButton" type="button" class="menu-button" aria-label="Open navigation" aria-expanded="false" aria-controls="mobileMenu">
                <span class="hamburger-icon" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>
        </div>

        <div class="hidden md:grid md:grid-cols-[1fr_auto_1fr] md:items-center md:gap-5">
            <nav class="flex items-center gap-2 text-sm font-semibold header-split-nav" aria-label="Primary navigation left">
                <a href="about.php" class="nav-link <?php echo in_array($currentPage, ['about.php','beliefs.php']) ? 'active' : ''; ?>">About</a>
                <a href="visit.php" class="nav-link <?php echo $currentPage === 'visit.php' ? 'active' : ''; ?>">Visit</a>
                <a href="ministries.php" class="nav-link <?php echo $currentPage === 'ministries.php' ? 'active' : ''; ?>">Ministries</a>
                <a href="sermons.php" class="nav-link <?php echo in_array($currentPage, ['sermons.php','livestream.php']) ? 'active' : ''; ?>">Sermons</a>
            </nav>

            <a href="index.php" class="wordmark" aria-label="Bridge Ministries International Home">
                <img class="wordmark-logo-img" src="assets/image/bmi%20logo%20new.png" alt="Bridge Ministries International logo">
            </a>

            <nav class="flex items-center justify-end gap-2 text-sm font-semibold header-split-nav" aria-label="Primary navigation right">
                <a href="events.php" class="nav-link <?php echo $currentPage === 'events.php' ? 'active' : ''; ?>">Events</a>
                <a href="blog.php" class="nav-link <?php echo $currentPage === 'blog.php' ? 'active' : ''; ?>">Good News</a>
                <a href="contact.php" class="nav-link <?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">Contact</a>
                <a href="donate.php" class="primary-action ml-2">Give</a>
            </nav>
        </div>
    </div>

    <nav id="mobileMenu" class="mobile-menu hidden md:hidden" aria-label="Mobile navigation">
        <div class="max-w-6xl mx-auto px-4 pb-4 grid grid-cols-2 gap-2 text-sm">
            <a href="index.php" class="nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="about.php" class="nav-link <?php echo $currentPage === 'about.php' ? 'active' : ''; ?>">About</a>
            <a href="beliefs.php" class="nav-link <?php echo $currentPage === 'beliefs.php' ? 'active' : ''; ?>">Beliefs</a>
            <a href="visit.php" class="nav-link <?php echo $currentPage === 'visit.php' ? 'active' : ''; ?>">Visit</a>
            <a href="ministries.php" class="nav-link <?php echo $currentPage === 'ministries.php' ? 'active' : ''; ?>">Ministries</a>
            <a href="events.php" class="nav-link <?php echo $currentPage === 'events.php' ? 'active' : ''; ?>">Events</a>
            <a href="sermons.php" class="nav-link <?php echo $currentPage === 'sermons.php' ? 'active' : ''; ?>">Sermons</a>
            <a href="livestream.php" class="nav-link <?php echo $currentPage === 'livestream.php' ? 'active' : ''; ?>">Live</a>
            <a href="blog.php" class="nav-link <?php echo $currentPage === 'blog.php' ? 'active' : ''; ?>">Good News</a>
            <a href="contact.php" class="nav-link <?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">Contact</a>
            <a href="donate.php" class="primary-action text-center col-span-2">Give</a>
        </div>
    </nav>
</header>
<main id="main-content">
