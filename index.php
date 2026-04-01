<?php
$pageTitle = 'Home | Bridge Ministries International';
include 'includes/header.php';
?>
<section class="hero-shell text-white">
    <div class="hero-slider" id="heroSlider" aria-label="Homepage highlights">
        <div class="hero-slide active" data-title="Glorify. Grow. Go." data-subtitle="A Bible-believing church family in Accra." style="background-image: url('assets/image/aaron-burden-535Npq1wFG8-unsplash.jpg');"></div>
        <div class="hero-slide" data-title="Worship With Us" data-subtitle="Join us this Sunday for worship and the Word." style="background-image: url('assets/image/tim-wildsmith-sjHDn0oakCc-unsplash.jpg');"></div>
        <div class="hero-slide" data-title="Find Community" data-subtitle="Grow in faith with people who care." style="background-image: url('assets/image/chad-kirchoff-ivqGyYLtBI8-unsplash.jpg');"></div>
        <div class="hero-slide" data-title="Grow In The Word" data-subtitle="Practical biblical teaching for daily life." style="background-image: url('assets/image/edward-cisneros-KoKAXLKJwhk-unsplash.jpg');"></div>
        <div class="hero-slide" data-title="Serve With Purpose" data-subtitle="Use your gifts to impact lives for Christ." style="background-image: url('assets/image/edward-cisneros-QSa-uv4WJ0k-unsplash.jpg');"></div>
        <div class="hero-slide" data-title="Take Your Next Step" data-subtitle="Plan your visit and get connected." style="background-image: url('assets/image/akira-hojo-_86u_Y0oAaM-unsplash.jpg');"></div>
        <div class="hero-overlay"></div>

        <div class="hero-controls" aria-label="Hero slider controls">
            <button class="hero-arrow" id="heroPrev" type="button" aria-label="Previous slide">&#10094;</button>
            <div class="hero-dots" id="heroDots" aria-label="Hero slide indicators"></div>
            <span class="hero-count" id="heroCount">1 / 6</span>
            <button class="hero-arrow" id="heroNext" type="button" aria-label="Next slide">&#10095;</button>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 hero-content-wrap">
        <div class="hero-text-panel">
            <h1 class="hero-title" id="heroHeading">Glorify. Grow. Go.</h1>
            <p class="hero-subtitle" id="heroSubheading">Welcome to Bridge Ministries International.</p>
            <div class="hero-cta-wrap">
                <a href="contact.php" class="primary-action">Plan Your Visit</a>
                <a href="sermons.php" class="secondary-action">Watch Sermons</a>
            </div>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold">Service Times</h2>
    <div class="grid md:grid-cols-3 gap-4 mt-5">
        <div class="section-card icon-card">
            <p class="font-semibold">Morning Worship</p>
            <p class="text-sm mt-1 muted-copy">Sunday 10:00 AM</p>
        </div>
        <div class="section-card icon-card">
            <p class="font-semibold">Prayer Service</p>
            <p class="text-sm mt-1 muted-copy">Sunday 5:30 PM</p>
        </div>
        <div class="section-card icon-card">
            <p class="font-semibold">Bible Classes</p>
            <p class="text-sm mt-1 muted-copy">Wednesday 6:00 PM</p>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold">Sermons</h2>
    <p class="muted-copy mt-2">Watch the latest messages.</p>
    <div class="section-card mt-5">
        <div class="divide-y divide-slate-200 text-sm">
            <a href="sermons.php" class="block py-3 hover:text-blue-700">That You Might Believe and Live (John 20:30-31) | Mar 22, 2026</a>
            <a href="sermons.php" class="block py-3 hover:text-blue-700">The Responsibility of Unity (Ephesians 4:1-6) | Mar 15, 2026</a>
            <a href="sermons.php" class="block py-3 hover:text-blue-700">Trusting God in Global Unrest | Mar 1, 2026</a>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="section-card">
        <h2 class="text-2xl font-bold">You've visited. You've loved it. Now what?</h2>
        <p class="mt-2 muted-copy">Take your next step by planning your visit, joining a ministry, or connecting with our team.</p>
        <div class="mt-5 flex flex-wrap gap-2">
            <a href="contact.php" class="primary-action">What Do I Do Next?</a>
            <a href="ministries.php" class="secondary-action">Explore Ministries</a>
            <a href="events.php" class="secondary-action">View Events</a>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
