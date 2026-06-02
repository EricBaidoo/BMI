<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$slug = $_GET['slug'] ?? '';
$event = null;
$error = null;

if (!$slug) {
    header('Location: events.php');
    exit;
}

try {
    $pdo = db_connect();
    $stmt = $pdo->prepare("SELECT * FROM events WHERE slug = ?");
    $stmt->execute([$slug]);
    $event = $stmt->fetch();
} catch (Throwable $e) {
    $error = "Unable to fetch event details.";
}

if (!$event) {
    $pageTitle = 'Event Not Found';
} else {
    $pageTitle = htmlspecialchars((string)$event['title']) . ' | Bridge Ministries International';
    $pageDescription = substr(strip_tags((string)$event['description']), 0, 160);
}

include 'includes/header.php';
?>

<div class="pt-20 md:pt-24 bg-slate-50 min-h-screen">
    <?php if ($error || !$event): ?>
        <div class="max-w-3xl mx-auto px-4 py-32 text-center">
            <h1 class="text-4xl font-display font-black text-slate-900 mb-6">Event Not Found</h1>
            <p class="text-lg text-slate-600 mb-8"><?php echo $error ?? "We couldn't find the event you were looking for."; ?></p>
            <a href="events" class="inline-block bg-[#c49a45] text-white px-8 py-4  font-bold hover:bg-[#d4ac57] transition-colors">Return to Events</a>
        </div>
    <?php else: 
        $imageUrl = !empty($event['event_image']) ? $event['event_image'] : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=1200&auto=format&fit=crop';
        $startDate = date('F j, Y', strtotime((string)$event['event_date']));
        $endDate = !empty($event['end_date']) ? date('F j, Y', strtotime((string)$event['end_date'])) : null;
        $eventTime = !empty($event['event_time']) ? date('g:i A', strtotime((string)$event['event_time'])) : null;
        
        $isFlagship = ($event['event_type'] === 'flagship');
    ?>

    <!-- EVENT HERO -->
    <section class="relative <?php echo $isFlagship ? 'h-[70vh] min-h-[37.5rem]' : 'h-[50vh] min-h-[25rem]'; ?> overflow-hidden">
        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars((string)$event['title']); ?>" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-slate-900/30"></div>
        
        <div class="absolute inset-0 flex flex-col justify-end">
            <div class="w-[90%] max-w-[112.5rem] mx-auto w-full pb-16 md:pb-24">
                
                <?php if ($isFlagship): ?>
                    <span class="inline-block bg-[#c49a45] text-white font-bold text-xs tracking-widest uppercase px-4 py-1.5 rounded-full mb-6">Flagship Program</span>
                <?php else: ?>
                    <span class="inline-block bg-white text-slate-900 font-bold text-xs tracking-widest uppercase px-4 py-1.5 rounded-full mb-6">Special Event</span>
                <?php endif; ?>

                <h1 class="text-5xl md:text-7xl font-display font-black text-white mb-6 leading-tight max-w-4xl">
                    <?php echo htmlspecialchars((string)$event['title']); ?>
                </h1>
                
                <div class="flex flex-wrap items-center gap-6 text-slate-300 font-medium text-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#c49a45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span><?php echo $startDate; ?><?php echo $endDate ? ' - ' . $endDate : ''; ?></span>
                    </div>
                    <?php if ($eventTime): ?>
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#c49a45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span><?php echo $eventTime; ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($event['venue'])): ?>
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#c49a45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span><?php echo htmlspecialchars((string)$event['venue']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTENT -->
    <section class="py-16 md:py-24">
        <div class="w-[90%] max-w-[112.5rem] mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                
                <!-- Main Details -->
                <div class="lg:col-span-2">
                    <div class="prose prose-lg prose-slate max-w-none">
                        <h2 class="text-3xl font-display font-black text-slate-900 mb-8">About This Event</h2>
                        <div class="text-slate-700 leading-loose">
                            <?php echo nl2br(htmlspecialchars((string)$event['description'])); ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar / Registration -->
                <div class="lg:col-span-1">
                    <div class="bg-white -none p-8 border border-slate-200 shadow-xl sticky top-32">
                        <h3 class="text-2xl font-display font-black text-slate-900 mb-6">Join Us</h3>
                        
                        <div class="space-y-6 mb-8">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 -none bg-slate-50 flex items-center justify-center flex-shrink-0 text-[#c49a45]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">Date</p>
                                    <p class="text-slate-600 text-sm"><?php echo $startDate; ?><?php echo $endDate ? ' to <br/>' . $endDate : ''; ?></p>
                                </div>
                            </div>
                            
                            <?php if (!empty($event['venue'])): ?>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 -none bg-slate-50 flex items-center justify-center flex-shrink-0 text-[#c49a45]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">Location</p>
                                    <p class="text-slate-600 text-sm"><?php echo htmlspecialchars((string)$event['venue']); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <a href="#" class="block w-full bg-[#c49a45] text-white text-center px-6 py-4 -none font-bold hover:bg-[#d4ac57] transition-all shadow-md hover:-translate-y-1">
                            Register Now
                        </a>
                        <p class="text-center text-xs text-slate-400 mt-4">Registration may be required for certain events.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
