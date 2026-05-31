<?php
$pageTitle = 'Bridge Ministries International';
$pageDescription = 'An international ministry dedicated to empowering communities, teaching uncompromised biblical truth, and fostering a global legacy of faith and action.';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings.php';

$upcomingEvents = [];
$latestSermons = [];

try {
    $pdo = db_connect();
    $stmt = $pdo->query("SELECT id, title, description, event_date, event_time, venue, event_image FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3");
    $upcomingEvents = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT id, title, sermon_date, topic, sermon_image FROM sermons ORDER BY sermon_date DESC LIMIT 3");
    $latestSermons = $stmt->fetchAll();
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
<section class="relative w-full h-screen min-h-[700px] max-h-[1000px] bg-slate-900 overflow-hidden" id="hero-carousel">
    
    <!-- Slide 1: Global Missions -->
    <div class="carousel-slide absolute inset-0 w-full h-full opacity-100 z-20 flex items-center justify-center" id="slide-0">
        <div class="absolute inset-0 z-0">
            <img src="assets/image/chad-kirchoff-ivqGyYLtBI8-unsplash.jpg" alt="Missions" class="bg-zoom w-full h-full object-cover object-center" onerror="this.src='https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=2000&auto=format&fit=crop';">
            <div class="absolute inset-0 bg-slate-900/60 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/40 to-slate-900/20"></div>
        </div>
        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 text-center">
            <h1 class="font-display font-black text-6xl md:text-7xl lg:text-[5.5rem] tracking-tight mb-8 text-white leading-[1.05]">
                Impact the <span class="text-[#c49a45]">World.</span>
            </h1>
            <p class="font-sans text-xl md:text-2xl text-white/90 mb-12 font-light leading-relaxed max-w-2xl mx-auto">
                Taking the uncompromised message of hope and truth to every nation. Join our global mandate.
            </p>
            <div class="flex items-center justify-center gap-6">
                <a href="ministries" class="inline-flex items-center justify-center bg-[#c49a45] text-white hover:bg-[#d4ac57] text-sm font-bold uppercase tracking-widest px-10 py-5 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1">
                    Discover Ministries
                </a>
            </div>
            
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex gap-3">
                <button onclick="goToSlide(0)" class="w-10 h-1.5 bg-[#c49a45] transition-all" aria-label="Current slide"></button>
                <button onclick="goToSlide(1)" class="w-4 h-1.5 bg-white/40 hover:bg-white/80 transition-all" aria-label="Go to slide 2"></button>
                <button onclick="goToSlide(2)" class="w-4 h-1.5 bg-white/40 hover:bg-white/80 transition-all" aria-label="Go to slide 3"></button>
            </div>
        </div>
    </div>

    <!-- Slide 2: Worship -->
    <div class="carousel-slide absolute inset-0 w-full h-full opacity-0 z-10 flex items-center justify-center" id="slide-1">
        <div class="absolute inset-0 z-0">
            <img src="assets/image/aaron-burden-535Npq1wFG8-unsplash.jpg" alt="Worship" class="bg-zoom w-full h-full object-cover object-center" onerror="this.src='https://images.unsplash.com/photo-1438283173091-5dbf5c5a3206?q=80&w=2000&auto=format&fit=crop';">
            <div class="absolute inset-0 bg-slate-900/60 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/40 to-slate-900/20"></div>
        </div>
        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 text-center">
            <h1 class="font-display font-black text-6xl md:text-7xl lg:text-[5.5rem] tracking-tight mb-8 text-white leading-[1.05]">
                Encounter <span class="text-white">His Presence.</span>
            </h1>
            <p class="font-sans text-xl md:text-2xl text-white/90 mb-12 font-light leading-relaxed max-w-2xl mx-auto">
                Experience powerful worship and authentic community. There is a place for you in our church family.
            </p>
            <div class="flex items-center justify-center gap-6">
                <a href="visit" class="inline-flex items-center justify-center bg-white text-slate-900 hover:bg-slate-100 text-sm font-bold uppercase tracking-widest px-10 py-5 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1">
                    Plan Your Visit
                </a>
            </div>
            
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex gap-3">
                <button onclick="goToSlide(0)" class="w-4 h-1.5 bg-white/40 hover:bg-white/80 transition-all" aria-label="Go to slide 1"></button>
                <button onclick="goToSlide(1)" class="w-10 h-1.5 bg-white transition-all" aria-label="Current slide"></button>
                <button onclick="goToSlide(2)" class="w-4 h-1.5 bg-white/40 hover:bg-white/80 transition-all" aria-label="Go to slide 3"></button>
            </div>
        </div>
    </div>

    <!-- Slide 3: The Word -->
    <div class="carousel-slide absolute inset-0 w-full h-full opacity-0 z-10 flex items-center justify-center bg-slate-900" id="slide-2">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1504052434569-70ad5836ab65?q=80&w=2000&auto=format&fit=crop" alt="The Word" class="bg-zoom w-full h-full object-cover object-center opacity-60">
            <div class="absolute inset-0 bg-slate-900/60 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/40 to-slate-900/20"></div>
        </div>
        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 text-center">
            <h1 class="font-display font-black text-6xl md:text-7xl lg:text-[5.5rem] tracking-tight mb-8 text-white leading-[1.05]">
                Uncompromised <span class="text-[#c49a45]">Truth.</span>
            </h1>
            <p class="font-sans text-xl md:text-2xl text-white/90 mb-12 font-light leading-relaxed max-w-2xl mx-auto">
                Dive deep into the Word of God with our latest sermon series designed to build resilient faith.
            </p>
            <div class="flex items-center justify-center gap-6">
                <a href="sermons" class="inline-flex items-center justify-center bg-[#c49a45] text-white hover:bg-[#d4ac57] text-sm font-bold uppercase tracking-widest px-10 py-5 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1">
                    Watch Latest Sermon
                </a>
            </div>
            
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex gap-3">
                <button onclick="goToSlide(0)" class="w-4 h-1.5 bg-white/40 hover:bg-white/80 transition-all" aria-label="Go to slide 1"></button>
                <button onclick="goToSlide(1)" class="w-4 h-1.5 bg-white/40 hover:bg-white/80 transition-all" aria-label="Go to slide 2"></button>
                <button onclick="goToSlide(2)" class="w-10 h-1.5 bg-[#c49a45] transition-all" aria-label="Current slide"></button>
            </div>
        </div>
    </div>
</section>

<!-- MEET THE FOUNDER -->
<section class="py-24 md:py-32 bg-slate-900 relative overflow-hidden border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            
            <!-- Portrait Placeholder -->
            <div class="relative group">
                <div class="absolute inset-0 bg-[#c49a45] rounded-3xl blur-[100px] opacity-10 group-hover:opacity-15 transition-opacity duration-700"></div>
                <div class="relative w-full aspect-[3/4] rounded-sm overflow-hidden border border-slate-800 shadow-2xl bg-slate-800 flex items-center justify-center">
                    <!-- Placeholder for Founder Image -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent z-10"></div>
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=800&auto=format&fit=crop" alt="Rev. Francis Duane Yalley" class="w-full h-full object-cover transition-all duration-700">
                    
                    <div class="absolute bottom-10 left-10 z-20">
                        <div class="inline-flex items-center gap-3 px-4 py-2 rounded-sm bg-slate-900/90 backdrop-blur-md border border-slate-700 mb-4 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-[#c49a45]"></span>
                            <span class="text-white text-xs font-bold tracking-widest uppercase">The Repairer</span>
                        </div>
                        <h3 class="text-4xl font-display font-black text-white leading-tight">Rev. F.D.<br>Yalley</h3>
                    </div>
                </div>
            </div>

            <!-- Biography Content -->
            <div class="relative">
                <!-- Massive Background Quote -->
                <div class="absolute -top-20 -left-10 text-[10rem] font-display font-black text-slate-800/50 leading-none whitespace-nowrap pointer-events-none select-none z-0 hidden md:block">
                    RESTORER
                </div>
                
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-4 mb-6">
                        <div class="h-px w-12 bg-[#c49a45]"></div>
                        <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Meet The Founder</span>
                    </div>
                    
                    <h2 class="text-4xl md:text-5xl font-display font-bold text-white mb-10 leading-tight">
                        A voice of restoration, <br>a builder of lives & <br><span class="text-[#c49a45]">a repairer of destinies.</span>
                    </h2>
                    
                    <div class="text-slate-300 font-medium leading-relaxed">
                        <p class="mb-6 text-lg">
                            From the vibrant nation of Ghana in West Africa emerges Reverend Francis Duane Yalley, affectionately known as “The Repairer.” For over two decades, he has served as the General Overseer and founder of Bridge Ministries International, building not just a church, but a global movement of Switching On lives.
                        </p>
                        <div class="p-6 my-8 border-l-4 border-[#c49a45] bg-slate-800/50">
                            <p class="text-xl text-white font-display font-medium italic">
                                "Rev. F.D. Yalley is more than a preacher. He is a restorer of broken lives. A builder of leaders. A voice of prophetic clarity. A repairer of destinies."
                            </p>
                        </div>
                        <a href="about.php" class="inline-flex items-center justify-center gap-3 bg-[#c49a45] text-white hover:bg-[#d4ac57] text-sm font-bold uppercase tracking-widest px-8 py-4 transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-1 mt-4">
                            Read Full Biography
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- OUR MISSION: Clean & Professional -->
<section class="py-24 md:py-32 bg-slate-950 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            
            <div class="lg:col-span-6 order-2 lg:order-1">
                <div class="inline-flex items-center gap-4 mb-6">
                    <div class="h-px w-12 bg-[#c49a45]"></div>
                    <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Our Mission</span>
                </div>
                <h2 class="text-4xl md:text-5xl lg:text-6xl mb-8 font-display font-bold text-white leading-tight">
                    A legacy of <span class="text-[#c49a45]">enduring faith</span> and action.
                </h2>
                <p class="text-slate-400 text-lg leading-relaxed mb-12 font-light">
                    Under the leadership of our General Overseer, Rev. Francis Duane Yalley, Bridge Ministries International operates with a profound commitment to establishing a lasting, positive impact on individuals and society.
                </p>
                
                <div class="flex flex-col gap-6">
                    <div class="bg-slate-900 p-6 border-l-4 border-l-[#c49a45] shadow-sm flex items-start gap-6 group">
                        <div class="w-12 h-12 rounded-sm bg-slate-800 text-[#c49a45] flex-shrink-0 flex items-center justify-center group-hover:bg-[#c49a45] group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-white mb-2">Missions & Outreach</h4>
                            <p class="text-slate-400 text-sm leading-relaxed">Impacting communities across the globe through active missions and support.</p>
                        </div>
                    </div>
                    <div class="bg-slate-900 p-6 border-l-4 border-l-slate-700 shadow-sm flex items-start gap-6 group">
                        <div class="w-12 h-12 rounded-sm bg-slate-800 text-slate-300 flex-shrink-0 flex items-center justify-center group-hover:bg-slate-700 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-white mb-2">Sound Doctrine</h4>
                            <p class="text-slate-400 text-sm leading-relaxed">Rooted firmly in biblical truth, theology, and the transformative power of the Word.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 order-1 lg:order-2">
                <div class="relative overflow-hidden shadow-2xl aspect-[4/3] md:aspect-video border border-slate-800 group">
                    <img src="assets/image/chad-kirchoff-ivqGyYLtBI8-unsplash.jpg" alt="Community Outreach" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" onerror="this.src='https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=1000&auto=format&fit=crop';">
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- TEACHINGS (SERMONS): Clean & Professional -->
<section class="py-24 md:py-32 bg-slate-900 relative overflow-hidden border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 md:mb-16">
            <div>
                <div class="inline-flex items-center gap-4 mb-4">
                    <div class="h-px w-12 bg-[#c49a45]"></div>
                    <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Teachings</span>
                </div>
                <h2 class="text-4xl md:text-5xl lg:text-6xl text-white font-display font-bold">Latest Sermons</h2>
            </div>
            <a href="sermons" class="hidden md:flex items-center text-slate-400 hover:text-[#c49a45] font-semibold transition-colors mt-4 md:mt-0 tracking-widest uppercase text-sm group">
                All Sermons
                <div class="ml-4 w-10 h-10 border border-slate-700 flex items-center justify-center group-hover:bg-[#c49a45] group-hover:border-transparent group-hover:text-white transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>
        </div>

        <?php if (!empty($latestSermons)): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 xl:gap-10">
                <?php foreach ($latestSermons as $sermon): ?>
                    <?php $dateText = date('M d, Y', strtotime((string) $sermon['sermon_date'])); ?>
                    <a href="sermons" class="group relative bg-slate-800 border border-slate-700 hover:border-[#c49a45] rounded-md transition-all duration-300 hover:-translate-y-1 flex flex-col h-full overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.4)] hover:shadow-[0_20px_40px_rgba(196,154,69,0.15)]">
                        
                        <div class="aspect-[16/10] relative bg-slate-900 overflow-hidden mb-6">
                            <?php if ($sermon['sermon_image']): ?>
                                <img src="<?php echo htmlspecialchars($sermon['sermon_image']); ?>" alt="<?php echo htmlspecialchars((string) $sermon['title']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out" loading="lazy">
                                <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors duration-300"></div>
                            <?php else: ?>
                                <!-- Generic Sermon Flyer Fallback -->
                                <img src="https://images.unsplash.com/photo-1543165365-07232ed12fad?q=80&w=800&auto=format&fit=crop" alt="Sermon Flyer" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out opacity-80" loading="lazy">
                                <div class="absolute inset-0 bg-slate-900/50 group-hover:bg-slate-900/30 transition-colors duration-300"></div>
                            <?php endif; ?>
                            
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-16 h-16 rounded-full bg-red-600 text-white flex items-center justify-center transform group-hover:scale-110 group-hover:bg-red-700 transition-all duration-300 shadow-[0_0_20px_rgba(220,38,38,0.4)] group-hover:shadow-[0_0_30px_rgba(220,38,38,0.6)]">
                                    <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                        </div>

                        <div class="px-4 pb-4 flex flex-col flex-grow relative z-10">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="px-3 py-1 bg-slate-900/50 text-[#c49a45] text-[10px] sm:text-xs font-bold uppercase tracking-wider border border-slate-700"><?php echo htmlspecialchars($dateText); ?></span>
                                <?php if (!empty($sermon['topic'])): ?>
                                    <span class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-[#c49a45]"></span>
                                        <?php echo htmlspecialchars($sermon['topic']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold text-white mb-3 group-hover:text-[#c49a45] transition-colors line-clamp-2 leading-tight"><?php echo htmlspecialchars((string) $sermon['title']); ?></h3>
                            <p class="text-slate-400 text-sm mt-auto flex items-center gap-2 group-hover:text-white font-bold transition-colors">
                                Watch Message <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- WATCH ONLINE: Deep Navy -->
<section class="relative w-full h-[600px] flex items-center justify-center overflow-hidden bg-slate-900 group">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=2000&auto=format&fit=crop" alt="Watch Live" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-[10s] opacity-30 mix-blend-luminosity">
        <div class="absolute inset-0 bg-slate-900/80"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/90 to-transparent"></div>
    </div>
    
    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-10">
        <div class="max-w-xl">
            <div class="inline-flex items-center gap-3 mb-6 px-4 py-2 bg-white/10 border border-white/20 text-white font-bold text-xs tracking-[0.2em] uppercase backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse shadow-[0_0_10px_rgba(239,68,68,0.8)]"></span>
                Watch Online
            </div>
            <h2 class="font-display font-extrabold text-5xl md:text-7xl tracking-tighter mb-6 text-white leading-tight">
                Sundays. <br><span class="text-[#c49a45]">Anywhere</span> in the world.
            </h2>
            <p class="font-sans text-xl text-white/80 mb-10 font-light leading-relaxed">
                Join us live each week or revisit recent messages. Wherever you are, the bridge reaches you.
            </p>
        </div>
        
        <div class="flex flex-col gap-4 w-full md:w-auto">
            <a href="live" class="inline-flex items-center justify-center bg-[#c49a45] text-white hover:bg-[#d4ac57] text-sm font-bold uppercase tracking-widest px-10 py-5 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                Watch Live Service
            </a>
            <a href="sermons" class="inline-flex items-center justify-center bg-transparent text-white border-2 border-white/30 hover:border-white hover:bg-white/5 text-sm font-bold uppercase tracking-widest px-10 py-5 transition-all gap-3">
                <svg class="w-5 h-5 text-[#c49a45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Browse Archive
            </a>
        </div>
    </div>
</section>

<!-- WEEKLY SERVICES: Bento Box Flyers -->
<section class="py-24 md:py-32 bg-[#00000a] relative overflow-hidden border-t border-white/5">
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-600/10 blur-[150px] rounded-full pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16 md:mb-20">
            <div class="inline-flex items-center justify-center gap-4 mb-4">
                <div class="h-px w-12 bg-blue-400"></div>
                <span class="text-blue-300 font-bold text-sm tracking-widest uppercase">Join Us</span>
                <div class="h-px w-12 bg-blue-400"></div>
            </div>
            <h2 class="text-4xl md:text-5xl lg:text-6xl text-white font-display font-bold">Our Weekly Services</h2>
        </div>

        <!-- Bento Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                     <!-- Restorers (Large Feature) -->
            <div class="group relative bg-white overflow-hidden rounded-sm border border-gray-200 hover:border-[#c49a45] transition-all duration-500 shadow-sm hover:shadow-xl lg:col-span-2 min-h-[450px] flex flex-col justify-end">
                <div class="absolute inset-0 z-0 bg-slate-100">
                    <img src="assets/image/flyer_restorers.png" alt="Restorers" onerror="this.src='https://images.unsplash.com/photo-1438283173091-5dbf5c5a3206?q=80&w=1200&auto=format&fit=crop'" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-[10s] opacity-90 mix-blend-multiply">
                    <div class="absolute inset-x-0 bottom-0 h-full bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
                </div>
                <div class="relative z-10 p-10 md:p-14">
                    <div class="inline-flex items-center px-5 py-2 rounded-sm bg-white/90 backdrop-blur-md border border-gray-200 text-gray-900 text-xs font-bold tracking-[0.2em] uppercase mb-6 shadow-sm">
                        Sunday 8:45 AM
                    </div>
                    <h3 class="text-5xl md:text-7xl font-display font-black text-white mb-2 tracking-tighter uppercase">RESTORERS</h3>
                    <p class="text-white/90 text-xl font-medium tracking-wide">Celebration Service</p>
                </div>
            </div>

            <!-- Repairers (Tall Sidebar) -->
            <div class="group relative bg-white overflow-hidden rounded-sm border border-gray-200 hover:border-[#c49a45] transition-all duration-500 shadow-sm hover:shadow-xl min-h-[450px] flex flex-col justify-end">
                <div class="absolute inset-0 z-0 bg-slate-100">
                    <img src="assets/image/flyer_repairers.png" alt="Repairers" onerror="this.src='https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=800&auto=format&fit=crop'" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-[10s] opacity-90 mix-blend-multiply">
                    <div class="absolute inset-x-0 bottom-0 h-3/4 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
                </div>
                <div class="relative z-10 p-8">
                    <div class="inline-flex items-center px-4 py-1.5 rounded-sm bg-white/90 backdrop-blur-md border border-gray-200 text-gray-900 text-xs font-bold tracking-[0.2em] uppercase mb-4 shadow-sm">
                        Wednesdays
                    </div>
                    <h3 class="text-4xl font-display font-black text-white mb-1 tracking-tight uppercase">REPAIRERS</h3>
                    <p class="text-white/90 text-sm font-medium tracking-wide">Cell Meetings</p>
                </div>
            </div>

            <!-- Switch On (Square) -->
            <div class="group relative bg-white overflow-hidden rounded-sm border border-gray-200 hover:border-[#c49a45] transition-all duration-500 shadow-sm hover:shadow-xl min-h-[350px] flex flex-col justify-end">
                <div class="absolute inset-0 z-0 bg-slate-100">
                    <img src="assets/image/flyer_switchon.png" alt="Switch On" onerror="this.src='https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=800&auto=format&fit=crop'" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-[10s] opacity-90 mix-blend-multiply">
                    <div class="absolute inset-x-0 bottom-0 h-3/4 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
                </div>
                <div class="relative z-10 p-8">
                    <div class="inline-flex items-center px-4 py-1.5 rounded-sm bg-white/90 backdrop-blur-md border border-gray-200 text-gray-900 text-xs font-bold tracking-[0.2em] uppercase mb-4 shadow-sm">
                        Sunday 8:45 AM
                    </div>
                    <h3 class="text-4xl font-display font-black text-white mb-1 tracking-tight uppercase">SWITCH ON</h3>
                    <p class="text-white/90 text-sm font-medium tracking-wide">Youth Service</p>
                </div>
            </div>

            <!-- Builders (Wide Bottom) -->
            <div class="group relative bg-white overflow-hidden rounded-sm border border-gray-200 hover:border-[#c49a45] transition-all duration-500 shadow-sm hover:shadow-xl lg:col-span-2 min-h-[350px] flex flex-col justify-end">
                <div class="absolute inset-0 z-0 bg-slate-100">
                    <img src="assets/image/flyer_builders.png" alt="Builders" onerror="this.src='https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=1200&auto=format&fit=crop'" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-[10s] opacity-90 mix-blend-multiply">
                    <div class="absolute inset-x-0 bottom-0 h-full bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
                </div>
                <div class="relative z-10 p-8 md:p-10">
                    <div class="inline-flex items-center px-4 py-1.5 rounded-sm bg-white/90 backdrop-blur-md border border-gray-200 text-gray-900 text-xs font-bold tracking-[0.2em] uppercase mb-4 shadow-sm">
                        Fridays 7:00 PM
                    </div>
                    <h3 class="text-4xl md:text-5xl font-display font-black text-white mb-1 tracking-tight uppercase">BUILDERS</h3>
                    <p class="text-white/90 text-base font-medium tracking-wide">Leadership Meeting</p>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- EVENTS: Grid with Flyers -->
<section class="py-24 md:py-32 bg-slate-950 relative overflow-hidden border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 md:mb-16">
            <div>
                <div class="inline-flex items-center gap-4 mb-4">
                    <div class="h-px w-12 bg-[#c49a45]"></div>
                    <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Church Life</span>
                </div>
                <h2 class="text-4xl md:text-5xl lg:text-6xl text-white font-display font-bold">Upcoming Events</h2>
            </div>
            <a href="events" class="hidden md:flex items-center text-slate-400 hover:text-[#c49a45] font-semibold transition-colors mt-4 md:mt-0 tracking-widest uppercase text-sm group">
                All Events
                <div class="ml-4 w-10 h-10 border border-slate-700 flex items-center justify-center group-hover:bg-[#c49a45] group-hover:text-white group-hover:border-transparent transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </div>
            </a>
        </div>

        <?php if (!empty($upcomingEvents)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($upcomingEvents as $event): ?>
                    <?php 
                        $dateMonth = date('M', strtotime((string) $event['event_date']));
                        $dateDay = date('d', strtotime((string) $event['event_date']));
                    ?>
                    <a href="events" class="group flex flex-col bg-slate-800 overflow-hidden border border-slate-700 hover:border-[#c49a45] transition-all duration-500 hover:-translate-y-1 shadow-[0_8px_30px_rgb(0,0,0,0.4)] hover:shadow-[0_20px_40px_rgba(196,154,69,0.15)] rounded-md">
                        
                        <!-- FLYER IMAGE -->
                        <div class="w-full aspect-[4/3] bg-slate-900 relative overflow-hidden border-b border-slate-700">
                            <?php if (!empty($event['event_image'])): ?>
                                <img src="<?php echo htmlspecialchars((string) $event['event_image']); ?>" alt="<?php echo htmlspecialchars((string) $event['title']); ?>" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                            <?php else: ?>
                                <!-- Fallback Flyer -->
                                <div class="absolute inset-0 flex items-center justify-center bg-slate-900">
                                    <svg class="w-16 h-16 text-slate-700 group-hover:text-[#c49a45]/50 transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Overlay Hover Effect -->
                            <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/10 transition-colors duration-500"></div>
                        </div>
                        
                        <!-- DETAILS (UNDER IT) -->
                        <div class="p-6 flex items-start gap-5">
                            <!-- Gold Date Badge -->
                            <div class="flex-shrink-0 w-[4.5rem] h-[4.5rem] bg-slate-900 border border-slate-700 flex flex-col items-center justify-center text-white group-hover:bg-[#c49a45] group-hover:text-white group-hover:border-[#c49a45] transition-colors duration-300">
                                <span class="text-3xl font-display font-black leading-none tracking-tight"><?php echo $dateDay; ?></span>
                                <span class="text-[11px] font-bold uppercase tracking-widest mt-0.5"><?php echo $dateMonth; ?></span>
                            </div>
                            
                            <!-- Text details -->
                            <div class="flex-grow pt-1">
                                <h3 class="text-xl font-bold text-white mb-1.5 group-hover:text-[#c49a45] transition-colors leading-tight"><?php echo htmlspecialchars((string) $event['title']); ?></h3>
                                <p class="text-slate-400 text-sm font-medium"><?php echo htmlspecialchars((string) (!empty($event['venue']) ? $event['venue'] : 'Online / TBD')); ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Mobile All Events Link -->
            <div class="mt-10 text-center md:hidden">
                <a href="events" class="inline-flex items-center text-[#c49a45] font-bold uppercase tracking-widest text-sm">
                    View All Events <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
            
        <?php else: ?>
            <!-- EMPTY STATE: Highly Professional Look -->
            <div class="text-center py-10">
                <div class="max-w-2xl mx-auto bg-slate-900 p-12 border border-slate-800 flex flex-col items-center justify-center">
                    <div class="w-20 h-20 bg-slate-800 border border-slate-700 flex items-center justify-center mb-6 text-slate-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-slate-400 text-lg mb-8 font-light">No upcoming events are available at the moment.</p>
                    <a href="events" class="inline-flex items-center justify-center bg-[#c49a45] text-white hover:bg-[#d4ac57] text-sm font-bold uppercase tracking-widest px-8 py-4 transition-all shadow-md hover:shadow-lg">View All Events</a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- FIND US THIS SUNDAY / NEXT STEPS -->
<section class="py-24 md:py-32 bg-slate-900 relative overflow-hidden border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16 md:mb-20">
            <div class="inline-flex items-center justify-center gap-4 mb-4">
                <div class="h-px w-12 bg-[#c49a45]"></div>
                <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Next Steps</span>
                <div class="h-px w-12 bg-[#c49a45]"></div>
            </div>
            <h2 class="text-4xl md:text-5xl lg:text-6xl text-white font-display font-bold">Find Us This Sunday</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 xl:gap-12">
            <!-- 01 Plan Your Visit -->
            <a href="visit" class="group bg-slate-800 p-8 lg:p-12 hover:-translate-y-1 transition-all duration-300 border border-slate-700 hover:border-[#c49a45] shadow-[0_8px_30px_rgb(0,0,0,0.4)] hover:shadow-[0_20px_40px_rgba(196,154,69,0.15)] flex flex-col items-center text-center relative overflow-hidden">
                <div class="text-6xl font-display font-black text-slate-700/50 group-hover:text-slate-700 transition-colors duration-500 absolute top-4 right-8 -z-10">01</div>
                
                <div class="w-20 h-20 bg-slate-900 border border-slate-700 flex items-center justify-center text-[#c49a45] mb-8 group-hover:bg-[#c49a45] group-hover:text-white transition-all duration-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-[#c49a45] transition-colors">Plan Your Visit</h3>
                <p class="text-slate-400 leading-relaxed mb-8">First time? We'll help you know exactly what to expect and walk you through your first Sunday.</p>
                <span class="mt-auto text-[#c49a45] font-bold tracking-widest text-sm uppercase flex items-center gap-2 group-hover:text-[#d4ac57] transition-colors">Plan a Visit <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
            </a>

            <!-- 02 Find Community -->
            <a href="ministries" class="group bg-slate-800 p-8 lg:p-12 hover:-translate-y-1 transition-all duration-300 border border-slate-700 hover:border-[#c49a45] shadow-[0_8px_30px_rgb(0,0,0,0.4)] hover:shadow-[0_20px_40px_rgba(196,154,69,0.15)] flex flex-col items-center text-center relative overflow-hidden">
                <div class="text-6xl font-display font-black text-slate-700/50 group-hover:text-slate-700 transition-colors duration-500 absolute top-4 right-8 -z-10">02</div>
                
                <div class="w-20 h-20 bg-slate-900 border border-slate-700 flex items-center justify-center text-[#c49a45] mb-8 group-hover:bg-[#c49a45] group-hover:text-white transition-all duration-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-[#c49a45] transition-colors">Find Your Community</h3>
                <p class="text-slate-400 leading-relaxed mb-8">From cell groups to age-based ministries, there is a place for you to belong and grow.</p>
                <span class="mt-auto text-[#c49a45] font-bold tracking-widest text-sm uppercase flex items-center gap-2 group-hover:text-[#d4ac57] transition-colors">Explore Ministries <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
            </a>

            <!-- 03 Prayer Request -->
            <a href="contact" class="group bg-slate-800 p-8 lg:p-12 hover:-translate-y-1 transition-all duration-300 border border-slate-700 hover:border-[#c49a45] shadow-[0_8px_30px_rgb(0,0,0,0.4)] hover:shadow-[0_20px_40px_rgba(196,154,69,0.15)] flex flex-col items-center text-center relative overflow-hidden">
                <div class="text-6xl font-display font-black text-slate-700/50 group-hover:text-slate-700 transition-colors duration-500 absolute top-4 right-8 -z-10">03</div>
                
                <div class="w-20 h-20 bg-slate-900 border border-slate-700 flex items-center justify-center text-[#c49a45] mb-8 group-hover:bg-[#c49a45] group-hover:text-white transition-all duration-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-[#c49a45] transition-colors">Send a Prayer Request</h3>
                <p class="text-slate-400 leading-relaxed mb-8">Whatever you're carrying, we'd be honoured to stand with you in prayer this week.</p>
                <span class="mt-auto text-[#c49a45] font-bold tracking-widest text-sm uppercase flex items-center gap-2 group-hover:text-[#d4ac57] transition-colors">Request Prayer <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
            </a>
        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-32 bg-slate-950 text-center relative overflow-hidden border-t border-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <h2 class="text-5xl md:text-7xl mb-8 font-display font-bold text-white leading-tight">Ready to make a <br/><span class="text-[#c49a45]">difference?</span></h2>
        <p class="text-xl text-slate-400 mb-12 font-light leading-relaxed max-w-2xl mx-auto">
            Your generous giving enables us to continue our global missions and the teaching of biblical truth around the world.
        </p>
        <a href="donate" class="inline-flex items-center justify-center bg-[#c49a45] text-white hover:bg-[#d4ac57] text-sm font-bold uppercase tracking-widest px-12 py-5 transition-all shadow-[0_8px_30px_rgba(196,154,69,0.3)] hover:shadow-[0_20px_40px_rgba(196,154,69,0.5)] hover:-translate-y-1">
            Give Online
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
