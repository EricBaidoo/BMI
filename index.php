<?php
$pageTitle = 'Bridge Ministries International';
$pageDescription = 'An international ministry dedicated to empowering communities, teaching uncompromised biblical truth, and fostering a global legacy of faith and action.';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';

$upcomingEvents = [];
$latestSermons = [];
$heroSlides = [];
$testimonies = [];
$weeklyServices = [];

try {
    $pdo = db_connect();
    $stmt = $pdo->query("SELECT id, title, event_type, description, event_date, end_date, event_time, venue, event_image FROM events WHERE event_date >= CURDATE() OR (end_date IS NOT NULL AND end_date >= CURDATE()) ORDER BY event_date ASC LIMIT 3");
    $upcomingEvents = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT id, title, sermon_date, topic, sermon_image FROM sermons ORDER BY sermon_date DESC LIMIT 3");
    $latestSermons = $stmt->fetchAll();

    $heroSlides = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC")->fetchAll();
    $testimonies = $pdo->query("SELECT * FROM testimonies ORDER BY sort_order ASC")->fetchAll();
    $weeklyServices = $pdo->query("SELECT * FROM weekly_services WHERE show_on_homepage = 1 ORDER BY sort_order ASC")->fetchAll();
} catch (Throwable $e) {}

include 'includes/header.php';
?>

<!-- OPTION C: TRUE CINEMATIC REDESIGN -->

<!-- HERO SECTION: Dynamic Slider (Server-Side Rendered) -->
<section class="relative w-full h-screen min-h-[50rem] bg-black overflow-hidden" id="hero-slider">
    
    <?php if (!empty($heroSlides)): ?>
    <?php foreach ($heroSlides as $index => $slide): ?>
        <div class="hero-slide absolute inset-0 z-0 transition-opacity duration-1000 ease-in-out <?php echo $index === 0 ? 'opacity-100' : 'opacity-0'; ?>"
             data-slide-index="<?php echo $index; ?>">
             
            <!-- Background Media -->
            <?php 
                $bgImage = $slide['bg_image'] ?? '';
                $isVideo = preg_match('/\.(mp4|webm)$/i', $bgImage);
            ?>
            <?php if ($isVideo): ?>
                <video src="<?php echo htmlspecialchars($bgImage); ?>" class="absolute inset-0 w-full h-full object-cover" autoplay loop muted playsinline></video>
            <?php else: ?>
                <img src="<?php echo htmlspecialchars($bgImage); ?>" alt="<?php echo htmlspecialchars(strip_tags($slide['title'] ?? '')); ?>" class="absolute inset-0 w-full h-full object-cover">
            <?php endif; ?>
            
            <!-- Dark Overlay (subtle, preserves image visibility) -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-black/20 z-[1]"></div>
            
            <!-- Text Content -->
            <div class="hero-slide-content absolute inset-0 z-10 w-full h-full flex flex-col justify-center items-center text-center px-6 mt-16 transition-all duration-700 <?php echo $index === 0 ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'; ?>">
                 
                <span class="font-sans font-bold text-white tracking-[0.3em] md:tracking-[0.5em] uppercase text-xs md:text-sm mb-6">
                    Welcome To Bridge Ministries
                </span>
                
                <h1 class="font-display font-black text-5xl sm:text-6xl md:text-[5.5rem] lg:text-[7rem] tracking-normal text-white leading-[0.9] uppercase max-w-[90%] mx-auto">
                    <?php echo $slide['title'] ?? ''; ?>
                </h1>
                
                <p class="mt-6 text-white/80 text-lg md:text-xl max-w-2xl font-medium">
                    <?php echo htmlspecialchars($slide['subtitle'] ?? ''); ?>
                </p>
                
                <?php if (!empty($slide['button_text'])): ?>
                <div class="mt-10 md:mt-12 flex flex-col sm:flex-row gap-4">
                    <a href="<?php echo htmlspecialchars($slide['button_url'] ?? '#'); ?>" class="btn bg-white text-black hover:bg-neutral-200 px-10 py-4 font-sans font-bold uppercase tracking-widest text-sm rounded-none border border-transparent transition-all">
                        <?php echo htmlspecialchars($slide['button_text']); ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php else: ?>
        <!-- Fallback when no slides exist -->
        <div class="absolute inset-0 z-0 flex items-center justify-center">
            <div class="text-center px-6">
                <span class="font-sans font-bold text-white tracking-[0.3em] md:tracking-[0.5em] uppercase text-xs md:text-sm mb-6 block">
                    Welcome To Bridge Ministries
                </span>
                <h1 class="font-display font-black text-5xl sm:text-6xl md:text-[5.5rem] lg:text-[7rem] tracking-normal text-white leading-[0.9] uppercase">
                    Faith In <span class="text-[#c49a45]">Action.</span>
                </h1>
            </div>
        </div>
    <?php endif; ?>

    <!-- FLOATING LIVESTREAM BADGE (Static Overlay) -->
    <?php if (setting('live.is_streaming_now') == '1'): ?>
    <a href="livestream.php" class="absolute top-32 right-6 md:right-12 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md border border-white/10 rounded-full pr-8 pl-3 py-3 hover:bg-black/80 hover:border-accent/50 transition-all duration-700 group overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-accent/0 via-accent/10 to-accent/0 opacity-0 group-hover:opacity-100 group-hover:animate-[shimmer_2s_infinite] transition-opacity duration-500"></div>
        <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-4 relative z-10">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-60"></span>
            <svg class="w-4 h-4 text-black ml-0.5 relative z-20" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        </div>
        <div class="flex flex-col relative z-10 text-left">
            <span class="text-white font-sans font-semibold uppercase tracking-[0.2em] text-[0.625rem] leading-none">Watch Live</span>
            <span class="text-accent text-[0.55rem] font-bold uppercase tracking-[0.3em] mt-1.5 leading-none">Streaming Now</span>
        </div>
    </a>
    <?php endif; ?>

    <!-- Slider Dots Navigation -->
    <?php if (count($heroSlides) > 1): ?>
    <div class="absolute bottom-12 left-0 right-0 z-20 flex justify-center gap-3">
        <?php foreach ($heroSlides as $index => $slide): ?>
            <button onclick="goToHeroSlide(<?php echo $index; ?>)" 
                    class="hero-dot w-3 h-3 rounded-full transition-all duration-500 border border-white/50 <?php echo $index === 0 ? 'bg-white scale-125' : 'bg-transparent hover:bg-white/30'; ?>"
                    data-dot-index="<?php echo $index; ?>">
            </button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<!-- Hero Slider JS (vanilla, no dependencies) -->
