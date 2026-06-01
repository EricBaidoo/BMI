</main>

<!-- FOOTER -->
<?php if (!isset($hideFooter) || !$hideFooter): ?>
<footer class="relative bg-slate-950 text-white pt-28 pb-10 overflow-hidden border-t border-white/5 mt-12">
    <!-- Subtle Background Glow -->
    <div class="absolute top-0 right-0 w-3/4 h-3/4 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#c49a45]/10 via-slate-950/0 to-transparent pointer-events-none z-0"></div>

    <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Top CTA Section -->
        <div class="flex flex-col lg:flex-row items-center justify-between gap-12 mb-20 pb-16 border-b border-white/10">
            <div class="max-w-2xl text-center lg:text-left">
                <h2 class="font-display font-black text-5xl md:text-6xl tracking-tight mb-6 text-white leading-none">
                    Stay <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#c49a45] to-[#e8c881]">Connected.</span>
                </h2>
                <p class="text-slate-400 text-lg md:text-xl leading-relaxed font-light">
                    Join our newsletter to receive weekly devotionals, updates on global missions, and upcoming events directly to your inbox.
                </p>
            </div>
            <div class="w-full lg:w-auto">
                <form class="flex flex-col sm:flex-row items-stretch w-full lg:w-[31.25rem] gap-3 sm:gap-0 group">
                    <input type="email" placeholder="Enter your email address" class="bg-white/5 border border-white/10 sm:border-r-0 text-white px-6 py-4 w-full focus:outline-none focus:border-[#c49a45]/50 focus:bg-white/10 transition-all placeholder-slate-500 font-medium backdrop-blur-md" required>
                    <button type="submit" class="bg-gradient-to-r from-[#c49a45] to-[#d4ac57] hover:from-[#d4ac57] hover:to-[#c49a45] text-white px-10 py-4 font-bold text-sm uppercase tracking-widest transition-all whitespace-nowrap shadow-[0_0_20px_rgba(196,154,69,0.3)] hover:shadow-[0_0_30px_rgba(196,154,69,0.5)]">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Links Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-16 lg:gap-8 mb-20">
            
            <!-- Brand Column -->
            <div class="lg:col-span-4 pr-8 text-center md:text-left">
                <a href="./" class="inline-flex items-center gap-4 mb-8 group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-[#c49a45] blur-md opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
                        <img class="h-12 w-auto relative z-10" src="assets/image/bmi%20logo%20new.png" alt="BMI Logo" onerror="this.style.display='none';">
                    </div>
                    <span class="font-display font-black text-2xl tracking-tight text-white">Bridge Ministries</span>
                </a>
                <p class="text-slate-400 text-sm leading-relaxed mb-8 font-light">
                    An international ministry dedicated to empowering communities, teaching uncompromised biblical truth, and fostering a global legacy of faith and action.
                </p>
                <div class="flex space-x-4 justify-center md:justify-start">
                    <?php if(setting('social.facebook')): ?>
                        <a href="<?php echo setting('social.facebook'); ?>" class="w-12 h-12 bg-white/5 flex items-center justify-center hover:bg-[#c49a45] hover:-translate-y-1 transition-all duration-300 text-slate-400 hover:text-white border border-white/10 hover:border-[#c49a45] shadow-lg hover:shadow-[#c49a45]/30">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if(setting('social.youtube')): ?>
                        <a href="<?php echo setting('social.youtube'); ?>" class="w-12 h-12 bg-white/5 flex items-center justify-center hover:bg-[#c49a45] hover:-translate-y-1 transition-all duration-300 text-slate-400 hover:text-white border border-white/10 hover:border-[#c49a45] shadow-lg hover:shadow-[#c49a45]/30">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Links Columns -->
            <div class="lg:col-span-2 lg:col-start-6">
                <h4 class="font-display font-bold text-white tracking-widest uppercase text-xs mb-8 flex items-center gap-3">
                    <span class="w-1.5 h-1.5 bg-gradient-to-r from-[#c49a45] to-[#e8c881] shadow-[0_0_10px_rgba(196,154,69,0.8)]"></span>
                    Church Life
                </h4>
                <ul class="space-y-4 text-sm font-medium">
                    <li><a href="about" class="text-slate-400 hover:text-white hover:translate-x-2 flex items-center gap-2 transition-all duration-300 group"><span class="w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-3"></span> About Us</a></li>
                    <li><a href="ministries" class="text-slate-400 hover:text-white hover:translate-x-2 flex items-center gap-2 transition-all duration-300 group"><span class="w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-3"></span> Our Ministries</a></li>
                    <li><a href="sermons" class="text-slate-400 hover:text-white hover:translate-x-2 flex items-center gap-2 transition-all duration-300 group"><span class="w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-3"></span> Watch Sermons</a></li>
                    <li><a href="events" class="text-slate-400 hover:text-white hover:translate-x-2 flex items-center gap-2 transition-all duration-300 group"><span class="w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-3"></span> Upcoming Events</a></li>
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h4 class="font-display font-bold text-white tracking-widest uppercase text-xs mb-8 flex items-center gap-3">
                    <span class="w-1.5 h-1.5 bg-gradient-to-r from-[#c49a45] to-[#e8c881] shadow-[0_0_10px_rgba(196,154,69,0.8)]"></span>
                    Get Involved
                </h4>
                <ul class="space-y-4 text-sm font-medium">
                    <li><a href="visit" class="text-slate-400 hover:text-white hover:translate-x-2 flex items-center gap-2 transition-all duration-300 group"><span class="w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-3"></span> Plan a Visit</a></li>
                    <li><a href="donate" class="text-slate-400 hover:text-white hover:translate-x-2 flex items-center gap-2 transition-all duration-300 group"><span class="w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-3"></span> Give Online</a></li>
                    <li><a href="contact" class="text-slate-400 hover:text-white hover:translate-x-2 flex items-center gap-2 transition-all duration-300 group"><span class="w-0 h-0.5 bg-[#c49a45] transition-all duration-300 group-hover:w-3"></span> Contact Us</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h4 class="font-display font-bold text-white tracking-widest uppercase text-xs mb-8 flex items-center gap-3">
                    <span class="w-1.5 h-1.5 bg-gradient-to-r from-[#c49a45] to-[#e8c881] shadow-[0_0_10px_rgba(196,154,69,0.8)]"></span>
                    Contact
                </h4>
                <ul class="space-y-6 text-sm text-slate-400 font-medium">
                    <?php $address = setting('contact.address'); if($address): ?>
                        <li class="flex items-start group">
                            <div class="w-10 h-10 bg-white/5 flex items-center justify-center mr-4 flex-shrink-0 border border-white/10 group-hover:border-[#c49a45] group-hover:bg-[#c49a45]/10 group-hover:text-[#c49a45] transition-all duration-300">
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-[#c49a45] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="leading-relaxed pt-2 group-hover:text-white transition-colors duration-300"><?php echo htmlspecialchars($address); ?></span>
                        </li>
                    <?php endif; ?>
                    
                    <?php $phone = setting('contact.phone_primary'); if($phone): ?>
                        <li class="flex items-start group">
                            <div class="w-10 h-10 bg-white/5 flex items-center justify-center mr-4 flex-shrink-0 border border-white/10 group-hover:border-[#c49a45] group-hover:bg-[#c49a45]/10 group-hover:text-[#c49a45] transition-all duration-300">
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-[#c49a45] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <span class="pt-2 group-hover:text-white transition-colors duration-300"><?php echo htmlspecialchars($phone); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>

        <!-- Bottom Copyright -->
        <div class="border-t border-white/10 pt-8 pb-4 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-slate-500 text-sm font-medium tracking-wide">
                &copy; <?php echo date('Y'); ?> Bridge Ministries International. All rights reserved.
            </p>
            <div class="flex items-center gap-8">
                <a href="#" class="text-slate-500 hover:text-white text-sm font-medium transition-colors hover:underline">Privacy Policy</a>
                <a href="#" class="text-slate-500 hover:text-white text-sm font-medium transition-colors hover:underline">Terms of Service</a>
                <a href="admin/login.php" class="text-slate-500 hover:text-white text-sm font-medium transition-colors hover:underline">Staff Login</a>
            </div>
        </div>
    </div>
</footer>
<?php endif; ?>

<script>
    // Header Scroll Effect
    const header = document.getElementById('site-header');
    const headerInner = document.getElementById('header-inner');
    const isHome = <?php echo $currentPage === 'index.php' ? 'true' : 'false'; ?>;
    const mobileBtn = document.getElementById('mobile-menu-btn');
    
    if (isHome) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                header.classList.remove('header-transparent');
                header.classList.add('header-solid');
                headerInner.classList.remove('md:h-24');
                headerInner.classList.add('md:h-20');
                if(mobileBtn) {
                    mobileBtn.classList.remove('text-white');
                    mobileBtn.classList.add('text-gray-600');
                }
            } else {
                header.classList.add('header-transparent');
                header.classList.remove('header-solid');
                headerInner.classList.add('md:h-24');
                headerInner.classList.remove('md:h-20');
                if(mobileBtn) {
                    mobileBtn.classList.add('text-white');
                    mobileBtn.classList.remove('text-gray-600');
                }
            }
        });
        
        // Initial check for mobile button color
        if(window.scrollY <= 20 && mobileBtn) {
             mobileBtn.classList.add('text-white');
             mobileBtn.classList.remove('text-gray-600');
        }
    }

    // Mobile Menu Toggle
    const menu = document.getElementById('mobile-menu');

    if(mobileBtn && menu) {
        mobileBtn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    }
</script>

<!-- AOS Animation JS -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        offset: 50,
        easing: 'ease-out-cubic'
    });
</script>

</body>
</html>
