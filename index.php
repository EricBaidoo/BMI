<?php
$pageTitle = 'Home | Bridge Ministries International';
require_once __DIR__ . '/includes/db.php';

$upcomingEvents = [];
$latestSermons = [];

try {
    $pdo = db_connect();
    $stmt = $pdo->query(
        "SELECT id, title, description, event_date, event_time, venue
         FROM events
         WHERE event_date >= CURDATE()
         ORDER BY event_date ASC, event_time ASC, id DESC
         LIMIT 3"
    );
    $upcomingEvents = $stmt->fetchAll();

    $stmt = $pdo->query(
        "SELECT id, title, sermon_date, topic
         FROM sermons
         ORDER BY sermon_date DESC, id DESC
         LIMIT 3"
    );
    $latestSermons = $stmt->fetchAll();
} catch (Throwable $e) {
    $upcomingEvents = [];
    $latestSermons = [];
}

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
            <p class="hero-subtitle" id="heroSubheading">A Christ-centered church family in Accra, committed to faith, fellowship, and purpose.</p>
            <div class="hero-cta-wrap">
                <a href="contact.php" class="primary-action">Plan Your Visit</a>
                <a href="sermons.php" class="secondary-action">Watch Sermons</a>
            </div>
        </div>
    </div>
</section>

<section class="welcome-section">
    <div class="max-w-6xl mx-auto px-4 py-12 md:py-16">
        <div class="welcome-intro text-center max-w-4xl mx-auto">
            <p class="welcome-eyebrow">Welcome</p>
            <h2 class="welcome-heading mt-3">Welcome to Bridge Ministries International.</h2>
            <p class="welcome-lead mt-4 mx-auto">
                We are glad you are here. Bridge Ministries International is a Bible-centered church family in Accra,
                and our heart is to help people know Christ, grow in faith, and live with purpose.
            </p>
            <p class="welcome-body mt-3 mx-auto">
                Whether you are visiting for the first time or looking for a church home, we want you to feel at home.
                We pray you will encounter God, find genuine fellowship, and take your next step in faith with confidence.
            </p>

            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="contact.php" class="primary-action">Plan Your Visit</a>
                <a href="about.php" class="secondary-action">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<section class="overseer-section">
    <div class="max-w-6xl mx-auto px-4 py-12">
        <div class="overseer-card grid lg:grid-cols-[0.75fr_1.25fr] gap-6 lg:gap-8 items-center">
            <div class="overseer-profile">
                <div class="overseer-avatar" aria-hidden="true">GO</div>
                <p class="overseer-label mt-4">General Overseer</p>
                <h3 class="overseer-name mt-2">Rev. Dr. [General Overseer Name]</h3>
                <p class="overseer-title mt-1">Bridge Ministries International</p>
            </div>

            <div>
                <p class="overseer-copy">
                    Our General Overseer serves with a commitment to sound doctrine, servant leadership, and a clear
                    vision for discipleship. His heart is to see believers strengthened in the Word and active in ministry.
                </p>
                <p class="overseer-copy mt-3">
                    Through teaching, pastoral care, and a focus on spiritual growth, he continues to help the church
                    build mature disciples who live out their faith with confidence and purpose.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold">Weekly Services</h2>
    <div class="grid md:grid-cols-3 gap-4 mt-5">
        <div class="section-card icon-card">
            <p class="font-semibold">RESTORERS</p>
            <p class="text-sm mt-1 muted-copy">Celebration Service</p>
            <p class="text-sm mt-1 muted-copy">Sunday 8:45 AM</p>
        </div>
        <div class="section-card icon-card">
            <p class="font-semibold">REPAIRERS</p>
            <p class="text-sm mt-1 muted-copy">Cell Meetings</p>
            <p class="text-sm mt-1 muted-copy">Wednesdays</p>
        </div>
        <div class="section-card icon-card">
            <p class="font-semibold">SWITCH ON</p>
            <p class="text-sm mt-1 muted-copy">Youth Service</p>
            <p class="text-sm mt-1 muted-copy">Sunday 8:45 AM</p>
        </div>
        <div class="section-card icon-card">
            <p class="font-semibold">BUILDERS</p>
            <p class="text-sm mt-1 muted-copy">Leadership Meeting</p>
            <p class="text-sm mt-1 muted-copy">Fridays 7:00 PM</p>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold">Upcoming Events</h2>
    <p class="muted-copy mt-2">Join us for these upcoming moments of worship, growth, and fellowship.</p>

    <div class="grid md:grid-cols-3 gap-4 mt-5">
        <?php if (empty($upcomingEvents)): ?>
            <div class="section-card md:col-span-3">No upcoming events are available at the moment.</div>
        <?php endif; ?>

        <?php foreach ($upcomingEvents as $event): ?>
            <?php
                $chipDate = date('M d', strtotime((string) $event['event_date']));
                $eventTime = !empty($event['event_time']) ? date('g:i A', strtotime((string) $event['event_time'])) : null;
                $venue = trim((string) ($event['venue'] ?? ''));
                $metaParts = [];
                if ($eventTime) {
                    $metaParts[] = $eventTime;
                }
                if ($venue !== '') {
                    $metaParts[] = $venue;
                }
            ?>
            <div class="section-card">
                <span class="tag-chip"><?php echo htmlspecialchars($chipDate); ?></span>
                <h3 class="text-lg font-bold mt-3"><?php echo htmlspecialchars((string) $event['title']); ?></h3>
                <p class="muted-copy mt-2"><?php echo htmlspecialchars((string) ($event['description'] ?? '')); ?></p>
                <?php if (!empty($metaParts)): ?>
                    <p class="text-sm mt-3 muted-copy"><?php echo htmlspecialchars(implode(' | ', $metaParts)); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-6">
        <a href="events.php" class="secondary-action">View All Events</a>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold">Latest Sermons</h2>
    <p class="muted-copy mt-2">Catch up on recent messages from our pulpit.</p>
    <div class="section-card mt-5">
        <div class="divide-y divide-slate-200 text-sm">
            <?php if (empty($latestSermons)): ?>
                <p class="py-3 muted-copy">No sermons have been published yet.</p>
            <?php endif; ?>

            <?php foreach ($latestSermons as $sermon): ?>
                <?php
                    $dateText = date('M d, Y', strtotime((string) $sermon['sermon_date']));
                    $topic = trim((string) ($sermon['topic'] ?? ''));
                    $line = (string) $sermon['title'];
                    if ($topic !== '') {
                        $line .= ' (' . $topic . ')';
                    }
                    $line .= ' | ' . $dateText;
                ?>
                <a href="sermons.php" class="block py-3 hover:text-blue-700"><?php echo htmlspecialchars($line); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="section-card">
        <span class="tag-chip">Online</span>
        <h2 class="text-2xl font-bold mt-3">Watch Sermons Online</h2>
        <p class="mt-2 muted-copy max-w-3xl">
            Join us live each week or watch recent messages anytime. Stay connected to the Word wherever you are.
        </p>

        <div class="mt-5 flex flex-wrap gap-2">
            <a href="livestream.php" class="primary-action">Watch Live Service</a>
            <a href="sermons.php" class="secondary-action">Browse Sermon Archive</a>
        </div>
    </div>
