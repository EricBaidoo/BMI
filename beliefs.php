<?php
$pageTitle = 'What We Believe | Bridge Ministries International';
$pageDescription = 'Our statement of faith — what Bridge Ministries International believes about Scripture, God, salvation, the church, and the Christian life.';

require_once __DIR__ . '/includes/helpers.php';
include 'includes/header.php';
?>
<!-- HERO SECTION -->
<section class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#000000] overflow-hidden gs-reveal-section">
    <div class="absolute inset-0 z-0">
        <img loading="lazy" src="<?= setting('beliefs.hero_bg_image', 'https://images.unsplash.com/photo-1438283173091-5dbf5c5a3206?q=80&w=1200&auto=format&fit=crop') ?>" alt="Beliefs Background" class="w-full h-full object-cover opacity-20 ">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/90 via-slate-900/80 to-slate-900"></div>
    </div>
    
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10 text-center">
        <div class="inline-flex items-center gap-4 mb-6">
            <div class="h-px w-12 bg-[#000000]"></div>
            <span class="text-white/60 font-bold text-sm tracking-widest uppercase">Our Beliefs</span>
            <div class="h-px w-12 bg-[#000000]"></div>
        </div>
        <h1 class="text-4xl md:text-7xl font-display font-black text-white mb-6 tracking-normal leading-tight">
            <?= setting('beliefs.hero_title', 'What We <br/><span class="text-white/60">Believe.</span>') ?>
        </h1>
        <p class="text-xl text-white/30 max-w-3xl mx-auto font-light leading-relaxed">
            <?= setting('beliefs.intro_text', 'We are a Bible-believing, Christ-centred church standing in the historic stream of evangelical Christian faith. What follows is a summary of the core convictions that shape our preaching, our gatherings, and our life together.') ?>
        </p>
    </div>
</section>