<script>
(function() {
    var slides = document.querySelectorAll('.hero-slide');
    var dots = document.querySelectorAll('.hero-dot');
    var currentSlide = 0;
    var totalSlides = slides.length;
    var autoTimer = null;

    if (totalSlides < 2) return;

    function showSlide(index) {
        slides.forEach(function(slide, i) {
            var content = slide.querySelector('.hero-slide-content');
            if (i === index) {
                slide.classList.remove('opacity-0');
                slide.classList.add('opacity-100');
                if (content) {
                    content.classList.remove('opacity-0', 'translate-y-8');
                    content.classList.add('opacity-100', 'translate-y-0');
                }
            } else {
                slide.classList.remove('opacity-100');
                slide.classList.add('opacity-0');
                if (content) {
                    content.classList.remove('opacity-100', 'translate-y-0');
                    content.classList.add('opacity-0', 'translate-y-8');
                }
            }
        });
        dots.forEach(function(dot, i) {
            if (i === index) {
                dot.classList.remove('bg-transparent', 'hover:bg-white/30');
                dot.classList.add('bg-white', 'scale-125');
            } else {
                dot.classList.remove('bg-white', 'scale-125');
                dot.classList.add('bg-transparent', 'hover:bg-white/30');
            }
        });
        currentSlide = index;
    }

    function nextSlide() {
        showSlide((currentSlide + 1) % totalSlides);
    }

    function startAuto() {
        if (autoTimer) clearInterval(autoTimer);
        autoTimer = setInterval(nextSlide, 6000);
    }

    // Expose globally for dot onclick
    window.goToHeroSlide = function(index) {
        showSlide(index);
        startAuto(); // Reset timer on manual navigation
    };

    // Pause on hover
    var heroSection = document.getElementById('hero-slider');
    if (heroSection) {
        heroSection.addEventListener('mouseenter', function() { clearInterval(autoTimer); });
        heroSection.addEventListener('mouseleave', startAuto);
    }

    // Pause when tab is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) { clearInterval(autoTimer); } else { startAuto(); }
    });

    startAuto();
})();
</script>

