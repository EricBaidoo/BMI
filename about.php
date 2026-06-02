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

<!-- HERO SECTION -->
<section class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-slate-900 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="<?= setting('about.hero_bg_image', 'https://images.unsplash.com/photo-1438283173091-5dbf5c5a3206?q=80&w=2000&auto=format&fit=crop') ?>" alt="Worship Background" class="w-full h-full object-cover opacity-30 mix-blend-luminosity">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/90 via-slate-900/80 to-slate-900"></div>
    </div>
    
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10 text-center">
        <div class="inline-flex items-center gap-4 mb-6">
            <div class="h-px w-12 bg-[#c49a45]"></div>
            <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">About Us</span>
            <div class="h-px w-12 bg-[#c49a45]"></div>
        </div>
        <h1 class="text-5xl md:text-7xl font-display font-black text-white mb-6 tracking-tight leading-tight">
            Building Bridges. <br/><span class="text-[#c49a45]">Building Lives.</span>
        </h1>
        <p class="text-xl text-slate-300 max-w-3xl mx-auto font-light leading-relaxed">
            Bridge Ministries International is a Christ-centred church family in Accra, Ghana, committed to one calling: Glorify God, Grow disciples, and Go on mission.
        </p>
    </div>
</section>

<!-- OUR STORY & FOUNDER -->
<section class="py-24 md:py-32 bg-white relative overflow-hidden border-t border-slate-200">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            
            <!-- Biography Content -->
            <div class="relative order-2 lg:order-1">
                <div class="inline-flex items-center gap-4 mb-6">
                    <div class="h-px w-12 bg-[#c49a45]"></div>
                    <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Our History</span>
                </div>
                
                <h2 class="text-4xl md:text-5xl font-display font-bold text-slate-900 mb-8 leading-tight">
                    From a small fellowship <br>to a global movement.
                </h2>
                
                <div class="text-slate-600 font-medium leading-relaxed space-y-6 text-lg">
                    <p>
                        <?= setting('about.history_text1', 'Bridge Ministries International began in ' . $founded . ' as a small group of believers gathering with a single conviction: that the local church should be a bridge — between God and people, between generations, and between the church and the community it serves.') ?>
                    </p>
                    <p>
                        <?= setting('about.history_text2', 'Under the leadership of Rev. Francis Duane Yalley, that conviction has grown into a thriving movement of cell-based ministries and congregations reaching thousands of believers across Ghana and beyond.') ?>
                    </p>
                    <p>
                        <?= setting('about.vision_text', 'Today we remain anchored in the same simple commitments that defined our first gatherings: faithful preaching of the Word, strategic prayer, intentional discipleship, and a love for the city.') ?>
                    </p>
                </div>
            </div>

            <!-- Founder Portrait -->
            <div class="relative group order-1 lg:order-2">
                <div class="absolute inset-0 bg-[#c49a45] blur-[6.25rem] opacity-10 group-hover:opacity-20 transition-opacity duration-700"></div>
                <div class="relative w-full aspect-[4/5] overflow-hidden border-4 border-white shadow-xl bg-slate-100 flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-transparent to-transparent z-10"></div>
                    <img src="<?= setting('about.founder_image', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=800&auto=format&fit=crop') ?>" alt="Rev. Francis Duane Yalley" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                    
                    <div class="absolute bottom-10 left-10 z-20">
                        <div class="inline-flex items-center gap-3 px-4 py-2 bg-white/90 backdrop-blur-md border border-slate-200 mb-4 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-[#c49a45]"></span>
                            <span class="text-slate-900 text-xs font-bold tracking-widest uppercase">General Overseer</span>
                        </div>
                        <h3 class="text-4xl font-display font-black text-white leading-tight"><?= setting('about.founder_name', 'Rev. F.D.<br>Yalley') ?></h3>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- CORE VALUES -->
