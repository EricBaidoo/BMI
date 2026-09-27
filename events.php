<?php
$pageTitle = 'Events | Bridge Ministries International';
$pageDescription = 'Plan your week with upcoming services, outreach, and special gatherings at BMI.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$flagships = [];
$specials = [];
$eventsError = null;

try {
    $pdo = db_connect();
    
    // Fetch Special Events (Show chronologically)
    $stmtSpecial = $pdo->query(
        "SELECT id, title, slug, description, event_date, event_time, venue, event_image 
         FROM events 
         WHERE event_type = 'special' AND (event_date >= CURDATE() OR (end_date IS NOT NULL AND end_date >= CURDATE()))
         ORDER BY event_date ASC, event_time ASC
         LIMIT 12"
    );
    $specials = $stmtSpecial->fetchAll();

} catch (Throwable $e) {
    // Fallback to mock data for local UI review if database is not set up
    $specials = [
        [
            'id' => 1,
            'title' => 'Global Leadership Summit',
            'slug' => 'global-leadership-summit',
            'description' => 'A two-day intensive for leaders.',
            'event_date' => date('Y') . '-10-15',
            'event_time' => '09:00:00',
            'venue' => 'Main Auditorium',
            'event_image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=1200&auto=format&fit=crop'
        ],
        [
            'id' => 2,
            'title' => 'Night of Worship',
            'slug' => 'night-of-worship',
            'description' => 'An evening of prophetic worship.',
            'event_date' => date('Y') . '-11-05',
            'event_time' => '18:00:00',
            'venue' => 'Sanctuary',
            'event_image' => 'https://images.unsplash.com/photo-1444053915174-884ee2678687?q=80&w=1200&auto=format&fit=crop'
        ]
    ];
}

include 'includes/header.php';
?>



<!-- HERO SECTION -->
<section class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#030303] overflow-hidden min-h-[50vh] flex items-center justify-center gs-reveal-section">
    <div class="absolute inset-0 z-0">
        <img src="<?= setting('events.hero_bg_image', 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1200&auto=format&fit=crop') ?>" alt="Events Background" class="w-full h-full object-cover opacity-20 mix-blend-luminosity grayscale">
        <div class="absolute inset-0 bg-gradient-to-b from-[#030303]/90 via-[#030303]/80 to-[#0a0a0c]"></div>
    </div>
    
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[40rem] h-[40rem] bg-indigo-600/10 blur-[120px] rounded-full mix-blend-screen pointer-events-none"></div>

    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10 text-center gs-reveal-up">
        <div class="inline-flex items-center gap-6 mb-8">
            <div class="h-px w-16 bg-white/20"></div>
            <span class="text-white/50 font-sans font-bold text-[0.625rem] tracking-[0.4em] uppercase">Church Life</span>
            <div class="h-px w-16 bg-white/20"></div>
        </div>
        <h1 class="text-5xl md:text-6xl lg:text-8xl font-display font-black uppercase text-white/95 mb-6 tracking-tight leading-[1.0] drop-shadow-xl">
            <?= setting('events.hero_title', 'Church <br/><i class="text-amber-500 font-light">Calendar</i>') ?>
        </h1>
        <p class="text-xl md:text-2xl text-neutral-400 max-w-3xl mx-auto font-medium leading-relaxed">
            <?= setting('events.hero_subtitle', 'From our major annual conferences to weekly cell meetings, discover where you belong at Bridge Ministries.') ?>
        </p>
    </div>
</section>


