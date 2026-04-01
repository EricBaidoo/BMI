<?php
$pageTitle = 'Contact | Bridge Ministries International';
include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip">Contact</span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3">Plan Your Visit and Reach Out</h1>
        <p class="mt-4 text-lg muted-copy max-w-3xl">Questions, prayer requests, or first-time visit details? We are ready to help.</p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="grid md:grid-cols-2 gap-6">
        <form method="post" action="#" class="section-card icon-card space-y-3" data-icon="@">
            <h2 class="text-xl font-semibold">Send a Message</h2>
            <div>
                <label class="text-sm font-medium">Full Name</label>
                <input type="text" name="name" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="text-sm font-medium">Email</label>
                <input type="email" name="email" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="text-sm font-medium">Message / Prayer Request</label>
                <textarea name="message" rows="5" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required></textarea>
            </div>
            <button type="submit" class="primary-action">Submit Request</button>
        </form>

        <div class="section-card icon-card" data-icon="i">
            <h2 class="font-semibold text-xl">Church Information</h2>
            <p class="text-sm mt-3"><strong>Address:</strong> 123 Hope Street, Accra</p>
            <p class="text-sm"><strong>Phone:</strong> +233 000 000 000</p>
            <p class="text-sm"><strong>Email:</strong> info@bmiglobal.org</p>
            <p class="text-sm mt-3 muted-copy">Service Times: Sunday Worship 10:00 AM, Prayer Service 5:30 PM, Bible Study Wednesday 6:00 PM.</p>
            <div class="mt-4 h-48 bg-slate-200 rounded-xl flex items-center justify-center text-slate-500 text-sm">Map Placeholder</div>
        </div>
    </div>

    <div class="section-card mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">First Time at BMI?</h2>
            <p class="text-sm muted-copy mt-1">Let us know you are coming and we will make your visit smooth and welcoming.</p>
        </div>
        <a href="index.php" class="secondary-action">Back to Home</a>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
