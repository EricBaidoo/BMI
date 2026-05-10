<?php
$pageTitle = 'Home | Bridge Ministries International';
$pageDescription = 'Bridge Ministries International — a Christ-centred church family in Accra, Ghana. Glorify. Grow. Go.';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';

$upcomingEvents = [];
$latestSermons = [];

try {
    $pdo = db_connect();
    $stmt = $pdo->query(
        "SELECT id, title, description, event_date, event_time, venue, event_image
         FROM events
         WHERE event_date >= CURDATE()
         ORDER BY event_date ASC, event_time ASC, id DESC"
    );
    $upcomingEvents = $stmt->fetchAll();

    $stmt = $pdo->query(
        "SELECT id, title, sermon_date, topic, sermon_image
         FROM sermons
         ORDER BY sermon_date DESC, id DESC"
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
        
            <h2 class="welcome-heading">We're glad you're here.</h2>
            <p class="welcome-lead mt-6">
                Bridge Ministries International is a Bible-centered church family in Accra committed to helping people
                know Christ, grow in faith, and live with purpose. Whether you're visiting for the first time or looking
                for a church home, we want you to feel at home here.
            </p>
            <p class="welcome-subtext mt-5">
                We pray you will encounter God, find genuine fellowship, and take your next step with confidence.
            </p>

            <div class="welcome-actions mt-8">
                <a href="visit.php" class="primary-action">Plan Your Visit</a>
                <a href="about.php" class="secondary-action">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<section class="overseer-section">
    <div class="page-container py-section">
        <div class="section-intro">
            <span class="tag-chip">Leadership</span>
            <h2 class="section-heading">Meet Our General Overseer</h2>
            <p class="section-lead">A faithful shepherd called to teach the Word, raise disciples, and lead the BMI family with integrity and vision.</p>
        </div>

        <div class="overseer-card">
            <div class="overseer-profile">
                <img src="assets/image/IMG_1061.jpg" alt="Rev. Francis Duane Yalley, General Overseer" class="overseer-avatar" loading="lazy">
                <p class="overseer-label">General Overseer</p>
                <h3 class="overseer-name">Rev. Francis Duane Yalley</h3>
            </div>
            <div class="overseer-content">
                <p class="overseer-copy">
                    Rev. Francis Duane Yalley is the Presiding General Overseer of Bridge Ministries International, headquartered in Accra, Ghana. A visionary leader with unwavering commitment to sound doctrine, he has dedicated his ministry to teaching the Word of God with clarity and power, building a movement rooted in faith, discipleship, and transformational leadership.
                </p>
                <p class="overseer-copy">
                    Bridge Ministries International has grown to encompass multiple thriving congregations and cell-based ministries, reaching thousands of believers across Ghana and beyond. Known throughout the region as a passionate advocate for biblical truth and strategic prayer, Rev. Yalley continues to shepherd the church with a clear vision: to develop mature believers who influence their communities, strengthen families, and advance God's kingdom with purpose and conviction.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="what-to-expect-section">
    <div class="page-container py-section">
        <div class="section-intro">
            <span class="tag-chip">What to Expect</span>
            <h2 class="section-heading">Your First Sunday at BMI</h2>
            <p class="section-lead">No pressure. No surprises. Just a warm family ready to welcome you.</p>
        </div>

        <div class="step-grid">
            <article class="step-card">
                <p class="step-number">01</p>
                <h3 class="step-title">Arrive a few minutes early</h3>
                <p class="step-copy">Look for our hosts at the entrance — they'll help you find a seat, the kids' area, or the restrooms.</p>
            </article>
            <article class="step-card">
                <p class="step-number">02</p>
                <h3 class="step-title">Worship with us</h3>
                <p class="step-copy">About 90 minutes of singing, prayer, scripture, and a Bible-centred message you can take into the week.</p>
            </article>
            <article class="step-card">
                <p class="step-number">03</p>
                <h3 class="step-title">Stay and connect</h3>
                <p class="step-copy">Stop by the welcome desk after service. A pastor will follow up — no pressure, just a real conversation.</p>
            </article>
        </div>

        <div class="section-cta">
            <a href="visit.php" class="primary-action">See the Full Visit Guide</a>
        </div>
    </div>
</section>

<section class="weekly-services-section">
    <div class="max-w-7xl mx-auto px-4 py-16 md:py-24">
        <div class="section-header mb-14 text-center">
            <h2 class="section-title-alt">Our Weekly Services</h2>
        </div>

        <div class="carousel-wrapper">
            <div class="services-carousel" id="servicesCarousel">
                <div class="service-image-card">
                    <img src="assets/image/IMG_1099.jpg" alt="RESTORERS - Celebration Service" class="service-image" loading="lazy">
                    <div class="service-overlay">
                        <h3 class="service-label">RESTORERS</h3>
                        <p class="service-detail">Celebration Service</p>
                        <p class="service-detail">Sunday 8:45 AM</p>
                    </div>
                </div>
                <div class="service-image-card">
                    <img src="assets/image/IMG_1104.jpg" alt="REPAIRERS - Cell Meetings" class="service-image" loading="lazy">
                    <div class="service-overlay">
                        <h3 class="service-label">REPAIRERS</h3>
                        <p class="service-detail">Cell Meetings</p>
                        <p class="service-detail">Wednesdays</p>
                    </div>
                </div>
                <div class="service-image-card">
                    <img src="assets/image/IMG_1111.jpg" alt="SWITCH ON - Youth Service" class="service-image" loading="lazy">
                    <div class="service-overlay">
                        <h3 class="service-label">SWITCH ON</h3>
                        <p class="service-detail">Youth Service</p>
                        <p class="service-detail">Sunday 8:45 AM</p>
                    </div>
                </div>
                <div class="service-image-card">
                    <img src="assets/image/IMG_1094.jpg" alt="BUILDERS - Leadership Meeting" class="service-image" loading="lazy">
                    <div class="service-overlay">
                        <h3 class="service-label">BUILDERS</h3>
                        <p class="service-detail">Leadership Meeting</p>
                        <p class="service-detail">Fridays 7:00 PM</p>
                    </div>
                </div>
            </div>
            <button class="carousel-btn carousel-prev" id="carouselPrev" aria-label="Previous service">&#10094;</button>
            <button class="carousel-btn carousel-next" id="carouselNext" aria-label="Next service">&#10095;</button>
        </div>
    </div>
</section>

<section class="upcoming-events-section">
    <div class="max-w-7xl mx-auto px-4 py-20 md:py-24">
        <div class="events-header text-center mb-16 md:mb-20">
            <h2 class="events-title">Upcoming Events</h2>
            <p class="events-subtitle">Mark your calendars for these great events coming up.</p>
        </div>

        <?php if (empty($upcomingEvents)): ?>
            <div class="empty-state empty-state--dark">
                <p>No upcoming events are available at the moment.</p>
            </div>
        <?php else: ?>
            <div class="carousel-wrapper">
            <div class="events-carousel" id="eventsCarousel">
                <?php foreach ($upcomingEvents as $event): ?>
                    <?php
                        $chipDay     = date('d',   strtotime((string) $event['event_date']));
                        $chipMonth   = date('M',   strtotime((string) $event['event_date']));
                        $chipWeekday = date('D',   strtotime((string) $event['event_date']));
                        $eventTime   = !empty($event['event_time']) ? date('g:i A', strtotime((string) $event['event_time'])) : null;
                        $venue       = trim((string) ($event['venue'] ?? ''));
                    ?>
                    <article class="event-card">
                        <div class="event-image-container">
                            <?php if ($event['event_image']): ?>
                                <img src="<?php echo htmlspecialchars($event['event_image']); ?>" alt="<?php echo htmlspecialchars((string) $event['title']); ?>" class="event-image" loading="lazy">
                            <?php else: ?>
                                <div class="event-image-placeholder"></div>
                            <?php endif; ?>
                            <div class="event-date-badge">
                                <span class="badge-month"><?php echo htmlspecialchars(strtoupper($chipMonth)); ?></span>
                                <span class="badge-day"><?php echo htmlspecialchars($chipDay); ?></span>
                                <span class="badge-weekday"><?php echo htmlspecialchars(strtoupper($chipWeekday)); ?></span>
                            </div>
                        </div>
                        <div class="event-content">
                            <h3 class="event-title"><?php echo htmlspecialchars((string) $event['title']); ?></h3>

                            <div class="event-meta">
                                <?php if ($eventTime): ?>
                                    <span class="event-meta-item">
                                        <svg class="event-icon" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7z" fill="currentColor"/>
                                        </svg>
                                        <?php echo htmlspecialchars($eventTime); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($venue): ?>
                                    <span class="event-meta-item">
                                        <svg class="event-icon" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 14.5 9 2.5 2.5 0 0 1 12 11.5z" fill="currentColor"/>
                                        </svg>
                                        <?php echo htmlspecialchars($venue); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($event['description'])): ?>
                                <p class="event-description"><?php echo htmlspecialchars(substr((string) $event['description'], 0, 120)); ?><?php echo strlen((string) $event['description']) > 120 ? '…' : ''; ?></p>
                            <?php endif; ?>

                            <a href="events.php" class="event-link">View Details</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <button class="carousel-btn carousel-prev" id="eventsPrev" type="button" aria-label="Previous event">&#10094;</button>
            <button class="carousel-btn carousel-next" id="eventsNext" type="button" aria-label="Next event">&#10095;</button>
            </div>
        <?php endif; ?>

        <div class="events-footer">
            <a href="events.php" class="primary-action">View All Events</a>
        </div>
    </div>
</section>

<section class="latest-sermons-section">
    <div class="max-w-7xl mx-auto px-4 py-20 md:py-24">
        <div class="sermons-header text-center mb-16 md:mb-20">
            <h2 class="sermons-title">Latest Sermons</h2>
            <p class="sermons-subtitle">Catch up on recent messages from our pulpit.</p>
        </div>

        <?php if (empty($latestSermons)): ?>
            <div class="empty-state">
                <p>No sermons have been published yet.</p>
            </div>
        <?php else: ?>
            <div class="carousel-wrapper">
            <div class="sermons-carousel" id="sermonsCarousel">
                <?php foreach ($latestSermons as $sermon): ?>
                    <?php
                        $dateDay = date('d', strtotime((string) $sermon['sermon_date']));
                        $dateMonth = date('M', strtotime((string) $sermon['sermon_date']));
                        $dateText = date('M d, Y', strtotime((string) $sermon['sermon_date']));
                        $topic = trim((string) ($sermon['topic'] ?? ''));
                        $title = (string) $sermon['title'];
                    ?>
                    <a href="sermons.php" class="sermon-card">
                        <div class="sermon-image-container">
                            <?php if ($sermon['sermon_image']): ?>
                                <img src="<?php echo htmlspecialchars($sermon['sermon_image']); ?>" alt="<?php echo htmlspecialchars($title); ?>" class="sermon-image" loading="lazy">
                            <?php else: ?>
                                <div class="sermon-image-placeholder"></div>
                            <?php endif; ?>
                            <div class="sermon-date-badge">
                                <span class="sermon-badge-day"><?php echo htmlspecialchars($dateDay); ?></span>
                                <span class="sermon-badge-month"><?php echo htmlspecialchars($dateMonth); ?></span>
                            </div>
                        </div>
                        <div class="sermon-content">
                            <h3 class="sermon-title"><?php echo htmlspecialchars($title); ?></h3>
                            <?php if ($topic !== ''): ?>
                                <p class="sermon-topic"><?php echo htmlspecialchars($topic); ?></p>
                            <?php endif; ?>
                            <p class="sermon-date"><?php echo htmlspecialchars($dateText); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <button class="carousel-btn carousel-prev" id="sermonsPrev" type="button" aria-label="Previous sermon">&#10094;</button>
            <button class="carousel-btn carousel-next" id="sermonsNext" type="button" aria-label="Next sermon">&#10095;</button>
            </div>
        <?php endif; ?>

        <div class="sermons-footer">
            <a href="sermons.php" class="primary-action">Browse All Sermons</a>
        </div>
    </div>
</section>

<section class="watch-online-band">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16 grid md:grid-cols-[1.4fr_1fr] gap-8 items-center">
        <div>
            <span class="tag-chip">Watch Online</span>
            <h2 class="watch-online-title mt-3">Sundays. Anywhere in the world.</h2>
            <p class="watch-online-copy mt-3">
                Join us live each week or revisit recent messages whenever you need a word. Wherever you are, the bridge reaches you.
            </p>
        </div>
        <div class="flex flex-wrap gap-3 md:justify-end">
            <a href="livestream.php" class="primary-action">Watch Live Service</a>
            <a href="sermons.php" class="secondary-action">Browse Archive</a>
        </div>
    </div>
</section>

<section class="testimonials-section">
    <div class="max-w-6xl mx-auto px-4 py-16 md:py-20">
        <div class="text-center max-w-2xl mx-auto">
            <span class="tag-chip">Stories from the Bridge</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-3">Lives Being Built</h2>
            <p class="mt-3 muted-copy">Real stories from people who call BMI home.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mt-10">
            <figure class="section-card">
                <blockquote class="text-base leading-relaxed">
                    "I came once to escape a hard week. I stayed because I finally felt seen. BMI didn't fix my life — but it walked me to the One who could."
                </blockquote>
                <figcaption class="mt-5 text-sm font-semibold">— Esi, member since 2022</figcaption>
            </figure>
            <figure class="section-card">
                <blockquote class="text-base leading-relaxed">
                    "Our marriage was on the edge. The pastors here didn't judge us — they prayed with us, taught us, and helped us rebuild. We're still here, three years on."
                </blockquote>
                <figcaption class="mt-5 text-sm font-semibold">— Kwame &amp; Akua</figcaption>
            </figure>
            <figure class="section-card">
                <blockquote class="text-base leading-relaxed">
                    "I came to BMI as a teenager who didn't believe in much. Today I lead a cell group and I'm finishing my degree. The Bridge changes lives. Mine is one of them."
                </blockquote>
                <figcaption class="mt-5 text-sm font-semibold">— David, youth ministry</figcaption>
            </figure>
        </div>

        <p class="text-xs muted-copy text-center mt-6">Names changed where requested.</p>
    </div>
</section>

<section class="next-steps-section">
    <div class="page-container py-section">
        <div class="section-intro">
            <span class="tag-chip">Take Your Next Step</span>
            <h2 class="section-heading">Let's Connect</h2>
            <p class="section-lead">Whether this is your first Sunday or you have been around for years, there is a next step waiting for you. Choose where to begin.</p>
        </div>

        <?php
            $sundayLine = setting('service.sunday_worship');
            $siteNameLine = setting('site.name', 'Bridge Ministries International');
            $addrLine = setting('contact.address');
            $metaParts = array_filter([$sundayLine, $siteNameLine, $addrLine]);
        ?>
        <?php if (!empty($metaParts)): ?>
            <div class="meta-highlight">
                <p class="meta-label">Find us this Sunday</p>
                <p class="meta-content"><?php echo htmlspecialchars(implode(' · ', $metaParts)); ?></p>
            </div>
        <?php endif; ?>

        <div class="next-steps-grid">
            <article class="next-step-card">
                <p class="next-step-number">01</p>
                <h3 class="next-step-title">Plan Your Visit</h3>
                <p class="next-step-copy">First time? We'll help you know exactly what to expect and walk you through your first Sunday.</p>
                <a href="visit.php" class="next-step-link">Plan a visit</a>
            </article>

            <article class="next-step-card">
                <p class="next-step-number">02</p>
                <h3 class="next-step-title">Find Your Community</h3>
                <p class="next-step-copy">From cell groups to age-based ministries, there is a place for you to belong and grow.</p>
                <a href="ministries.php" class="next-step-link">Explore ministries</a>
            </article>

            <article class="next-step-card">
                <p class="next-step-number">03</p>
                <h3 class="next-step-title">Send a Prayer Request</h3>
                <p class="next-step-copy">Whatever you're carrying, we'd be honoured to stand with you in prayer this week.</p>
                <a href="contact.php" class="next-step-link">Request prayer</a>
            </article>
        </div>
    </div>
</section>

<script>
/**
 * Generic horizontal scroll-snap carousel driver.
 * Wires up prev/next buttons + optional auto-play.
 */
document.addEventListener('DOMContentLoaded', function () {
    function initCarousel(opts) {
        var track = document.getElementById(opts.trackId);
        var prev = document.getElementById(opts.prevId);
        var next = document.getElementById(opts.nextId);
        if (!track) return;
        var items = track.children;
        if (!items.length) return;

        function step(dir) {
            var w = items[0].getBoundingClientRect().width;
            var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || 0) || 0;
            track.scrollBy({ left: dir * (w + gap), behavior: 'smooth' });
        }

        if (prev) prev.addEventListener('click', function () { step(-1); reset(); });
        if (next) next.addEventListener('click', function () { step(1); reset(); });

        var timer = null;
        function play() {
            if (!opts.autoPlay) return;
            timer = setInterval(function () {
                var maxScroll = track.scrollWidth - track.clientWidth;
                if (track.scrollLeft >= maxScroll - 4) {
                    track.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    step(1);
                }
            }, opts.autoPlay);
        }
        function stop() { if (timer) { clearInterval(timer); timer = null; } }
        function reset() { stop(); play(); }

        track.addEventListener('mouseenter', stop);
        track.addEventListener('mouseleave', play);
        track.addEventListener('touchstart', stop, { passive: true });
        play();
    }

    initCarousel({ trackId: 'servicesCarousel', prevId: 'carouselPrev', nextId: 'carouselNext', autoPlay: 4500 });
    initCarousel({ trackId: 'eventsCarousel',   prevId: 'eventsPrev',   nextId: 'eventsNext',   autoPlay: 0 });
    initCarousel({ trackId: 'sermonsCarousel',  prevId: 'sermonsPrev',  nextId: 'sermonsNext',  autoPlay: 0 });
});
</script>

<?php include 'includes/footer.php'; ?>
