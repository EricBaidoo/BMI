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
         WHERE event_type = 'special' AND event_date >= CURDATE()
         ORDER BY event_date ASC, event_time ASC
         LIMIT 12"
    );
    $specials = $stmtSpecial->fetchAll();

} catch (Throwable $e) {
    $eventsError = 'Events are temporarily unavailable. ' . $e->getMessage();
}

include 'includes/header.php';
?>

<!-- HERO SECTION -->
<section class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#06080f] overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=2000&auto=format&fit=crop" alt="Events Background" class="w-full h-full object-cover opacity-20 mix-blend-luminosity">
        <div class="absolute inset-0 bg-gradient-to-b from-[#06080f]/90 via-[#06080f]/80 to-[#06080f]"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <div class="inline-flex items-center gap-4 mb-6">
            <div class="h-px w-12 bg-[#c49a45]"></div>
            <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Church Life</span>
            <div class="h-px w-12 bg-[#c49a45]"></div>
        </div>
        <h1 class="text-5xl md:text-7xl font-display font-black text-white mb-6 tracking-tight leading-tight">
            Upcoming <br/><span class="text-[#c49a45]">Events.</span>
        </h1>
        <p class="text-xl text-slate-300 max-w-3xl mx-auto font-medium leading-relaxed">
            From our major annual conferences to weekly cell meetings, discover where you belong at Bridge Ministries.
        </p>
    </div>
</section>


<!-- SPECIAL EVENTS LIST -->
<section class="py-24 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div class="max-w-2xl">
                <h2 class="text-4xl font-display font-black text-slate-900 tracking-tight mb-4">Upcoming Special Events</h2>
                <p class="text-lg text-slate-600 font-medium">Don't miss out on these powerful one-off gatherings, seminars, and special worship nights.</p>
            </div>
        </div>

        <?php if (empty($specials)): ?>
            <div class="text-center py-16 bg-white rounded-none border border-slate-200 shadow-sm">
                <div class="w-20 h-20 bg-slate-50 border border-slate-100 flex items-center justify-center mb-6 text-[#c49a45] rounded-full mx-auto">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-slate-500 font-bold text-lg">No special events scheduled at the moment.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($specials as $event):
                    $eventDate = strtotime((string) $event['event_date']);
                    $month = date('M', $eventDate);
                    $day = date('d', $eventDate);
                    $eventTime = !empty($event['event_time']) ? date('g:i A', strtotime((string) $event['event_time'])) : null;
                    $venue = trim((string) ($event['venue'] ?? ''));
                    $imageUrl = !empty($event['event_image']) ? $event['event_image'] : 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=800&auto=format&fit=crop';
                ?>
                    <div class="group bg-white border border-slate-200 rounded-none hover:border-[#c49a45] transition-all duration-300 shadow-sm hover:shadow-[0_20px_40px_rgba(196,154,69,0.15)] flex flex-col h-full overflow-hidden">
                        
                        <!-- Event Image -->
                        <div class="aspect-[16/9] relative overflow-hidden bg-slate-100">
                            <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars((string) $event['title']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out" loading="lazy">
                            
                            <!-- Date Badge -->
                            <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-sm shadow-xl rounded-none text-center px-4 py-3 group-hover:border-[#c49a45] border border-transparent transition-colors">
                                <p class="text-[#c49a45] font-bold text-xs uppercase tracking-widest"><?php echo $month; ?></p>
                                <p class="text-slate-900 font-display font-black text-2xl leading-none mt-1"><?php echo $day; ?></p>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-8 flex flex-col flex-grow">
                            <h3 class="text-2xl font-bold text-slate-900 mb-4 group-hover:text-[#c49a45] transition-colors leading-tight"><?php echo htmlspecialchars((string) $event['title']); ?></h3>
                            
                            <div class="space-y-3 mb-8 flex-grow">
                                <?php if ($eventTime): ?>
                                    <div class="flex items-center gap-3 text-sm">
                                        <svg class="w-5 h-5 text-[#c49a45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="text-slate-600 font-medium"><?php echo htmlspecialchars($eventTime); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($venue !== ''): ?>
                                    <div class="flex items-center gap-3 text-sm">
                                        <svg class="w-5 h-5 text-[#c49a45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span class="text-slate-600 font-medium"><?php echo htmlspecialchars($venue); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <a href="event-detail.php?slug=<?php echo urlencode((string)$event['slug']); ?>" class="inline-flex items-center font-bold text-slate-900 hover:text-[#c49a45] transition-colors group/link mt-auto">
                                View Details 
                                <svg class="w-5 h-5 ml-2 transform group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- DISCOVER FLAGSHIP PROGRAMS CTA -->
<section class="py-20 bg-slate-900 relative overflow-hidden">
    <!-- Abstract Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-[#c49a45]/20 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1544365558-35aa4afc111c?q=80&w=2000&auto=format&fit=crop')] bg-cover bg-center opacity-10 mix-blend-luminosity"></div>
    
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h2 class="text-3xl md:text-5xl font-display font-black text-white tracking-tight mb-6">Experience Transformation</h2>
        <p class="text-xl text-slate-300 font-medium leading-relaxed mb-10">
            Our flagship programs are not just dates on a calendar; they are milestones in our shared journey of faith. Discover our major annual events that shape our community.
        </p>
        <a href="flagship-programs" class="inline-flex items-center justify-center bg-[#c49a45] text-white hover:bg-[#d4ac57] px-8 py-4 rounded-none font-bold text-lg transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
            View Flagship Programs
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
