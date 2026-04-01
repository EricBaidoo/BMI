<?php
$pageTitle = 'Blog and Announcements | Bridge Ministries International';
include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip">Blog and Announcements</span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3">Church News and Devotionals</h1>
        <p class="mt-4 text-lg muted-copy max-w-3xl">Stay informed about updates, events, and encouragement for your daily walk with Christ.</p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="mt-1 space-y-4">
        <article class="section-card">
            <span class="tag-chip">Devotional</span>
            <h2 class="font-semibold text-xl mt-3">Weekly Devotional: Walking in Grace</h2>
            <p class="text-sm mt-2 muted-copy">Posted on 2026-03-30</p>
            <p class="text-sm mt-2">Grace is not only God's gift for salvation, but also the strength for faithful living every day.</p>
            <a href="#" class="inline-block mt-3 text-teal-700 text-sm font-semibold">Read Article</a>
        </article>
        <article class="section-card">
            <span class="tag-chip">Announcement</span>
            <h2 class="font-semibold text-xl mt-3">Easter Convention Weekend</h2>
            <p class="text-sm mt-2 muted-copy">Posted on 2026-03-25</p>
            <p class="text-sm mt-2">Join us for a full Easter weekend featuring special music, focused prayer, and celebration services.</p>
            <a href="events.php" class="inline-block mt-3 text-teal-700 text-sm font-semibold">See Event Details</a>
        </article>
    </div>

    <div class="section-card mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Get New Posts by Email</h2>
            <p class="text-sm muted-copy mt-1">Subscribe to updates and never miss church news.</p>
        </div>
        <a href="contact.php" class="secondary-action">Subscribe</a>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
