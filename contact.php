<?php
$pageTitle = 'Contact Us | Bridge Ministries International';
$pageDescription = 'Get in touch with Bridge Ministries International. Submit a prayer request or send us a message.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/settings.php';

// Handle contact form submission
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $type = trim($_POST['type'] ?? 'General Inquiry');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        // Here you would normally insert into a database or send an email.
        // For demonstration, we'll just show a success message.
        $successMessage = "Thank you, $name. Your $type has been received. Our team will reach out to you soon.";
    } else {
        $errorMessage = "Please fill in all required fields.";
    }
}

include 'includes/header.php';
?>

<!-- HERO SECTION -->
<div class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#06080f] overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="<?= setting('contact.hero_bg_image', 'https://images.unsplash.com/photo-1516383740770-fbcc5ccbece0?q=80&w=1200&auto=format&fit=crop') ?>" alt="Contact Background" class="w-full h-full object-cover opacity-10" onerror="this.src='https://images.unsplash.com/photo-1516383740770-fbcc5ccbece0?q=80&w=1200&auto=format&fit=crop';">
        <div class="absolute inset-0 bg-gradient-to-t from-[#06080f] via-[#06080f]/80 to-transparent"></div>
    </div>

    <!-- Abstract Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[50rem] h-[50rem] bg-[#c49a45]/10 blur-[7.5rem] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-[#c49a45]/30 to-transparent"></div>

    <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto text-center">
        <h1 class="text-5xl md:text-7xl font-display font-black text-white tracking-tight mb-6">
            <?= setting('contact.hero_title', 'Get in Touch') ?>
        </h1>
        <p class="text-xl text-slate-300 max-w-2xl mx-auto font-medium">
            <?= setting('contact.hero_subtitle', 'Whether you have a question, need prayer, or want to learn more about our ministries, we are here for you.') ?>
        </p>
    </div>
</div>

<!-- CONTACT SECTION -->
<div class="py-24 bg-white relative">
    <div class="w-[90%] max-w-[112.5rem] mx-auto">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-8">
            
            <!-- Contact Info -->
            <div class="lg:col-span-5 space-y-12 pr-0 lg:pr-8">
                <div>
                    <h2 class="text-3xl font-display font-black text-slate-900 tracking-tight mb-8">Contact Information</h2>
                    
                    <ul class="space-y-8">
                        <li class="flex items-start">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 text-[#c49a45] mr-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Mailing Address</h3>
                                <p class="text-slate-900 font-medium text-lg">
                                    <?php echo htmlspecialchars(setting('contact.address', '123 Bridge Avenue, Faith City, FC 12345')); ?>
                                </p>
                            </div>
                        </li>
                        
                        <li class="flex items-start">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 text-[#c49a45] mr-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Email</h3>
                                <p class="text-slate-900 font-medium text-lg">
                                    <a href="mailto:<?php echo htmlspecialchars(setting('contact.email_general', 'info@bridge.test')); ?>" class="hover:text-[#c49a45] transition-colors">
                                        <?php echo htmlspecialchars(setting('contact.email_general', 'info@bridge.test')); ?>
                                    </a>
                                </p>
                            </div>
                        </li>

                        <li class="flex items-start">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 text-[#c49a45] mr-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Phone</h3>
                                <p class="text-slate-900 font-medium text-lg">
                                    <a href="tel:<?php echo htmlspecialchars(setting('contact.phone_primary', '(555) 123-4567')); ?>" class="hover:text-[#c49a45] transition-colors">
                                        <?php echo htmlspecialchars(setting('contact.phone_primary', '(555) 123-4567')); ?>
                                    </a>
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Social Media (Big Icons) -->
                <div class="mt-12">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Connect with Us</h3>
                    <div class="flex flex-wrap gap-4">
                        <?php 
                            $dynamicSocials = json_decode(setting('social.links', '[]'), true) ?: [];
                            foreach ($dynamicSocials as $socialLink): 
                                if(empty($socialLink['url'])) continue;
                        ?>
                            <a href="<?php echo htmlspecialchars($socialLink['url']); ?>" class="w-16 h-16 rounded-full bg-slate-900 text-white flex items-center justify-center hover:bg-[#c49a45] hover:-translate-y-1 transition-all duration-300 shadow-lg" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($socialLink['name']); ?>" title="<?php echo htmlspecialchars($socialLink['name']); ?>">
                                <?php if (!empty($socialLink['icon'])): ?>
                                    <span class="w-7 h-7 flex items-center justify-center *:w-full *:h-full">
                                        <?php echo $socialLink['icon']; ?>
                                    </span>
                                <?php else: ?>
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Office Hours -->
                <div class="p-8 bg-slate-50  border border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Office Hours</h3>
                    <ul class="space-y-3 text-slate-600 font-medium text-sm">
                        <li class="flex justify-between"><span>Monday - Thursday</span> <span>9:00 AM - 5:00 PM</span></li>
                        <li class="flex justify-between"><span>Friday</span> <span>9:00 AM - 1:00 PM</span></li>
                        <li class="flex justify-between"><span>Saturday</span> <span>Closed</span></li>
                        <li class="flex justify-between text-slate-900 font-bold border-t border-slate-200 pt-3 mt-3"><span>Sunday Services</span> <span>9:00 AM</span></li>
                    </ul>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-7">
                <div class="bg-white  shadow-2xl shadow-slate-200/50 border border-slate-100 p-8 md:p-12 h-full">
                    
                    <h2 class="text-3xl font-display font-black text-slate-900 tracking-tight mb-2">Send us a Message</h2>
                    <p class="text-slate-500 font-medium mb-8">We would love to hear from you. Please fill out the form below.</p>

                    <?php if ($successMessage): ?>
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4  mb-8 flex items-start">
                            <svg class="w-6 h-6 text-emerald-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-medium"><?php echo htmlspecialchars($successMessage); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMessage): ?>
                        <div class="bg-red-50 border border-red-200 text-red-800 p-4  mb-8 flex items-start">
                            <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-medium"><?php echo htmlspecialchars($errorMessage); ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="contact.php" method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                                <input type="text" id="name" name="name" class="w-full bg-slate-50 border border-slate-200  px-4 py-3.5 focus:outline-none focus:border-[#c49a45] focus:ring-1 focus:ring-[#c49a45] transition-colors" required>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                                <input type="email" id="email" name="email" class="w-full bg-slate-50 border border-slate-200  px-4 py-3.5 focus:outline-none focus:border-[#c49a45] focus:ring-1 focus:ring-[#c49a45] transition-colors" required>
                            </div>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-bold text-slate-700 mb-2">How can we help you?</label>
                            <div class="relative">
                                <select id="type" name="type" class="w-full bg-slate-50 border border-slate-200  px-4 py-3.5 focus:outline-none focus:border-[#c49a45] focus:ring-1 focus:ring-[#c49a45] transition-colors appearance-none">
                                    <option>General Inquiry</option>
                                    <option>Prayer Request</option>
                                    <option>Testimony</option>
                                    <option>Join a Ministry</option>
                                    <option>Website Support</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-bold text-slate-700 mb-2">Your Message</label>
                            <textarea id="message" name="message" rows="5" class="w-full bg-slate-50 border border-slate-200  px-4 py-3.5 focus:outline-none focus:border-[#c49a45] focus:ring-1 focus:ring-[#c49a45] transition-colors resize-none" required></textarea>
                        </div>

                        <button type="submit" class="w-full bg-slate-900 hover:bg-[#c49a45] text-white font-bold text-lg py-4  shadow-lg hover:-translate-y-1 transition-all duration-300">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
