<?php
$pageTitle = 'Flagship Programs | Bridge Ministries International';
$pageDescription = 'Experience transformation through our major annual events and milestones.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$flagships = [];
$eventsError = null;

try {
    $pdo = db_connect();
    
    // Fetch Flagship Programs (Show all as a catalog of annual events)
    $stmtFlagship = $pdo->query(
        "SELECT id, title, slug, description, event_date, end_date, venue, event_image 
         FROM events 
         WHERE event_type = 'flagship'
         ORDER BY event_date ASC"
    );
    $flagships = $stmtFlagship->fetchAll();

} catch (Throwable $e) {
    // Fallback to mock data for local UI review if database is not set up
    $flagships = [
        [
            'id' => 1,
            'title' => '21 Days of Fasting',
            'slug' => '21-days-fasting',
            'description' => "Our year begins with consecration. For 21 days, we gather to pray, fast, and seek God's face for the year ahead. It is a time of spiritual recalibration, prophetic direction, and miraculous encounters.\n\nJoin us daily as we press into the presence of God.",
            'event_date' => date('Y') . '-01-02',
            'end_date' => date('Y') . '-01-22',
            'venue' => 'Main Auditorium',
            'event_image' => 'https://images.unsplash.com/photo-1444053915174-884ee2678687?q=80&w=1200&auto=format&fit=crop'
        ],
        [
            'id' => 2,
            'title' => 'Annual Convention',
            'slug' => 'annual-convention',
            'description' => "The high point of our ministry calendar. The Annual Convention is a week-long gathering of all branches and partners globally. Expect powerful word ministration, explosive worship, and impartation from seasoned guest ministers.\n\nYou do not want to miss this milestone event.",
            'event_date' => date('Y') . '-08-15',
            'end_date' => date('Y') . '-08-21',
            'venue' => 'National Sports Stadium',
            'event_image' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=1200&auto=format&fit=crop'
        ]
    ];
}

include 'includes/header.php';
?>

<!-- CUSTOM ANIMATION STYLES -->
<!-- HERO SECTION -->
<section class="relative min-h-[65vh] flex items-center justify-center overflow-hidden bg-[#030303] pt-32 pb-20 gs-reveal-section">
    <!-- Ambient Glowing Orbs -->
    <div class="absolute top-10 left-1/4 w-[30rem] h-[30rem] bg-amber-500/10 blur-[120px] rounded-full mix-blend-screen pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-[40rem] h-[40rem] bg-indigo-600/10 blur-[150px] rounded-full mix-blend-screen pointer-events-none"></div>
    <div class="absolute inset-0 bg-[url('<?= setting('flagship.hero_bg_image', 'https://images.unsplash.com/photo-1544365558-35aa4afc111c?q=80&w=1200&auto=format&fit=crop') ?>')] bg-cover bg-center opacity-20 mix-blend-overlay"></div>
    
    <div class="relative z-10 text-center px-6 max-w-5xl mx-auto gs-reveal-up">
        <span class="inline-block py-2 px-4 rounded-full bg-white/5 border border-white/10 text-amber-500 text-xs font-bold tracking-[0.25em] uppercase mb-8 backdrop-blur-md shadow-2xl">
            Our Milestones
        </span>
        <h1 class="text-6xl md:text-8xl lg:text-9xl font-display font-black text-transparent bg-clip-text bg-gradient-to-br from-white via-neutral-200 to-neutral-600 uppercase tracking-normal mb-6 leading-[0.9]">
            <?= setting('flagship.hero_title', 'Flagship <br/><span class="italic font-light text-white/50">Programs</span>') ?>
        </h1>
        <p class="text-lg md:text-2xl text-neutral-400 font-medium max-w-3xl mx-auto leading-relaxed">
            <?= setting('flagship.hero_subtitle', 'Discover the core annual events that define our spiritual journey, bringing believers together for extraordinary moments of divine encounter.') ?>
        </p>
    </div>
</section>

<!-- VISION SECTION -->
<section class="py-24 md:py-40 bg-[#050505] relative overflow-hidden border-t border-white/5 gs-reveal-section">
    <div class="w-[90%] max-w-[112.5rem] mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 md:gap-24 items-center">
        
        <!-- Left Text Content -->
        <div class="gs-reveal-left">
            <h2 class="text-5xl md:text-7xl font-display font-black text-white uppercase tracking-normal leading-[0.95] mb-10">
                Experience<br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-600 italic">Transformation</span>
            </h2>
            <div class="space-y-8 text-neutral-400 text-lg md:text-xl leading-relaxed font-medium">
                <p>
                    At Bridge Ministries International, our flagship programs stand as pillars of spiritual growth and community cohesion. These meticulously crafted events, ranging from periods of fasting and prayer to celebratory gatherings, serve as pivotal moments in our collective journey of faith.
                </p>
                <p>
                    Each program offers a unique opportunity for transformation, fostering deeper connections with God and fellow believers while addressing diverse spiritual needs. Join us in these transformative experiences, where we pause, reflect, and align our lives with divine purpose and grace.
                </p>
            </div>
        </div>
        
        <!-- Right Frosted Glass Card -->
        <div class="relative gs-reveal-right delay-200">
            <!-- Glow behind card -->
            <div class="absolute -inset-1 bg-gradient-to-br from-amber-500/20 to-purple-600/20 blur-3xl rounded-[3rem] opacity-70"></div>
            
            <div class="relative bg-white/[0.03] backdrop-blur-2xl border border-white/10 p-10 md:p-16 rounded-[3rem] shadow-[0_8px_32px_0_rgba(0,0,0,0.3)]">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-600 flex items-center justify-center mb-10 shadow-lg shadow-amber-500/20 transform -rotate-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="text-3xl md:text-4xl font-display font-black text-white uppercase tracking-tight mb-6">Milestones of Faith</h3>
                <p class="text-neutral-400 leading-relaxed font-medium text-lg">
                    Our flagship programs are not just dates on a calendar; they are milestones in our shared journey. They symbolize hope, guidance, and renewal, inviting all to partake in moments of spiritual awakening, relationship enrichment, and miraculous encounters.
                </p>
            </div>
        </div>
        
    </div>
