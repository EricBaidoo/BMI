</main>

<!-- FOOTER -->
<?php if (!isset($hideFooter) || !$hideFooter): ?>
<footer class="relative bg-slate-900 text-white pt-24 pb-10 overflow-hidden border-t border-slate-800 mt-12">
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Top CTA Section -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-10 mb-20 pb-16 border-b border-slate-800">
            <div class="max-w-2xl">
                <h2 class="font-display font-black text-4xl md:text-5xl tracking-tighter mb-4 text-white">
                    Stay Connected.
                </h2>
                <p class="text-slate-400 text-lg leading-relaxed">
                    Join our newsletter to receive weekly devotionals, updates on global missions, and upcoming events directly to your inbox.
                </p>
            </div>
            <div class="w-full md:w-auto">
                <form class="flex items-center bg-slate-800/50 border border-slate-700 rounded-sm p-1.5 focus-within:border-[#c49a45] focus-within:bg-slate-800 transition-all duration-300 w-full md:w-[400px]">
                    <input type="email" placeholder="Enter your email address" class="bg-transparent text-white px-6 py-3 w-full focus:outline-none placeholder-slate-500 text-sm font-medium" required>
                    <button type="submit" class="bg-[#c49a45] hover:bg-[#d4ac57] text-white px-8 py-3 rounded-sm font-bold text-sm transition-all whitespace-nowrap shadow-md">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Links Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 lg:gap-8 mb-16">
            
            <!-- Brand Column -->
            <div class="lg:col-span-4 pr-8">
                <a href="./" class="flex items-center gap-3 mb-8">
                    <img class="h-12 w-auto" src="assets/image/bmi%20logo%20new.png" alt="BMI Logo" onerror="this.style.display='none';">
                    <span class="font-display font-bold text-2xl tracking-tight text-white">Bridge Ministries</span>
                </a>
                <p class="text-slate-400 text-sm leading-relaxed mb-8 font-medium">
                    An international ministry dedicated to empowering communities, teaching uncompromised biblical truth, and fostering a global legacy of faith and action.
                </p>
                <div class="flex space-x-4">
                    <?php if(setting('social.facebook')): ?>
                        <a href="<?php echo setting('social.facebook'); ?>" class="bg-slate-800 hover:bg-[#c49a45] hover:-translate-y-1 p-3 rounded-sm transition-all duration-300 text-slate-400 hover:text-white border border-slate-700 hover:border-[#c49a45] shadow-sm">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if(setting('social.youtube')): ?>
                        <a href="<?php echo setting('social.youtube'); ?>" class="bg-slate-800 hover:bg-[#c49a45] hover:-translate-y-1 p-3 rounded-sm transition-all duration-300 text-slate-400 hover:text-white border border-slate-700 hover:border-[#c49a45] shadow-sm">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Spacer for layout -->
            <div class="hidden lg:block lg:col-span-1"></div>

            <!-- Links Columns -->
            <div class="lg:col-span-2">
                <h4 class="font-display font-bold text-white tracking-[0.2em] uppercase text-xs mb-8 flex items-center gap-3">
                    <span class="w-1.5 h-1.5 rounded-sm bg-[#c49a45]"></span>
                    Church Life
                </h4>
                <ul class="space-y-4 text-sm font-medium">
                    <li><a href="about" class="text-slate-400 hover:text-[#c49a45] hover:translate-x-1 inline-block transition-transform duration-300">About Us</a></li>
                    <li><a href="ministries" class="text-slate-400 hover:text-[#c49a45] hover:translate-x-1 inline-block transition-transform duration-300">Our Ministries</a></li>
                    <li><a href="sermons" class="text-slate-400 hover:text-[#c49a45] hover:translate-x-1 inline-block transition-transform duration-300">Watch Sermons</a></li>
                    <li><a href="events" class="text-slate-400 hover:text-[#c49a45] hover:translate-x-1 inline-block transition-transform duration-300">Upcoming Events</a></li>
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h4 class="font-display font-bold text-white tracking-[0.2em] uppercase text-xs mb-8 flex items-center gap-3">
                    <span class="w-1.5 h-1.5 rounded-sm bg-[#c49a45]"></span>
                    Get Involved
                </h4>
                <ul class="space-y-4 text-sm font-medium">
                    <li><a href="visit" class="text-slate-400 hover:text-[#c49a45] hover:translate-x-1 inline-block transition-transform duration-300">Plan a Visit</a></li>
                    <li><a href="donate" class="text-slate-400 hover:text-[#c49a45] hover:translate-x-1 inline-block transition-transform duration-300">Give Online</a></li>
                    <li><a href="contact" class="text-slate-400 hover:text-[#c49a45] hover:translate-x-1 inline-block transition-transform duration-300">Contact Us</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h4 class="font-display font-bold text-white tracking-[0.2em] uppercase text-xs mb-8 flex items-center gap-3">
                    <span class="w-1.5 h-1.5 rounded-sm bg-[#c49a45]"></span>
                    Contact
                </h4>
                <ul class="space-y-6 text-sm text-slate-400 font-medium">
                    <?php $address = setting('contact.address'); if($address): ?>
                        <li class="flex items-start group cursor-default">
                            <div class="w-10 h-10 rounded-sm bg-slate-800 flex items-center justify-center mr-4 group-hover:bg-[#c49a45] group-hover:text-white transition-all duration-300 flex-shrink-0 border border-slate-700">
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="leading-relaxed pt-2 group-hover:text-white transition-colors duration-300"><?php echo htmlspecialchars($address); ?></span>
                        </li>
                    <?php endif; ?>
                    
                    <?php $phone = setting('contact.phone_primary'); if($phone): ?>
                        <li class="flex items-start group cursor-default">
                            <div class="w-10 h-10 rounded-sm bg-slate-800 flex items-center justify-center mr-4 group-hover:bg-[#c49a45] group-hover:text-white transition-all duration-300 flex-shrink-0 border border-slate-700">
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <span class="pt-2 group-hover:text-white transition-colors duration-300"><?php echo htmlspecialchars($phone); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>

        <!-- Bottom Copyright -->
        <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-slate-500 text-sm font-medium tracking-wide">
                &copy; <?php echo date('Y'); ?> Bridge Ministries International. All rights reserved.
            </p>
            <div class="flex items-center gap-8">
                <a href="#" class="text-slate-500 hover:text-white text-sm font-medium transition-colors">Privacy Policy</a>
                <a href="#" class="text-slate-500 hover:text-white text-sm font-medium transition-colors">Terms of Service</a>
                <a href="admin/login.php" class="text-slate-500 hover:text-white text-sm font-medium transition-colors">Staff Login</a>
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

</body>
</html>
