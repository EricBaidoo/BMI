<?php
$pageTitle = 'About Us | Bridge Ministries International';
$pageDescription = 'Learn about Bridge Ministries International — our story, mission, vision, values, and leadership team. A Christ-centred church family in Accra, Ghana.';

require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/helpers.php';

$founded = setting('site.founded_year', '2005');
$siteName = setting('site.name', 'Bridge Ministries International');
$svcSunday = setting('service.sunday_worship');

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
.delay-500 { transition-delay: 500ms; }
</style>

<!-- HERO SECTION: Cinematic True Black -->
<section class="relative w-full h-[60vh] min-h-[40rem] bg-[#030303] overflow-hidden flex items-center justify-center pt-20">
    <div class="absolute inset-0 z-0">
        <img loading="lazy" src="<?= setting('about.hero_bg_image', 'https://images.unsplash.com/photo-1438283173091-5dbf5c5a3206?q=80&w=2000&auto=format&fit=crop') ?>" alt="Worship Background" class="w-full h-full object-cover opacity-30 mix-blend-luminosity">
        <div class="absolute inset-0 bg-gradient-to-b from-[#030303]/90 via-[#030303]/60 to-[#050505]"></div>
    </div>
    
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[40rem] h-[40rem] bg-amber-600/10 blur-[120px] rounded-full mix-blend-screen pointer-events-none"></div>
    
    <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto text-center reveal">
        <div class="inline-flex items-center gap-6 mb-8">
            <div class="h-px w-16 bg-white/20"></div>
            <span class="text-white/50 font-sans font-medium text-[0.625rem] tracking-[0.4em] uppercase">About Us</span>
            <div class="h-px w-16 bg-white/20"></div>
        </div>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-display font-black uppercase text-transparent bg-clip-text bg-gradient-to-br from-white via-neutral-300 to-neutral-600 mb-6 tracking-normal leading-[1.0]">
            <?= setting('about.hero_title', 'Building Bridges.') ?><br><i class="text-white/50 font-light">Building Lives.</i>
        </h1>
    </div>
</section>

<!-- OUR STORY & FOUNDER: High Contrast Layout -->
<section class="py-24 md:py-32 bg-[#050505] relative overflow-hidden border-t border-white/5">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-8 items-center">
            
            <!-- Biography Content -->
            <div class="lg:col-span-6 lg:col-start-1 relative order-2 lg:order-1 reveal-right">
                <div class="inline-flex items-center gap-6 mb-12">
                    <span class="text-white/50 font-sans font-bold text-[0.625rem] tracking-[0.3em] uppercase">Our History</span>
                    <div class="h-px w-16 bg-white/20"></div>
                </div>
                
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-display font-black uppercase text-white mb-10 leading-[0.95] tracking-normal">
                    <?= setting('about.history_title', 'From a local fellowship to a <br><i class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-600">global bridge.</i>') ?>
                </h2>
                
                <div class="text-neutral-400 font-sans font-medium leading-relaxed space-y-8 text-lg max-w-xl">
                    <p>
                        <?= setting('about.history_text1', 'Bridge Ministries International began in ' . $founded . ' as a small group of believers gathering with a single conviction: that the local church should be a bridge — between God and people, between generations, and between the church and the community it serves.') ?>
                    </p>
                    <p>
                        <?= setting('about.history_text2', 'Under the leadership of Rev. Francis Duane Yalley, that conviction has grown into a thriving movement of cell-based ministries and congregations reaching thousands of believers across Ghana and beyond.') ?>
                    </p>
                    <p class="italic text-white font-display font-black uppercase text-xl border-l-4 border-amber-500 pl-6 leading-snug">
                        <?= setting('about.vision_text', 'Today we remain anchored in the same simple commitments that defined our first gatherings: faithful preaching of the Word, strategic prayer, intentional discipleship, and a love for the city.') ?>
                    </p>
                </div>
            </div>

            <!-- Founder Portrait (Glassmorphic Accent) -->
            <div class="lg:col-span-5 lg:col-start-8 relative group order-1 lg:order-2 reveal-left">
                <!-- Abstract Glow Behind Portrait -->
                <div class="absolute -inset-4 bg-gradient-to-br from-amber-500/20 to-purple-600/20 blur-3xl rounded-[3rem] opacity-70 group-hover:opacity-100 transition-opacity duration-1000"></div>
                
                <div class="relative w-full aspect-[3/4] rounded-[2.5rem] overflow-hidden bg-black z-10 border border-white/10 shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent z-10 pointer-events-none opacity-80 group-hover:opacity-60 transition-opacity duration-700"></div>
                    <img loading="lazy" src="<?= setting('about.founder_image', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=800&auto=format&fit=crop') ?>" alt="Rev. Francis Duane Yalley" class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-110">
                    
                    <div class="absolute bottom-10 left-10 z-20">
                        <div class="inline-flex items-center gap-3 mb-4 bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/10">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span class="text-white/90 text-[0.625rem] font-sans font-bold uppercase tracking-[0.3em]">General Overseer</span>
                        </div>
                        <h3 class="text-4xl md:text-5xl font-display font-black tracking-normal uppercase text-white leading-[0.9] group-hover:text-amber-500 transition-colors duration-500"><?= setting('about.founder_name', 'Rev. F.D.<br>Yalley') ?></h3>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- CORE VALUES: Premium Glass Cards -->
