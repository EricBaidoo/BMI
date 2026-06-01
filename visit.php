<?php
$pageTitle = 'Plan a Visit | Bridge Ministries International';
$pageDescription = 'Join us this Sunday at Bridge Ministries International. Find service times, location, and what to expect.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/settings.php';

include 'includes/header.php';
?>

<!-- HERO SECTION -->
<div class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#06080f] overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="assets/image/PXL_20240329_213926615.jpg" alt="Church Worship" class="w-full h-full object-cover opacity-20" onerror="this.src='https://images.unsplash.com/photo-1543332143-4e8c27e3256f?q=80&w=2000&auto=format&fit=crop';">
        <div class="absolute inset-0 bg-gradient-to-t from-[#06080f] via-[#06080f]/80 to-transparent"></div>
    </div>
    
    <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto text-center">
        <span class="inline-block py-1 px-3 rounded-full bg-[#1a1f2e] border border-white/10 text-[#c49a45] text-sm font-bold tracking-widest uppercase mb-6">You Belong Here</span>
        <h1 class="text-5xl md:text-7xl font-display font-black text-white tracking-tight mb-6">
            Plan a Visit
        </h1>
        <p class="text-xl text-slate-300 max-w-2xl mx-auto font-medium">
            We can't wait to welcome you to our family. Experience powerful worship, transforming truth, and genuine community.
        </p>
    </div>
</div>

<!-- WHEN & WHERE SECTION -->
<div class="py-24 bg-white relative">
    <div class="w-[90%] max-w-[112.5rem] mx-auto">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <!-- Text Content -->
            <div>
                <h2 class="text-4xl md:text-5xl font-display font-black text-slate-900 tracking-tight mb-8">When & Where</h2>
                
                <div class="space-y-8">
                    <!-- Service Times -->
                    <div class="flex gap-6">
                        <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 border border-slate-200">
                            <svg class="w-6 h-6 text-[#c49a45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Service Times</h3>
                            <ul class="space-y-3 text-slate-600 font-medium">
                                <li class="flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full bg-[#c49a45]"></span>
                                    <span>Sunday Celebration — 9:00 AM</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <span class="w-2 h-2 rounded-full bg-[#c49a45]"></span>
                                    <span>Wednesday Midweek — 6:30 PM</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="flex gap-6">
                        <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 border border-slate-200">
                            <svg class="w-6 h-6 text-[#c49a45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Location</h3>
                            <p class="text-slate-600 font-medium leading-relaxed mb-4">
                                <?php echo htmlspecialchars(setting('contact.address', '123 Bridge Avenue, Faith City, FC 12345')); ?>
                            </p>
                            <a href="#" class="inline-flex items-center font-bold text-[#c49a45] hover:text-[#d4ac57] transition-colors group">
                                Get Directions
                                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image/Map Container -->
            <div class="relative  overflow-hidden shadow-2xl aspect-square md:aspect-video lg:aspect-square group">
                <img src="assets/image/church-building.jpg" alt="Church Exterior" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" onerror="this.src='https://images.unsplash.com/photo-1438032005730-c779502df39b?q=80&w=1000&auto=format&fit=crop';">
                <div class="absolute inset-0 border border-black/10  pointer-events-none"></div>
            </div>
        </div>
        
    </div>
</div>

<!-- WHAT TO EXPECT SECTION -->
<div class="py-24 bg-slate-50 border-t border-slate-200">
    <div class="w-[90%] max-w-[112.5rem] mx-auto">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-4xl md:text-5xl font-display font-black text-slate-900 tracking-tight mb-6">What to Expect</h2>
            <p class="text-lg text-slate-600 font-medium leading-relaxed">
                Visiting a new church can be intimidating, but we want you to feel right at home. Here is a brief look at what our services are like.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Item 1 -->
            <div class="bg-white p-8  border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12  bg-[#c49a45]/10 text-[#c49a45] flex items-center justify-center mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Passionate Worship</h3>
                <p class="text-slate-600 font-medium leading-relaxed">
                    Our services begin with dynamic, Spirit-led worship. We sing contemporary songs and hymns designed to exalt Jesus.
                </p>
            </div>
            
            <!-- Item 2 -->
            <div class="bg-white p-8  border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12  bg-[#c49a45]/10 text-[#c49a45] flex items-center justify-center mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Biblical Teaching</h3>
                <p class="text-slate-600 font-medium leading-relaxed">
                    You will hear an engaging, uncompromising message based entirely on the Word of God that applies directly to your life.
                </p>
            </div>

            <!-- Item 3 -->
            <div class="bg-white p-8  border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12  bg-[#c49a45]/10 text-[#c49a45] flex items-center justify-center mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">BMI Kids</h3>
                <p class="text-slate-600 font-medium leading-relaxed">
                    We offer a safe, fun, and educational environment for children (infants through 5th grade) during all main services.
                </p>
            </div>
        </div>

    </div>
</div>

<!-- PLAN A VISIT FORM -->
<div class="py-24 bg-slate-900 relative overflow-hidden" id="visit-form">
    
    <!-- Decorative SVG -->
    <div class="absolute top-0 right-0 opacity-10 pointer-events-none transform translate-x-1/3 -translate-y-1/3">
        <svg width="800" height="800" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="white" stroke-width="2"/></svg>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-display font-black text-white tracking-tight mb-6">Let Us Know You're Coming!</h2>
            <p class="text-lg text-slate-400 font-medium">
                Fill out the form below and our team will meet you at the door, show you around, and help get your kids checked in!
            </p>
        </div>

        <div class="bg-white  shadow-xl p-8 md:p-12">
            <form action="#" method="POST" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-bold text-slate-700 mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="w-full bg-slate-50 border border-slate-200  px-4 py-3 focus:outline-none focus:border-[#c49a45] focus:ring-1 focus:ring-[#c49a45] transition-colors" required>
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-bold text-slate-700 mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="w-full bg-slate-50 border border-slate-200  px-4 py-3 focus:outline-none focus:border-[#c49a45] focus:ring-1 focus:ring-[#c49a45] transition-colors" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" class="w-full bg-slate-50 border border-slate-200  px-4 py-3 focus:outline-none focus:border-[#c49a45] focus:ring-1 focus:ring-[#c49a45] transition-colors" required>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-bold text-slate-700 mb-2">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="w-full bg-slate-50 border border-slate-200  px-4 py-3 focus:outline-none focus:border-[#c49a45] focus:ring-1 focus:ring-[#c49a45] transition-colors">
                    </div>
                </div>

                <div>
                    <label for="date" class="block text-sm font-bold text-slate-700 mb-2">When are you planning to visit?</label>
                    <input type="date" id="date" name="date" class="w-full bg-slate-50 border border-slate-200  px-4 py-3 focus:outline-none focus:border-[#c49a45] focus:ring-1 focus:ring-[#c49a45] transition-colors text-slate-700" required>
                </div>

                <div>
                    <label for="kids" class="block text-sm font-bold text-slate-700 mb-2">Will you be bringing any children?</label>
                    <select id="kids" name="kids" class="w-full bg-slate-50 border border-slate-200  px-4 py-3 focus:outline-none focus:border-[#c49a45] focus:ring-1 focus:ring-[#c49a45] transition-colors text-slate-700">
                        <option value="no">No children this time</option>
                        <option value="yes">Yes, I will bring my kids</option>
                    </select>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#c49a45] hover:bg-[#d4ac57] text-white font-bold text-lg py-4  shadow-lg hover:-translate-y-1 transition-all duration-300">
                        Plan My Visit
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>

<?php include 'includes/footer.php'; ?>