</section>

<section class="lets-connect-section">
    <div class="max-w-6xl mx-auto px-4 py-16 md:py-20">
        <div class="lets-connect-hero mb-16 md:mb-20">
            <h2 class="lets-connect-title">Let's Connect</h2>
            <p class="lets-connect-copy mt-5">
                If you have landed here, we are glad to meet you. Whether this is your first Sunday or you have
                been around for a while, we would love to connect with you and help you take your next step.
            </p>
            <p class="lets-connect-copy mt-4">
                Join us every Sunday at <strong>8:45 AM</strong>, and through the week in our gatherings and groups.
                You are welcome here, and we would love to walk with you in faith and community.
            </p>
            <div class="lets-connect-meta mt-8">
                <div class="meta-highlight">
                    <p class="meta-label">Find us this Sunday</p>
                    <p class="meta-content">8:45 AM • Bridge Ministries International • Nanamio, Accra</p>
                </div>
            </div>
        </div>

        <div class="connect-row">
            <div class="connect-image-wrap connect-image-left">
                <div class="connect-accent connect-accent-pink" aria-hidden="true"></div>
                <img src="assets/image/IMG_1094.jpg" alt="Church members smiling together" class="connect-image">
            </div>
            <div class="connect-text">
                <h3 class="connect-heading">Let's get acquainted</h3>
                <p class="connect-copy mt-4">
                    We would love to connect and help you find your next step at BMI. Fill out a connect card,
                    and our pastors and ministry leaders will follow up with you.
                </p>
                <a href="contact.php" class="connect-link"><span>Online Connect Card</span></a>
            </div>
        </div>

        <div class="connect-row connect-row-reverse">
            <div class="connect-image-wrap connect-image-right">
                <div class="connect-accent connect-accent-blue" aria-hidden="true"></div>
                <img src="assets/image/IMG_1230.jpg" alt="Young adults in fellowship" class="connect-image">
            </div>
            <div class="connect-text">
                <h3 class="connect-heading">Discover your place</h3>
                <p class="connect-copy mt-4">
                    Take a step and get involved. From young adults to service teams and discipleship groups,
                    there is a place for you to belong, serve, and grow.
                </p>
                <a href="ministries.php" class="connect-link"><span>Explore Ministries</span></a>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
