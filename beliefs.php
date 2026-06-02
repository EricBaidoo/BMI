<?php
$pageTitle = 'What We Believe | Bridge Ministries International';
$pageDescription = 'Our statement of faith — what Bridge Ministries International believes about Scripture, God, salvation, the church, and the Christian life.';

require_once __DIR__ . '/includes/helpers.php';
include 'includes/header.php';
?>
<!-- HERO SECTION -->
<section class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-slate-900 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="<?= setting('beliefs.hero_bg_image', 'https://images.unsplash.com/photo-1438283173091-5dbf5c5a3206?q=80&w=1200&auto=format&fit=crop') ?>" alt="Beliefs Background" class="w-full h-full object-cover opacity-20 mix-blend-luminosity">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/90 via-slate-900/80 to-slate-900"></div>
    </div>
    
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10 text-center">
        <div class="inline-flex items-center gap-4 mb-6">
            <div class="h-px w-12 bg-[#c49a45]"></div>
            <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Our Beliefs</span>
            <div class="h-px w-12 bg-[#c49a45]"></div>
        </div>
        <h1 class="text-5xl md:text-7xl font-display font-black text-white mb-6 tracking-tight leading-tight">
            <?= setting('beliefs.hero_title', 'What We <br/><span class="text-[#c49a45]">Believe.</span>') ?>
        </h1>
        <p class="text-xl text-slate-300 max-w-3xl mx-auto font-light leading-relaxed">
            <?= setting('beliefs.intro_text', 'We are a Bible-believing, Christ-centred church standing in the historic stream of evangelical Christian faith. What follows is a summary of the core convictions that shape our preaching, our gatherings, and our life together.') ?>
        </p>
    </div>
</section>

