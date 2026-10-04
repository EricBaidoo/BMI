<?php
$pageTitle = 'Plan a Visit | Bridge Ministries International';
$pageDescription = 'Join us this Sunday at Bridge Ministries International. Find service times, location, and what to expect.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/csrf.php';

// Handle Plan a Visit submissions: save to the admin Inbox and notify the welcome team.
$visitSuccess = '';
$visitError = '';
$visitOld = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $visitOld = array_map(fn ($v) => is_string($v) ? trim($v) : '', $_POST);
    $firstName = mb_substr($visitOld['first_name'] ?? '', 0, 60);
    $lastName = mb_substr($visitOld['last_name'] ?? '', 0, 60);
    $email = filter_var($visitOld['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $phone = mb_substr(preg_replace('/[^0-9+()\-\s]/', '', $visitOld['phone'] ?? ''), 0, 30);
    $date = $visitOld['date'] ?? '';
    $kids = ($visitOld['kids'] ?? '') === 'yes' ? 'Yes' : 'No';
    $validDate = DateTime::createFromFormat('Y-m-d', $date);

    if (($visitOld['website'] ?? '') !== '') {
        // Honeypot field filled in: treat as spam but respond normally.
        $visitSuccess = 'Thank you. We look forward to welcoming you.';
    } elseif ($firstName === '' || $lastName === '' || !$email || !$validDate) {
        $visitError = 'Please enter your name, a valid email address and the date you plan to visit.';
    } else {
        $fullName = $firstName . ' ' . $lastName;
        $visitDate = $validDate->format('l, j F Y');
        $details = "Planned visit: {$visitDate}\nBringing children: {$kids}\nPhone: " . ($phone !== '' ? $phone : 'not given');
        try {
            $stmt = db_connect()->prepare('INSERT INTO messages (full_name, email, subject, message, type) VALUES (:name, :email, :subject, :msg, :type)');
            $stmt->execute([
                ':name' => $fullName,
                ':email' => $email,
                ':subject' => 'Plan a Visit: ' . $visitDate,
                ':msg' => $details,
                ':type' => 'visit',
            ]);

            $teamEmail = setting('contact.email_general', 'info@bmiglobal.org');
            $fromHost = parse_url(setting('site.url', 'https://bmiglobal.org'), PHP_URL_HOST) ?: 'bmiglobal.org';
            $body = "Someone is planning to visit.\n\nName: {$fullName}\nEmail: {$email}\n{$details}\n\nLog in to the admin panel to view all messages.";
            @mail($teamEmail, 'New visit planned: ' . $fullName, $body, "From: no-reply@{$fromHost}\r\nReply-To: {$email}\r\n");

            $visitSuccess = "Thank you, {$firstName}. We look forward to welcoming you on {$visitDate}. Our welcome team will be in touch before your visit.";
            $visitOld = [];
        } catch (Throwable $e) {
            log_exception($e, 'visit');
            $visitError = 'Sorry, we could not save your details. Please try again or contact us directly.';
        }
    }
}

include 'includes/header.php';
?>

<!-- CUSTOM ANIMATION STYLES -->
<!-- HERO SECTION -->
<div class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#030303] overflow-hidden min-h-[50vh] flex items-center justify-center">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <?php 
            $visitHeroBg = setting('visit.hero_bg_image', 'assets/image/uncategorized/IMG_4059.JPG');
            $is_video = preg_match('/\.(mp4|webm)$/i', $visitHeroBg);
        ?>
        <?php if ($is_video): ?>
            <video data-src="<?= htmlspecialchars(safe_url($visitHeroBg)) ?>" class="bg-video w-full h-full object-cover opacity-20 mix-blend-luminosity grayscale" loop muted playsinline preload="none" aria-hidden="true"></video>
        <?php else: ?>
            <img fetchpriority="high" src="<?= htmlspecialchars(safe_url($visitHeroBg)) ?>" alt="" class="w-full h-full object-cover opacity-20 mix-blend-luminosity grayscale" onerror="this.src='https://images.unsplash.com/photo-1543332143-4e8c27e3256f?q=80&w=1200&auto=format&fit=crop';">
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-b from-[#030303]/90 via-[#030303]/80 to-[#0a0a0c]"></div>
    </div>
    
    <!-- Abstract Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[50rem] h-[50rem] bg-amber-500/10 blur-[120px] rounded-full mix-blend-screen pointer-events-none"></div>

    <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto text-center gs-reveal-up">
        <div class="inline-flex items-center gap-6 mb-8">
            <div class="h-px w-16 bg-white/20"></div>
            <span class="text-white/50 font-sans font-bold text-[0.625rem] tracking-[0.4em] uppercase">You Belong Here</span>
            <div class="h-px w-16 bg-white/20"></div>
        </div>
        <h1 class="text-5xl md:text-7xl lg:text-9xl font-display font-black text-transparent bg-clip-text bg-gradient-to-br from-white via-neutral-300 to-neutral-600 mb-6 tracking-normal uppercase leading-[0.9]">
            <?= setting_html('visit.hero_title', 'Plan a <span class="italic font-light">Visit</span>') ?>
        </h1>
        <p class="text-xl md:text-2xl text-neutral-400 max-w-3xl mx-auto font-medium leading-relaxed mb-12">
            <?= setting_html('visit.hero_subtitle', 'We can\'t wait to welcome you to our family. Experience powerful worship, transforming truth, and genuine community.') ?>
        </p>
    </div>
</div>

<!-- WHEN & WHERE SECTION -->
<div class="py-24 md:py-32 bg-[#0a0a0c] text-white relative overflow-hidden">
    <!-- Ambient Glows -->
    <div class="absolute top-0 right-0 w-[50rem] h-[50rem] bg-indigo-600/5 blur-[150px] rounded-full mix-blend-screen pointer-events-none z-0"></div>

    <div class="w-[95%] max-w-[112.5rem] mx-auto relative z-10">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 xl:gap-24 items-center">
            <!-- Text Content -->
            <div class="gs-reveal-right">
                <h2 class="text-4xl md:text-5xl lg:text-7xl font-display font-black text-white uppercase tracking-normal mb-12 leading-[1.0]">When & <i class="text-amber-500 font-light">Where</i></h2>
                
                <div class="space-y-12">
                    <!-- Service Times -->
                    <div class="flex gap-6 group">
                        <div class="w-16 h-16 rounded-[1.5rem] bg-[#050505] border border-white/10 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-500 group-hover:text-black group-hover:border-amber-400 transition-all duration-300 shadow-xl">
                            <svg class="w-6 h-6 text-amber-500 group-hover:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-display font-black uppercase text-white mb-4 tracking-normal group-hover:text-amber-500 transition-colors">Service Times</h3>
                            <ul class="space-y-4 text-neutral-400 font-medium">
                                <?php foreach (service_times() as $label => $time): ?>
                                <li class="flex flex-wrap items-center gap-x-4 gap-y-1 bg-[#050505] border border-white/5 rounded-2xl p-4 hover:border-white/20 transition-colors">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.5)]" aria-hidden="true"></span>
                                    <span class="text-white"><?php echo htmlspecialchars($label); ?></span>
                                    <span class="ml-auto text-amber-500 font-bold"><?php echo htmlspecialchars($time); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (setting('service.notes') !== ''): ?>
                                <p class="mt-4 text-sm text-neutral-400"><?php echo htmlspecialchars(setting('service.notes')); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="flex gap-6 group">
                        <div class="w-16 h-16 rounded-[1.5rem] bg-[#050505] border border-white/10 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-500 group-hover:text-black group-hover:border-amber-400 transition-all duration-300 shadow-xl">
                            <svg class="w-6 h-6 text-amber-500 group-hover:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-display font-black uppercase text-white mb-4 tracking-normal group-hover:text-amber-500 transition-colors">Location</h3>
                            <div class="bg-[#050505] border border-white/5 rounded-2xl p-6">
                                <p class="text-neutral-400 font-medium leading-relaxed mb-6">
                                    <?php echo htmlspecialchars(setting('contact.address')); ?>
                                </p>
                                <a href="https://maps.google.com/?q=<?php echo urlencode(setting('contact.address')); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center font-bold font-sans uppercase tracking-[0.2em] text-xs text-white bg-white/5 border border-white/10 px-6 py-4 rounded-full hover:bg-white hover:text-black transition-all duration-300 group/btn">
                                    Get Directions
                                    <svg class="w-4 h-4 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image/Map Container -->
            <div class="relative overflow-hidden rounded-[3rem] shadow-2xl gs-reveal-left group border border-white/10 h-full min-h-[400px]">
                <img loading="lazy" src="<?= setting_url('visit.church_image', 'assets/image/uncategorized/IMG_3156.JPG') ?>" alt="Our welcome team ready to greet you" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105 opacity-80 group-hover:opacity-100" onerror="this.src='https://images.unsplash.com/photo-1438032005730-c779502df39b?q=80&w=1000&auto=format&fit=crop';">
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent opacity-80"></div>
                <div class="absolute bottom-10 left-10 right-10">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 flex items-center justify-between">
                        <div>
                            <p class="text-white font-display font-black text-2xl uppercase tracking-normal">Main Campus</p>
                            <p class="text-white/60 text-sm font-medium">Join us this Sunday</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-amber-500 text-black flex items-center justify-center shadow-[0_0_20px_rgba(245,158,11,0.5)]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- WHAT TO EXPECT SECTION -->
