<?php
$pageTitle = 'Contact Us | Bridge Ministries International';
$pageDescription = 'Get in touch with Bridge Ministries International. Submit a prayer request or send us a message.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/csrf.php';

// Handle contact form submission
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $type = trim($_POST['type'] ?? 'General Inquiry');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        try {
            $pdo = db_connect();
            $stmt = $pdo->prepare("INSERT INTO messages (full_name, email, subject, message, type) VALUES (:name, :email, :subject, :msg, :msgtype)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':subject' => $type,
                ':msg' => $message,
                ':msgtype' => (stripos($type, 'prayer') !== false ? 'prayer' : 'contact')
            ]);
            
            // Send email notification to Admin
            $adminEmail = setting('contact.email_general', 'info@bmiglobal.org');
            $subjectLine = "New Website Submission: " . $type;
            $emailBody = "You have received a new submission from the website.\n\nName: $name\nEmail: $email\nType: $type\nMessage:\n$message\n\nLog in to the admin panel to view all messages.";
            $headers = "From: no-reply@" . parse_url(setting('site.url', 'https://bmiglobal.org'), PHP_URL_HOST) . "\r\n";
            @mail($adminEmail, $subjectLine, $emailBody, $headers);
            
            $successMessage = "Thank you, $name. Your message has been received. Our team will reach out to you soon.";
        } catch (Throwable $e) {
            log_exception($e, 'contact');
            $errorMessage = "Sorry, an error occurred while saving your message. Please try again later.";
        }
    } else {
        $errorMessage = "Please fill in all required fields.";
    }
}

include 'includes/header.php';
?>

