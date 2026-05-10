<?php
$pageTitle = 'About Us | Bridge Ministries International';
$pageDescription = 'Learn about Bridge Ministries International — our story, mission, vision, values, and leadership team. A Christ-centred church family in Accra, Ghana.';

require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/helpers.php';

$founded = setting('site.founded_year', '2005');
$siteName = setting('site.name', 'Bridge Ministries International');
$svcSunday = setting('service.sunday_worship');

include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip">About <?php echo e($siteName); ?></span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3">Building Bridges. Building Lives.</h1>
        <p class="mt-4 text-lg muted-copy max-w-3xl">
            Bridge Ministries International is a Christ-centred church family in Accra, Ghana, committed to one calling:
            <em>Glorify God, Grow disciples, and Go on mission.</em> We exist to bridge people to Jesus, to one another, and to a life of purpose.
        </p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12 space-y-10">

    <!-- Our Story -->
    <div class="grid md:grid-cols-3 gap-6">
        <div class="section-card md:col-span-2">
            <span class="tag-chip">Our Story</span>
            <h2 class="text-2xl font-semibold mt-3">From a small fellowship to a movement</h2>
            <p class="mt-4 text-sm muted-copy leading-relaxed">
                Bridge Ministries International began in <?php echo e($founded); ?> as a small group of believers gathering with a single conviction:
                that the local church should be a bridge — between God and people, between generations, and between the church and the community it serves.
                Under the leadership of Rev. Francis Duane Yalley, that conviction has grown into a thriving movement of cell-based ministries
                and congregations reaching thousands of believers across Ghana and beyond.
            </p>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                Today we remain anchored in the same simple commitments that defined our first gatherings: faithful preaching of the Word,
                strategic prayer, intentional discipleship, and a love for the city. Whether you are a long-time member, a first-time guest,
                or a believer somewhere in between, you are welcome at the bridge.
            </p>
        </div>
        <div class="section-card">
            <span class="tag-chip">Visit This Sunday</span>
            <p class="mt-3 text-sm muted-copy"><?php echo e($svcSunday !== '' ? $svcSunday : 'Join us at our weekly celebration service.'); ?></p>
            <p class="text-sm muted-copy mt-2">Come as you are — we'll save you a seat.</p>
            <a href="visit.php" class="primary-action mt-4">Plan Your Visit</a>
        </div>
    </div>

    <!-- Mission, Vision, Values -->
    <div class="grid md:grid-cols-3 gap-6">
        <div class="section-card">
            <span class="tag-chip">Mission</span>
            <h2 class="text-xl font-semibold mt-3">Why we exist</h2>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                To lead people to Jesus Christ and shape mature disciples through Scripture, prayer, community, and service —
                so that lives, families, and communities are transformed by the Gospel.
            </p>
        </div>
        <div class="section-card">
            <span class="tag-chip">Vision</span>
            <h2 class="text-xl font-semibold mt-3">Where we are headed</h2>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                A Christ-centred, compassionate, and globally-engaged church reaching the city and the nations —
                building bridges of faith that endure for generations.
            </p>
        </div>
        <div class="section-card">
            <span class="tag-chip">Motto</span>
            <h2 class="text-xl font-semibold mt-3">Glorify. Grow. Go.</h2>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                Three words that shape every gathering, decision, and ministry. Worship that honours God,
                discipleship that produces growth, and mission that sends us into the world.
            </p>
        </div>
    </div>

    <!-- Core Values -->
    <div class="section-card">
        <span class="tag-chip">Our Values</span>
        <h2 class="text-2xl font-semibold mt-3">What we hold tightly</h2>
        <div class="grid md:grid-cols-2 gap-x-10 gap-y-5 mt-5 text-sm">
            <div>
                <p class="font-semibold">1. Scripture, Above All</p>
                <p class="muted-copy mt-1">The Bible is our final authority for faith and practice. We teach it, trust it, and live by it.</p>
            </div>
            <div>
                <p class="font-semibold">2. Prayer, Without Ceasing</p>
                <p class="muted-copy mt-1">Prayer is not a programme — it is the engine of everything we do, both privately and corporately.</p>
            </div>
            <div>
                <p class="font-semibold">3. Discipleship, On Purpose</p>
                <p class="muted-copy mt-1">We don't just gather crowds; we build disciples who follow Jesus and form others to do the same.</p>
            </div>
            <div>
                <p class="font-semibold">4. Family, Across Generations</p>
                <p class="muted-copy mt-1">From children to elders, every age group has a seat at the table and a voice in the body.</p>
            </div>
            <div>
                <p class="font-semibold">5. Mission, Beyond Walls</p>
                <p class="muted-copy mt-1">We are sent — to our neighbours, our city, and the nations — with the love and message of Jesus.</p>
            </div>
            <div>
                <p class="font-semibold">6. Integrity, At All Costs</p>
                <p class="muted-copy mt-1">We pursue financial transparency, ethical leadership, and Christ-like character in private and in public.</p>
            </div>
        </div>
    </div>

    <!-- Leadership -->
    <div>
        <div class="text-center max-w-2xl mx-auto">
            <span class="tag-chip">Leadership</span>
            <h2 class="text-3xl font-bold mt-3">Meet Our Leaders</h2>
            <p class="mt-3 muted-copy">A team of pastors, elders, and ministry leaders shepherding the church together.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mt-8">
            <div class="section-card text-center">
                <img src="assets/image/IMG_1061.jpg" alt="Rev. Francis Duane Yalley" class="w-32 h-32 rounded-full object-cover mx-auto" loading="lazy">
                <h3 class="font-semibold text-lg mt-4">Rev. Francis Duane Yalley</h3>
                <p class="text-xs uppercase tracking-wide muted-copy mt-1">General Overseer</p>
                <p class="mt-3 text-sm muted-copy">A teacher of the Word with a passion for biblical truth, strategic prayer, and raising mature disciples who influence their communities.</p>
            </div>
            <div class="section-card text-center">
                <div class="w-32 h-32 rounded-full bg-slate-200 mx-auto flex items-center justify-center text-slate-400 text-3xl font-bold">+</div>
                <h3 class="font-semibold text-lg mt-4">Pastoral Team</h3>
                <p class="text-xs uppercase tracking-wide muted-copy mt-1">Associate Pastors</p>
                <p class="mt-3 text-sm muted-copy">A faithful team of associate pastors serving alongside the General Overseer in teaching, counselling, and shepherding.</p>
            </div>
            <div class="section-card text-center">
                <div class="w-32 h-32 rounded-full bg-slate-200 mx-auto flex items-center justify-center text-slate-400 text-3xl font-bold">+</div>
                <h3 class="font-semibold text-lg mt-4">Ministry Leaders</h3>
                <p class="text-xs uppercase tracking-wide muted-copy mt-1">Elders & Coordinators</p>
                <p class="mt-3 text-sm muted-copy">Trusted men and women leading every ministry — from worship and discipleship to outreach, youth, and missions.</p>
            </div>
        </div>

        <p class="text-xs muted-copy text-center mt-4">More leadership profiles coming soon.</p>
    </div>

    <!-- Closing CTA grid -->
    <div class="grid md:grid-cols-3 gap-6">
        <div class="section-card">
            <h2 class="text-xl font-semibold">What We Believe</h2>
            <p class="mt-2 text-sm muted-copy">Read our full statement of faith — what we hold to be true about God, Scripture, and salvation.</p>
            <a href="beliefs.php" class="secondary-action mt-4">Our Beliefs</a>
        </div>
        <div class="section-card">
            <h2 class="text-xl font-semibold">Find Your Community</h2>
            <p class="mt-2 text-sm muted-copy">From cell groups to age-based ministries, there is a place for you to belong, serve, and grow.</p>
            <a href="ministries.php" class="secondary-action mt-4">Explore Ministries</a>
        </div>
        <div class="section-card">
            <h2 class="text-xl font-semibold">Need Prayer?</h2>
            <p class="mt-2 text-sm muted-copy">Whatever you are facing, we'd be honoured to stand with you in prayer.</p>
            <a href="contact.php" class="secondary-action mt-4">Send a Request</a>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