<div class="py-32 bg-[#050505] relative overflow-hidden">
    <!-- Grid Overlay -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LCAyNTUsIDI1NSwgMC4wNSkiLz48L3N2Zz4=')] opacity-30 z-0"></div>

    <div class="w-[95%] max-w-[112.5rem] mx-auto relative z-10">
        
        <div class="text-center max-w-3xl mx-auto mb-20 gs-reveal-up">
            <h2 class="text-4xl md:text-5xl lg:text-7xl font-display font-black text-white uppercase tracking-normal mb-6 leading-[1.0]">What to <i class="text-amber-500 font-light">Expect</i></h2>
            <p class="text-lg text-neutral-400 font-medium leading-relaxed">
                <?= setting_html('visit.expect_text', 'Visiting a new church can be intimidating, but we want you to feel right at home. Here is a brief look at what our services are like.') ?>
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Item 1 -->
            <div class="bg-white/[0.02] backdrop-blur-3xl p-10 lg:p-12 rounded-[2.5rem] border border-white/5 shadow-2xl hover:border-amber-500/30 transition-all duration-700 hover:-translate-y-2 gs-reveal-up group relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-t from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                
                <div class="w-20 h-20 rounded-[1.5rem] bg-black border border-white/10 text-white/50 flex items-center justify-center mb-8 group-hover:bg-amber-500 group-hover:text-black group-hover:border-amber-400 transition-all duration-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black uppercase text-white mb-4 tracking-normal leading-none group-hover:text-amber-500 transition-colors">Passionate Worship</h3>
                <p class="text-neutral-400 font-medium leading-relaxed relative z-10">
                    Our services begin with dynamic, Spirit-led worship. We sing contemporary songs and hymns designed to exalt Jesus.
                </p>
            </div>
            
            <!-- Item 2 -->
            <div class="bg-white/[0.02] backdrop-blur-3xl p-10 lg:p-12 rounded-[2.5rem] border border-white/5 shadow-2xl hover:border-amber-500/30 transition-all duration-700 hover:-translate-y-2 gs-reveal-up delay-100 group relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-t from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                
                <div class="w-20 h-20 rounded-[1.5rem] bg-black border border-white/10 text-white/50 flex items-center justify-center mb-8 group-hover:bg-amber-500 group-hover:text-black group-hover:border-amber-400 transition-all duration-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black uppercase text-white mb-4 tracking-normal leading-none group-hover:text-amber-500 transition-colors">Biblical Teaching</h3>
                <p class="text-neutral-400 font-medium leading-relaxed relative z-10">
                    You will hear an engaging, uncompromising message based entirely on the Word of God that applies directly to your life.
                </p>
            </div>

            <!-- Item 3 -->
            <div class="bg-white/[0.02] backdrop-blur-3xl p-10 lg:p-12 rounded-[2.5rem] border border-white/5 shadow-2xl hover:border-amber-500/30 transition-all duration-700 hover:-translate-y-2 gs-reveal-up delay-200 group relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-t from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                
                <div class="w-20 h-20 rounded-[1.5rem] bg-black border border-white/10 text-white/50 flex items-center justify-center mb-8 group-hover:bg-amber-500 group-hover:text-black group-hover:border-amber-400 transition-all duration-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black uppercase text-white mb-4 tracking-normal leading-none group-hover:text-amber-500 transition-colors">BMI Kids</h3>
                <p class="text-neutral-400 font-medium leading-relaxed relative z-10">
                    We provide a safe, caring environment for children during the main Sunday service, so parents can worship with peace of mind.
                </p>
            </div>
        </div>

    </div>
