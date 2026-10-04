</main>

<!-- CINEMATIC FOOTER (End Credits Style) -->
<?php if (!isset($hideFooter) || !$hideFooter): ?>
<footer class="relative bg-obsidian-950 text-white pt-32 pb-12 overflow-hidden mt-0 z-10 border-t border-white/5">
    <!-- Subtle radial glow from the bottom -->
    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[150%] h-[50rem] bg-[radial-gradient(ellipse_at_bottom,_var(--tw-gradient-stops))] from-accent/5 via-transparent to-transparent pointer-events-none z-0"></div>

    <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto text-center flex flex-col items-center">
        
        <h2 class="font-display font-black text-5xl md:text-7xl lg:text-[7rem] tracking-tight mb-12 text-white leading-[0.85] uppercase">
            Stay <i class="text-accent italic font-light">Connected.</i>
        </h2>
        
        <!-- Newsletter Block -->
        <?php
        $subscribeStatus = (string) ($_GET['subscribed'] ?? '');
        $subscribeMessages = [
            'ok' => ['Thank you for subscribing. You will hear from us soon.', 'text-emerald-400'],
            'invalid' => ['Please enter a valid email address.', 'text-red-400'],
            'limit' => ['Too many sign-ups from your connection. Please try again later.', 'text-red-400'],
            'error' => ['Sorry, we could not save your sign-up. Please try again.', 'text-red-400'],
        ];
        ?>
        <form id="newsletter" method="post" action="api/subscribe" class="flex flex-col sm:flex-row items-stretch w-full max-w-xl mx-auto gap-0 group mb-6 border-b border-white/20 pb-4 focus-within:border-accent transition-colors">
            <label for="newsletter-email" class="sr-only">Email address</label>
            <input type="email" id="newsletter-email" name="email" autocomplete="email" placeholder="Enter your email address" class="bg-transparent border-none text-white px-2 py-4 w-full focus:outline-none placeholder-white/30 font-sans tracking-widest-xl text-xs uppercase text-center sm:text-left transition-all" required>
            <div class="hidden" aria-hidden="true"><label for="newsletter-website">Leave empty</label><input type="text" id="newsletter-website" name="website" tabindex="-1" autocomplete="off"></div>
            <button type="submit" class="bg-transparent text-white/50 hover:text-accent px-8 py-4 font-sans font-bold text-[0.65rem] uppercase tracking-[0.3em] transition-colors whitespace-nowrap">
                Subscribe
            </button>
        </form>
        <p class="mb-24 min-h-[1.5rem] text-sm <?php echo $subscribeMessages[$subscribeStatus][1] ?? ''; ?>" role="status"><?php echo htmlspecialchars($subscribeMessages[$subscribeStatus][0] ?? ''); ?></p>

        <!-- Service times and location -->
        <?php $footerTimes = service_times(); ?>
        <?php if ($footerTimes || setting('contact.address') !== ''): ?>
        <div class="flex flex-wrap justify-center gap-x-12 gap-y-4 mb-16 text-sm text-white/60 max-w-4xl mx-auto">
            <?php foreach ($footerTimes as $label => $time): ?>
                <p><span class="text-white/90 font-semibold"><?php echo htmlspecialchars($label); ?>:</span> <?php echo htmlspecialchars($time); ?></p>
            <?php endforeach; ?>
            <?php if (setting('contact.address') !== ''): ?>
                <p class="w-full"><?php echo htmlspecialchars(setting('contact.address')); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Minimalist Site Map -->
        <div class="flex flex-wrap justify-center gap-x-16 gap-y-8 mb-24 max-w-4xl mx-auto">
            <a href="visit" class="text-white/40 hover:text-white font-sans text-[0.65rem] font-bold uppercase tracking-widest-xl transition-colors">Plan a Visit</a>
            <a href="about" class="text-white/40 hover:text-white font-sans text-[0.65rem] font-bold uppercase tracking-widest-xl transition-colors">About Us</a>
            <a href="ministries" class="text-white/40 hover:text-white font-sans text-[0.65rem] font-bold uppercase tracking-widest-xl transition-colors">Ministries</a>
            <a href="sermons" class="text-white/40 hover:text-white font-sans text-[0.65rem] font-bold uppercase tracking-widest-xl transition-colors">Sermons</a>
            <a href="events" class="text-white/40 hover:text-white font-sans text-[0.65rem] font-bold uppercase tracking-widest-xl transition-colors">Events</a>
            <a href="livestream" class="text-white/40 hover:text-white font-sans text-[0.65rem] font-bold uppercase tracking-widest-xl transition-colors">Watch Live</a>
            <a href="blog" class="text-white/40 hover:text-white font-sans text-[0.65rem] font-bold uppercase tracking-widest-xl transition-colors">Blog</a>
            <a href="donate" class="text-accent hover:text-white font-sans text-[0.65rem] font-bold uppercase tracking-widest-xl transition-colors">Give</a>
            <a href="contact" class="text-white/40 hover:text-white font-sans text-[0.65rem] font-bold uppercase tracking-widest-xl transition-colors">Contact</a>
        </div>

        <!-- Social & Contact Lines -->
        <div class="flex flex-col items-center gap-8 mb-24">
            <div class="flex items-center gap-12">
                <?php 
                    $dynamicSocials = json_decode(setting('social.links', '[]'), true) ?: [];
                    foreach ($dynamicSocials as $socialLink): 
                        if(empty($socialLink['url'])) continue;
                ?>
                    <a href="<?php echo htmlspecialchars(safe_url($socialLink['url'])); ?>" class="text-white/30 hover:text-accent transition-colors duration-500 hover:scale-110" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($socialLink['name']); ?>">
                        <?php if (!empty($socialLink['icon'])): ?>
                            <span class="w-6 h-6 flex items-center justify-center *:w-full *:h-full">
                                <?php echo safe_html($socialLink['icon'], 'svg'); ?>
                            </span>
                        <?php else: ?>
                            <span class="font-sans text-[0.65rem] font-bold uppercase tracking-widest-xl"><?php echo htmlspecialchars($socialLink['name']); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Final Mark -->
        <div class="flex flex-col items-center w-full pt-12 border-t border-white/5">
            <div class="flex flex-col md:flex-row items-center justify-between w-full gap-8">
                <p class="text-white/20 text-[0.55rem] font-bold font-sans tracking-widest-xl uppercase">
                    &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(setting('site.name', 'Bridge Ministries')); ?>. All rights reserved.
                </p>
                <div class="flex items-center gap-8">
                    <a href="privacy" class="text-white/20 hover:text-white text-[0.55rem] font-bold font-sans tracking-widest-xl uppercase transition-colors">Privacy</a>
                    <a href="admin/login.php" class="text-white/20 hover:text-white text-[0.55rem] font-bold font-sans tracking-widest-xl uppercase transition-colors">Staff Login</a>
                </div>
            </div>
        </div>

    </div>