<section class="py-24 md:py-32 bg-[#F5F5F5] relative overflow-hidden text-black gs-reveal-section">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        
        <div class="max-w-4xl mx-auto text-center mb-20 gs-reveal-up">
            <h2 class="text-3xl md:text-5xl font-display font-black uppercase text-black mb-6 tracking-normal">A Note Before You Read</h2>
            <p class="text-lg md:text-xl text-black/60 font-sans font-medium leading-relaxed">
                <?= setting('beliefs.note_text', 'We don\'t see this statement as the last word — only the Bible is. Rather, we see it as a faithful summary of what we believe the Scriptures teach. We hold these truths with conviction, teach them with clarity, and welcome honest questions from anyone exploring faith.') ?>
            </p>
        </div>

        <!-- BENTO GRID FOR BELIEFS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 auto-rows-min">
            
            <!-- Bento Item 01 (Large) -->
            <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] p-10 md:p-14 text-white shadow-2xl gs-reveal-up hover:border-amber-500/30 transition-all duration-500 lg:col-span-2 group">
                <div class="flex items-start justify-between mb-8">
                    <span class="text-amber-500 font-display font-black text-6xl leading-none opacity-50 group-hover:opacity-100 transition-opacity">01</span>
                    <svg class="w-8 h-8 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black uppercase tracking-normal mb-4">The Bible</h3>
                <p class="text-lg text-white/60 font-sans font-medium leading-relaxed mb-6">
                    We believe the Bible — the sixty-six books of the Old and New Testaments — is the inspired, inerrant, and authoritative Word of God. It is the supreme and final standard for what we believe and how we live.
                </p>
                <div class="inline-flex bg-white/5 rounded-full px-4 py-2 border border-white/10">
                    <span class="text-xs font-bold text-white/50 tracking-widest uppercase">2 Timothy 3:16-17 · 2 Peter 1:20-21 · Psalm 119:105</span>
                </div>
            </div>

            <!-- Bento Item 02 -->
            <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] p-10 text-white shadow-2xl gs-reveal-up hover:border-amber-500/30 transition-all duration-500 group">
                <span class="text-amber-500 font-display font-black text-6xl leading-none opacity-50 block mb-8 group-hover:opacity-100 transition-opacity">02</span>
                <h3 class="text-2xl font-display font-black uppercase tracking-normal mb-4">God</h3>
                <p class="text-base text-white/60 font-sans font-medium leading-relaxed mb-6">
                    We believe in one true and living God who exists eternally in three persons — Father, Son, and Holy Spirit. He is the Creator, Sustainer, and Sovereign over all things.
                </p>
                <div class="inline-flex bg-white/5 rounded-xl px-4 py-2 border border-white/10 w-full text-center">
                    <span class="text-[0.65rem] font-bold text-white/50 tracking-widest uppercase w-full">Deut 6:4 · Matt 28:19</span>
                </div>
            </div>

            <!-- Bento Item 03 -->
            <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] p-10 text-white shadow-2xl gs-reveal-up hover:border-amber-500/30 transition-all duration-500 group">
                <span class="text-amber-500 font-display font-black text-6xl leading-none opacity-50 block mb-8 group-hover:opacity-100 transition-opacity">03</span>
                <h3 class="text-2xl font-display font-black uppercase tracking-normal mb-4">Jesus Christ</h3>
                <p class="text-base text-white/60 font-sans font-medium leading-relaxed mb-6">
                    We believe Jesus Christ is fully God and fully man. He was crucified for our sins, was buried and bodily raised from the dead, and will return in glory.
                </p>
                <div class="inline-flex bg-white/5 rounded-xl px-4 py-2 border border-white/10 w-full text-center">
                    <span class="text-[0.65rem] font-bold text-white/50 tracking-widest uppercase w-full">John 1:1 · Phil 2:5-11</span>
                </div>
            </div>

            <!-- Bento Item 04 (Large) -->
            <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] p-10 md:p-14 text-white shadow-2xl gs-reveal-up hover:border-amber-500/30 transition-all duration-500 lg:col-span-2 group">
                <div class="flex items-start justify-between mb-8">
                    <span class="text-amber-500 font-display font-black text-6xl leading-none opacity-50 group-hover:opacity-100 transition-opacity">04</span>
                </div>
                <h3 class="text-3xl font-display font-black uppercase tracking-normal mb-4">The Holy Spirit</h3>
                <p class="text-lg text-white/60 font-sans font-medium leading-relaxed mb-6">
                    We believe the Holy Spirit is God — equal with the Father and the Son. He convicts the world of sin, regenerates those who believe, indwells every Christian, empowers us for holy living, and gifts the church for ministry.
                </p>
                <div class="inline-flex bg-white/5 rounded-full px-4 py-2 border border-white/10">
                    <span class="text-xs font-bold text-white/50 tracking-widest uppercase">John 14:16-17 · Acts 1:8 · 1 Cor 12:4-11</span>
                </div>
            </div>

            <!-- Bento Item 05 -->
            <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] p-10 text-white shadow-2xl gs-reveal-up hover:border-amber-500/30 transition-all duration-500 group">
                <span class="text-amber-500 font-display font-black text-6xl leading-none opacity-50 block mb-8 group-hover:opacity-100 transition-opacity">05</span>
                <h3 class="text-2xl font-display font-black uppercase tracking-normal mb-4">Humanity & Sin</h3>
                <p class="text-base text-white/60 font-sans font-medium leading-relaxed mb-6">
                    Every person is created in the image of God. Yet because of Adam's fall, all humanity is born into sin and stands in need of God's saving grace.
                </p>
                <div class="inline-flex bg-white/5 rounded-xl px-4 py-2 border border-white/10 w-full text-center">
                    <span class="text-[0.65rem] font-bold text-white/50 tracking-widest uppercase w-full">Gen 1:27 · Rom 3:23</span>
                </div>
            </div>
            
            <!-- Bento Item 06 (Large) -->
            <div class="bg-black border border-amber-500/20 rounded-[2.5rem] p-10 md:p-14 text-white shadow-2xl gs-reveal-up hover:border-amber-500/50 transition-all duration-500 lg:col-span-3 group relative overflow-hidden">
                <div class="absolute -top-32 -right-32 w-96 h-96 bg-amber-500/10 blur-[5rem] rounded-full"></div>
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row gap-8 items-start md:items-center justify-between">
                        <div class="flex-grow">
                            <div class="flex items-center gap-6 mb-6">
                                <span class="text-amber-500 font-display font-black text-6xl leading-none">06</span>
                                <h3 class="text-4xl font-display font-black uppercase tracking-normal">Salvation</h3>
                            </div>
                            <p class="text-xl text-white/80 font-sans font-medium leading-relaxed max-w-4xl mb-6">
                                We believe salvation is by grace alone, through faith alone, in Jesus Christ alone. It is a free gift from God, not earned by good works. Through repentance and faith in Christ's finished work, we are forgiven, justified, adopted as God's children, and given eternal life.
                            </p>
                            <div class="inline-flex bg-white/5 rounded-full px-4 py-2 border border-white/10">
                                <span class="text-xs font-bold text-amber-500/80 tracking-widest uppercase">Ephesians 2:8-9 · Romans 10:9-10 · John 3:16</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bento Item 07 -->
            <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] p-10 text-white shadow-2xl gs-reveal-up hover:border-amber-500/30 transition-all duration-500 group lg:col-span-2">
                <span class="text-amber-500 font-display font-black text-6xl leading-none opacity-50 block mb-8 group-hover:opacity-100 transition-opacity">07</span>
                <h3 class="text-3xl font-display font-black uppercase tracking-normal mb-4">The Church</h3>
                <p class="text-lg text-white/60 font-sans font-medium leading-relaxed mb-6">
                    We believe the Church is the body of Christ — composed of all true believers, expressed locally in gathered congregations. Every Christian is called to be an active part of a healthy local church.
                </p>
                <div class="inline-flex bg-white/5 rounded-full px-4 py-2 border border-white/10">
                    <span class="text-[0.65rem] font-bold text-white/50 tracking-widest uppercase">1 Cor 12:12-27 · Heb 10:24-25</span>
                </div>
            </div>

            <!-- Bento Item 08 -->
            <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] p-10 text-white shadow-2xl gs-reveal-up hover:border-amber-500/30 transition-all duration-500 group">
                <span class="text-amber-500 font-display font-black text-6xl leading-none opacity-50 block mb-8 group-hover:opacity-100 transition-opacity">08</span>
                <h3 class="text-2xl font-display font-black uppercase tracking-normal mb-4">Baptism & Communion</h3>
                <p class="text-base text-white/60 font-sans font-medium leading-relaxed mb-6">
                    We practise two ordinances given by Jesus: water baptism and the Lord's Supper (Communion).
                </p>
                <div class="inline-flex bg-white/5 rounded-xl px-4 py-2 border border-white/10 w-full text-center">
                    <span class="text-[0.65rem] font-bold text-white/50 tracking-widest uppercase w-full">Matt 28:19 · 1 Cor 11:23</span>
                </div>
            </div>

            <!-- Bento Item 09 -->
            <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] p-10 text-white shadow-2xl gs-reveal-up hover:border-amber-500/30 transition-all duration-500 group">
                <span class="text-amber-500 font-display font-black text-6xl leading-none opacity-50 block mb-8 group-hover:opacity-100 transition-opacity">09</span>
                <h3 class="text-2xl font-display font-black uppercase tracking-normal mb-4">The Christian Life</h3>
                <p class="text-base text-white/60 font-sans font-medium leading-relaxed mb-6">
                    Every Christian is called to a life of growing holiness, daily prayer, faithful service, and bold witness in the world.
                </p>
                <div class="inline-flex bg-white/5 rounded-xl px-4 py-2 border border-white/10 w-full text-center">
                    <span class="text-[0.65rem] font-bold text-white/50 tracking-widest uppercase w-full">Rom 12:1-2 · Gal 5:22-25</span>
                </div>
            </div>

            <!-- Bento Item 10 -->
            <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] p-10 text-white shadow-2xl gs-reveal-up hover:border-amber-500/30 transition-all duration-500 group lg:col-span-2">
                <span class="text-amber-500 font-display font-black text-6xl leading-none opacity-50 block mb-8 group-hover:opacity-100 transition-opacity">10</span>
                <h3 class="text-3xl font-display font-black uppercase tracking-normal mb-4">The Return of Christ & Eternity</h3>
                <p class="text-lg text-white/60 font-sans font-medium leading-relaxed mb-6">
                    We believe Jesus Christ will return personally, visibly, and gloriously to judge the living and the dead and to establish His everlasting kingdom. This hope shapes how we live today.
                </p>
                <div class="inline-flex bg-white/5 rounded-full px-4 py-2 border border-white/10">
                    <span class="text-[0.65rem] font-bold text-white/50 tracking-widest uppercase">1 Thess 4:13-18 · Rev 21:1-5</span>
                </div>
            </div>
            
        </div>

        <!-- CTA -->
        <div class="mt-20 bg-black border border-white/10 rounded-[3rem] p-12 md:p-20 flex flex-col md:flex-row md:items-center justify-between gap-10 gs-reveal-up shadow-2xl text-white text-center md:text-left">
            <div>
                <h2 class="text-4xl font-display font-black uppercase tracking-normal mb-3">Have Questions?</h2>
                <p class="text-xl text-white/60 font-sans font-medium">Our pastors would love to talk with you — whatever you believe right now.</p>
            </div>
            <div class="flex-shrink-0">
                <a href="contact" class="inline-flex items-center justify-center bg-white text-black hover:bg-neutral-200 px-10 py-5 font-sans font-bold uppercase tracking-widest text-sm rounded-xl transition-all hover:-translate-y-1">
                    Talk to a Pastor
                </a>
            </div>
        </div>
        
    </div>
</section>
<?php include 'includes/footer.php'; ?>