<section class="max-w-4xl mx-auto px-4 py-12">

    <div class="section-card">
        <h2 class="text-2xl font-semibold">A Note Before You Read</h2>
        <p class="mt-3 text-sm muted-copy leading-relaxed">
            <?= setting('beliefs.note_text', 'We don\'t see this statement as the last word — only the Bible is. Rather, we see it as a faithful summary of what we believe the Scriptures teach. We hold these truths with conviction, teach them with clarity, and welcome honest questions from anyone exploring faith.') ?>
        </p>
    </div>

    <ol class="mt-8 space-y-6">

        <li class="section-card">
            <p class="text-sm font-semibold text-blue-700">01</p>
            <h3 class="text-xl font-semibold mt-1">The Bible</h3>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                We believe the Bible — the sixty-six books of the Old and New Testaments — is the inspired, inerrant, and authoritative
                Word of God. It is the supreme and final standard for what we believe and how we live.
            </p>
            <p class="text-xs muted-copy mt-3">2 Timothy 3:16-17 · 2 Peter 1:20-21 · Psalm 119:105</p>
        </li>

        <li class="section-card">
            <p class="text-sm font-semibold text-blue-700">02</p>
            <h3 class="text-xl font-semibold mt-1">God</h3>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                We believe in one true and living God who exists eternally in three persons — Father, Son, and Holy Spirit.
                He is the Creator, Sustainer, and Sovereign over all things, perfect in love, holiness, justice, and mercy.
            </p>
            <p class="text-xs muted-copy mt-3">Deuteronomy 6:4 · Matthew 28:19 · Genesis 1:1 · 1 John 4:8</p>
        </li>

        <li class="section-card">
            <p class="text-sm font-semibold text-blue-700">03</p>
            <h3 class="text-xl font-semibold mt-1">Jesus Christ</h3>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                We believe Jesus Christ is fully God and fully man — conceived by the Holy Spirit, born of the virgin Mary, lived a
                sinless life, taught with authority, performed miracles, was crucified for our sins, was buried and bodily raised
                from the dead, ascended to the right hand of the Father, and will return in glory.
            </p>
            <p class="text-xs muted-copy mt-3">John 1:1, 14 · Philippians 2:5-11 · 1 Corinthians 15:3-4 · Acts 1:11</p>
        </li>

        <li class="section-card">
            <p class="text-sm font-semibold text-blue-700">04</p>
            <h3 class="text-xl font-semibold mt-1">The Holy Spirit</h3>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                We believe the Holy Spirit is God — equal with the Father and the Son. He convicts the world of sin, regenerates
                those who believe, indwells every Christian, empowers us for holy living, and gifts the church for ministry.
            </p>
            <p class="text-xs muted-copy mt-3">John 14:16-17 · John 16:7-11 · Acts 1:8 · 1 Corinthians 12:4-11</p>
        </li>

        <li class="section-card">
            <p class="text-sm font-semibold text-blue-700">05</p>
            <h3 class="text-xl font-semibold mt-1">Humanity & Sin</h3>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                We believe every person is created in the image of God, with dignity and worth. Yet because of Adam's fall,
                all humanity is born into sin and stands in need of God's saving grace. We cannot save ourselves.
            </p>
            <p class="text-xs muted-copy mt-3">Genesis 1:27 · Romans 3:23 · Romans 5:12 · Ephesians 2:1-3</p>
        </li>

        <li class="section-card">
            <p class="text-sm font-semibold text-blue-700">06</p>
            <h3 class="text-xl font-semibold mt-1">Salvation</h3>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                We believe salvation is by grace alone, through faith alone, in Jesus Christ alone. It is a free gift from God,
                not earned by good works. Through repentance and faith in Christ's finished work, we are forgiven, justified,
                adopted as God's children, and given eternal life.
            </p>
            <p class="text-xs muted-copy mt-3">Ephesians 2:8-9 · Romans 10:9-10 · John 3:16 · Titus 3:5</p>
        </li>

        <li class="section-card">
            <p class="text-sm font-semibold text-blue-700">07</p>
            <h3 class="text-xl font-semibold mt-1">The Church</h3>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                We believe the Church is the body of Christ — composed of all true believers, expressed locally in gathered congregations.
                The local church exists to worship God, build up disciples, and bear witness to the world. Every Christian is called
                to be an active part of a healthy local church.
            </p>
            <p class="text-xs muted-copy mt-3">1 Corinthians 12:12-27 · Acts 2:42-47 · Hebrews 10:24-25</p>
        </li>

        <li class="section-card">
            <p class="text-sm font-semibold text-blue-700">08</p>
            <h3 class="text-xl font-semibold mt-1">Baptism & Communion</h3>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                We practise two ordinances given by Jesus to the church: <strong>water baptism</strong>, as a public confession of faith
                in Christ for those who have believed; and <strong>the Lord's Supper</strong> (Communion), as an ongoing remembrance of
                Christ's death and a proclamation of His coming return.
            </p>
            <p class="text-xs muted-copy mt-3">Matthew 28:19 · Acts 2:38 · 1 Corinthians 11:23-26</p>
        </li>

        <li class="section-card">
            <p class="text-sm font-semibold text-blue-700">09</p>
            <h3 class="text-xl font-semibold mt-1">The Christian Life</h3>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                We believe every Christian is called to a life of growing holiness, daily prayer and Scripture, faithful service in
                the local church, generous stewardship, and bold witness in the world. The Holy Spirit empowers us — we don't walk
                this journey alone.
            </p>
            <p class="text-xs muted-copy mt-3">Romans 12:1-2 · Galatians 5:22-25 · 2 Corinthians 5:17-20</p>
        </li>

        <li class="section-card">
            <p class="text-sm font-semibold text-blue-700">10</p>
            <h3 class="text-xl font-semibold mt-1">The Return of Christ & Eternity</h3>
            <p class="mt-3 text-sm muted-copy leading-relaxed">
                We believe Jesus Christ will return personally, visibly, and gloriously to judge the living and the dead and to
                establish His everlasting kingdom. Those who trust in Him will live forever in God's presence; those who reject
                Him will be eternally separated from Him. This hope shapes how we live today.
            </p>
            <p class="text-xs muted-copy mt-3">Acts 1:11 · 1 Thessalonians 4:13-18 · Revelation 21:1-5 · Matthew 25:31-46</p>
        </li>

    </ol>

    <div class="section-card mt-10 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">Have Questions?</h2>
            <p class="text-sm muted-copy mt-1">Our pastors would love to talk with you — whatever you believe right now.</p>
        </div>
        <a href="contact" class="primary-action">Talk to a Pastor</a>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