<!-- ANIMATED MARQUEE TICKER: Monochromatic & Huge -->
<div class="w-full overflow-hidden bg-black py-6 border-b border-white/5 relative z-20">
    <div class="whitespace-nowrap flex items-center animate-marquee font-sans font-medium tracking-[0.3em] uppercase text-[0.625rem] text-white/40">
        <?php for ($m = 0; $m < 3; $m++): ?>
        <span class="mx-8 flex items-center gap-12">
            <span><?php echo htmlspecialchars(setting('home.marquee_text1', 'WORSHIP WITH US IN PERSON')); ?></span> 
            <span class="w-1 h-1 bg-white/20 rounded-full"></span> 
            <span class="text-white"><?php echo htmlspecialchars(setting('home.marquee_text2', 'OR JOIN OUR LIVESTREAM')); ?></span> 
            <span class="w-1 h-1 bg-white/20 rounded-full"></span> 
            <span><?php echo htmlspecialchars(setting('home.marquee_text3', 'EXPERIENCE TRANSFORMATION')); ?></span> 
            <span class="w-1 h-1 bg-white/20 rounded-full"></span> 
            <span><?php echo htmlspecialchars(setting('home.marquee_text4', 'UNCOMPROMISED TRUTH')); ?></span> 
            <span class="w-1 h-1 bg-white/20 rounded-full"></span>
            <span><?php echo htmlspecialchars(setting('home.marquee_text5', 'A LEGACY OF ENDURING FAITH')); ?></span>
        </span>
        <?php endfor; ?>
    </div>
</div>

<!-- MEET THE FOUNDER: Cinematic Dark Asymmetry -->
<section class="py-20 md:py-24 bg-black relative overflow-hidden">
    <!-- Massive Watermark -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[15rem] md:text-[25rem] font-display font-black text-white/[0.02] whitespace-nowrap pointer-events-none select-none z-0 tracking-normal">
        YALLEY
    </div>
    
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-8 items-center">
            
            <!-- Biography Content -->
            <div class="lg:col-span-6 lg:col-start-1 order-2 lg:order-1 relative z-20" class="reveal-right">
                <div class="inline-flex items-center gap-6 mb-12">
                    <span class="text-white/50 font-sans font-medium text-[0.625rem] tracking-[0.3em] uppercase">Meet The Founder</span>
                    <div class="h-px w-16 bg-white/20"></div>
                </div>
                
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-display text-white mb-10 leading-[1.1] tracking-tight text-balance">
                    <?= setting('home.founder_title', 'A voice of restoration, a builder of lives & <i class="text-white/50">a repairer of destinies.</i>') ?>
                </h2>
                
                <div class="font-sans font-light text-white/60 leading-relaxed max-w-lg text-sm md:text-base mb-12">
                    <p class="mb-8">
                        <?= setting('home.founder_bio1', 'From the vibrant nation of Ghana in West Africa emerges Reverend Francis Duane Yalley, affectionately known as “The Repairer.” For over two decades, he has served as the General Overseer and founder of Bridge Ministries International, building not just a church, but a global movement dedicated to switching on lives and repairing destinies.') ?>
                    </p>
                    
                    <p class="text-white italic font-display text-2xl leading-snug border-l border-white/20 pl-6">
                        "<?= setting('home.founder_bio2', 'He is more than a preacher. He is a restorer of broken lives. A builder of leaders. A voice of prophetic clarity.') ?>"
                    </p>
                </div>
                
                <a href="about.php" class="btn btn-outline-white">
                    Read Biography
                </a>
            </div>

            <!-- Portrait -->
            <div class="lg:col-span-5 lg:col-start-8 order-1 lg:order-2 relative group" class="reveal-left">
                <!-- Floating Portrait -->
                <div class="relative w-full aspect-[3/4] overflow-hidden bg-[#0a0a0a] z-10 rounded-lg">
                    <img src="<?= setting('home.founder_image', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=800&auto=format&fit=crop') ?>" alt="Rev. Francis Duane Yalley" class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent z-10"></div>
                </div>
                
                <!-- Accents -->
                <div class="absolute -bottom-8 -left-8 w-48 h-48 border border-white/10 z-0 rounded-full"></div>
            </div>
            
        </div>
    </div>
</section>