<!-- CUSTOM ANIMATION STYLES -->
<!-- HERO SECTION -->
<div class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#030303] overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img fetchpriority="high" src="<?= setting_url('contact.hero_bg_image', 'https://images.unsplash.com/photo-1516383740770-fbcc5ccbece0?q=80&w=1200&auto=format&fit=crop') ?>" alt="Contact Background" class="w-full h-full object-cover opacity-10 mix-blend-luminosity grayscale" onerror="this.src='https://images.unsplash.com/photo-1516383740770-fbcc5ccbece0?q=80&w=1200&auto=format&fit=crop';">
        <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0c] via-[#030303]/80 to-[#030303]"></div>
    </div>

    <!-- Abstract Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[40rem] h-[40rem] bg-indigo-600/10 blur-[120px] rounded-full mix-blend-screen pointer-events-none"></div>

    <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto text-center gs-reveal-up">
        <div class="inline-flex items-center gap-6 mb-8">
            <div class="h-px w-16 bg-white/20"></div>
            <span class="text-white/50 font-sans font-bold text-[0.625rem] tracking-[0.4em] uppercase">Contact Us</span>
            <div class="h-px w-16 bg-white/20"></div>
        </div>
        <h1 class="text-5xl md:text-7xl font-display font-black text-transparent bg-clip-text bg-gradient-to-br from-white via-neutral-300 to-neutral-600 tracking-normal mb-6 uppercase leading-[0.9]">
            <?= setting_html('contact.hero_title', 'Get in <span class="italic font-light">Touch</span>') ?>
        </h1>
        <p class="text-xl text-neutral-400 max-w-2xl mx-auto font-medium">
            <?= setting_html('contact.hero_subtitle', 'Whether you have a question, need prayer, or want to learn more about our ministries, we are here for you.') ?>
        </p>
    </div>
</div>

<!-- CONTACT BENTO GRID -->
<div class="py-24 md:py-32 bg-[#0a0a0c] relative overflow-hidden">
    <!-- Ambient Orbs -->
    <div class="absolute top-0 right-0 w-[50rem] h-[50rem] bg-amber-600/5 blur-[150px] rounded-full pointer-events-none mix-blend-screen"></div>

    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Bento Column 1: Contact Info -->
            <div class="lg:col-span-5 flex flex-col gap-8 gs-reveal-right">
                
                <!-- Main Info Card -->
                <div class="relative bg-[#050505] border border-white/5 rounded-[2.5rem] p-10 md:p-14 text-white shadow-2xl group overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

                    <h2 class="text-3xl font-display font-black tracking-normal mb-10 uppercase relative z-10">Reach Out</h2>
                    
                    <ul class="space-y-8 relative z-10">
                        <li class="flex items-start group/item cursor-default">
                            <div class="w-14 h-14 rounded-[1.25rem] bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 text-amber-500 mr-5 group-hover/item:bg-amber-500 group-hover/item:text-black group-hover/item:-rotate-6 transition-all duration-500 shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-white/40 uppercase tracking-[0.2em] mb-2">Mailing Address</h3>
                                <p class="text-neutral-300 font-sans font-medium text-lg leading-snug group-hover/item:text-amber-500 transition-colors duration-500">
                                    <?php echo htmlspecialchars(setting('contact.address')); ?>
                                </p>
                            </div>
                        </li>
                        
                        <li class="flex items-start group/item cursor-default">
                            <div class="w-14 h-14 rounded-[1.25rem] bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 text-amber-500 mr-5 group-hover/item:bg-amber-500 group-hover/item:text-black group-hover/item:-rotate-6 transition-all duration-500 shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-white/40 uppercase tracking-[0.2em] mb-2">Email Us</h3>
                                <a href="mailto:<?php echo htmlspecialchars(setting('contact.email_general', 'info@bmiglobal.org')); ?>" class="text-neutral-300 font-sans font-medium text-lg leading-snug hover:text-amber-500 transition-colors duration-300">
                                    <?php echo htmlspecialchars(setting('contact.email_general', 'info@bmiglobal.org')); ?>
                                </a>
                            </div>
                        </li>
                        
                        <li class="flex items-start group/item cursor-default">
                            <div class="w-14 h-14 rounded-[1.25rem] bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 text-amber-500 mr-5 group-hover/item:bg-amber-500 group-hover/item:text-black group-hover/item:-rotate-6 transition-all duration-500 shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-white/40 uppercase tracking-[0.2em] mb-2">Call Us</h3>
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', setting('contact.phone_primary')); ?>" class="text-neutral-300 font-sans font-medium text-lg leading-snug hover:text-amber-500 transition-colors duration-300">
                                    <?php echo htmlspecialchars(setting('contact.phone_primary')); ?>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Office Hours & Socials (Split Bento) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 h-full">
                    
                    <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] p-8 text-white shadow-2xl gs-reveal-up group">
                        <h3 class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-6">Service Times</h3>
                        <ul class="space-y-4 text-white/60 font-medium text-sm">
                            <?php foreach (service_times() as $label => $time): ?>
                                <li class="flex flex-col"><span class="text-white"><?php echo htmlspecialchars($label); ?></span> <span><?php echo htmlspecialchars($time); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if (setting('contact.office_hours') !== ''): ?>
                            <h3 class="text-xs font-bold text-amber-500 uppercase tracking-widest mt-8 mb-3">Office Hours</h3>
                            <p class="text-white/60 font-medium text-sm whitespace-pre-line"><?php echo htmlspecialchars(setting('contact.office_hours')); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] p-8 text-white shadow-2xl gs-reveal-up flex flex-col justify-between group">
                        <h3 class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-6">Social</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <?php 
                                $dynamicSocials = json_decode(setting('social.links', '[]'), true) ?: [];
                                foreach ($dynamicSocials as $socialLink): 
                                    if(empty($socialLink['url'])) continue;
                            ?>
                                <a href="<?php echo htmlspecialchars(safe_url($socialLink['url'])); ?>" class="w-14 h-14 rounded-2xl bg-white/5 text-white flex items-center justify-center hover:bg-amber-500 hover:text-black hover:-translate-y-1 transition-all duration-300" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($socialLink['name']); ?>" title="<?php echo htmlspecialchars($socialLink['name']); ?>">
                                    <?php if (!empty($socialLink['icon'])): ?>
                                        <span class="w-6 h-6 flex items-center justify-center *:w-full *:h-full">
                                            <?php echo safe_html($socialLink['icon'], 'svg'); ?>
                                        </span>
                                    <?php else: ?>
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bento Column 2: Contact Form -->
            <div class="lg:col-span-7">
                <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] p-10 md:p-14 text-white shadow-2xl gs-reveal-up h-full group hover:border-amber-500/20 transition-all duration-500">
                    
                    <h2 class="text-3xl md:text-5xl font-display font-black tracking-normal mb-3 uppercase">Send a Message</h2>
                    <p class="text-white/50 font-sans font-medium text-lg mb-10">We would love to hear from you. Please fill out the form below.</p>

                    <?php if ($successMessage): ?>
                        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-6 rounded-2xl mb-10 flex items-start">
                            <svg class="w-6 h-6 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-medium"><?php echo htmlspecialchars($successMessage); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMessage): ?>
                        <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-6 rounded-2xl mb-10 flex items-start">
                            <svg class="w-6 h-6 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-medium"><?php echo htmlspecialchars($errorMessage); ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="contact" method="POST" class="space-y-8">
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label for="name" class="block text-xs font-bold text-white/40 uppercase tracking-widest mb-3">Full Name</label>
                                <input type="text" id="name" name="name" class="w-full bg-white/5 border-none rounded-xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-medium" required>
                            </div>
                            <div>
                                <label for="email" class="block text-xs font-bold text-white/40 uppercase tracking-widest mb-3">Email Address</label>
                                <input type="email" id="email" name="email" class="w-full bg-white/5 border-none rounded-xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-medium" required>
                            </div>
                        </div>

                        <div>
                            <label for="type" class="block text-xs font-bold text-white/40 uppercase tracking-widest mb-3">How can we help you?</label>
                            <div class="relative">
                                <select id="type" name="type" class="w-full bg-white/5 border-none rounded-xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-medium appearance-none cursor-pointer">
                                    <?php
                                    $subjects = ['General Inquiry', 'Prayer Request', 'Giving', 'Testimony', 'Join a Ministry'];
                                    $preselect = (string) ($_POST['type'] ?? $_GET['subject'] ?? '');
                                    foreach ($subjects as $subject):
                                    ?>
                                    <option class="bg-[#111111] text-white"<?php echo strcasecmp($preselect, $subject) === 0 ? ' selected' : ''; ?>><?php echo htmlspecialchars($subject); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-5 text-white/30">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="message" class="block text-xs font-bold text-white/40 uppercase tracking-widest mb-3">Your Message</label>
                            <textarea id="message" name="message" rows="5" class="w-full bg-white/5 border-none rounded-2xl px-5 py-5 text-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-medium resize-none" required></textarea>
                        </div>

                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-black font-bold uppercase tracking-widest text-sm py-5 rounded-2xl hover:-translate-y-1 transition-all duration-300">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