</section>

<!-- CATALOG SECTION -->
<section class="py-32 bg-[#0a0a0c] relative gs-reveal-section">
    <div class="w-[90%] max-w-[112.5rem] mx-auto">
        
        <?php if ($eventsError): ?>
            <div class="bg-red-500/10 text-red-400 p-6 rounded-2xl font-bold text-center border border-red-500/20 gs-reveal-up">
                <?php echo e($eventsError); ?>
            </div>
        <?php elseif (empty($flagships)): ?>
            <div class="text-center py-20 bg-white/5 rounded-[3rem] border border-white/10 gs-reveal-up">
                <p class="text-white/50 font-medium text-xl">No flagship programs are currently published.</p>
            </div>
        <?php else: ?>
            <div class="space-y-12 md:space-y-20">
                <?php foreach ($flagships as $index => $event): 
                    $startDate = date('M j', strtotime((string)$event['event_date']));
                    $endDate = !empty($event['end_date']) ? ' - ' . date('M j, Y', strtotime((string)$event['end_date'])) : ', ' . date('Y', strtotime((string)$event['event_date']));
                    $isReversed = $index % 2 !== 0;
                    $imageUrl = !empty($event['event_image']) ? $event['event_image'] : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=1200&auto=format&fit=crop';
                ?>
                <div class="group flex flex-col <?php echo $isReversed ? 'lg:flex-row-reverse' : 'lg:flex-row'; ?> gap-0 rounded-[2.5rem] overflow-hidden bg-black border border-white/10 hover:border-white/20 transition-all duration-700 gs-reveal-up shadow-2xl hover:shadow-[0_20px_50px_rgba(245,158,11,0.05)]">
                    
                    <!-- Image -->
                    <div class="w-full lg:w-1/2 aspect-video lg:aspect-auto relative overflow-hidden bg-neutral-900">
                        <img loading="lazy" src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars((string)$event['title']); ?>" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-[1.5s] ease-out">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-700"></div>
                        
                        <!-- Floating Date Tag on Image -->
                        <div class="absolute bottom-6 <?php echo $isReversed ? 'right-6' : 'left-6'; ?> bg-white/10 backdrop-blur-md border border-white/20 px-6 py-3 rounded-full flex items-center gap-3 transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500 delay-100">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-white font-sans font-bold tracking-widest text-xs uppercase"><?php echo $startDate . $endDate; ?></span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="w-full lg:w-1/2 p-10 md:p-16 lg:p-24 flex flex-col justify-center relative">
                        <!-- Abstract Card Accent -->
                        <div class="absolute top-0 <?php echo $isReversed ? 'right-0' : 'left-0'; ?> w-1 h-full bg-gradient-to-b from-amber-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                        
                        <h3 class="text-4xl md:text-5xl lg:text-6xl font-display font-black uppercase tracking-normal text-white leading-[0.95] mb-8 group-hover:text-amber-500 transition-colors duration-500">
                            <?php echo htmlspecialchars((string)$event['title']); ?>
                        </h3>
                        
                        <p class="text-lg text-neutral-400 font-medium leading-relaxed mb-10 line-clamp-4">
                            <?php echo strip_tags((string)$event['description']); ?>
                        </p>

                        <div class="flex flex-wrap items-center gap-6 mt-auto">
                            <a href="event-detail.php?id=<?php echo (int)$event['id']; ?>" class="inline-flex items-center justify-center bg-white text-black hover:bg-amber-500 hover:text-white px-8 py-4 rounded-full font-sans font-bold text-xs tracking-[0.2em] uppercase transition-all duration-500 transform group-hover:translate-x-2">
                                Discover More
                                <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                            
                            <?php if (!empty($event['venue'])): ?>
                            <div class="flex items-center gap-2 text-neutral-500 font-sans font-bold text-xs tracking-widest uppercase">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span><?php echo htmlspecialchars((string)$event['venue']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- INTERSECTION OBSERVER SCRIPT FOR ANIMATIONS -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("active");
                // Optional: Stop observing once revealed
                // observer.unobserve(entry.target); 
            }
        });
    }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });

    document.querySelectorAll('.gs-reveal-up, .gs-reveal-left, .gs-reveal-right').forEach((el) => {
        observer.observe(el);
    });
});
</script>

<?php include 'includes/footer.php'; ?>