<!-- SPECIAL EVENTS LIST -->
<section class="py-24 md:py-32 bg-[#111113] relative overflow-hidden gs-reveal-section">

    <div class="w-[95%] max-w-[112.5rem] mx-auto relative z-10">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-20 gap-6">
            <div class="max-w-3xl gs-reveal-right">
                <h2 class="text-4xl md:text-5xl lg:text-7xl font-display font-black text-white uppercase tracking-normal mb-6 leading-[1.0]">Upcoming <br><i class="text-amber-500 font-light">Special Events</i></h2>
                <p class="text-lg text-neutral-400 font-medium max-w-2xl">Don't miss out on these powerful one-off gatherings, seminars, and special worship nights.</p>
            </div>
        </div>

        <?php if ($eventsError): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-8 rounded-[2rem] text-center font-bold">
                <?php echo e($eventsError); ?>
            </div>
        <?php elseif (empty($specials)): ?>
            <div class="text-center py-24 bg-transparent gs-reveal-up">
                <div class="w-24 h-24 bg-white/5 border border-white/10 flex items-center justify-center mb-8 text-white/30 rounded-full mx-auto">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-neutral-400 font-medium text-xl">No special events scheduled at the moment.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 xl:gap-12">
                <?php foreach ($specials as $index => $event):
                    $eventDate = strtotime((string) $event['event_date']);
                    $month = date('M', $eventDate);
                    $day = date('d', $eventDate);
                    $eventTime = !empty($event['event_time']) ? date('g:i A', strtotime((string) $event['event_time'])) : null;
                    $venue = trim((string) ($event['venue'] ?? ''));
                    $imageUrl = !empty($event['event_image']) ? $event['event_image'] : 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=800&auto=format&fit=crop';
                    $delayClass = 'delay-' . (($index % 3) + 1) * 100;
                ?>
                    <div class="group relative bg-[#0A0A0B] hover:bg-[#161619] transition-colors duration-700 hover:-translate-y-2 flex flex-col h-full overflow-hidden gs-reveal-up <?php echo $delayClass; ?>">
                        
                        <!-- Event Image -->
                        <div class="aspect-[16/10] relative overflow-hidden bg-black mb-8">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                            <img loading="lazy" src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars((string) $event['title']); ?>" class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-105">
                            
                            <!-- Date Badge -->
                            <div class="absolute top-6 right-6 bg-white/10 backdrop-blur-md rounded-[1.5rem] text-center px-6 py-4 border border-white/20 shadow-[0_0_30px_rgba(0,0,0,0.5)] group-hover:bg-amber-500 group-hover:border-amber-400 group-hover:text-black transition-all duration-500 z-20">
                                <p class="text-white/60 group-hover:text-black/60 font-sans font-bold text-[0.625rem] uppercase tracking-[0.2em] transition-colors"><?php echo $month; ?></p>
                                <p class="text-white group-hover:text-black font-display font-black text-3xl leading-none mt-1 transition-colors"><?php echo $day; ?></p>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="flex-grow flex flex-col px-6 pb-8 z-20">
                            <h3 class="text-2xl font-display font-black uppercase text-white/95 mb-4 group-hover:text-amber-500 transition-colors duration-500"><?php echo htmlspecialchars((string) $event['title']); ?></h3>
                            
                            <div class="space-y-4 mb-10 flex-grow">
                                <?php if ($eventTime): ?>
                                    <div class="flex items-center gap-4 text-sm group/item">
                                        <div class="w-12 h-12 rounded-[1rem] bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 group-hover/item:bg-amber-500 group-hover/item:text-black transition-colors duration-300">
                                            <svg class="w-5 h-5 text-amber-500 group-hover/item:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <span class="text-neutral-400 font-medium group-hover/item:text-white transition-colors"><?php echo htmlspecialchars($eventTime); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($venue !== ''): ?>
                                    <div class="flex items-center gap-4 text-sm group/item">
                                        <div class="w-12 h-12 rounded-[1rem] bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 group-hover/item:bg-amber-500 group-hover/item:text-black transition-colors duration-300">
                                            <svg class="w-5 h-5 text-amber-500 group-hover/item:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <span class="text-neutral-400 font-medium group-hover/item:text-white transition-colors"><?php echo htmlspecialchars($venue); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <a href="event-detail.php?id=<?php echo (int)$event['id']; ?>" class="inline-flex items-center justify-between font-sans font-bold uppercase tracking-[0.2em] text-xs text-white bg-white/5 border border-white/10 px-6 py-5 rounded-[1.25rem] hover:bg-amber-500 hover:text-black hover:border-amber-500 transition-all duration-300 group/link mt-auto w-full text-left">
                                View Event Details 
                                <svg class="w-5 h-5 transform group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- DISCOVER FLAGSHIP PROGRAMS CTA -->
<section class="py-32 bg-[#050505] relative overflow-hidden gs-reveal-section">
    <!-- Grid Overlay -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LCAyNTUsIDI1NSwgMC4wNSkiLz48L3N2Zz4=')] opacity-30 z-0"></div>

    <div class="max-w-[112.5rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-[90%]">
        <div class="bg-white/[0.02] backdrop-blur-3xl border border-white/10 rounded-[3.5rem] p-12 md:p-24 text-center relative overflow-hidden gs-reveal-up shadow-[0_0_50px_rgba(0,0,0,0.5)]">
            <!-- Intense Inner Glow -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[50rem] h-[50rem] bg-indigo-600/20 blur-[120px] rounded-full pointer-events-none mix-blend-screen"></div>
            
            <h2 class="text-5xl md:text-6xl lg:text-8xl font-display font-black text-transparent bg-clip-text bg-gradient-to-br from-white to-white/40 uppercase tracking-normal mb-8 leading-[0.9] relative z-10">Experience <br><i class="text-amber-500 font-light">Transformation</i></h2>
            
            <p class="text-xl text-neutral-400 font-medium leading-relaxed mb-12 max-w-2xl mx-auto relative z-10">
                Our flagship programs are not just dates on a calendar; they are milestones in our shared journey of faith. Discover our major annual events that shape our community.
            </p>
            
            <a href="flagship-programs.php" class="inline-flex items-center justify-center bg-white text-black hover:bg-amber-500 px-12 py-6 font-bold uppercase tracking-[0.2em] text-sm rounded-full transition-all hover:-translate-y-1 relative z-10 shadow-[0_0_30px_rgba(255,255,255,0.1)] hover:shadow-[0_0_40px_rgba(245,158,11,0.3)]">
                View Flagship Programs
                <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
