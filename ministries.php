<?php
$pageTitle = 'Ministries | Bridge Ministries International';
$pageDescription = 'Find your community at Bridge Ministries International — youth, children, women, men, and serving teams.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$ministries = [];
try {
    $pdo = db_connect();
    $ministries = $pdo->query('SELECT * FROM ministries ORDER BY name ASC')->fetchAll();
} catch (Throwable $e) {
    $ministries = [];
}

include 'includes/header.php';
?>

<!-- HERO SECTION -->
<section class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-slate-900 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=2000&auto=format&fit=crop" alt="Community Background" class="w-full h-full object-cover opacity-20 mix-blend-luminosity">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/90 via-slate-900/80 to-slate-900"></div>
    </div>
    
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10 text-center">
        <div class="inline-flex items-center gap-4 mb-6">
            <div class="h-px w-12 bg-[#c49a45]"></div>
            <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Community</span>
            <div class="h-px w-12 bg-[#c49a45]"></div>
        </div>
        <h1 class="text-5xl md:text-7xl font-display font-black text-white mb-6 tracking-tight leading-tight">
            Find Your <br/><span class="text-[#c49a45]">Place.</span>
        </h1>
        <p class="text-xl text-slate-300 max-w-3xl mx-auto font-light leading-relaxed">
            Every ministry at BMI is designed to help you grow in Christ, build authentic relationships, and serve the world with purpose.
        </p>
    </div>
</section>

<!-- MINISTRIES GRID -->
<section class="py-24 md:py-32 bg-white relative overflow-hidden border-t border-slate-200">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        
        <?php if (empty($ministries)): ?>
            <!-- EMPTY STATE -->
            <div class="text-center py-10">
                <div class="max-w-2xl mx-auto bg-slate-50 p-12 border border-slate-200 flex flex-col items-center justify-center">
                    <div class="w-20 h-20 bg-white border border-slate-200 flex items-center justify-center mb-6 text-slate-500 rounded-full">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <p class="text-slate-600 text-lg mb-8 font-light">Ministry groups will be listed here soon. Please contact the church office to learn how to get involved.</p>
                    <a href="contact" class="inline-flex items-center justify-center bg-[#c49a45] text-white hover:bg-[#d4ac57] text-sm font-bold uppercase tracking-widest px-8 py-4 transition-all shadow-[0_8px_30px_rgba(196,154,69,0.3)] hover:-translate-y-1">Get Connected</a>
                </div>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($ministries as $m):
                    $iconLetter = strtoupper(substr((string) $m['name'], 0, 1));
                ?>
                    <div class="group bg-slate-50 border border-slate-200 hover:border-[#c49a45] p-8 transition-all duration-300 shadow-sm hover:shadow-[0_8px_30px_rgba(196,154,69,0.15)] flex flex-col h-full">
                        <div class="flex items-center gap-5 mb-6">
                            <div class="w-16 h-16 bg-white border border-slate-200 flex flex-shrink-0 items-center justify-center text-[#c49a45] font-display font-black text-2xl group-hover:bg-[#c49a45] group-hover:text-white transition-colors duration-300">
                                <?php echo e($iconLetter); ?>
                            </div>
                            <h2 class="font-bold text-2xl text-slate-900 leading-tight group-hover:text-[#c49a45] transition-colors"><?php echo e($m['name']); ?></h2>
                        </div>
                        
                        <div class="flex-grow space-y-4">
                            <?php if (!empty($m['description'])): ?>
                                <p class="text-slate-600 leading-relaxed text-sm"><?php echo e($m['description']); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($m['leader_name']) || !empty($m['meeting_schedule'])): ?>
                                <div class="pt-4 mt-auto border-t border-slate-200 space-y-2">
                                    <?php if (!empty($m['leader_name'])): ?>
                                        <div class="flex items-center gap-3 text-sm">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span class="text-slate-800 font-medium">Leader:</span>
                                            <span class="text-slate-600"><?php echo e($m['leader_name']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($m['meeting_schedule'])): ?>
                                        <div class="flex items-center gap-3 text-sm">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span class="text-slate-800 font-medium">Meets:</span>
                                            <span class="text-slate-600"><?php echo e($m['meeting_schedule']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-24 bg-slate-900 relative overflow-hidden border-t border-slate-800">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="bg-slate-950 border border-slate-800 p-10 md:p-14 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
            <div>
                <h2 class="text-3xl font-display font-bold text-white mb-3">Take Your Next Step</h2>
                <p class="text-slate-400 text-lg max-w-xl">Not sure where to begin? Reach out to our pastoral team and we will help you find the perfect ministry fit for your journey.</p>
            </div>
            <a href="contact" class="inline-flex items-center justify-center bg-[#c49a45] text-white hover:bg-[#d4ac57] text-sm font-bold uppercase tracking-widest px-8 py-4 transition-all shadow-[0_8px_30px_rgba(196,154,69,0.3)] hover:-translate-y-1 flex-shrink-0">
                Get Connected
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
