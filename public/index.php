<?php
$pageTitle = 'Bridge Ministries International';
$pageDescription = 'An international ministry dedicated to empowering communities, teaching uncompromised biblical truth, and fostering a global legacy of faith and action.';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/helpers.php'; // Autoloads components

$upcomingEvents = [];
$latestSermons = [];
$heroSlides = [];
$testimonies = [];
$weeklyServices = [];

try {
    $pdo = db_connect();
    $stmt = $pdo->query("SELECT id, title, event_type, description, event_date, end_date, event_time, venue, event_image FROM events WHERE event_date >= CURDATE() OR (end_date IS NOT NULL AND end_date >= CURDATE()) ORDER BY event_date ASC LIMIT 3");
    $upcomingEvents = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT id, title, sermon_date, topic, sermon_image FROM sermons ORDER BY sermon_date DESC LIMIT 4");
    $latestSermons = $stmt->fetchAll();

    $heroSlides = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC")->fetchAll();
    $testimonies = $pdo->query("SELECT * FROM testimonies ORDER BY sort_order ASC")->fetchAll();
    $weeklyServices = $pdo->query("SELECT * FROM weekly_services WHERE show_on_homepage = 1 ORDER BY sort_order ASC")->fetchAll();
} catch (Throwable $e) {
    log_exception($e, 'index');
}

include __DIR__ . '/../includes/header.php';
?>

<!-- HERO SECTION: Cinematic GSAP Slider -->
<?php 
// We will pass the full array to the Hero_Cinematic component to build the slider
render_hero_cinematic([
    'slides' => $heroSlides
]);
?>

<!-- ANIMATED MARQUEE TICKER -->
<div class="w-full overflow-hidden bg-[#0A0A0B] py-8 border-b border-white/[0.03] relative z-20">
    <div class="whitespace-nowrap flex items-center animate-marquee font-sans font-bold tracking-[0.3em] uppercase text-xs text-white/40">
        <?php
        // Ticker phrases come from Admin → Ministries & Homepage (home.marquee_text1..5).
        $marquee = array_values(array_filter(array_map(fn ($i) => trim(setting('home.marquee_text' . $i)), range(1, 5))));
        if (!$marquee) {
            $marquee = ['Worship with us in person', 'Or join our livestream', 'Experience transformation', 'Uncompromised truth'];
        }
        for ($m = 0; $m < 4; $m++): ?>
        <span class="mx-12 flex items-center gap-16" <?php echo $m > 0 ? 'aria-hidden="true"' : ''; ?>>
            <?php foreach ($marquee as $i => $phrase): ?>
            <?php if ($i > 0): ?><span class="w-1.5 h-1.5 bg-amber-500 rounded-full" aria-hidden="true"></span><?php endif; ?>
            <span class="<?php echo $i % 2 === 1 ? 'text-white/95' : ''; ?>"><?php echo htmlspecialchars($phrase); ?></span>
            <?php endforeach; ?>
        </span>
        <?php endfor; ?>
    </div>
</div>

<!-- MISSION: Deep Scroll Narrative -->
<section class="py-32 md:py-48 bg-[#111113] relative overflow-hidden text-white gs-reveal-section">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
        <div class="lg:col-span-7 gs-reveal-up">
            <div class="inline-flex items-center gap-6 mb-12">
                <span class="text-amber-500 font-sans font-bold text-[0.65rem] tracking-[0.3em] uppercase">Our Mission</span>
                <div class="h-px w-16 bg-white/20"></div>
            </div>
            
            <h2 class="text-5xl md:text-7xl lg:text-[7rem] font-display font-black text-white/95 mb-10 leading-[0.85] uppercase tracking-tighter">
                <?= setting_html('home.mission_title', 'Rooted In Faith.<br>Reaching The <span class="text-amber-500">World.</span>') ?>
            </h2>

            <div class="text-white/60 font-sans text-lg md:text-xl leading-relaxed mb-12 max-w-2xl font-light space-y-6">
                <?php
                $missionText = setting('home.mission_text', "Under the visionary leadership of our General Overseer, Bridge Ministries International operates with a profound commitment to establishing a lasting, positive impact across the globe. We believe in the uncompromised truth of the Gospel.\n\nOur mandate is simple yet expansive: to empower communities, equip believers for leadership, and create a legacy of faith that transcends borders. Join us as we build bridges of hope to every nation.");
                foreach (preg_split('/\R{2,}/', trim($missionText)) as $para): ?>
                <p><?= safe_html(nl2br(trim($para), false)) ?></p>
                <?php endforeach; ?>
            </div>

            <a href="about" class="inline-flex items-center bg-white text-black hover:bg-amber-500 hover:text-white px-10 py-5 rounded-full font-sans font-bold uppercase tracking-[0.2em] text-xs transition-colors mt-4">
                Discover Our Story
            </a>
        </div>

        <div class="lg:col-span-5 relative gs-reveal-up" style="transition-delay: 0.2s;">
            <div class="relative w-full aspect-[3/4] overflow-hidden group border border-white/[0.03]">
                <!-- General Overseer Picture -->
                <?php 
                    $founderImg = setting('home.founder_image') ?: 'assets/image/staff/IMG_0540.jpg';
                ?>
                <img loading="lazy" decoding="async" src="<?= htmlspecialchars($founderImg) ?>" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-[2s] opacity-80" alt="General Overseer">
                <div class="absolute inset-0 bg-gradient-to-t from-[#111113] via-transparent to-transparent"></div>
            </div>
        </div>
    </div>