</div>

<!-- PLAN A VISIT FORM -->
<div class="py-24 md:py-32 bg-[#0a0a0c] relative overflow-hidden" id="visit-form">
    <!-- Abstract Glow -->
    <div class="absolute top-1/2 right-0 translate-x-1/4 -translate-y-1/2 w-[50rem] h-[50rem] bg-amber-500/10 blur-[150px] rounded-full mix-blend-screen pointer-events-none z-0"></div>

    <div class="max-w-[112.5rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-[95%]">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 xl:gap-24 items-center">
            <div class="text-left gs-reveal-right">
                <h2 class="text-5xl md:text-6xl lg:text-8xl font-display font-black text-transparent bg-clip-text bg-gradient-to-br from-white to-white/40 uppercase tracking-normal mb-8 leading-[0.9]">Let Us Know <br><i class="text-amber-500 font-light">You're Coming!</i></h2>
                <p class="text-xl text-neutral-400 font-medium max-w-xl leading-relaxed">
                    Fill out the form below and our team will meet you at the door, show you around, and help get your kids checked in!
                </p>
                
                <div class="mt-12 flex items-center gap-6">
                    <img class="w-16 h-16 rounded-full border-2 border-[#0a0a0c] object-cover" src="<?= setting_url('visit.church_image', 'assets/image/uncategorized/IMG_3156.JPG') ?>" alt="" loading="lazy" decoding="async">
                    <p class="text-sm text-neutral-400 font-medium">Our welcome team <br>is ready for you.</p>
                </div>
            </div>

            <div class="bg-white/[0.02] backdrop-blur-3xl rounded-[3rem] p-10 md:p-14 shadow-[0_0_50px_rgba(0,0,0,0.5)] border border-white/10 gs-reveal-left relative">
                <!-- Inner Glow for Form -->
                <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none rounded-[3rem]"></div>
                
                <?php if ($visitSuccess !== ''): ?>
                <div id="plan-visit-form" role="status" class="relative z-10 text-center py-10">
                    <h3 class="text-2xl font-bold text-white mb-4">You're all set</h3>
                    <p class="text-neutral-300 leading-relaxed"><?= htmlspecialchars($visitSuccess, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <?php else: ?>
                <form id="plan-visit-form" action="visit#plan-visit-form" method="POST" class="space-y-6 relative z-10">
                    <?= csrf_field() ?>
                    <div class="hidden" aria-hidden="true"><label for="website">Leave this empty</label><input type="text" id="website" name="website" tabindex="-1" autocomplete="off"></div>
                    <?php if ($visitError !== ''): ?>
                    <p role="alert" class="rounded-2xl border border-red-500/30 bg-red-500/10 text-red-200 px-5 py-4 text-sm"><?= htmlspecialchars($visitError, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-xs font-bold uppercase tracking-[0.2em] text-white/60 mb-2">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($visitOld['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full bg-[#050505] border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all shadow-inner" required>
                        </div>
                        <div>
                            <label for="last_name" class="block text-xs font-bold uppercase tracking-[0.2em] text-white/60 mb-2">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($visitOld['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full bg-[#050505] border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all shadow-inner" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-[0.2em] text-white/60 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($visitOld['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full bg-[#050505] border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all shadow-inner" required>
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-[0.2em] text-white/60 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($visitOld['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full bg-[#050505] border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all shadow-inner">
                        </div>
                    </div>

                    <div>
                        <label for="date" class="block text-xs font-bold uppercase tracking-[0.2em] text-white/60 mb-2">When are you planning to visit?</label>
                        <input type="date" id="date" name="date" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($visitOld['date'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full bg-[#050505] border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all shadow-inner [color-scheme:dark]" required>
                    </div>

                    <div>
                        <label for="kids" class="block text-xs font-bold uppercase tracking-[0.2em] text-white/60 mb-2">Will you be bringing any children?</label>
                        <select id="kids" name="kids" class="w-full bg-[#050505] border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all shadow-inner appearance-none cursor-pointer">
                            <option value="no" class="bg-black">No children this time</option>
                            <option value="yes" class="bg-black"<?= ($visitOld['kids'] ?? '') === 'yes' ? ' selected' : '' ?>>Yes, I will bring my kids</option>
                        </select>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full bg-white text-black font-bold uppercase tracking-[0.2em] text-sm py-6 rounded-2xl hover:bg-amber-500 hover:-translate-y-1 transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_30px_rgba(245,158,11,0.3)]">
                            Plan My Visit
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</div>

<?php include 'includes/footer.php'; ?>

