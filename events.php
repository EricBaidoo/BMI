<?php
$pageTitle = 'Events | Bridge Ministries International';
include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip">Events</span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3">What Is Happening at BMI</h1>
        <p class="mt-4 text-lg muted-copy max-w-3xl">Plan your week with upcoming services, outreach opportunities, and special gatherings.</p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="space-y-4">
        <article class="section-card icon-card" data-icon="E">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                <h2 class="font-semibold text-xl">Easter Service Celebration</h2>
                <span class="tag-chip">Featured</span>
            </div>
            <p class="text-sm mt-3 muted-copy">Date: 2026-04-05 | Time: 10:00 AM | Venue: Main Auditorium</p>
            <p class="text-sm mt-2">Join us for uplifting worship, choir ministration, and a practical resurrection message for the whole family.</p>
        </article>
        <article class="section-card icon-card" data-icon="O">
            <h2 class="font-semibold text-xl">Community Outreach</h2>
            <p class="text-sm mt-3 muted-copy">Date: 2026-04-12 | Time: 10:00 AM | Venue: City Center</p>
            <p class="text-sm mt-2">Serve with us as we support families through prayer, encouragement, and practical outreach activities.</p>
        </article>
        <article class="section-card icon-card" data-icon="P">
            <h2 class="font-semibold text-xl">Prayer and Fasting Week</h2>
            <p class="text-sm mt-3 muted-copy">Date: 2026-04-20 to 2026-04-24 | Time: 6:00 PM Daily | Venue: Prayer Hall</p>
            <p class="text-sm mt-2">A focused week for spiritual renewal, intercession, and seeking God's direction together.</p>
        </article>
    </div>

    <div class="section-card mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Do Not Miss an Event</h2>
            <p class="text-sm muted-copy mt-1">Get event reminders and ministry updates directly in your inbox.</p>
        </div>
        <a href="contact.php" class="secondary-action">Request Weekly Updates</a>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
