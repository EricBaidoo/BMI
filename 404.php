<?php
http_response_code(404);
$pageTitle = 'Page Not Found | Bridge Ministries International';
$pageDescription = 'The page you were looking for could not be found.';
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-20 text-center">
        <p class="text-7xl font-extrabold text-slate-300">404</p>
        <h1 class="text-3xl md:text-4xl font-bold mt-4">We could not find that page</h1>
        <p class="mt-3 muted-copy max-w-xl mx-auto">The link may be old, mistyped, or the page may have moved. Try one of these instead:</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="./" class="primary-action">Go Home</a>
            <a href="sermons" class="secondary-action">Sermons</a>
            <a href="events" class="secondary-action">Events</a>
            <a href="contact" class="secondary-action">Contact</a>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
