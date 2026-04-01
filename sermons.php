<?php
$pageTitle = 'Sermons | Bridge Ministries International';
include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip">Sermons</span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3">Watch, Listen, and Grow</h1>
        <p class="mt-4 text-lg muted-copy max-w-3xl">Browse messages by date, speaker, and topic. Whether you missed a service or want to revisit a teaching, start here.</p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="section-card">
        <h2 class="text-xl font-semibold">Latest Messages</h2>
        <div class="mt-4 grid md:grid-cols-2 gap-4">
            <article class="rounded-xl border border-slate-200 p-4">
                <h3 class="font-semibold">Faith That Endures</h3>
                <div class="detail-row">
                    <span>Speaker: Pastor Daniel</span>
                    <span>Date: 2026-03-29</span>
                    <span>Topic: Faith</span>
                </div>
                <a href="#" class="inline-block mt-3 text-teal-700 text-sm font-semibold">View Details</a>
            </article>
            <article class="rounded-xl border border-slate-200 p-4">
                <h3 class="font-semibold">Living with Purpose</h3>
                <div class="detail-row">
                    <span>Speaker: Rev. Grace</span>
                    <span>Date: 2026-03-22</span>
                    <span>Topic: Discipleship</span>
                </div>
                <a href="#" class="inline-block mt-3 text-teal-700 text-sm font-semibold">View Details</a>
            </article>
            <article class="rounded-xl border border-slate-200 p-4">
                <h3 class="font-semibold">The Responsibility of Unity</h3>
                <div class="detail-row">
                    <span>Speaker: Pastor Daniel</span>
                    <span>Date: 2026-03-15</span>
                    <span>Topic: Church Life</span>
                </div>
                <a href="#" class="inline-block mt-3 text-teal-700 text-sm font-semibold">View Details</a>
            </article>
            <article class="rounded-xl border border-slate-200 p-4">
                <h3 class="font-semibold">Trusting God in Global Unrest</h3>
                <div class="detail-row">
                    <span>Speaker: Rev. Grace</span>
                    <span>Date: 2026-03-01</span>
                    <span>Topic: Hope</span>
                </div>
                <a href="#" class="inline-block mt-3 text-teal-700 text-sm font-semibold">View Details</a>
            </article>
        </div>
    </div>

    <div class="section-card mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Never Miss a Message</h2>
            <p class="text-sm muted-copy mt-1">Join us in person or online each week.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="livestream.php" class="primary-action">Watch Live</a>
            <a href="contact.php" class="secondary-action">Plan Your Visit</a>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