</footer>
<?php endif; ?>

<!-- Cookie Consent Toast -->
<div id="cookie-banner" class="fixed bottom-4 left-4 sm:bottom-6 sm:left-6 max-w-sm w-[calc(100%-2rem)] sm:w-auto bg-[#111111]/90 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl z-[100] opacity-0 pointer-events-none transition-all duration-500 translate-y-8 p-5">
    <p class="text-white/80 text-sm font-sans leading-relaxed mb-4">
        We use cookies to enhance your browsing experience and analyze traffic. 
        <a href="privacy" class="text-amber-500 hover:text-amber-400 font-bold ml-1">Read More</a>
    </p>
    <div class="flex items-center gap-3">
        <button onclick="acceptCookies()" class="flex-1 bg-amber-500 text-black px-4 py-2.5 rounded-xl font-bold text-sm hover:bg-amber-400 transition-colors">Accept</button>
        <button onclick="dismissCookies()" class="flex-1 bg-white/10 text-white px-4 py-2.5 rounded-xl font-bold text-sm hover:bg-white/20 transition-colors">Decline</button>
    </div>
</div>

<script>
    function acceptCookies() {
        try {
            localStorage.setItem('cookieConsent', 'accepted');
        } catch (e) {
            console.warn('Local storage is disabled or blocked.');
        }
        hideCookieBanner();
    }
    function dismissCookies() {
        try {
            localStorage.setItem('cookieConsent', 'declined');
        } catch (e) {
            console.warn('Local storage is disabled or blocked.');
        }
        hideCookieBanner();
    }
    function hideCookieBanner() {
        const banner = document.getElementById('cookie-banner');
        if (banner) {
            banner.classList.remove('opacity-100', 'translate-y-0');
            banner.classList.add('opacity-0', 'pointer-events-none', 'translate-y-8');
        }
    }
    
    // Check consent on load
    document.addEventListener('DOMContentLoaded', () => {
        if (!localStorage.getItem('cookieConsent')) {
            setTimeout(() => {
                const banner = document.getElementById('cookie-banner');
                if (banner) {
                    banner.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-8');
                    banner.classList.add('opacity-100', 'translate-y-0');
                }
            }, 1000);
        }
    });
</script>

<!-- Swiper JS (pinned version; deferred so it never blocks rendering) -->
<script defer src="https://cdn.jsdelivr.net/npm/swiper@11.1.14/swiper-bundle.min.js"></script>

<!-- Site JS -->
<script defer src="assets/js/main.js?v=<?php echo filemtime(PUBLIC_DIR . '/assets/js/main.js') ?: time(); ?>"></script>

</body>
</html>
