<?php
$pageTitle = 'Ministries | Bridge Ministries International';
$pageDescription = 'Find your community at Bridge Ministries International — youth, children, women, men, and serving teams.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$ministries = [];
try {
    $pdo = db_connect();
    $ministries = $pdo->query('SELECT * FROM weekly_services ORDER BY sort_order ASC, id ASC')->fetchAll();
} catch (Throwable $e) {
    log_exception($e, 'ministries');
    $ministries = [];
}

include 'includes/header.php';

// Render Cinematic Hero
render_hero_cinematic([
    'title' => setting('ministries.hero_title', 'Our <i class="text-amber-500 font-light">Ministries</i>'),
    'subtitle' => setting('ministries.hero_subtitle', 'Get Involved'),
    'bg_image' => setting('ministries.hero_bg_image', 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=1920&auto=format&fit=crop'),
    'button_text' => 'Join A Ministry',
    'button_url' => '#join',
    'is_video' => false
]);
?>

<!-- MINISTRIES GRID: Editorial Layout -->
<section class="py-32 bg-[#111113] relative overflow-hidden gs-reveal-section" id="join">
    
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        
        <?php if (empty($ministries)): ?>
            <!-- EMPTY STATE -->
            <div class="text-center py-20 bg-transparent max-w-4xl mx-auto gs-reveal-up">
                <div class="w-24 h-24 mx-auto bg-black/50 border border-white/5 flex items-center justify-center mb-8 text-white/50 rounded-full">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-4xl font-display font-black text-white/95 uppercase tracking-normal mb-4">Coming Soon</h3>
                <p class="text-neutral-400 text-lg mb-10 font-medium max-w-xl mx-auto">Ministry groups will be listed here soon. Please contact the church office to learn how to get involved.</p>
                <?php render_button_primary(['text' => 'Get Connected', 'url' => 'contact.php', 'style' => 'light']); ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($ministries as $index => $m):
                    $iconLetter = strtoupper(substr((string) $m['title'], 0, 1));
                ?>
                    <a href="ministry_detail?id=<?php echo $m['id']; ?>" class="group relative bg-[#0A0A0B] hover:bg-[#161619] transition-colors duration-700 hover:-translate-y-2 gs-reveal-up flex flex-col h-full overflow-hidden block">
                        <div class="relative z-10 flex flex-col h-full p-10">
                            <div class="flex items-center gap-6 mb-10">
                                <div class="w-16 h-16 bg-black flex flex-shrink-0 items-center justify-center text-amber-500/80 font-display font-black text-3xl group-hover:bg-amber-500 group-hover:text-black group-hover:-rotate-6 transition-all duration-500 shadow-lg shadow-black/50">
                                    <?php echo htmlspecialchars($iconLetter); ?>
                                </div>
                                <h2 class="font-display font-black uppercase text-3xl text-white/95 leading-[1.0] tracking-normal group-hover:text-amber-500 transition-colors duration-500"><?php echo htmlspecialchars($m['title']); ?></h2>
                            </div>
                            
                            <div class="flex-grow space-y-6">
                                <?php if (!empty($m['description'])): ?>
                                    <p class="text-neutral-400 font-sans font-medium leading-relaxed text-base"><?php echo htmlspecialchars($m['description']); ?></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($m['leader_name']) || !empty($m['time_info'])): ?>
                                    <div class="pt-8 mt-auto border-t border-white/10 space-y-4">
                                        <?php if (!empty($m['leader_name'])): ?>
                                            <div class="flex items-center justify-between text-xs font-sans uppercase tracking-widest font-bold">
                                                <div class="flex items-center gap-3 text-white/40">
                                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    Leader
                                                </div>
                                                <span class="text-white text-right"><?php echo htmlspecialchars($m['leader_name']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($m['time_info'])): ?>
                                            <div class="flex items-center justify-between text-xs font-sans uppercase tracking-widest font-bold">
                                                <div class="flex items-center gap-3 text-white/40">
                                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Meets
                                                </div>
                                                <span class="text-white text-right"><?php echo htmlspecialchars($m['time_info']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-24 bg-[#0A0A0B] relative overflow-hidden border-t border-white/5 gs-reveal-section">
    <div class="max-w-[112.5rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10 gs-reveal-up w-[90%]">
        <div class="bg-transparent p-12 md:p-20 flex flex-col md:flex-row items-center justify-between gap-12 text-center md:text-left relative overflow-hidden">
            <div class="relative z-10 max-w-2xl">
                <h2 class="text-4xl md:text-5xl font-display font-black tracking-normal uppercase text-white/95 mb-6">Take Your Next Step</h2>
                <p class="text-neutral-400 font-medium text-lg leading-relaxed">Not sure where to begin? Reach out to our pastoral team and we will help you find the perfect ministry fit for your journey.</p>
            </div>
            
            <div class="relative z-10 flex-shrink-0">
                <a href="contact" class="inline-flex items-center bg-white text-black hover:bg-amber-500 hover:text-white px-10 py-5 rounded-full font-sans font-bold uppercase tracking-widest text-xs transition-colors">
                    Get Connected
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
