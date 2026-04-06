<?php
$pageTitle = 'Home | Bridge Ministries International';
require_once __DIR__ . '/includes/db.php';

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
                <a href="contact.php" class="primary-action">Plan Your Visit</a>
                <a href="about.php" class="secondary-action">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<section class="overseer-section">
    <div class="max-w-6xl mx-auto px-4 py-16 md:py-20">
        <div class="overseer-card grid lg:grid-cols-[1fr_1.6fr] gap-10 lg:gap-12 items-center lg:items-start">
            <div class="overseer-profile">
                <img src="assets/image/IMG_1061.jpg" alt="Rev. Francis Duane Yalley, General Overseer" class="overseer-avatar">
                <p class="overseer-label mt-6">General Overseer</p>
                <h3 class="overseer-name mt-3">Rev. Francis Duane Yalley</h3>
            
            </div>

            <div class="overseer-content">
                <p class="overseer-copy">
                    Rev. Francis Duane Yalley is the Presiding General Overseer of Bridge Ministries International, headquartered in Accra, Ghana. A visionary leader with unwavering commitment to sound doctrine, he has dedicated his ministry to teaching the Word of God with clarity and power, building a movement rooted in faith, discipleship, and transformational leadership.
                </p>
                <p class="overseer-copy mt-5">
                    Bridge Ministries International has grown to encompass multiple thriving congregations and cell-based ministries, reaching thousands of believers across Ghana and beyond. Known throughout the region as a passionate advocate for biblical truth and strategic prayer, Rev. Yalley continues to shepherd the church with a clear vision: to develop mature believers who influence their communities, strengthen families, and advance God's kingdom with purpose and conviction.
                </p>
            </div>
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
                    <img src="assets/image/IMG_1099.jpg" alt="RESTORERS - Celebration Service" class="service-image">
                    <div class="service-overlay">
                        <h3 class="service-label">RESTORERS</h3>
                        <p class="service-detail">Celebration Service</p>
                        <p class="service-detail">Sunday 8:45 AM</p>
                    </div>
                </div>
                <div class="service-image-card">
                    <img src="assets/image/IMG_1104.jpg" alt="REPAIRERS - Cell Meetings" class="service-image">
                    <div class="service-overlay">
                        <h3 class="service-label">REPAIRERS</h3>
                        <p class="service-detail">Cell Meetings</p>
                        <p class="service-detail">Wednesdays</p>
                    </div>
                </div>
                <div class="service-image-card">
                    <img src="assets/image/IMG_1111.jpg" alt="SWITCH ON - Youth Service" class="service-image">
                    <div class="service-overlay">
                        <h3 class="service-label">SWITCH ON</h3>
                        <p class="service-detail">Youth Service</p>
                        <p class="service-detail">Sunday 8:45 AM</p>
                    </div>
                </div>
                <div class="service-image-card">
                    <img src="assets/image/IMG_1094.jpg" alt="BUILDERS - Leadership Meeting" class="service-image">
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
            <div class="text-center py-12">
                <p class="text-white text-lg">No upcoming events are available at the moment.</p>
            </div>
        <?php else: ?>
            <div class="events-grid">
                <?php foreach ($upcomingEvents as $event): ?>
                    <?php
                        $chipDay = date('d', strtotime((string) $event['event_date']));
                        $chipMonth = date('M', strtotime((string) $event['event_date']));
                        $eventTime = !empty($event['event_time']) ? date('g:i A', strtotime((string) $event['event_time'])) : null;
                        $venue = trim((string) ($event['venue'] ?? ''));
                    ?>
                    <div class="event-card">
                        <div class="event-image-container">
                            <?php if ($event['event_image']): ?>
                                <img src="<?php echo htmlspecialchars($event['event_image']); ?>" alt="<?php echo htmlspecialchars((string) $event['title']); ?>" class="event-image">
                            <?php else: ?>
                                <div class="event-image-placeholder"></div>
                            <?php endif; ?>
                            <div class="event-date-badge">
                                <span class="badge-day"><?php echo htmlspecialchars($chipDay); ?></span>
                                <span class="badge-month"><?php echo htmlspecialchars($chipMonth); ?></span>
                            </div>
                        </div>
                        <div class="event-content">
                            <h3 class="event-title"><?php echo htmlspecialchars((string) $event['title']); ?></h3>
                            <div class="event-details">
                                <?php if ($eventTime): ?>
                                    <p class="event-detail-item">
                                        <span class="detail-icon">⏰</span>
                                        <?php echo htmlspecialchars($eventTime); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($venue): ?>
                                    <p class="event-detail-item">
                                        <span class="detail-icon">📍</span>
                                        <?php echo htmlspecialchars($venue); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($event['description'])): ?>
                                <p class="event-description"><?php echo htmlspecialchars(substr((string) $event['description'], 0, 120)); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
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
            <div class="text-center py-12">
                <p class="text-neutral-600 text-lg">No sermons have been published yet.</p>
            </div>
        <?php else: ?>
            <div class="sermons-grid">
                <?php foreach ($latestSermons as $sermon): ?>
                    <?php
                        $dateText = date('M d, Y', strtotime((string) $sermon['sermon_date']));
                        $dateDay = date('d', strtotime((string) $sermon['sermon_date']));
                        $dateMonth = date('M', strtotime((string) $sermon['sermon_date']));
                        $topic = trim((string) ($sermon['topic'] ?? ''));
                        $title = (string) $sermon['title'];
                    ?>
                    <a href="sermons.php" class="sermon-card">
                        <div class="sermon-image-container">
                            <div class="sermon-image-placeholder"></div>
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
        <?php endif; ?>

        <div class="sermons-footer">
            <a href="sermons.php" class="primary-action">Browse All Sermons</a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('servicesCarousel');
    const prevBtn = document.getElementById('carouselPrev');
    const nextBtn = document.getElementById('carouselNext');
    const cards = carousel.querySelectorAll('.service-image-card');
    
    if (!carousel || !prevBtn || !nextBtn || cards.length === 0) return;
    
    const cardWidth = cards[0].offsetWidth;
    const gap = 24; // 1.5rem in pixels (1rem = 16px)
    const cardTotalWidth = cardWidth + gap;
    const autoPlayInterval = 4000; // 4 seconds
    
    function scrollToCard(index) {
        const maxIndex = cards.length;
        const normalizedIndex = ((index % maxIndex) + maxIndex) % maxIndex;
        const scrollAmount = normalizedIndex * cardTotalWidth;
        carousel.scrollLeft = scrollAmount;
    }
    
    let currentIndex = 0;
    let autoPlayTimer;
    
    function startAutoPlay() {
        autoPlayTimer = setInterval(function() {
            currentIndex = (currentIndex + 1) % cards.length;
            scrollToCard(currentIndex);
        }, autoPlayInterval);
    }
    
    function resetAutoPlay() {
        clearInterval(autoPlayTimer);
        startAutoPlay();
    }
    
    prevBtn.addEventListener('click', function() {
        currentIndex = (currentIndex - 1 + cards.length) % cards.length;
        scrollToCard(currentIndex);
        resetAutoPlay();
    });
    
    nextBtn.addEventListener('click', function() {
        currentIndex = (currentIndex + 1) % cards.length;
        scrollToCard(currentIndex);
        resetAutoPlay();
    });
    
    // Start auto-play on load
    startAutoPlay();
});
</script>

<?php include 'includes/footer.php'; ?>