<section class="py-24 md:py-32 bg-slate-50 relative overflow-hidden border-t border-slate-200">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="text-center mb-16 md:mb-20">
            <div class="inline-flex items-center justify-center gap-4 mb-4">
                <div class="h-px w-12 bg-[#c49a45]"></div>
                <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Our Values</span>
                <div class="h-px w-12 bg-[#c49a45]"></div>
            </div>
            <h2 class="text-4xl md:text-5xl lg:text-6xl text-slate-900 font-display font-bold">What we hold tightly</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Value 1 -->
            <div class="bg-white p-8 border border-slate-200 hover:border-[#c49a45] transition-all duration-300 shadow-sm hover:shadow-[0_8px_30px_rgba(196,154,69,0.15)] group">
                <div class="text-5xl font-display font-black text-slate-200 mb-6 group-hover:text-[#c49a45] transition-colors">01</div>
                <h3 class="text-2xl font-bold text-slate-900 mb-3">Scripture, Above All</h3>
                <p class="text-slate-600 leading-relaxed">The Bible is our final authority for faith and practice. We teach it, trust it, and live by it without compromise.</p>
            </div>
            
            <!-- Value 2 -->
            <div class="bg-white p-8 border border-slate-200 hover:border-[#c49a45] transition-all duration-300 shadow-sm hover:shadow-[0_8px_30px_rgba(196,154,69,0.15)] group">
                <div class="text-5xl font-display font-black text-slate-200 mb-6 group-hover:text-[#c49a45] transition-colors">02</div>
                <h3 class="text-2xl font-bold text-slate-900 mb-3">Prayer, Without Ceasing</h3>
                <p class="text-slate-600 leading-relaxed">Prayer is not a programme — it is the engine of everything we do, both privately and corporately as a church.</p>
            </div>

            <!-- Value 3 -->
            <div class="bg-white p-8 border border-slate-200 hover:border-[#c49a45] transition-all duration-300 shadow-sm hover:shadow-[0_8px_30px_rgba(196,154,69,0.15)] group">
                <div class="text-5xl font-display font-black text-slate-200 mb-6 group-hover:text-[#c49a45] transition-colors">03</div>
                <h3 class="text-2xl font-bold text-slate-900 mb-3">Discipleship, On Purpose</h3>
                <p class="text-slate-600 leading-relaxed">We don't just gather crowds; we build disciples who follow Jesus and form others to do exactly the same.</p>
            </div>

            <!-- Value 4 -->
            <div class="bg-white p-8 border border-slate-200 hover:border-[#c49a45] transition-all duration-300 shadow-sm hover:shadow-[0_8px_30px_rgba(196,154,69,0.15)] group">
                <div class="text-5xl font-display font-black text-slate-200 mb-6 group-hover:text-[#c49a45] transition-colors">04</div>
                <h3 class="text-2xl font-bold text-slate-900 mb-3">Family, Across Generations</h3>
                <p class="text-slate-600 leading-relaxed">From children to elders, every age group has a seat at the table and a powerful voice in the body of Christ.</p>
            </div>

            <!-- Value 5 -->
            <div class="bg-white p-8 border border-slate-200 hover:border-[#c49a45] transition-all duration-300 shadow-sm hover:shadow-[0_8px_30px_rgba(196,154,69,0.15)] group">
                <div class="text-5xl font-display font-black text-slate-200 mb-6 group-hover:text-[#c49a45] transition-colors">05</div>
                <h3 class="text-2xl font-bold text-slate-900 mb-3">Mission, Beyond Walls</h3>
                <p class="text-slate-600 leading-relaxed">We are sent — to our neighbours, our city, and the nations — with the love, compassion, and message of Jesus.</p>
            </div>

            <!-- Value 6 -->
            <div class="bg-white p-8 border border-slate-200 hover:border-[#c49a45] transition-all duration-300 shadow-sm hover:shadow-[0_8px_30px_rgba(196,154,69,0.15)] group">
                <div class="text-5xl font-display font-black text-slate-200 mb-6 group-hover:text-[#c49a45] transition-colors">06</div>
                <h3 class="text-2xl font-bold text-slate-900 mb-3">Integrity, At All Costs</h3>
                <p class="text-slate-600 leading-relaxed">We pursue financial transparency, ethical leadership, and Christ-like character in private and in public.</p>
            </div>
        </div>
    </div>
</section>

