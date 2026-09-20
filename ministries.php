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
    $ministries = [];
}

include 'includes/header.php';
?>

<!-- CUSTOM ANIMATION STYLES -->
<style>
.reveal { opacity: 0; transform: translateY(40px); transition: all 1s cubic-bezier(0.16, 1, 0.3, 1); }
.reveal.revealed { opacity: 1; transform: translateY(0); }
.reveal-left { opacity: 0; transform: translateX(-40px); transition: all 1s cubic-bezier(0.16, 1, 0.3, 1); }
.reveal-left.revealed { opacity: 1; transform: translateX(0); }
.reveal-right { opacity: 0; transform: translateX(40px); transition: all 1s cubic-bezier(0.16, 1, 0.3, 1); }
.reveal-right.revealed { opacity: 1; transform: translateX(0); }
.delay-100 { transition-delay: 100ms; }
.delay-200 { transition-delay: 200ms; }
.delay-300 { transition-delay: 300ms; }
.delay-400 { transition-delay: 400ms; }
</style>

<!-- HERO SECTION -->
<section class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#030303] overflow-hidden min-h-[50vh] flex items-center justify-center">
    <div class="absolute inset-0 z-0">
        <img loading="lazy" src="<?= setting('ministries.hero_bg_image', 'https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=1200&auto=format&fit=crop') ?>" alt="Community Background" class="w-full h-full object-cover opacity-20 mix-blend-luminosity grayscale">
        <div class="absolute inset-0 bg-gradient-to-b from-[#030303]/90 via-[#030303]/80 to-[#0a0a0c]"></div>
    </div>
    
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[30rem] h-[30rem] bg-indigo-600/10 blur-[120px] rounded-full mix-blend-screen pointer-events-none"></div>

    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10 text-center reveal">
        <div class="inline-flex items-center gap-6 mb-8">
            <div class="h-px w-16 bg-white/20"></div>
            <span class="text-white/50 font-sans font-bold text-[0.625rem] tracking-[0.4em] uppercase">Community</span>
            <div class="h-px w-16 bg-white/20"></div>
        </div>
        <h1 class="text-6xl md:text-8xl lg:text-9xl font-display font-black tracking-normal uppercase text-transparent bg-clip-text bg-gradient-to-br from-white via-neutral-300 to-neutral-600 mb-6 leading-[0.9]">
            <?= setting('ministries.hero_title', 'Discover Your <br/><span class="italic font-light text-white/50">Calling.</span>') ?>
        </h1>
        <p class="text-xl md:text-2xl text-neutral-400 max-w-3xl mx-auto font-medium leading-relaxed">
            <?= setting('ministries.hero_subtitle', 'Every ministry at BMI is designed to help you grow in Christ, build authentic relationships, and serve the world with purpose.') ?>
        </p>
    </div>
</section>

<!-- MINISTRIES GRID: Premium Frosted Glass -->
<section class="py-32 bg-[#0a0a0c] relative overflow-hidden">
    <!-- Ambient Background Glows -->
    <div class="absolute top-0 right-1/4 w-[40rem] h-[40rem] bg-amber-600/5 blur-[150px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 left-1/4 w-[50rem] h-[50rem] bg-indigo-600/5 blur-[150px] rounded-full pointer-events-none"></div>
    
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        
        <?php if (empty($ministries)): ?>
            <!-- EMPTY STATE -->
            <div class="text-center py-20 bg-white/5 rounded-[3rem] border border-white/10 reveal backdrop-blur-md shadow-2xl max-w-4xl mx-auto">
                <div class="w-24 h-24 mx-auto bg-black/50 border border-white/10 flex items-center justify-center mb-8 text-white/50 rounded-full">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-4xl font-display font-black text-white uppercase tracking-normal mb-4">Coming Soon</h3>
                <p class="text-neutral-400 text-lg mb-10 font-medium max-w-xl mx-auto">Ministry groups will be listed here soon. Please contact the church office to learn how to get involved.</p>
                <a href="contact.php" class="inline-flex items-center bg-white text-black hover:bg-amber-500 hover:text-white px-8 py-4 rounded-full font-sans font-bold uppercase tracking-widest text-xs transition-all">Get Connected <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg></a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($ministries as $index => $m):
                    $iconLetter = strtoupper(substr((string) $m['title'], 0, 1));
                    $delayClass = 'delay-' . (($index % 3) + 1) * 100;
                ?>
                    <a href="ministry_detail.php?id=<?php echo $m['id']; ?>" class="group relative bg-[#050505] rounded-[2.5rem] p-10 border border-white/5 hover:border-amber-500/30 transition-all duration-700 hover:-translate-y-2 reveal <?php echo $delayClass; ?> shadow-2xl flex flex-col h-full overflow-hidden block">
                        
                        <!-- Glow effect behind the card content -->
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>

                        <div class="relative z-10 flex flex-col h-full">
                            <div class="flex items-center gap-6 mb-10">
                                <div class="w-16 h-16 bg-white/5 border border-white/10 flex flex-shrink-0 items-center justify-center text-white/50 font-display font-black text-3xl rounded-2xl group-hover:bg-amber-500 group-hover:text-black group-hover:-rotate-6 transition-all duration-500 shadow-lg shadow-black/50">
                                    <?php echo e($iconLetter); ?>
                                </div>
                                <h2 class="font-display font-black uppercase text-3xl text-white leading-[1.0] tracking-normal group-hover:text-amber-500 transition-colors duration-500"><?php echo e($m['title']); ?></h2>
                            </div>
                            
                            <div class="flex-grow space-y-6">
                                <?php if (!empty($m['description'])): ?>
                                    <p class="text-neutral-400 font-sans font-medium leading-relaxed text-base"><?php echo e($m['description']); ?></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($m['leader_name']) || !empty($m['time_info'])): ?>
                                    <div class="pt-8 mt-auto border-t border-white/10 space-y-4">
                                        <?php if (!empty($m['leader_name'])): ?>
                                            <div class="flex items-center justify-between text-xs font-sans uppercase tracking-widest font-bold">
                                                <div class="flex items-center gap-3 text-white/40">
                                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    Leader
                                                </div>
                                                <span class="text-white text-right"><?php echo e($m['leader_name']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($m['time_info'])): ?>
                                            <div class="flex items-center justify-between text-xs font-sans uppercase tracking-widest font-bold">
                                                <div class="flex items-center gap-3 text-white/40">
                                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Meets
                                                </div>
                                                <span class="text-white text-right"><?php echo e($m['time_info']); ?></span>
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
<section class="py-24 bg-[#030303] relative overflow-hidden border-t border-white/5">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-r from-amber-600/10 to-purple-600/10 opacity-50"></div>
    </div>
    
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 reveal">
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-12 md:p-20 rounded-[3rem] flex flex-col md:flex-row items-center justify-between gap-12 text-center md:text-left shadow-2xl relative overflow-hidden">
            <!-- Glass Accents -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-amber-500/20 blur-[100px] rounded-full"></div>
            
            <div class="relative z-10">
                <h2 class="text-4xl md:text-5xl font-display font-black tracking-normal uppercase text-white mb-6">Take Your Next Step</h2>
                <p class="text-neutral-400 font-medium text-lg max-w-xl leading-relaxed">Not sure where to begin? Reach out to our pastoral team and we will help you find the perfect ministry fit for your journey.</p>
            </div>
            
            <a href="contact.php" class="relative z-10 inline-flex items-center justify-center bg-white text-black hover:bg-amber-500 hover:text-white px-10 py-5 rounded-full font-sans font-bold text-xs tracking-widest uppercase transition-all whitespace-nowrap">
                Get Connected <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

