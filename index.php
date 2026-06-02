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
    $stmt = $pdo->query("SELECT id, title, description, event_date, event_time, venue, event_image FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3");
    $upcomingEvents = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT id, title, sermon_date, topic, sermon_image FROM sermons ORDER BY sermon_date DESC LIMIT 3");
    $latestSermons = $stmt->fetchAll();

    $heroSlides = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC")->fetchAll();
    $testimonies = $pdo->query("SELECT * FROM testimonies ORDER BY sort_order ASC")->fetchAll();
    $weeklyServices = $pdo->query("SELECT * FROM weekly_services ORDER BY sort_order ASC")->fetchAll();
} catch (Throwable $e) {}

include 'includes/header.php';
?>

<!-- OPTION B: PROFESSIONAL REFINEMENTS -->
<style>
  .carousel-slide { transition: opacity 1.5s ease-in-out; }
  .bg-zoom { animation: slowZoom 15s linear infinite alternate; }
  @keyframes slowZoom {
    0% { transform: scale(1); }
    100% { transform: scale(1.15); }
  }
</style>

<!-- HERO CAROUSEL: Clean Professional Design -->
<section class="relative w-full h-screen min-h-[43.75rem] max-h-[62.5rem] bg-slate-900 overflow-hidden" id="hero-carousel">
    <?php if (empty($heroSlides)): ?>
        <div class="carousel-slide absolute inset-0 w-full h-full opacity-100 z-20 flex items-center justify-center">
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-slate-900/60 mix-blend-multiply"></div>
            </div>
            <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto text-center">
                <h1 class="font-display font-black text-6xl text-white">Welcome</h1>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($heroSlides as $i => $slide): ?>
            <div class="carousel-slide absolute inset-0 w-full h-full <?php echo $i === 0 ? 'opacity-100 z-20' : 'opacity-0 z-10'; ?> flex items-center justify-center" id="slide-<?php echo $i; ?>">
                <div class="absolute inset-0 z-0">
                    <?php if ($slide['bg_image']): ?>
                        <img src="<?php echo strpos($slide['bg_image'], 'http') === 0 ? htmlspecialchars($slide['bg_image']) : htmlspecialchars($slide['bg_image']); ?>" alt="" class="bg-zoom w-full h-full object-cover object-center">
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-slate-900/60 mix-blend-multiply"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/40 to-slate-900/20"></div>
                </div>
                <div class="relative z-10 w-full w-[90%] max-w-[112.5rem] mx-auto mt-16 text-center">
                    <?php if ($slide['title']): ?>
                        <h1 class="font-display font-black text-6xl md:text-7xl lg:text-[5.5rem] tracking-tight mb-8 text-white leading-[1.05]">
                            <?php echo $slide['title']; // Allow HTML like <span> as requested ?>
                        </h1>
                    <?php endif; ?>
                    <?php if ($slide['subtitle']): ?>
                        <p class="font-sans text-xl md:text-2xl text-white/90 mb-12 font-light leading-relaxed max-w-2xl mx-auto">
                            <?php echo htmlspecialchars($slide['subtitle']); ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($slide['button_text'] && $slide['button_url']): ?>
                        <div class="flex items-center justify-center gap-6">
                            <a href="<?php echo htmlspecialchars($slide['button_url']); ?>" class="inline-flex items-center justify-center <?php echo $i % 2 === 0 ? 'bg-[#c49a45] text-white hover:bg-[#d4ac57]' : 'bg-white text-slate-900 hover:bg-slate-100'; ?> text-sm font-bold uppercase tracking-widest px-10 py-5 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1">
                                <?php echo htmlspecialchars($slide['button_text']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex gap-3">
                        <?php foreach ($heroSlides as $j => $s): ?>
                            <button onclick="goToSlide(<?php echo $j; ?>)" class="<?php echo $i === $j ? 'w-10' : 'w-4'; ?> h-1.5 <?php echo $i === 0 ? 'bg-[#c49a45]' : 'bg-white'; ?> <?php echo $i !== $j ? 'bg-white/40 hover:bg-white/80' : ''; ?> transition-all" aria-label="Go to slide <?php echo $j+1; ?>"></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<!-- ANIMATED MARQUEE TICKER -->
<div class="w-full overflow-hidden bg-slate-950 py-3.5 border-y border-white/5 relative z-20 shadow-2xl">
    <!-- Subtle glow behind the marquee -->
    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#c49a45]/10 to-transparent pointer-events-none"></div>
    <div class="whitespace-nowrap flex items-center animate-marquee font-display font-black tracking-[0.25em] uppercase text-[0.6875rem] md:text-xs">
        <?php for ($m = 0; $m < 3; $m++): ?>
        <span class="mx-8 flex items-center gap-8">
            <span class="text-slate-300"><?php echo htmlspecialchars(setting('home.marquee_text1', 'WORSHIP WITH US')); ?></span> 
            <span class="w-1.5 h-1.5 bg-gradient-to-br from-[#c49a45] to-[#e8c881] rotate-45 shadow-[0_0_8px_rgba(196,154,69,0.8)]"></span> 
            <span class="text-slate-300"><?php echo htmlspecialchars(setting('home.marquee_text2', 'SUNDAYS AT 8:45 AM')); ?></span> 
            <span class="w-1.5 h-1.5 bg-gradient-to-br from-[#c49a45] to-[#e8c881] rotate-45 shadow-[0_0_8px_rgba(196,154,69,0.8)]"></span> 
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#c49a45] to-[#e8c881]"><?php echo htmlspecialchars(setting('home.marquee_text3', 'EXPERIENCE TRANSFORMATION')); ?></span> 
            <span class="w-1.5 h-1.5 bg-gradient-to-br from-[#c49a45] to-[#e8c881] rotate-45 shadow-[0_0_8px_rgba(196,154,69,0.8)]"></span> 
            <span class="text-slate-300"><?php echo htmlspecialchars(setting('home.marquee_text4', 'UNCOMPROMISED TRUTH')); ?></span> 
            <span class="w-1.5 h-1.5 bg-gradient-to-br from-[#c49a45] to-[#e8c881] rotate-45 shadow-[0_0_8px_rgba(196,154,69,0.8)]"></span>
            <span class="text-slate-300"><?php echo htmlspecialchars(setting('home.marquee_text5', 'A LEGACY OF ENDURING FAITH')); ?></span>
        </span>
        <?php endfor; ?>
    </div>
</div>

<!-- MEET THE FOUNDER: Professional & Elegant -->
<section class="py-24 md:py-32 bg-white relative overflow-hidden">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-24 items-center">
            
            <!-- Portrait -->
            <div class="lg:col-span-5 relative group" data-aos="fade-right">
                <!-- Gold Offset Frame -->
                <div class="absolute -right-4 -bottom-4 w-full h-full bg-[#c49a45] z-0"></div>
                
                <!-- Main Image Container -->
                <div class="relative w-full aspect-[3/4] overflow-hidden border-8 border-white bg-slate-100 flex items-center justify-center z-10 shadow-sm">
                    <img src="<?= setting('home.founder_image', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=800&auto=format&fit=crop') ?>" alt="Rev. Francis Duane Yalley" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent z-10"></div>
                    
                    <div class="absolute bottom-0 left-0 p-8 z-20 w-full">
                        <div class="inline-block px-3 py-1 bg-[#c49a45] mb-3">
                            <span class="text-white text-[0.625rem] font-bold tracking-widest uppercase">The Repairer</span>
                        </div>
                        <h3 class="text-4xl lg:text-5xl font-display font-black text-white leading-tight">Rev. F.D.<br>Yalley</h3>
                    </div>
                </div>
            </div>

            <!-- Biography Content -->
            <div class="lg:col-span-7 relative" data-aos="fade-left">
                <!-- Subtle Watermark -->
                <div class="absolute -top-16 right-0 text-[12rem] font-display font-black text-slate-50 leading-none pointer-events-none select-none z-0 hidden lg:block">
                    FDY
                </div>
                
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-4 mb-6">
                        <div class="h-px w-10 bg-[#c49a45]"></div>
                        <span class="text-[#c49a45] font-bold text-xs tracking-widest uppercase">Meet The Founder</span>
                    </div>
                    
                    <h2 class="text-[2.5rem] md:text-5xl lg:text-[3.5rem] font-display font-black text-slate-900 mb-8 leading-[1.1] tracking-tight">
                        <?= setting('home.founder_title', 'A voice of restoration,<br>a builder of lives &<br><span class="text-[#c49a45]">a repairer of destinies.</span>') ?>
                    </h2>
                    
                    <div class="text-slate-600 font-medium leading-relaxed">
                        <p class="mb-8 text-sm md:text-base">
                            <?= setting('home.founder_bio1', 'From the vibrant nation of Ghana in West Africa emerges Reverend Francis Duane Yalley, affectionately known as “The Repairer.” For over two decades, he has served as the General Overseer and founder of Bridge Ministries International, building not just a church, but a global movement of Switching On lives.') ?>
                        </p>
                        
                        <div class="p-8 my-8 border-l-[0.1875rem] border-[#c49a45] bg-[#faf9f6] relative flex gap-4">
                            <div class="text-[#c49a45]/30 pt-1">
                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                            </div>
                            <p class="text-base text-[#111827] font-bold italic leading-relaxed">
                                "<?= setting('home.founder_bio2', 'Rev. F.D. Yalley is more than a preacher. He is a restorer of broken lives. A builder of leaders. A voice of prophetic clarity.') ?>"
                            </p>
                        </div>
                        
                        <a href="about.php" class="inline-flex items-center justify-center gap-3 bg-[#111827] text-white hover:bg-[#c49a45] text-[0.625rem] font-bold uppercase tracking-widest px-6 py-4 transition-all duration-300">
                            Read Full Biography
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- OUR MISSION: Professional & Elegant -->
<section class="py-24 md:py-32 bg-[#080b12] relative overflow-hidden text-white">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            
            <div class="order-2 lg:order-1" data-aos="fade-right">
                <div class="inline-flex items-center gap-4 mb-6">
                    <div class="h-px w-8 bg-[#c49a45]"></div>
                    <span class="text-[#c49a45] font-bold text-[0.625rem] tracking-widest uppercase">Our Mission</span>
                </div>
                
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-display font-black text-white mb-6 leading-tight">
                    <?= setting('home.mission_title', 'A legacy of <span class="text-[#c49a45]">enduring<br>faith</span> and action.') ?>
                </h2>
                
                <p class="text-slate-300 text-sm md:text-base leading-relaxed mb-10 font-medium">
                    <?= setting('home.mission_text', 'Under the leadership of our General Overseer, Bridge Ministries International operates with a profound commitment to establishing a lasting, positive impact on individuals and society.') ?>
                </p>
                
                <div class="flex flex-col gap-4">
                    <div class="bg-[#111522] p-5 flex items-start gap-5">
                        <div class="w-12 h-12 bg-[#1a1f2e] text-[#c49a45] flex-shrink-0 flex items-center justify-center border border-transparent">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-white mb-1"><?= setting('home.mission_box1_title', 'Missions & Outreach') ?></h4>
                            <p class="text-slate-400 text-xs leading-relaxed"><?= setting('home.mission_box1_desc', 'Impacting communities across the globe through active missions and support.') ?></p>
                        </div>
                    </div>
                    
                    <div class="bg-[#111522] p-5 flex items-start gap-5">
                        <div class="w-12 h-12 bg-[#1a1f2e] text-slate-400 flex-shrink-0 flex items-center justify-center border border-transparent">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-white mb-1"><?= setting('home.mission_box2_title', 'Sound Doctrine') ?></h4>
                            <p class="text-slate-400 text-xs leading-relaxed"><?= setting('home.mission_box2_desc', 'Rooted firmly in biblical truth, theology, and the transformative power of the Word.') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2" data-aos="fade-left">
                <div class="relative overflow-hidden aspect-square border border-slate-700/50">
                    <img src="<?= setting('home.mission_bg', 'assets/image/chad-kirchoff-ivqGyYLtBI8-unsplash.jpg') ?>" alt="Community Outreach" class="w-full h-full object-cover grayscale opacity-90 hover:grayscale-0 hover:opacity-100 transition-all duration-1000" onerror="this.src='https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=1000&auto=format&fit=crop';">
                </div>
            </div>
            
        </div>
    </div>
</section>
<!-- BY THE NUMBERS (DYNAMIC COUNTERS) -->
<section class="py-20 bg-white border-y border-slate-200 relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1544365558-35aa4afc111c?q=80&w=1200&auto=format&fit=crop')] bg-cover bg-center opacity-5 mix-blend-luminosity"></div>
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="grid grid-cols-2 max-w-4xl mx-auto gap-8 text-center" id="counter-section">
            <div class="p-6" data-aos="fade-up" data-aos-delay="0">
                <div class="text-[#c49a45] text-5xl md:text-6xl font-display font-black mb-2"><span class="counter" data-target="<?php echo (int)setting('home.counter1_number', 20); ?>">0</span>+</div>
                <div class="text-slate-900 font-bold uppercase tracking-widest text-sm"><?php echo htmlspecialchars(setting('home.counter1_label', 'Years of Ministry')); ?></div>
            </div>
            <div class="p-6" data-aos="fade-up" data-aos-delay="100">
                <div class="text-[#c49a45] text-5xl md:text-6xl font-display font-black mb-2"><span class="counter" data-target="<?php echo (int)setting('home.counter2_number', 10); ?>">0</span>K+</div>
                <div class="text-slate-900 font-bold uppercase tracking-widest text-sm"><?php echo htmlspecialchars(setting('home.counter2_label', 'Lives Impacted')); ?></div>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIES (STORIES OF CHANGE): Premium Glassmorphism Grid -->
<section class="py-24 md:py-32 relative overflow-hidden" id="testimonies-section">
    <!-- Cinematic Background -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1438232992991-995b7058bbb3?q=80&w=1200&auto=format&fit=crop" alt="Worship Background" class="w-full h-full object-cover grayscale opacity-30">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-900/95 to-slate-950"></div>
    </div>
    
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10 lg:pb-16">
        <div class="text-center mb-20 md:mb-28" data-aos="fade-up">
            <div class="inline-flex items-center justify-center gap-4 mb-4">
                <div class="h-px w-12 bg-[#c49a45]"></div>
                <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Changed Lives</span>
                <div class="h-px w-12 bg-[#c49a45]"></div>
            </div>
            <h2 class="text-4xl md:text-5xl lg:text-6xl text-white font-display font-bold">Stories of Impact</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            
            <?php foreach ($testimonies as $index => $test): ?>
                <?php
                    // Create staggered effect for middle item if 3 columns
                    $staggerClass = ($index % 3 === 1) ? 'lg:translate-y-16 mt-8 lg:mt-0' : 'mt-0';
                    $delay = ($index % 3) * 100;
                ?>
                <div class="group relative <?php echo $staggerClass; ?>" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <div class="absolute -inset-0.5 bg-gradient-to-b from-[#c49a45]/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl blur"></div>
                    <div class="relative bg-white/5 backdrop-blur-xl border border-white/10 p-10 lg:p-12 h-full flex flex-col transition-transform duration-500 group-hover:-translate-y-2">
                        <svg class="w-12 h-12 text-[#c49a45] opacity-50 mb-8 transform -translate-x-2 -translate-y-2 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                        <p class="text-slate-300 text-lg lg:text-xl font-light leading-relaxed flex-grow mb-10">
                            "<?php echo htmlspecialchars($test['quote']); ?>"
                        </p>
                        <div class="flex items-center gap-5 pt-6 border-t border-white/10">
                            <?php if ($test['image_url']): ?>
                                <img src="<?php echo strpos($test['image_url'], 'http') === 0 ? htmlspecialchars($test['image_url']) : htmlspecialchars($test['image_url']); ?>" alt="<?php echo htmlspecialchars($test['author_name']); ?>" class="w-16 h-16 rounded-full object-cover border-2 border-slate-700 group-hover:border-[#c49a45] transition-colors duration-500">
                            <?php else: ?>
                                <div class="w-16 h-16 rounded-full bg-slate-800 border-2 border-slate-700 flex items-center justify-center text-[#c49a45] font-bold text-xl group-hover:border-[#c49a45] transition-colors duration-500">
                                    <?php echo strtoupper(substr($test['author_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h4 class="text-white font-bold font-display uppercase tracking-wider text-base mb-1"><?php echo htmlspecialchars($test['author_name']); ?></h4>
                                <span class="text-[#c49a45] text-[0.625rem] font-bold uppercase tracking-widest"><?php echo htmlspecialchars($test['author_role']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</section>

<!-- TEACHINGS (SERMONS): Colorful & Cinematic -->
<section class="py-24 md:py-32 bg-white relative overflow-hidden border-b border-slate-100">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 md:mb-24">
            <div data-aos="fade-up">
                <div class="inline-flex items-center gap-4 mb-4">
                    <div class="h-px w-12 bg-rose-500"></div>
                    <span class="text-rose-500 font-bold text-sm tracking-widest uppercase">Teachings</span>
                </div>
                <h2 class="text-5xl md:text-7xl text-slate-900 font-display font-black tracking-tight">Latest Sermons</h2>
            </div>
            <a href="sermons" class="hidden md:flex items-center text-rose-600 hover:text-rose-700 font-bold transition-colors mt-4 md:mt-0 tracking-widest uppercase text-sm group" data-aos="fade-left">
                All Sermons
                <div class="ml-4 w-12 h-12 bg-rose-50 flex items-center justify-center group-hover:bg-rose-500 group-hover:text-white transition-colors duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>
        </div>

        <?php if (!empty($latestSermons)): ?>
            <!-- Massive Looping Slider Container -->
            <div class="w-[calc(100%+2rem)] -ml-4 sm:w-[calc(100%+3rem)] sm:-ml-6 lg:w-[calc(100%+4rem)] lg:-ml-8 overflow-hidden mt-8 md:mt-12 group/marquee" data-aos="fade-up">
                
                <div class="flex w-max animate-marquee hover:[animation-play-state:paused]" style="animation-duration: 40s;">
                    
                    <?php for ($i = 0; $i < 2; $i++): ?>
                    <div class="flex gap-6 md:gap-8 pr-6 md:pr-8" style="flex-shrink: 0;" <?php echo $i === 1 ? 'aria-hidden="true"' : ''; ?>>
                        <?php foreach ($latestSermons as $index => $sermon): ?>
                            <?php 
                                $dateText = date('M d, Y', strtotime((string) $sermon['sermon_date'])); 
                                $isReversed = $index % 2 !== 0;
                                $sermonBg = $isReversed ? 'bg-amber-50' : 'bg-rose-50';
                                $sermonAccent = $isReversed ? 'text-amber-600' : 'text-rose-600';
                                $sermonBorder = $isReversed ? 'border-amber-100' : 'border-rose-100';
                                
                                // Dynamic hover states
                                $sermonHoverBg = $isReversed ? 'group-hover:bg-amber-600' : 'group-hover:bg-rose-600';
                            ?>
                            <div class="flex flex-col bg-white border <?php echo $sermonBorder; ?> group shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden" style="flex-shrink: 0; width: 350px;">
                                
                                <div class="w-full aspect-video relative overflow-hidden bg-slate-900 border-b <?php echo $sermonBorder; ?>">
                                    <?php if ($sermon['sermon_image']): ?>
                                        <img src="<?php echo htmlspecialchars($sermon['sermon_image']); ?>" alt="<?php echo htmlspecialchars((string) $sermon['title']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out">
                                    <?php else: ?>
                                        <img src="https://images.unsplash.com/photo-1543165365-07232ed12fad?q=80&w=800&auto=format&fit=crop" alt="Sermon Flyer" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out opacity-80">
                                    <?php endif; ?>
                                    <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors duration-500"></div>
                                    
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <div class="w-16 h-16 bg-white/95 backdrop-blur-sm <?php echo $sermonAccent; ?> flex items-center justify-center transform group-hover:scale-110 <?php echo $sermonHoverBg; ?> group-hover:text-white transition-all duration-500 shadow-lg pointer-events-auto cursor-pointer rounded-full">
                                            <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6 md:p-8 flex flex-col flex-grow <?php echo $sermonBg; ?>">
                                    <div class="inline-flex items-center gap-2 mb-4 bg-white px-3 py-1.5 border <?php echo $sermonBorder; ?> shadow-sm self-start">
                                        <svg class="w-4 h-4 <?php echo $sermonAccent; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="<?php echo $sermonAccent; ?> font-bold tracking-widest text-[0.625rem] uppercase"><?php echo htmlspecialchars($dateText); ?></span>
                                    </div>
                                    
                                    <h3 class="text-2xl font-display font-black text-slate-900 leading-tight mb-4 line-clamp-2"><?php echo htmlspecialchars((string) $sermon['title']); ?></h3>
                                    
                                    <?php if (!empty($sermon['topic'])): ?>
                                        <p class="text-sm text-slate-600 font-medium mb-6 flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full <?php echo $isReversed ? 'bg-amber-500' : 'bg-rose-500'; ?>"></span>
                                            <?php echo htmlspecialchars($sermon['topic']); ?>
                                        </p>
                                    <?php endif; ?>

                                    <a href="sermons" class="mt-auto inline-flex items-center gap-2 <?php echo $sermonAccent; ?> font-bold uppercase tracking-widest text-xs hover:text-slate-900 transition-colors">
                                        Watch <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endfor; ?>
                    
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- WATCH ONLINE: Deep Navy -->
<section class="relative w-full py-28 md:py-36 flex items-center justify-center overflow-hidden bg-[#11131e]">
    <div class="absolute inset-0 z-0">
        <img src="<?= setting('home.watch_bg', 'https://images.unsplash.com/photo-1490730141103-6cac27aaab94?q=80&w=1200&auto=format&fit=crop') ?>" alt="Watch Live" class="w-full h-full object-cover opacity-10 mix-blend-luminosity">
        <div class="absolute inset-0 bg-[#11131e]/80"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#11131e] via-[#11131e]/95 to-transparent"></div>
    </div>
    
    <div class="relative z-10 w-[90%] max-w-[75rem] mx-auto flex flex-col md:flex-row items-center justify-between gap-16">
        <div class="max-w-xl">
            <div class="inline-flex items-center gap-2.5 mb-8 px-3 py-1.5 bg-white/5 border border-white/10 text-white/80 font-bold text-[0.625rem] tracking-[0.15em] uppercase">
                <span class="w-1.5 h-1.5 rounded-full bg-[#c93b3b]"></span>
                Watch Online
            </div>
            
            <h2 class="font-display font-black text-[4.5rem] md:text-[5.5rem] tracking-[-0.02em] mb-8 text-white leading-[0.95]">
                <?= setting('home.watch_title', 'Sundays.<br><span class="text-[#c19f54]">Anywhere</span> in the<br>world.') ?>
            </h2>
            
            <p class="font-sans text-base text-[#a0a4b0] font-normal leading-relaxed max-w-sm">
                <?= setting('home.watch_subtitle', 'Join us live each week or revisit recent messages. Wherever you are, the bridge reaches you.') ?>
            </p>
        </div>
        
        <div class="flex flex-col gap-4 w-full md:w-auto md:min-w-[20rem]">
            <a href="livestream.php" class="inline-flex items-center justify-center bg-[#c19f54] text-white hover:bg-[#a88946] text-[0.6875rem] font-bold uppercase tracking-[0.15em] px-8 py-5 transition-all gap-3">
                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                Watch Live Service
            </a>
            <a href="sermons" class="inline-flex items-center justify-center bg-[#181a26] text-white border border-white/10 hover:bg-white/5 text-[0.6875rem] font-bold uppercase tracking-[0.15em] px-8 py-5 transition-all gap-3">
                <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Browse Archive
            </a>
        </div>
    </div>
</section>

<!-- WEEKLY SERVICES: Clean & Professional -->
<section class="py-24 md:py-32 bg-[#f8f9fa] relative overflow-hidden">
    <div class="w-[90%] max-w-[70rem] mx-auto relative z-10">
        <div class="text-center mb-12 md:mb-16" data-aos="fade-up">
            <h2 class="text-[2.5rem] md:text-[3.5rem] text-[#111827] font-display font-black tracking-[-0.02em] leading-tight">Our Weekly Services</h2>
        </div>

        <div class="space-y-10 md:space-y-16">
            <?php foreach ($weeklyServices as $index => $svc): ?>
                <?php 
                    $isReversed = $index % 2 !== 0; 
                    $theme = $svc['theme_color'] ?: 'cyan';
                    
                    $themeMap = [
                        'cyan'   => ['bg' => 'bg-[#f4fbfc]', 'badge_text' => 'text-[#5a758f]', 'badge_border' => 'border-[#dce7f0]', 'title' => 'text-[#1e2a3b]', 'sub' => 'text-[#4b6a88]', 'desc' => 'text-[#71859c]'],
                        'purple' => ['bg' => 'bg-[#faf5ff]', 'badge_text' => 'text-[#7e5a8b]', 'badge_border' => 'border-[#e9d5ff]', 'title' => 'text-[#2e1e3b]', 'sub' => 'text-[#724f8a]', 'desc' => 'text-[#8b719c]'],
                        'orange' => ['bg' => 'bg-[#fff7ed]', 'badge_text' => 'text-[#8b6a5a]', 'badge_border' => 'border-[#fed7aa]', 'title' => 'text-[#3b271e]', 'sub' => 'text-[#8a5d4f]', 'desc' => 'text-[#9c8271]'],
                        'teal'   => ['bg' => 'bg-[#f0fdfa]', 'badge_text' => 'text-[#5a8b7c]', 'badge_border' => 'border-[#ccfbf1]', 'title' => 'text-[#1e3b2f]', 'sub' => 'text-[#4f8a75]', 'desc' => 'text-[#719c8d]'],
                        'blue'   => ['bg' => 'bg-[#f0f9ff]', 'badge_text' => 'text-[#5a728b]', 'badge_border' => 'border-[#e0f2fe]', 'title' => 'text-[#1e293b]', 'sub' => 'text-[#4f6b8a]', 'desc' => 'text-[#71859c]'],
                        'red'    => ['bg' => 'bg-[#fef2f2]', 'badge_text' => 'text-[#8b5a5a]', 'badge_border' => 'border-[#fecaca]', 'title' => 'text-[#3b1e1e]', 'sub' => 'text-[#8a4f4f]', 'desc' => 'text-[#9c7171]'],
                        'green'  => ['bg' => 'bg-[#f0fdf4]', 'badge_text' => 'text-[#5f8b5a]', 'badge_border' => 'border-[#bbf7d0]', 'title' => 'text-[#1e3b1e]', 'sub' => 'text-[#4f8a4f]', 'desc' => 'text-[#7a9c71]'],
                        'yellow' => ['bg' => 'bg-[#fefce8]', 'badge_text' => 'text-[#8b825a]', 'badge_border' => 'border-[#fef08a]', 'title' => 'text-[#3b371e]', 'sub' => 'text-[#8a7f4f]', 'desc' => 'text-[#9c9371]'],
                        'pink'   => ['bg' => 'bg-[#fdf2f8]', 'badge_text' => 'text-[#8b5a76]', 'badge_border' => 'border-[#fbcfe8]', 'title' => 'text-[#3b1e2a]', 'sub' => 'text-[#8a4f6d]', 'desc' => 'text-[#9c7188]'],
                    ];
                    
                    $classes = $themeMap[$theme] ?? $themeMap['cyan'];
                ?>
                <div data-aos="fade-up" class="flex flex-col <?php echo $isReversed ? 'md:flex-row-reverse' : 'md:flex-row'; ?> items-stretch rounded-[1.25rem] overflow-hidden shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] bg-white transition-transform hover:-translate-y-1 duration-500">
                    <div class="w-full md:w-[45%] lg:w-[42%] aspect-square md:aspect-auto relative flex-shrink-0 bg-slate-900">
                        <?php if ($svc['image_url']): ?>
                            <img src="<?php echo strpos($svc['image_url'], 'http') === 0 ? htmlspecialchars($svc['image_url']) : htmlspecialchars($svc['image_url']); ?>" alt="<?php echo htmlspecialchars($svc['title']); ?>" class="absolute inset-0 w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <div class="w-full md:w-[55%] lg:w-[58%] p-8 md:p-12 lg:p-16 flex flex-col justify-center <?php echo $classes['bg']; ?> min-h-[22rem] md:min-h-[28rem]">
                        <?php if ($svc['time_info']): ?>
                            <div class="inline-flex items-center px-3 py-1 bg-white border <?php echo $classes['badge_border']; ?> <?php echo $classes['badge_text']; ?> font-bold tracking-[0.08em] text-[0.625rem] uppercase mb-6 rounded shadow-sm w-fit">
                                <?php echo htmlspecialchars($svc['time_info']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <h3 class="text-3xl md:text-4xl lg:text-[2.75rem] font-display font-black <?php echo $classes['title']; ?> mb-2 tracking-[-0.03em] uppercase leading-none"><?php echo htmlspecialchars($svc['title']); ?></h3>
                        
                        <?php if ($svc['subtitle']): ?>
                            <p class="<?php echo $classes['sub']; ?> text-sm md:text-base font-semibold tracking-wide mb-6"><?php echo htmlspecialchars($svc['subtitle']); ?></p>
                        <?php endif; ?>
                        
                        <p class="<?php echo $classes['desc']; ?> leading-relaxed text-xs md:text-sm max-w-lg"><?php echo nl2br(htmlspecialchars($svc['description'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- EVENTS: Colorful Alternating Layout -->
<section class="py-24 md:py-32 bg-white relative overflow-hidden border-b border-slate-100">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 md:mb-24" data-aos="fade-up">
            <div>
                <div class="inline-flex items-center gap-4 mb-4">
                    <div class="h-px w-12 bg-sky-500"></div>
                    <span class="text-sky-600 font-bold text-sm tracking-widest uppercase">Church Life</span>
                </div>
                <h2 class="text-5xl md:text-7xl text-slate-900 font-display font-black tracking-tight">Upcoming Events</h2>
            </div>
            <a href="events" class="hidden md:flex items-center text-sky-600 hover:text-sky-700 font-bold transition-colors mt-4 md:mt-0 tracking-widest uppercase text-sm group">
                All Events
                <div class="ml-4 w-12 h-12 bg-sky-50 flex items-center justify-center group-hover:bg-sky-500 group-hover:text-white transition-colors duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>
        </div>

        <?php if (!empty($upcomingEvents)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($upcomingEvents as $index => $event): ?>
                    <?php 
                        $dateMonth = date('M', strtotime((string) $event['event_date']));
                        $dateDay = date('d', strtotime((string) $event['event_date']));
                        
                        $colors = [
                            ['bg' => 'bg-sky-50', 'border' => 'border-sky-100', 'text' => 'text-sky-600', 'hover' => 'hover:border-sky-400', 'icon' => 'text-sky-500'],
                            ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-100', 'text' => 'text-indigo-600', 'hover' => 'hover:border-indigo-400', 'icon' => 'text-indigo-500'],
                            ['bg' => 'bg-cyan-50', 'border' => 'border-cyan-100', 'text' => 'text-cyan-600', 'hover' => 'hover:border-cyan-400', 'icon' => 'text-cyan-500'],
                        ];
                        $c = $colors[$index % 3];
                    ?>
                    <a href="events" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>" class="group flex flex-col <?php echo $c['bg']; ?> border <?php echo $c['border']; ?> <?php echo $c['hover']; ?> overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        
                        <!-- FLYER IMAGE -->
                        <div class="w-full aspect-video bg-slate-900 relative overflow-hidden border-b <?php echo $c['border']; ?>">
                            <?php if (!empty($event['event_image'])): ?>
                                <img src="<?php echo htmlspecialchars((string) $event['event_image']); ?>" alt="<?php echo htmlspecialchars((string) $event['title']); ?>" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                            <?php else: ?>
                                <div class="absolute inset-0 flex items-center justify-center bg-slate-900">
                                    <svg class="w-16 h-16 text-slate-700 group-hover:<?php echo $c['icon']; ?>/50 transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors duration-500"></div>
                        </div>
                        
                        <!-- DETAILS -->
                        <div class="p-6 flex items-start gap-5 relative bg-white flex-grow">
                            <!-- Date Badge -->
                            <div class="flex-shrink-0 w-16 h-16 bg-white border <?php echo $c['border']; ?> flex flex-col items-center justify-center <?php echo $c['text']; ?> shadow-sm">
                                <span class="text-2xl font-display font-black leading-none tracking-tight"><?php echo $dateDay; ?></span>
                                <span class="text-[0.625rem] font-bold uppercase tracking-widest mt-1"><?php echo $dateMonth; ?></span>
                            </div>
                            
                            <!-- Text details -->
                            <div class="flex-grow pt-1">
                                <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:<?php echo $c['text']; ?> transition-colors leading-tight line-clamp-2"><?php echo htmlspecialchars((string) $event['title']); ?></h3>
                                <p class="text-slate-500 text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4 <?php echo $c['icon']; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <?php echo htmlspecialchars((string) (!empty($event['venue']) ? $event['venue'] : 'Online / TBD')); ?>
                                </p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-16 text-center md:hidden">
                <a href="events" class="inline-flex items-center text-sky-600 font-bold uppercase tracking-widest text-sm bg-sky-50 px-8 py-4 border border-sky-100">
                    View All Events <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
            
        <?php else: ?>
            <div class="text-center py-10">
                <div class="max-w-2xl mx-auto bg-slate-50 p-12 border border-slate-100 flex flex-col items-center justify-center">
                    <div class="w-20 h-20 bg-white border border-slate-200 flex items-center justify-center mb-6 text-slate-400">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-slate-500 text-lg mb-8 font-medium">No upcoming events are available at the moment.</p>
                    <a href="events" class="inline-flex items-center justify-center bg-sky-600 text-white hover:bg-sky-700 text-sm font-bold uppercase tracking-widest px-8 py-4 transition-all shadow-md">View All Events</a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- FIND US THIS SUNDAY / NEXT STEPS -->
<section class="py-24 md:py-32 bg-slate-950 relative overflow-hidden border-t border-white/5">
    <!-- Subtle Background Element -->
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-[0.03] pointer-events-none"></div>

    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="text-center mb-16 md:mb-24" data-aos="fade-up">
            <div class="inline-flex items-center justify-center gap-4 mb-6">
                <div class="h-[0.125rem] w-12 bg-gradient-to-r from-transparent to-[#c49a45]"></div>
                <span class="text-[#c49a45] font-bold text-xs tracking-[0.2em] uppercase">Next Steps</span>
                <div class="h-[0.125rem] w-12 bg-gradient-to-l from-transparent to-[#c49a45]"></div>
            </div>
            <h2 class="text-5xl md:text-6xl font-display font-black text-white tracking-tight">Find Us This Sunday</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- 01 Plan Your Visit -->
            <a href="visit" class="group relative overflow-hidden aspect-[4/5] shadow-2xl hover:shadow-[0_0_2.5rem_rgba(196,154,69,0.3)] transition-all duration-700 hover:-translate-y-2 border border-white/10 hover:border-[#c49a45]/50 flex flex-col justify-end" data-aos="fade-up" data-aos-delay="0">
                <img src="https://images.unsplash.com/photo-1438032005730-c779502df39b?q=80&w=1000&auto=format&fit=crop" alt="Plan Your Visit" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/80 to-slate-900/20 group-hover:via-slate-900/60 transition-colors duration-500"></div>
                
                <div class="relative p-8 md:p-10 z-10 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                    <div class="w-14 h-14 bg-[#c49a45]/20 backdrop-blur-sm border border-[#c49a45]/30 flex items-center justify-center text-[#c49a45] mb-6 group-hover:bg-[#c49a45] group-hover:text-white transition-all duration-500 shadow-[0_0_0.9375rem_rgba(196,154,69,0)] group-hover:shadow-[0_0_0.9375rem_rgba(196,154,69,0.5)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-3xl font-display font-bold text-white mb-3">Plan Your Visit</h3>
                    <p class="text-slate-300 text-sm leading-relaxed mb-6 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                        First time? We'll help you know exactly what to expect and walk you through your first Sunday.
                    </p>
                    <span class="inline-flex items-center text-[#c49a45] font-bold tracking-widest text-xs uppercase group-hover:text-[#d4ac57]">
                        Get Started <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </div>
            </a>

            <!-- 02 Find Community -->
            <a href="ministries" class="group relative overflow-hidden aspect-[4/5] shadow-2xl hover:shadow-[0_0_2.5rem_rgba(196,154,69,0.3)] transition-all duration-700 hover:-translate-y-2 border border-white/10 hover:border-[#c49a45]/50 flex flex-col justify-end" data-aos="fade-up" data-aos-delay="100">
                <img src="https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=1000&auto=format&fit=crop" alt="Find Your Community" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/80 to-slate-900/20 group-hover:via-slate-900/60 transition-colors duration-500"></div>
                
                <div class="relative p-8 md:p-10 z-10 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                    <div class="w-14 h-14 bg-[#c49a45]/20 backdrop-blur-sm border border-[#c49a45]/30 flex items-center justify-center text-[#c49a45] mb-6 group-hover:bg-[#c49a45] group-hover:text-white transition-all duration-500 shadow-[0_0_0.9375rem_rgba(196,154,69,0)] group-hover:shadow-[0_0_0.9375rem_rgba(196,154,69,0.5)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-3xl font-display font-bold text-white mb-3">Find Community</h3>
                    <p class="text-slate-300 text-sm leading-relaxed mb-6 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                        From cell groups to age-based ministries, there is a place for you to belong and grow.
                    </p>
                    <span class="inline-flex items-center text-[#c49a45] font-bold tracking-widest text-xs uppercase group-hover:text-[#d4ac57]">
                        Explore Ministries <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </div>
            </a>

            <!-- 03 Prayer Request -->
            <a href="contact" class="group relative overflow-hidden aspect-[4/5] shadow-2xl hover:shadow-[0_0_2.5rem_rgba(196,154,69,0.3)] transition-all duration-700 hover:-translate-y-2 border border-white/10 hover:border-[#c49a45]/50 flex flex-col justify-end" data-aos="fade-up" data-aos-delay="200">
                <img src="https://images.unsplash.com/photo-1490730141103-6cac27aaab94?q=80&w=1000&auto=format&fit=crop" alt="Send a Prayer Request" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/80 to-slate-900/20 group-hover:via-slate-900/60 transition-colors duration-500"></div>
                
                <div class="relative p-8 md:p-10 z-10 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                    <div class="w-14 h-14 bg-[#c49a45]/20 backdrop-blur-sm border border-[#c49a45]/30 flex items-center justify-center text-[#c49a45] mb-6 group-hover:bg-[#c49a45] group-hover:text-white transition-all duration-500 shadow-[0_0_0.9375rem_rgba(196,154,69,0)] group-hover:shadow-[0_0_0.9375rem_rgba(196,154,69,0.5)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <h3 class="text-3xl font-display font-bold text-white mb-3">Prayer Request</h3>
                    <p class="text-slate-300 text-sm leading-relaxed mb-6 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                        Whatever you're carrying, we'd be honoured to stand with you in prayer this week.
                    </p>
                    <span class="inline-flex items-center text-[#c49a45] font-bold tracking-widest text-xs uppercase group-hover:text-[#d4ac57]">
                        Request Prayer <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- CALL TO ACTION: Cinematic & Premium -->
<section class="relative py-32 md:py-48 flex items-center justify-center overflow-hidden border-t border-white/10">
    <!-- Parallax Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1544365558-35aa4afc111c?q=80&w=1200&auto=format&fit=crop" alt="Worship Background" class="w-full h-full object-cover transform scale-105" style="filter: brightness(0.6) saturate(1.2);">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/80 via-slate-950/60 to-slate-950/90 mix-blend-multiply"></div>
        <!-- Radial glow effect -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-[#c49a45]/20 via-transparent to-transparent"></div>
    </div>
    
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-aos="zoom-in" data-aos-duration="1000">
        <div class="inline-flex items-center justify-center gap-3 mb-8">
            <span class="w-12 h-[0.0625rem] bg-[#c49a45]"></span>
            <span class="text-[#c49a45] font-bold text-sm tracking-[0.2em] uppercase">Global Missions</span>
            <span class="w-12 h-[0.0625rem] bg-[#c49a45]"></span>
        </div>
        
        <h2 class="text-5xl md:text-7xl lg:text-[5.5rem] mb-8 font-display font-black text-white leading-[1.1] tracking-tight">
            Ready to make a<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-[#c49a45] to-[#e8c881]">difference?</span>
        </h2>
        
        <p class="text-xl md:text-2xl text-slate-200 mb-12 font-light leading-relaxed max-w-2xl mx-auto drop-shadow-md">
            Your generous giving enables us to continue our global missions and the teaching of biblical truth around the world.
        </p>
        
        <a href="donate" class="group relative inline-flex items-center justify-center overflow-hidden p-4 px-12 font-bold uppercase tracking-widest text-white shadow-[0_0_40px_rgba(196,154,69,0.4)] hover:shadow-[0_0_60px_rgba(196,154,69,0.6)] transition-all duration-300 hover:-translate-y-1">
            <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-[#c49a45] via-[#d4ac57] to-[#c49a45] bg-[length:200%_100%] animate-gradient"></span>
            <span class="absolute inset-0 w-full h-full bg-white/20 scale-0 transition-all duration-300 group-hover:scale-100 ease-out z-10"></span>
            <span class="relative z-20 flex items-center gap-3">
                Give Online
                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </span>
        </a>
    </div>
</section>

<!-- Vanilla JS Carousel Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const slides = [
        document.getElementById('slide-0'),
        document.getElementById('slide-1'),
        document.getElementById('slide-2')
    ];
    
    if(!slides[0] || !slides[1] || !slides[2]) return;

    let currentSlide = 0;
    let slideInterval;

    function activateSlide(index) {
        // Hide all slides
        slides.forEach(slide => {
            slide.classList.remove('opacity-100', 'z-20');
            slide.classList.add('opacity-0', 'z-10');
            slide.style.pointerEvents = 'none'; // Prevent clicking buttons on hidden slides
        });

        // Activate new slide
        slides[index].classList.remove('opacity-0', 'z-10');
        slides[index].classList.add('opacity-100', 'z-20');
        slides[index].style.pointerEvents = 'auto';
        
        currentSlide = index;
    }

    function nextSlide() {
        activateSlide((currentSlide + 1) % slides.length);
    }

    window.goToSlide = function(index) {
        clearInterval(slideInterval);
        activateSlide(index);
        startInterval();
    };

    function startInterval() {
        slideInterval = setInterval(nextSlide, 8000); // Slower transition (8s) for a more cinematic feel
    }

    // Initialize state
    activateSlide(0);
    startInterval();
});
</script>

<?php include 'includes/footer.php'; ?>