<!-- OUR MISSION: High Contrast Megachurch Layout -->
<section class="py-24 md:py-32 bg-[#F5F5F5] relative overflow-hidden text-black">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 lg:gap-24 items-center">
            
            <div class="reveal-left">
                <div class="inline-flex items-center gap-6 mb-12">
                    <span class="text-black/50 font-sans font-bold text-[0.625rem] tracking-[0.3em] uppercase">Our Mission</span>
                    <div class="h-px w-16 bg-black/20"></div>
                </div>
                
                <h2 class="text-4xl md:text-5xl lg:text-7xl font-display font-black text-black mb-10 leading-[1.0] tracking-normal uppercase">
                    <?= setting('home.mission_title', 'ROOTED IN FAITH.<br>REACHING THE WORLD.') ?>
                </h2>
                
                <p class="text-black/60 font-sans font-medium text-lg leading-relaxed mb-12 max-w-md">
                    <?= setting('home.mission_text', 'Under the leadership of our General Overseer, Bridge Ministries International operates with a profound commitment to establishing a lasting, positive impact on individuals and society.') ?>
                </p>
                
                <div class="mt-8 reveal delay-200">
                    <a href="about" class="btn bg-transparent text-black hover:bg-black hover:text-white px-10 py-4 font-sans font-bold uppercase tracking-widest text-sm rounded-none border border-black transition-all">
                        About Us &rarr;
                    </a>
                </div>
            </div>

            <div class="reveal-right relative">
                <!-- Bento Box Style Image Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="relative w-full aspect-[4/5] bg-neutral-200 rounded-3xl overflow-hidden shadow-2xl translate-y-12">
                        <img src="<?= setting('home.mission_bg', 'assets/image/chad-kirchoff-ivqGyYLtBI8-unsplash.jpg') ?>" alt="Community Outreach" class="absolute inset-0 w-full h-full object-cover hover:scale-105 transition-transform duration-1000" onerror="this.src='https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=1000&auto=format&fit=crop';">
                    </div>
                    <div class="relative w-full aspect-[4/5] bg-neutral-200 rounded-3xl overflow-hidden shadow-2xl -translate-y-4">
                        <img src="assets/image/IMG_1061.jpg" alt="Worship" class="absolute inset-0 w-full h-full object-cover hover:scale-105 transition-transform duration-1000" onerror="this.src='https://images.unsplash.com/photo-1438232992991-995b7058bbb3?q=80&w=1000&auto=format&fit=crop';">
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- BY THE NUMBERS (DYNAMIC COUNTERS) -->
<section class="py-20 bg-black relative border-y border-white/5">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="grid grid-cols-2 max-w-3xl mx-auto gap-8 text-center" id="counter-section">
            <div class="p-8 border-r border-white/5 reveal">
                <div class="text-white text-5xl md:text-6xl font-display font-normal mb-4 leading-none"><span class="counter" data-target="<?php echo (int)setting('home.counter1_number', 20); ?>">0</span><span class="text-white/30">+</span></div>
                <div class="text-white/40 font-sans font-medium uppercase tracking-[0.3em] text-[0.625rem]"><?php echo htmlspecialchars(setting('home.counter1_label', 'Years of Ministry')); ?></div>
            </div>
            <div class="p-8 reveal delay-100">
                <div class="text-white text-5xl md:text-6xl font-display font-normal mb-4 leading-none"><span class="counter" data-target="<?php echo (int)setting('home.counter2_number', 10); ?>">0</span><span class="text-white/30">K+</span></div>
                <div class="text-white/40 font-sans font-medium uppercase tracking-[0.3em] text-[0.625rem]"><?php echo htmlspecialchars(setting('home.counter2_label', 'Lives Impacted')); ?></div>
            </div>
        </div>
    </div>
</section>

