<?php
$pageTitle = 'Home | Bridge Ministries International';
include 'includes/header.php';
?>
<section class="hero-shell text-white">
    <div class="hero-slider" id="heroSlider" aria-label="Homepage highlights">
        <div class="hero-slide active" data-title="Glorify. Grow. Go." data-subtitle="Welcome to Bridge Ministries International." style="background-image: url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1800&q=80');"></div>
        <div class="hero-slide" data-title="Worship With Us" data-subtitle="Gather every Sunday for Christ-centered worship and teaching." style="background-image: url('https://images.unsplash.com/photo-1438232992991-995b7058bbb3?auto=format&fit=crop&w=1800&q=80');"></div>
        <div class="hero-slide" data-title="Find Community" data-subtitle="Build meaningful relationships through ministries and fellowship." style="background-image: url('https://images.unsplash.com/photo-1490122417551-6ee9691429d0?auto=format&fit=crop&w=1800&q=80');"></div>
        <div class="hero-slide" data-title="Grow In The Word" data-subtitle="Discover practical biblical teaching for everyday life." style="background-image: url('https://images.unsplash.com/photo-1468070454955-c5b6932bd08d?auto=format&fit=crop&w=1800&q=80');"></div>
        <div class="hero-slide" data-title="Serve With Purpose" data-subtitle="Use your gifts to impact lives in church and community." style="background-image: url('https://images.unsplash.com/photo-1478145046317-39f10e56b5e9?auto=format&fit=crop&w=1800&q=80');"></div>
        <div class="hero-slide" data-title="Take Your Next Step" data-subtitle="Plan your visit, join a ministry, and walk with us in faith." style="background-image: url('https://images.unsplash.com/photo-1504052434569-70ad5836ab65?auto=format&fit=crop&w=1800&q=80');"></div>
        <div class="hero-overlay"></div>

        <div class="hero-controls" aria-label="Hero slider controls">
            <button class="hero-arrow" id="heroPrev" type="button" aria-label="Previous slide">&#10094;</button>
            <div class="hero-dots" id="heroDots" aria-label="Hero slide indicators"></div>
            <button class="hero-arrow" id="heroNext" type="button" aria-label="Next slide">&#10095;</button>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 hero-content-wrap">
        <h1 class="hero-title" id="heroHeading">Glorify. Grow. Go.</h1>
        <p class="hero-subtitle" id="heroSubheading">Welcome to Bridge Ministries International.</p>
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