<!-- LEADERSHIP -->
<section class="py-24 md:py-32 bg-white relative overflow-hidden border-t border-slate-200">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="text-center mb-16 md:mb-20">
            <div class="inline-flex items-center justify-center gap-4 mb-4">
                <div class="h-px w-12 bg-[#c49a45]"></div>
                <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Leadership</span>
                <div class="h-px w-12 bg-[#c49a45]"></div>
            </div>
            <h2 class="text-4xl md:text-5xl lg:text-6xl text-slate-900 font-display font-bold">Meet Our Leaders</h2>
            <p class="mt-6 text-slate-600 text-lg max-w-2xl mx-auto">A team of pastors, elders, and ministry leaders shepherding the church together.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <!-- Leader 1 -->
            <div class="group bg-slate-50 border border-slate-200 p-8 text-center hover:border-[#c49a45] transition-all duration-300">
                <div class="w-40 h-40 mx-auto mb-6 rounded-full overflow-hidden border-4 border-slate-200 group-hover:border-[#c49a45] transition-colors">
                    <img src="assets/image/IMG_1061.jpg" alt="Rev. Francis Duane Yalley" class="w-full h-full object-cover" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=800&auto=format&fit=crop';">
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-1">Rev. F.D. Yalley</h3>
                <p class="text-[#c49a45] text-xs font-bold uppercase tracking-widest mb-4">General Overseer</p>
                <p class="text-slate-600 text-sm leading-relaxed">A teacher of the Word with a passion for biblical truth, strategic prayer, and raising mature disciples who influence their communities.</p>
            </div>

            <!-- Leader 2 -->
            <div class="group bg-slate-50 border border-slate-200 p-8 text-center hover:border-[#c49a45] transition-all duration-300">
                <div class="w-40 h-40 mx-auto mb-6 rounded-full overflow-hidden border-4 border-slate-200 group-hover:border-[#c49a45] transition-colors bg-white flex items-center justify-center">
                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-1">Pastoral Team</h3>
                <p class="text-[#c49a45] text-xs font-bold uppercase tracking-widest mb-4">Associate Pastors</p>
                <p class="text-slate-600 text-sm leading-relaxed">A faithful team of associate pastors serving alongside the General Overseer in teaching, counselling, and shepherding.</p>
            </div>

            <!-- Leader 3 -->
            <div class="group bg-slate-50 border border-slate-200 p-8 text-center hover:border-[#c49a45] transition-all duration-300">
                <div class="w-40 h-40 mx-auto mb-6 rounded-full overflow-hidden border-4 border-slate-200 group-hover:border-[#c49a45] transition-colors bg-white flex items-center justify-center">
                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-1">Ministry Leaders</h3>
                <p class="text-[#c49a45] text-xs font-bold uppercase tracking-widest mb-4">Elders & Coordinators</p>
                <p class="text-slate-600 text-sm leading-relaxed">Trusted men and women leading every ministry — from worship and discipleship to outreach, youth, and missions.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA GRID -->
<section class="py-24 bg-slate-900 relative overflow-hidden border-t border-slate-800">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-slate-950 p-10 border border-slate-800 flex flex-col items-start text-left group hover:border-[#c49a45] transition-colors">
                <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-[#c49a45] transition-colors">What We Believe</h3>
                <p class="text-slate-400 leading-relaxed mb-8 flex-grow">Read our full statement of faith — what we hold to be true about God, Scripture, and salvation.</p>
                <a href="beliefs" class="inline-flex items-center text-[#c49a45] font-bold uppercase tracking-widest text-sm hover:text-white transition-colors">
                    Our Beliefs <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
            <div class="bg-slate-950 p-10 border border-slate-800 flex flex-col items-start text-left group hover:border-[#c49a45] transition-colors">
                <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-[#c49a45] transition-colors">Find Your Community</h3>
                <p class="text-slate-400 leading-relaxed mb-8 flex-grow">From cell groups to age-based ministries, there is a place for you to belong, serve, and grow.</p>
                <a href="ministries" class="inline-flex items-center text-[#c49a45] font-bold uppercase tracking-widest text-sm hover:text-white transition-colors">
                    Explore Ministries <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
            <div class="bg-slate-950 p-10 border border-slate-800 flex flex-col items-start text-left group hover:border-[#c49a45] transition-colors">
                <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-[#c49a45] transition-colors">Need Prayer?</h3>
                <p class="text-slate-400 leading-relaxed mb-8 flex-grow">Whatever you are facing, we'd be honoured to stand with you in prayer.</p>
                <a href="contact" class="inline-flex items-center text-[#c49a45] font-bold uppercase tracking-widest text-sm hover:text-white transition-colors">
                    Send a Request <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