<!-- FLOATING DARK SECTION: Testimonies -->
<section class="py-24 bg-[#F5F5F5] relative overflow-hidden" id="testimonies-section">
    <div class="w-[95%] max-w-[112.5rem] mx-auto bg-[#111111] rounded-[2.5rem] relative z-10 px-8 py-20 md:p-24 shadow-2xl">
        
        <div class="flex flex-col text-center items-center mb-24 reveal">
            <h2 class="text-4xl md:text-7xl text-white font-display font-black tracking-normal uppercase">Stories of Impact</h2>
            <div class="h-1 w-24 bg-amber-500 mt-8 rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($testimonies as $index => $test): ?>
                <div class="bg-black/50 backdrop-blur-md rounded-3xl p-10 lg:p-12 flex flex-col group border border-white/5 reveal delay-<?php echo ($index % 3) * 100; ?>">
                    <svg class="w-8 h-8 text-amber-500 mb-8 transform group-hover:-translate-y-2 transition-all duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                    
                    <p class="text-white/80 font-sans font-medium text-lg leading-relaxed flex-grow mb-12">
                        "<?php echo htmlspecialchars($test['quote']); ?>"
                    </p>
                    
                    <div class="flex flex-col pt-8 border-t border-white/10">
                        <h4 class="text-white font-sans font-bold uppercase tracking-[0.1em] text-sm mb-1"><?php echo htmlspecialchars($test['author_name']); ?></h4>
                        <span class="text-white/40 text-[0.625rem] font-sans uppercase tracking-[0.2em]"><?php echo htmlspecialchars($test['author_role']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- TEACHINGS & EVENTS: Deep Dark Cinematic Cards -->
<section class="py-24 bg-[#000000] relative border-t border-white/5">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-24">
            
            <!-- Latest Sermons -->
            <div>
                <div class="flex justify-between items-end mb-16 reveal">
                    <h2 class="text-4xl md:text-5xl font-display font-black text-white uppercase tracking-normal">Teachings</h2>
                    <a href="sermons" class="text-amber-500 hover:text-amber-400 font-sans text-xs uppercase tracking-widest transition-colors font-bold">View All &rarr;</a>
                </div>
                
                <div class="flex flex-col gap-6">
                    <?php if (!empty($latestSermons)): ?>
                        <?php foreach ($latestSermons as $index => $sermon): ?>
                            <?php $dateText = date('M d, Y', strtotime((string) $sermon['sermon_date'])); ?>
                            <a href="sermons" class="group flex items-center justify-between p-8 bg-[#0a0a0a] rounded-3xl border border-white/5 hover:border-amber-500/30 transition-all hover:-translate-y-1 shadow-2xl reveal delay-<?php echo $index * 100; ?>">
                                <div>
                                    <span class="block text-amber-500 text-xs font-sans font-bold uppercase tracking-widest mb-3"><?php echo htmlspecialchars($dateText); ?></span>
                                    <h3 class="text-2xl font-display font-black text-white mb-2 tracking-tight"><?php echo htmlspecialchars((string) $sermon['title']); ?></h3>
                                    <?php if (!empty($sermon['topic'])): ?>
                                        <p class="text-white/40 font-sans text-xs tracking-wide uppercase font-medium"><?php echo htmlspecialchars($sermon['topic']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-black group-hover:border-amber-500 transition-all">
                                    <svg class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Upcoming Events -->
            <div>
                <div class="flex justify-between items-end mb-16 reveal">
                    <h2 class="text-4xl md:text-5xl font-display font-black text-white uppercase tracking-normal">Events</h2>
                    <a href="events" class="text-amber-500 hover:text-amber-400 font-sans text-xs uppercase tracking-widest transition-colors font-bold">View All &rarr;</a>
                </div>
                
                <div class="flex flex-col gap-6">
                    <?php if (!empty($upcomingEvents)): ?>
                        <?php foreach ($upcomingEvents as $index => $event): ?>
                            <?php 
                                $dateMonth = date('M', strtotime((string) $event['event_date']));
                                $dateDay = date('d', strtotime((string) $event['event_date']));
                            ?>
                            <a href="event-detail.php?id=<?php echo (int) $event['id']; ?>" class="group flex items-stretch bg-[#0a0a0a] rounded-3xl border border-white/5 overflow-hidden hover:border-amber-500/30 transition-all hover:-translate-y-1 shadow-2xl reveal delay-<?php echo $index * 100; ?>">
                                <div class="w-32 bg-white/5 flex flex-col items-center justify-center py-8 text-white/50 group-hover:text-amber-500 group-hover:bg-amber-500/10 transition-colors">
                                    <span class="text-4xl font-display font-black leading-none tracking-normal"><?php echo $dateDay; ?></span>
                                    <span class="text-xs font-sans font-bold uppercase tracking-widest mt-2"><?php echo $dateMonth; ?></span>
                                </div>
                                <div class="p-8 flex-grow">
                                    <h3 class="text-2xl font-display font-black text-white mb-3 tracking-tight"><?php echo htmlspecialchars((string) $event['title']); ?></h3>
                                    <p class="text-white/40 font-sans text-xs tracking-widest uppercase font-medium flex items-center gap-2">
                                        <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <?php echo htmlspecialchars((string) (!empty($event['venue']) ? $event['venue'] : 'Online')); ?>
                                    </p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- MINISTRIES THAT MOVE YOU -->
<section class="py-24 md:py-32 bg-[#F5F5F5] relative overflow-hidden text-black border-t border-black/5">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-8 items-center">
            
            <!-- Left Column: Typography -->
            <div class="lg:col-span-5 reveal-left">
                <h2 class="text-4xl md:text-5xl lg:text-5xl font-display font-black tracking-normal uppercase leading-[1.0] mb-8 text-black">
                    Ministries<br>&<br>Services
                </h2>
                <p class="text-black/70 font-sans font-medium text-lg lg:text-xl leading-relaxed max-w-lg">
                    From specialized youth and young adult groups to impactful weekly services, we create spaces for growth, service, and lasting connection. Wherever you are on your journey, there's a place for you here.
                </p>
                <div class="mt-10">
                    <a href="ministries.php" class="btn bg-transparent text-black hover:bg-black hover:text-white px-10 py-4 font-sans font-bold uppercase tracking-widest text-sm rounded-none border border-black transition-all">
                        Explore All &rarr;
                    </a>
                </div>
            </div>

            <!-- Right Column: Asymmetrical Bento Grid -->
            <div class="lg:col-span-7 reveal-right">
                <div class="grid grid-cols-2 gap-4 md:gap-6">
                    
                    <?php 
                    // Split services into two columns for the staggered layout
                    $col1 = [];
                    $col2 = [];
                    foreach ($weeklyServices as $index => $service) {
                        if ($index % 2 == 0) {
                            $col1[] = $service;
                        } else {
                            $col2[] = $service;
                        }
                    }
                    ?>
                    
                    <!-- Col 1 -->
                    <div class="flex flex-col gap-4 md:gap-6">
                        <?php foreach ($col1 as $index => $service): 
                            $isShort = ($index % 2 == 0); // Short, Tall, Short, Tall...
                            $minHeight = $isShort ? '15.625rem' : '25rem';
                            $imgSrc = empty($service['image_url']) ? 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=800&auto=format&fit=crop' : (strpos($service['image_url'], 'http') === 0 ? htmlspecialchars($service['image_url']) : '/BMI/' . htmlspecialchars($service['image_url']));
                        ?>
                        <a href="ministry_detail.php?id=<?= $service['id'] ?>" class="group relative w-full rounded-2xl md:rounded-[2rem] overflow-hidden shadow-2xl block" style="min-height: <?= $minHeight ?>;">
                            <img src="<?= $imgSrc ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" alt="<?= htmlspecialchars($service['title']) ?>">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-6 left-6 right-6 flex flex-col justify-end">
                                <h3 class="text-white font-display font-bold text-2xl md:text-3xl uppercase mb-1"><?= htmlspecialchars($service['title']) ?></h3>
                                <?php if (!empty($service['subtitle']) || !empty($service['time_info'])): ?>
                                <p class="text-white/80 font-sans text-sm mb-3">
                                    <?= htmlspecialchars(implode(' · ', array_filter([$service['subtitle'], $service['time_info']]))) ?>
                                </p>
                                <?php endif; ?>
                                <div class="w-10 h-10 rounded-full bg-black flex items-center justify-center text-white border border-white/20 group-hover:bg-white group-hover:text-black transition-colors shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Col 2 (Staggered offset) -->
                    <div class="flex flex-col gap-4 md:gap-6 pt-12 md:pt-16">
                        <?php foreach ($col2 as $index => $service): 
                            $isTall = ($index % 2 == 0); // Tall, Short, Tall, Short...
                            $minHeight = $isTall ? '25rem' : '15.625rem';
                            $imgSrc = empty($service['image_url']) ? 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?q=80&w=800&auto=format&fit=crop' : (strpos($service['image_url'], 'http') === 0 ? htmlspecialchars($service['image_url']) : '/BMI/' . htmlspecialchars($service['image_url']));
                        ?>
                        <a href="ministry_detail.php?id=<?= $service['id'] ?>" class="group relative w-full rounded-2xl md:rounded-[2rem] overflow-hidden shadow-2xl block" style="min-height: <?= $minHeight ?>;">
                            <img src="<?= $imgSrc ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" alt="<?= htmlspecialchars($service['title']) ?>">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-6 left-6 right-6 flex flex-col justify-end">
                                <h3 class="text-white font-display font-bold text-2xl md:text-3xl uppercase mb-1"><?= htmlspecialchars($service['title']) ?></h3>
                                <?php if (!empty($service['subtitle']) || !empty($service['time_info'])): ?>
                                <p class="text-white/80 font-sans text-sm mb-3">
                                    <?= htmlspecialchars(implode(' · ', array_filter([$service['subtitle'], $service['time_info']]))) ?>
                                </p>
                                <?php endif; ?>
                                <div class="w-10 h-10 rounded-full bg-black flex items-center justify-center text-white border border-white/20 group-hover:bg-white group-hover:text-black transition-colors shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</section>

<!-- PODCAST FEATURE SECTION -->
<section class="py-24 md:py-32 bg-[#F5F5F5] relative overflow-hidden">
    <div class="w-[95%] md:w-[90%] max-w-[112.5rem] mx-auto relative z-10 bg-[#050505] rounded-[2.5rem] md:rounded-[4rem] shadow-2xl overflow-hidden border border-white/5 reveal">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 items-center">
            
            <!-- Left Side: Phone Mockup -->
            <div class="relative w-full h-[30rem] lg:h-[45rem] bg-[#0a0a0a] flex items-center justify-center overflow-hidden order-2 lg:order-1 border-r border-white/5">
                <!-- Abstract Glow Behind Phone -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-amber-500/20 rounded-full blur-[5rem]"></div>
                
                <!-- iPhone Mockup CSS -->
                <div class="relative z-10 w-[17.5rem] md:w-[20rem] h-[36.25rem] md:h-[40.625rem] bg-black rounded-[3rem] border-[0.75rem] border-[#222] shadow-2xl flex flex-col overflow-hidden translate-y-8 lg:translate-y-12 group hover:translate-y-4 lg:hover:translate-y-6 transition-transform duration-700">
                    <!-- Dynamic Island Notch -->
                    <div class="absolute top-4 left-1/2 -translate-x-1/2 w-24 h-6 bg-black rounded-full z-20"></div>
                    
                    <!-- App Interface (Spotify-esque) -->
                    <div class="flex-grow bg-gradient-to-b from-indigo-950 via-gray-900 to-black w-full h-full p-6 flex flex-col text-white pt-14">
                        <div class="flex justify-between items-center mb-8">
                            <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            <span class="text-xs font-bold tracking-widest text-white/70 uppercase">Bridge Ministries</span>
                            <svg class="w-5 h-5 text-white/70" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8a2 2 0 100-4 2 2 0 000 4zm0 6a2 2 0 100-4 2 2 0 000 4zm0 6a2 2 0 100-4 2 2 0 000 4z"/></svg>
                        </div>
                        
                        <!-- Album Art -->
                        <div class="w-full aspect-square bg-indigo-900 rounded-xl mb-8 shadow-2xl overflow-hidden relative group-hover:scale-[1.02] transition-transform duration-500">
                            <img src="https://images.unsplash.com/photo-1589903308904-1010c2294adc?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover opacity-80" alt="Podcast Art">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-4">
                                <span class="font-display font-black text-2xl uppercase leading-none tracking-normal">Glory In The<br>Changing</span>
                            </div>
                        </div>
                        
                        <h4 class="font-bold text-xl mb-1">Rev. Yalley</h4>
                        <p class="text-white/50 text-sm font-medium mb-6">Bridge Ministries Podcast</p>
                        
                        <!-- Scrubber -->
                        <div class="w-full h-1 bg-white/20 rounded-full mb-2">
                            <div class="w-1/3 h-full bg-white rounded-full relative">
                                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full"></div>
                            </div>
                        </div>
                        <div class="flex justify-between text-[0.6rem] text-white/50 font-bold mb-6">
                            <span>12:04</span>
                            <span>-34:21</span>
                        </div>
                        
                        <!-- Controls -->
                        <div class="flex justify-between items-center px-2">
                            <svg class="w-5 h-5 text-white/70" fill="currentColor" viewBox="0 0 24 24"><path d="M10.5 4v16a1.5 1.5 0 01-1.5 1.5H5a1.5 1.5 0 01-1.5-1.5V4A1.5 1.5 0 015 2.5h4a1.5 1.5 0 011.5 1.5zm10 0v16a1.5 1.5 0 01-1.5 1.5h-4a1.5 1.5 0 01-1.5-1.5V4A1.5 1.5 0 0115 2.5h4a1.5 1.5 0 011.5 1.5z"/></svg>
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M11 5L4 12l7 7V5z"/><path d="M18 5l-7 7 7 7V5z"/></svg>
                            <div class="w-16 h-16 bg-white text-black rounded-full flex items-center justify-center cursor-pointer hover:scale-105 transition-transform">
                                <svg class="w-8 h-8 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M13 5l7 7-7 7V5z"/><path d="M6 5l7 7-7 7V5z"/></svg>
                            <svg class="w-5 h-5 text-white/70" fill="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Content -->
            <div class="p-12 md:p-20 order-1 lg:order-2 flex flex-col justify-center">
                <h2 class="text-4xl md:text-5xl lg:text-5xl font-display font-black text-white tracking-normal uppercase leading-[0.95] mb-8">
                    Listen To Our<br>Podcast
                </h2>
                <p class="text-white/60 font-sans font-medium text-lg leading-relaxed mb-12 max-w-lg">
                    Stream Bridge Ministries on Spotify or anywhere you listen to podcasts anytime. Catch your favorite messages that fuel your faith and provide clarity to your season.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="https://spotify.com" target="_blank" class="flex items-center justify-center gap-3 px-8 py-4 bg-transparent border border-white/20 text-white rounded-xl hover:bg-white/10 transition-colors font-bold text-sm tracking-wide">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm4.586 14.424c-.18.295-.563.387-.857.207-2.35-1.434-5.305-1.76-8.786-.963-.335.077-.67-.133-.746-.47-.077-.334.132-.67.47-.745 3.808-.87 7.077-.492 9.712 1.114.293.18.386.563.207.857zm1.31-3.132c-.225.367-.704.482-1.07.257-2.686-1.65-6.785-2.13-9.965-1.166-.412.125-.85-.107-.976-.52-.125-.412.107-.85.52-.976 3.633-1.1 8.163-.563 11.233 1.326.368.226.483.705.258 1.071zm.106-3.266C14.73 8.11 8.528 7.892 4.962 8.973c-.492.15-1.01-.13-1.16-.622-.15-.49.13-1.01.623-1.16 4.108-1.246 10.977-.992 14.743 1.246.444.264.59.84.327 1.285-.262.443-.84.588-1.284.325z"/></svg>
                        Stream To Spotify
                    </a>
                    <a href="https://spotify.com" target="_blank" class="flex items-center justify-center gap-3 px-8 py-4 bg-transparent border border-white/20 text-white/70 rounded-xl hover:text-white hover:border-white transition-colors font-bold text-sm tracking-wide">
                        Explore Playlist &rarr;
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- WATCH ONLINE: Monochromatic Deep -->
<section class="relative w-full py-24 md:py-32 flex items-center justify-center overflow-hidden bg-black">
    <div class="absolute inset-0 z-0">
        <img src="<?= setting('home.watch_bg', 'https://images.unsplash.com/photo-1490730141103-6cac27aaab94?q=80&w=2000&auto=format&fit=crop') ?>" alt="Watch Live" class="w-full h-full object-cover opacity-50">
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/80 to-black"></div>
    </div>
    
    <div class="relative z-10 w-[90%] max-w-[75rem] mx-auto text-center reveal">
        <div class="inline-flex items-center justify-center gap-4 mb-8">
            <span class="w-1.5 h-1.5 rounded-full bg-accent animate-pulse"></span>
            <span class="text-white/70 font-sans font-medium text-[0.625rem] tracking-[0.4em] uppercase">Watch Online</span>
        </div>
        
        <h2 class="font-display font-black text-5xl md:text-7xl tracking-normal mb-12 text-white leading-[1.0] uppercase">
            Sundays.<br>Anywhere<span class="text-amber-500">.</span>
        </h2>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <a href="livestream.php" class="btn btn-outline-white">
                Watch Live Service
            </a>
            <a href="sermons" class="btn border border-transparent text-white/50 hover:text-white hover:underline">
                Browse Archive
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

