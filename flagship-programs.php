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
    $eventsError = 'Flagship programs are temporarily unavailable. ' . $e->getMessage();
}

include 'includes/header.php';
?>

<!-- HERO SECTION -->
<section class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#06080f] overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="<?= setting('flagship.hero_bg_image', 'https://images.unsplash.com/photo-1544365558-35aa4afc111c?q=80&w=1200&auto=format&fit=crop') ?>" alt="Flagship Programs Background" class="w-full h-full object-cover opacity-20 mix-blend-luminosity">
        <div class="absolute inset-0 bg-gradient-to-b from-[#06080f]/90 via-[#06080f]/80 to-[#06080f]"></div>
    </div>
    
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10 text-center">
        <div class="inline-flex items-center gap-4 mb-6">
            <div class="h-px w-12 bg-[#c49a45]"></div>
            <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Our Milestones</span>
            <div class="h-px w-12 bg-[#c49a45]"></div>
        </div>
        <h1 class="text-4xl md:text-7xl font-display font-black text-white mb-6 tracking-tight leading-tight">
            <?= setting('flagship.hero_title', 'Flagship <br/><span class="text-[#c49a45]">Programs.</span>') ?>
        </h1>
        <p class="text-xl text-slate-300 max-w-3xl mx-auto font-medium leading-relaxed">
            <?= setting('flagship.hero_subtitle', 'Discover the core annual events that define our spiritual journey and community life.') ?>
        </p>
    </div>
</section>

<!-- FLAGSHIP PROGRAMS CATALOG -->
<section class="py-24 bg-white relative overflow-hidden">
    <div class="w-[90%] max-w-6xl mx-auto">
        <div class="text-center max-w-4xl mx-auto mb-20">
            <h2 class="text-4xl md:text-5xl font-display font-black text-slate-900 tracking-tight mb-6">Experience Transformation Through BMI's Flagship Programs</h2>
            <p class="text-lg text-slate-600 font-medium leading-relaxed mb-6">
                At Bridge Ministries International, our flagship programs stand as pillars of spiritual growth and community cohesion. These meticulously crafted events, ranging from periods of fasting and prayer to celebratory gatherings, serve as pivotal moments in our collective journey of faith. Each program offers a unique opportunity for transformation, fostering deeper connections with God and fellow believers while addressing diverse spiritual needs. Join us in these transformative experiences, where we pause, reflect, and align our lives with divine purpose and grace.
            </p>
            <p class="text-lg text-slate-600 font-medium leading-relaxed">
                Our flagship programs are not just dates on a calendar; they are milestones in our shared journey. They symbolize hope, guidance, and renewal, inviting all to partake in moments of spiritual awakening, relationship enrichment, and miraculous encounters. Embrace these transformative journeys with us at Bridge Ministries International, where lives are changed, destinies are embraced, and hearts are aligned with a higher calling.
            </p>
        </div>

        <?php if ($eventsError): ?>
            <div class="bg-red-50 text-red-600 p-6 -none font-bold text-center border border-red-200">
                <?php echo e($eventsError); ?>
            </div>
        <?php elseif (empty($flagships)): ?>
            <div class="text-center py-12 bg-slate-50 -none border border-slate-100">
                <p class="text-slate-500 font-medium text-lg">No flagship programs are currently published.</p>
            </div>
        <?php else: ?>
            <div class="space-y-16">
                <?php foreach ($flagships as $index => $event): 
                    $startDate = date('M j', strtotime((string)$event['event_date']));
                    $endDate = !empty($event['end_date']) ? ' - ' . date('M j, Y', strtotime((string)$event['end_date'])) : ', ' . date('Y', strtotime((string)$event['event_date']));
                    $isReversed = $index % 2 !== 0;
                    $imageUrl = !empty($event['event_image']) ? $event['event_image'] : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=1200&auto=format&fit=crop';
                ?>
                <div class="flex flex-col <?php echo $isReversed ? 'lg:flex-row-reverse' : 'lg:flex-row'; ?> gap-12 items-center bg-slate-50 -none overflow-hidden border border-slate-100 group shadow-sm hover:shadow-2xl transition-all duration-500">
                    
                    <!-- Image -->
                    <div class="w-full lg:w-1/2 aspect-video lg:aspect-square relative overflow-hidden">
                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars((string)$event['title']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    </div>

                    <!-- Content -->
                    <div class="w-full lg:w-1/2 p-8 lg:p-12">
                        <div class="inline-flex items-center gap-3 mb-6 bg-[#c49a45]/10 px-4 py-2 -none border border-[#c49a45]/20">
                            <svg class="w-5 h-5 text-[#c49a45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-[#c49a45] font-bold tracking-widest text-sm uppercase"><?php echo $startDate . $endDate; ?></span>
                        </div>
                        
                        <h3 class="text-3xl lg:text-4xl font-display font-black text-slate-900 leading-tight mb-6"><?php echo htmlspecialchars((string)$event['title']); ?></h3>
                        
                        <p class="text-lg text-slate-600 font-medium leading-relaxed mb-8">
                            <?php echo nl2br(htmlspecialchars((string)$event['description'])); ?>
                        </p>

                        <?php if (!empty($event['venue'])): ?>
                        <div class="flex items-center gap-3 text-slate-500 font-medium mb-10">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span><?php echo htmlspecialchars((string)$event['venue']); ?></span>
                        </div>
                        <?php else: ?>
                        <div class="mb-10"></div>
                        <?php endif; ?>

                        <a href="event-detail.php?slug=<?php echo urlencode((string)$event['slug']); ?>" class="inline-flex items-center justify-center bg-slate-900 text-white hover:bg-[#c49a45] px-8 py-4 -none font-bold text-lg transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                            Learn More
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
