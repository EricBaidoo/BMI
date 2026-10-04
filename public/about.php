<?php
$pageTitle = 'About Us | Bridge Ministries International';
$pageDescription = 'Learn about Bridge Ministries International — our story, mission, vision, values, and leadership team. A Christ-centred church family in Accra, Ghana.';

require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/helpers.php';

$founded = setting('site.founded_year', '2005');
$siteName = setting('site.name', 'Bridge Ministries International');
$svcSunday = setting('service.sunday_worship');

include __DIR__ . '/../includes/header.php';
?>

<!-- CUSTOM ANIMATION STYLES -->
<!-- HERO SECTION: Natural Premium -->
<section class="relative w-full h-[60vh] min-h-[40rem] bg-[#0A0A0B] overflow-hidden flex items-center justify-center pt-20 gs-reveal-section">
    <div class="absolute inset-0 z-0">
        <?php 
            $aboutHeroBg = setting('about.hero_bg_image', 'https://images.unsplash.com/photo-1438283173091-5dbf5c5a3206?q=80&w=2000&auto=format&fit=crop');
            $is_video = preg_match('/\.(mp4|webm)$/i', $aboutHeroBg);
        ?>
        <?php if ($is_video): ?>
            <video data-src="<?= htmlspecialchars(safe_url($aboutHeroBg)) ?>" class="bg-video w-full h-full object-cover opacity-50" loop muted playsinline preload="none" aria-hidden="true"></video>
        <?php else: ?>
            <img loading="lazy" src="<?= htmlspecialchars($aboutHeroBg) ?>" alt="Worship Background" class="w-full h-full object-cover opacity-50">
        <?php endif; ?>
        <!-- Smooth gradient fade into background -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A0A0B]/80 via-[#0A0A0B]/40 to-[#0A0A0B]"></div>
    </div>
    
    <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto text-center gs-reveal-up">
        <div class="inline-flex items-center gap-6 mb-8">
            <div class="h-px w-16 bg-white/20"></div>
            <span class="text-white/50 font-sans font-medium text-[0.625rem] tracking-[0.4em] uppercase">About Us</span>
            <div class="h-px w-16 bg-white/20"></div>
        </div>
        <h1 class="text-4xl md:text-5xl lg:text-7xl font-display font-black uppercase text-white/95 mb-6 tracking-normal leading-[1.0] drop-shadow-xl">
            <?= setting_html('about.hero_title', 'Building Bridges.') ?><br><i class="text-amber-500 font-light">Building Lives.</i>
        </h1>
    </div>
</section>

<!-- OUR STORY & FOUNDER: Editorial Layout -->
<section class="py-24 md:py-32 bg-[#0A0A0B] relative overflow-hidden gs-reveal-section">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-12 items-center">
            
            <!-- Biography Content -->
            <div class="lg:col-span-6 lg:col-start-1 relative order-2 lg:order-1 gs-reveal-right">
                <div class="inline-flex items-center gap-6 mb-12">
                    <span class="text-white/50 font-sans font-bold text-[0.625rem] tracking-[0.3em] uppercase">Our History</span>
                    <div class="h-px w-16 bg-white/20"></div>
                </div>
                
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-display font-black uppercase text-white/95 mb-10 leading-[1.0] tracking-tight">
                    <?= setting_html('about.history_title', 'From a local fellowship to a <br><i class="text-amber-500 font-light">global bridge.</i>') ?>
                </h2>
                
                <div class="text-neutral-400 font-sans font-medium leading-relaxed space-y-8 text-lg max-w-xl">
                    <p>
                        <?= setting_html('about.history_text1', 'Bridge Ministries International began in ' . $founded . ' as a small group of believers gathering with a single conviction: that the local church should be a bridge — between God and people, between generations, and between the church and the community it serves.') ?>
                    </p>
                    <p>
                        <?= setting_html('about.history_text2', 'Under the leadership of Rev. Francis Duane Yalley, that conviction has grown into a thriving movement of cell-based ministries and congregations reaching thousands of believers across Ghana and beyond.') ?>
                    </p>
                    <p class="italic text-white font-display font-black uppercase text-xl border-l-4 border-amber-500 pl-6 leading-snug">
                        <?= setting_html('about.vision_text', 'Today we remain anchored in the same simple commitments that defined our first gatherings: faithful preaching of the Word, strategic prayer, intentional discipleship, and a love for the city.') ?>
                    </p>
                </div>
            </div>

            <!-- Founder Portrait (Natural Lighting) -->
            <div class="lg:col-span-5 lg:col-start-8 relative group order-1 lg:order-2 gs-reveal-left">
                <div class="relative w-full aspect-[3/4] rounded-sm overflow-hidden bg-[#111113] z-10 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.8)]">
                    <!-- Smooth subtle overlay for text readability -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent z-10 pointer-events-none"></div>
                    <!-- Full color natural image -->
                    <img loading="lazy" src="<?= setting_url('about.founder_image', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=800&auto=format&fit=crop') ?>" alt="Rev. Francis Duane Yalley" class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-105">
                    
                    <div class="absolute bottom-12 left-12 z-20">
                        <div class="inline-flex items-center gap-3 mb-4">
                            <span class="text-white/70 text-[0.65rem] font-sans font-bold uppercase tracking-[0.3em]">General Overseer</span>
                        </div>
                        <h3 class="text-4xl md:text-5xl font-display font-black tracking-normal uppercase text-white leading-[1.0]"><?= setting_html('about.founder_name', 'Rev. F.D.<br>Yalley') ?></h3>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- CORE VALUES: Editorial Cards -->
<section class="py-32 bg-[#111113] relative overflow-hidden gs-reveal-section">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="text-center max-w-4xl mx-auto mb-20 gs-reveal-up">
            <div class="inline-flex items-center justify-center gap-6 mb-8">
                <div class="h-px w-16 bg-white/10"></div>
                <span class="text-white/50 font-sans font-bold text-[0.625rem] tracking-[0.4em] uppercase">Our Values</span>
                <div class="h-px w-16 bg-white/10"></div>
            </div>
            <h2 class="text-4xl md:text-5xl lg:text-6xl text-white/95 font-display font-black uppercase leading-[1.0] tracking-normal">What we hold <br><i class="text-amber-500 font-light">tightly.</i></h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            <?php
            $values = [
                ['Scripture, Above All', 'The Bible is our final authority for faith and practice. We teach it, trust it, and live by it without compromise.'],
                ['Prayer, Without Ceasing', 'Prayer is not a programme — it is the engine of everything we do, both privately and corporately as a church.'],
                ['Discipleship, On Purpose', 'We don\'t just gather crowds; we build disciples who follow Jesus and form others to do exactly the same.'],
                ['Family, Across Generations', 'From children to elders, every age group has a seat at the table and a powerful voice in the body of Christ.'],
                ['Mission, Beyond Walls', 'We are sent — to our neighbours, our city, and the nations — with the love, compassion, and message of Jesus.'],
                ['Integrity, At All Costs', 'We pursue financial transparency, ethical leadership, and Christ-like character in private and in public.']
            ];
            foreach ($values as $index => $val):
                $delayClass = 'delay-' . (($index % 3) + 1) * 100;
            ?>
            <div class="group relative bg-[#0A0A0B] p-10 lg:p-12 hover:bg-[#161619] transition-colors duration-500 gs-reveal-up <?php echo $delayClass; ?> overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-8">
                        <div class="text-4xl font-display font-black text-amber-500/80">
                            0<?php echo $index + 1; ?>
                        </div>
                    </div>
                    
                    <h3 class="text-2xl font-display font-black uppercase text-white/95 mb-4 tracking-normal"><?php echo $val[0]; ?></h3>
                    <p class="text-neutral-400 font-medium text-base leading-relaxed"><?php echo $val[1]; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA GRID -->
<section class="bg-[#0A0A0B] relative gs-reveal-section">
    <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-white/[0.03]">
        
        <div class="bg-transparent p-16 lg:p-20 flex flex-col group hover:bg-[#111113] transition-colors duration-500 gs-reveal-up relative overflow-hidden">
            <h3 class="text-3xl font-display font-black tracking-normal uppercase text-white/95 mb-4">What We Believe</h3>
            <p class="text-neutral-400 font-medium text-base leading-relaxed mb-12 flex-grow">Read our full statement of faith — what we hold to be true about God, Scripture, and salvation.</p>
            <a href="beliefs" class="inline-flex items-center text-amber-500 hover:text-white font-sans font-bold uppercase tracking-widest text-xs transition-colors w-fit">
                Our Beliefs <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        
        <div class="bg-transparent p-16 lg:p-20 flex flex-col group hover:bg-[#111113] transition-colors duration-500 gs-reveal-up delay-100 relative overflow-hidden">
            <h3 class="text-3xl font-display font-black tracking-normal uppercase text-white/95 mb-4">Find Community</h3>
            <p class="text-neutral-400 font-medium text-base leading-relaxed mb-12 flex-grow">From cell groups to age-based ministries, there is a place for you to belong, serve, and grow.</p>
            <a href="ministries" class="inline-flex items-center text-amber-500 hover:text-white font-sans font-bold uppercase tracking-widest text-xs transition-colors w-fit">
                Explore Ministries <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        
        <div class="bg-transparent p-16 lg:p-20 flex flex-col group hover:bg-[#111113] transition-colors duration-500 gs-reveal-up delay-200 relative overflow-hidden">
            <h3 class="text-3xl font-display font-black tracking-normal uppercase text-white/95 mb-4">Need Prayer?</h3>
            <p class="text-neutral-400 font-medium text-base leading-relaxed mb-12 flex-grow">Whatever you are facing, we'd be honoured to stand with you in prayer and believing God for a miracle.</p>
            <a href="contact" class="inline-flex items-center text-amber-500 hover:text-white font-sans font-bold uppercase tracking-widest text-xs transition-colors w-fit">
                Send a Request <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