<section class="py-32 bg-[#0a0a0c] relative overflow-hidden">
    <!-- Background Ambient Orbs -->
    <div class="absolute top-0 right-0 w-[50rem] h-[50rem] bg-amber-600/5 blur-[150px] rounded-full mix-blend-screen pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[50rem] h-[50rem] bg-indigo-600/5 blur-[150px] rounded-full mix-blend-screen pointer-events-none"></div>

    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="text-center max-w-4xl mx-auto mb-20 reveal">
            <div class="inline-flex items-center justify-center gap-6 mb-8">
                <div class="h-px w-16 bg-white/20"></div>
                <span class="text-white/50 font-sans font-bold text-[0.625rem] tracking-[0.4em] uppercase">Our Values</span>
                <div class="h-px w-16 bg-white/20"></div>
            </div>
            <h2 class="text-4xl md:text-5xl lg:text-6xl text-white font-display font-black uppercase leading-[0.9] tracking-normal">What we hold <br><i class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-600">tightly.</i></h2>
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
            <div class="group relative bg-[#050505] rounded-[2.5rem] p-10 lg:p-12 border border-white/5 hover:border-amber-500/30 transition-all duration-700 hover:-translate-y-2 reveal <?php echo $delayClass; ?> shadow-2xl overflow-hidden">
                <!-- Abstract Glow on Hover -->
                <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-8">
                        <div class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-white/50 group-hover:bg-amber-500 group-hover:text-black group-hover:border-amber-500 transition-all duration-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div class="text-6xl font-display font-black uppercase text-white/[0.03] group-hover:text-amber-500/10 transition-colors duration-700">0<?php echo $index + 1; ?></div>
                    </div>
                    
                    <h3 class="text-2xl md:text-3xl font-display font-black uppercase text-white mb-4 tracking-normal group-hover:text-amber-500 transition-colors duration-500"><?php echo $val[0]; ?></h3>
                    <p class="text-neutral-400 font-medium text-base leading-relaxed"><?php echo $val[1]; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA GRID -->
<section class="py-1 bg-black relative border-t border-white/5">
    <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-white/10 border-b border-white/10">
        
        <div class="bg-[#050505] p-16 lg:p-20 flex flex-col group hover:bg-[#0a0a0a] transition-colors reveal relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            <h3 class="text-3xl md:text-4xl font-display font-black tracking-normal uppercase text-white mb-6">What We Believe</h3>
            <p class="text-neutral-400 font-medium text-base leading-relaxed mb-12 flex-grow">Read our full statement of faith — what we hold to be true about God, Scripture, and salvation.</p>
            <a href="beliefs.php" class="inline-flex items-center bg-white text-black hover:bg-amber-500 hover:text-white px-8 py-4 rounded-full font-sans font-bold uppercase tracking-widest text-xs transition-all w-fit">
                Our Beliefs <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        
        <div class="bg-[#050505] p-16 lg:p-20 flex flex-col group hover:bg-[#0a0a0a] transition-colors reveal delay-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            <h3 class="text-3xl md:text-4xl font-display font-black tracking-normal uppercase text-white mb-6">Find Community</h3>
            <p class="text-neutral-400 font-medium text-base leading-relaxed mb-12 flex-grow">From cell groups to age-based ministries, there is a place for you to belong, serve, and grow.</p>
            <a href="ministries.php" class="inline-flex items-center bg-white text-black hover:bg-amber-500 hover:text-white px-8 py-4 rounded-full font-sans font-bold uppercase tracking-widest text-xs transition-all w-fit">
                Explore Ministries <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        
        <div class="bg-[#050505] p-16 lg:p-20 flex flex-col group hover:bg-[#0a0a0a] transition-colors reveal delay-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            <h3 class="text-3xl md:text-4xl font-display font-black tracking-normal uppercase text-white mb-6">Need Prayer?</h3>
            <p class="text-neutral-400 font-medium text-base leading-relaxed mb-12 flex-grow">Whatever you are facing, we'd be honoured to stand with you in prayer and believing God for a miracle.</p>
            <a href="contact.php" class="inline-flex items-center bg-white text-black hover:bg-amber-500 hover:text-white px-8 py-4 rounded-full font-sans font-bold uppercase tracking-widest text-xs transition-all w-fit">
                Send a Request <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        
    </div>
</section>

<?php include 'includes/footer.php'; ?>