</section>

<!-- UPCOMING EVENTS -->
<section class="py-24 bg-[#0A0A0B] relative overflow-hidden border-t border-white/[0.03] gs-reveal-section">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="flex justify-between items-end mb-16 gs-reveal-up">
            <h2 class="text-5xl md:text-6xl font-display font-black text-white/95 uppercase tracking-tighter">Upcoming <span class="text-amber-500">Events</span></h2>
            <a href="events" class="hidden md:flex items-center gap-3 text-xs font-bold text-white/50 uppercase tracking-[0.2em] hover:text-amber-500 transition-colors">
                View Calendar <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($upcomingEvents as $index => $event): 
                $dateObj = new DateTime($event['event_date']);
                $imgSrc = empty($event['event_image']) ? 'https://images.unsplash.com/photo-1543165365-07232ed12fad?q=80&w=800&auto=format&fit=crop' : htmlspecialchars($event['event_image']);
            ?>
            <a href="event-detail?id=<?= (int) $event['id'] ?>" class="group block gs-reveal-up" style="transition-delay: <?= $index * 0.1 ?>s;">
                <div class="w-full aspect-[4/3] bg-black overflow-hidden relative border border-white/[0.03]">
                    <img loading="lazy" decoding="async" src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-[2s]">
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
                </div>
                <div class="pt-6 flex gap-6">
                    <div class="flex-shrink-0 text-center border border-white/[0.03] px-4 py-3 bg-[#111113]">
                        <span class="block text-2xl font-display font-black text-amber-500 leading-none"><?= $dateObj->format('d') ?></span>
                        <span class="block text-[0.6rem] font-bold text-white/50 uppercase tracking-[0.2em] mt-1"><?= $dateObj->format('M') ?></span>
                    </div>
                    <div>
                        <?php if(!empty($event['event_type'])): ?>
                            <span class="inline-block border border-amber-500/30 text-amber-500 text-[0.6rem] px-2 py-1 uppercase tracking-[0.2em] font-bold mb-3"><?= htmlspecialchars($event['event_type']) ?></span>
                        <?php endif; ?>
                        <h3 class="text-xl font-display font-black text-white/95 uppercase leading-tight group-hover:text-amber-500 transition-colors duration-300"><?= htmlspecialchars($event['title']) ?></h3>
                        <?php if(!empty($event['venue'])): ?>
                            <p class="text-white/40 font-sans text-xs flex items-center gap-2 mt-3 font-medium">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <?= htmlspecialchars($event['venue']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- NEXT STEPS GRID -->
<section class="py-24 bg-[#111113] relative overflow-hidden gs-reveal-section border-t border-white/[0.03]">
    <div class="w-[90%] max-w-[112.5rem] mx-auto text-center mb-16 gs-reveal-up">
        <h2 class="text-5xl md:text-6xl font-display font-black text-white/95 uppercase tracking-tighter">Find Us <span class="text-amber-500">This Sunday</span></h2>
    </div>
    <div class="w-[90%] max-w-[112.5rem] mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
        <a href="visit" class="group block relative w-full aspect-[4/5] overflow-hidden bg-black border border-white/[0.03] gs-reveal-up">
            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1438283173091-5dbf5c5a3206?q=80&w=800&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-70 group-hover:scale-105 transition-all duration-[2s]" alt="">
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
            <div class="absolute bottom-10 left-10 right-10">
                <div class="w-12 h-12 border border-white/10 flex items-center justify-center mb-6 text-amber-500 bg-[#0A0A0B]/80 backdrop-blur-sm group-hover:bg-amber-500 group-hover:text-black transition-colors duration-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black text-white/95 uppercase leading-none mb-8">Plan Your Visit</h3>
                <span class="text-amber-500 text-[0.6rem] font-bold uppercase tracking-[0.2em] flex items-center gap-2 group-hover:gap-4 transition-all duration-300">Get Started &rarr;</span>
            </div>
        </a>
        <a href="ministries" class="group block relative w-full aspect-[4/5] overflow-hidden bg-black border border-white/[0.03] gs-reveal-up" style="transition-delay: 0.1s;">
            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=800&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-70 group-hover:scale-105 transition-all duration-[2s]" alt="">
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
            <div class="absolute bottom-10 left-10 right-10">
                <div class="w-12 h-12 border border-white/10 flex items-center justify-center mb-6 text-amber-500 bg-[#0A0A0B]/80 backdrop-blur-sm group-hover:bg-amber-500 group-hover:text-black transition-colors duration-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black text-white/95 uppercase leading-none mb-8">Find Community</h3>
                <span class="text-amber-500 text-[0.6rem] font-bold uppercase tracking-[0.2em] flex items-center gap-2 group-hover:gap-4 transition-all duration-300">Explore Ministries &rarr;</span>
            </div>
        </a>
        <a href="contact" class="group block relative w-full aspect-[4/5] overflow-hidden bg-black border border-white/[0.03] gs-reveal-up" style="transition-delay: 0.2s;">
            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1490730141103-6cac27aaab94?q=80&w=800&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-70 group-hover:scale-105 transition-all duration-[2s]" alt="">
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
            <div class="absolute bottom-10 left-10 right-10">
                <div class="w-12 h-12 border border-white/10 flex items-center justify-center mb-6 text-amber-500 bg-[#0A0A0B]/80 backdrop-blur-sm group-hover:bg-amber-500 group-hover:text-black transition-colors duration-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black text-white/95 uppercase leading-none mb-8">Prayer Request</h3>
                <span class="text-amber-500 text-[0.6rem] font-bold uppercase tracking-[0.2em] flex items-center gap-2 group-hover:gap-4 transition-all duration-300">Request Prayer &rarr;</span>
            </div>
        </a>
    </div>
</section>

<!-- HORIZONTAL SCROLL: MINISTRIES (GSAP Pinned Section) -->
<section id="ministries-horizontal" class="bg-[#0A0A0B] py-32 overflow-hidden relative border-y border-white/[0.03] h-screen flex flex-col justify-center gs-reveal-section">
    <div class="w-[90%] max-w-[112.5rem] mx-auto mb-16 shrink-0 gs-reveal-up">
        <div class="flex justify-between items-end">
            <h2 class="text-5xl md:text-7xl font-display font-black text-white/95 uppercase tracking-tighter leading-[0.85]">SERVICES AND <span class="text-amber-500">MINISTRIES</span></h2>
            <a href="ministries" class="hidden md:flex items-center gap-3 text-[0.65rem] font-bold text-white/50 uppercase tracking-[0.2em] hover:text-amber-500 transition-colors">
                View All <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>

    <!-- The scrolling container -->
    <div class="horizontal-scroll-container flex gap-8 px-[5%] w-fit h-[60vh] min-h-[400px]">
        <?php foreach ($weeklyServices as $index => $service): 
            $imgSrc = empty($service['image_url']) ? 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=800&auto=format&fit=crop' : htmlspecialchars($service['image_url']);
        ?>
        <a href="ministry_detail?id=<?= $service['id'] ?>" class="horizontal-panel relative w-[80vw] md:w-[40vw] lg:w-[30vw] h-full overflow-hidden shrink-0 group block bg-black border border-white/[0.03] hover:border-amber-500/50 transition-colors duration-700">
            <img loading="lazy" decoding="async" src="<?= $imgSrc ?>" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-90 group-hover:scale-105 transition-all duration-[2s]" alt="<?= htmlspecialchars($service['title']) ?>">
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>
            
            <div class="absolute bottom-10 left-10 right-10 flex flex-col justify-end">
                <h3 class="text-white/95 font-display font-black text-4xl uppercase leading-none mb-4 group-hover:text-amber-500 transition-colors duration-500"><?= htmlspecialchars($service['title']) ?></h3>
                <?php if (!empty($service['time_info'])): ?>
                    <p class="text-white/60 font-sans text-xs tracking-[0.2em] uppercase font-bold"><?= htmlspecialchars($service['time_info']) ?></p>
                <?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- TEACHINGS & SERMONS -->
<section class="py-32 bg-[#111113] relative gs-reveal-section">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-20 gap-8 gs-reveal-up">
            <h2 class="text-5xl md:text-7xl font-display font-black text-white/95 uppercase tracking-tighter leading-[0.85]">Latest<br><span class="text-amber-500">Teachings</span></h2>
            <a href="sermons" class="inline-flex items-center bg-transparent border border-white/20 text-white hover:border-amber-500 hover:text-amber-500 px-10 py-5 rounded-full font-sans font-bold uppercase tracking-[0.2em] text-xs transition-colors">
                Sermon Archive
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($latestSermons as $index => $sermon): 
                $dateText = date('M d, Y', strtotime((string) $sermon['sermon_date']));
                $imgSrc = empty($sermon['sermon_image']) ? 'https://images.unsplash.com/photo-1589903308904-1010c2294adc?q=80&w=800&auto=format&fit=crop' : htmlspecialchars($sermon['sermon_image']);
            ?>
            <a href="sermon?id=<?= $sermon['id'] ?>" class="group relative overflow-hidden aspect-[3/4] bg-black border border-white/[0.03] hover:border-amber-500/50 transition-colors duration-700 gs-reveal-up block" style="transition-delay: <?= $index * 0.1 ?>s;">
                <img loading="lazy" decoding="async" src="<?= $imgSrc ?>" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-90 group-hover:scale-105 transition-all duration-[2s]" alt="<?= htmlspecialchars((string) $sermon['title']) ?>">
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>
                
                <!-- Play Button Overlay -->
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-20">
                    <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-md text-white flex items-center justify-center transform scale-90 group-hover:scale-100 transition-all duration-500 border border-white/20">
                        <svg class="w-8 h-8 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>

                <div class="absolute bottom-8 left-8 right-8 z-10">
                    <span class="block text-white/50 text-[0.6rem] font-bold uppercase tracking-[0.2em] mb-3"><?= $dateText ?></span>
                    <h3 class="text-2xl font-display font-black text-white/95 uppercase leading-none mb-2 group-hover:text-amber-500 transition-colors duration-500"><?= htmlspecialchars((string) $sermon['title']) ?></h3>
                    <?php if (!empty($sermon['topic'])): ?>
                        <p class="text-amber-500 font-sans text-[0.65rem] tracking-[0.2em] uppercase font-bold"><?= htmlspecialchars($sermon['topic']) ?></p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- TESTIMONIES -->
<?php if (!empty($testimonies)): ?>
<section class="py-32 bg-[#111113] relative overflow-hidden gs-reveal-section border-t border-white/[0.03]">
    <div class="w-[90%] max-w-[112.5rem] mx-auto text-center mb-20 gs-reveal-up">
        <div class="inline-flex items-center justify-center gap-6 mb-6">
            <div class="h-px w-12 bg-amber-500/50"></div>
            <span class="text-amber-500 font-sans font-bold text-[0.65rem] tracking-[0.3em] uppercase">Changed Lives</span>
            <div class="h-px w-12 bg-amber-500/50"></div>
        </div>
        <h2 class="text-5xl md:text-6xl font-display font-black text-white/95 uppercase tracking-tighter">Stories of Impact</h2>
    </div>
    <div class="w-[90%] max-w-[112.5rem] mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php foreach ($testimonies as $index => $testimony): 
            $avatar = empty($testimony['author_image']) ? 'https://ui-avatars.com/api/?name='.urlencode((string)$testimony['author_name']).'&background=0A0A0B&color=fff' : htmlspecialchars($testimony['author_image']);
        ?>
        <div class="bg-[#0A0A0B] border border-white/[0.03] p-10 flex flex-col gs-reveal-up" style="transition-delay: <?= $index * 0.1 ?>s;">
            <svg class="w-10 h-10 text-amber-500/30 mb-8" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
            <p class="text-white/70 font-sans text-lg font-medium leading-relaxed mb-12 flex-grow">"<?= htmlspecialchars((string) $testimony['quote']) ?>"</p>
            <div class="flex items-center gap-4 pt-8 border-t border-white/[0.03]">
                <img loading="lazy" decoding="async" src="<?= $avatar ?>" class="w-12 h-12 rounded-full object-cover grayscale opacity-80" alt="<?= htmlspecialchars((string)$testimony['author_name']) ?>">
                <div>
                    <h4 class="text-white/95 font-display font-black uppercase text-sm tracking-[0.1em]"><?= htmlspecialchars((string)$testimony['author_name']) ?></h4>
                    <?php if (!empty($testimony['author_role'])): ?>
                        <span class="text-amber-500 text-[0.55rem] font-bold uppercase tracking-[0.2em]"><?= htmlspecialchars((string)$testimony['author_role']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- GLOBAL MISSIONS CTA -->
<section class="py-40 bg-[#0A0A0B] relative overflow-hidden gs-reveal-section border-t border-white/[0.03]">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-amber-500/10 via-transparent to-transparent pointer-events-none"></div>
    <div class="w-[90%] max-w-4xl mx-auto text-center relative z-10 gs-reveal-up">
        <div class="inline-flex items-center justify-center gap-6 mb-8">
            <div class="h-px w-16 bg-white/10"></div>
            <span class="text-amber-500 font-sans font-bold text-[0.65rem] tracking-[0.3em] uppercase">Global Missions</span>
            <div class="h-px w-16 bg-white/10"></div>
        </div>
        <h2 class="text-5xl md:text-7xl font-display font-black text-white/95 uppercase tracking-tighter mb-8 leading-[0.9]">Ready to make a<br><span class="text-amber-500">difference?</span></h2>
        <p class="text-white/60 font-sans text-xl font-medium leading-relaxed mb-12 max-w-2xl mx-auto">
            Your generous giving enables us to continue our global missions and the teaching of biblical truth around the world.
        </p>
        <a href="donate" class="inline-flex items-center bg-amber-500 text-black hover:bg-white hover:text-black px-12 py-5 rounded-full font-sans font-bold uppercase tracking-[0.2em] text-xs transition-colors shadow-[0_0_40px_rgba(245,158,11,0.2)] hover:shadow-[0_0_40px_rgba(255,255,255,0.3)]">
            Give Online &rarr;
        </a>
    </div>
</section>

<!-- WATCH ONLINE (Cinematic Footer Lead-in) -->
<section class="relative w-full py-40 flex items-center justify-center overflow-hidden bg-[#0A0A0B] gs-reveal-section">
    <div class="absolute inset-0 z-0">
        <img loading="lazy" decoding="async" src="<?= setting_url('home.watch_bg', 'https://images.unsplash.com/photo-1490730141103-6cac27aaab94?q=80&w=2000&auto=format&fit=crop') ?>" alt="Watch Live" class="w-full h-full object-cover opacity-40 gs-parallax-bg">
        <div class="absolute inset-0 bg-gradient-to-t from-[#0A0A0B] via-[#0A0A0B]/60 to-[#0A0A0B]"></div>
    </div>
    
    <div class="relative z-10 w-[90%] max-w-[75rem] mx-auto text-center gs-reveal-up">
        <div class="inline-flex items-center justify-center gap-4 mb-8">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span class="text-white/70 font-sans font-bold text-[0.65rem] tracking-[0.3em] uppercase">Watch Online</span>
        </div>
        
        <h2 class="font-display font-black text-6xl md:text-[8rem] tracking-tighter mb-8 text-white/95 leading-[0.85] uppercase drop-shadow-2xl">
            <?= setting_html('home.watch_title', 'Sundays.<br>Anywhere.') ?>
        </h2>

        <p class="text-white/60 font-sans text-xl max-w-2xl mx-auto mb-12 font-medium">
            <?= htmlspecialchars(setting('home.watch_subtitle', 'Distance is no longer a barrier to fellowship. Join thousands of believers worldwide as we stream our services live every Sunday.')) ?>
        </p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <a href="livestream" class="inline-flex items-center bg-white text-black hover:bg-amber-500 hover:text-white px-10 py-5 rounded-full font-sans font-bold uppercase tracking-[0.2em] text-xs transition-colors">
                Watch Live Service
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
