<?php
$pageTitle = 'Give Online | Bridge Ministries International';
$pageDescription = 'Partner with us financially to spread the Gospel and empower communities globally.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/settings.php';

// Fetch the external ChMS Payment Portal URL from the environment or settings
$chmsPaymentUrl = env('CHMS_PAYMENT_URL', '');
if (empty($chmsPaymentUrl)) {
    // Fallback if not configured in .env
    $chmsPaymentUrl = setting('donate.chms_url', '#');
}

include 'includes/header.php';

// Render Cinematic Hero
render_hero_cinematic([
    'title' => setting('donate.hero_title', '<span class="italic font-light">Give</span> Online'),
    'subtitle' => 'Partnership',
    'bg_image' => setting('donate.hero_bg_image', 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?q=80&w=1920&auto=format&fit=crop'),
    'button_text' => 'Give Securely Now',
    'button_url' => $chmsPaymentUrl,
    'is_video' => false
]);
?>

<!-- WAYS TO GIVE SECTION -->
<section class="py-24 md:py-32 bg-obsidian-900 relative overflow-hidden gs-reveal-section">
    <!-- Ambient Glows -->
    <div class="absolute top-0 right-0 w-[50rem] h-[50rem] bg-accent-glow blur-[150px] rounded-full mix-blend-screen pointer-events-none z-0"></div>

    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        
        <div class="text-center max-w-3xl mx-auto mb-20 gs-reveal-up">
            <h2 class="text-4xl md:text-5xl lg:text-7xl font-display font-black text-white uppercase tracking-normal mb-6 leading-[1.0]">3 Ways to <i class="text-accent font-light">Give</i></h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Online -->
            <div class="bg-obsidian-800/40 p-10 lg:p-12 rounded-[2.5rem] shadow-glass border border-white/5 text-center group hover:border-accent/30 transition-all duration-700 hover:-translate-y-2 gs-reveal-up relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-t from-accent/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                
                <div class="w-24 h-24 rounded-[1.5rem] bg-white/5 border border-white/10 text-neutral-400 flex items-center justify-center mx-auto mb-8 group-hover:bg-accent group-hover:text-black transition-colors duration-500 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black uppercase text-white mb-4 tracking-normal leading-none group-hover:text-accent transition-colors">Give Online</h3>
                <p class="text-neutral-400 font-sans font-medium leading-relaxed mb-8 flex-grow">
                    Simple and secure. Give a single gift, or schedule recurring giving using your checking account, debit, or credit card through our dedicated portal.
                </p>
                <?php render_button_primary(['text' => 'Give Online', 'url' => $chmsPaymentUrl, 'style' => 'light']); ?>
            </div>

            <!-- Bank Transfer -->
            <div class="bg-obsidian-800/40 p-10 lg:p-12 rounded-[2.5rem] shadow-glass border border-white/5 text-center group hover:border-white/20 transition-all duration-700 hover:-translate-y-2 gs-reveal-up relative overflow-hidden flex flex-col delay-100">
                <div class="w-24 h-24 rounded-[1.5rem] bg-white/5 border border-white/10 text-neutral-400 flex items-center justify-center mx-auto mb-8 group-hover:bg-white group-hover:text-black transition-colors duration-500 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black uppercase text-white mb-6 tracking-normal leading-none group-hover:text-white/80 transition-colors">Bank Transfer</h3>
                <div class="bg-obsidian-950/50 border border-white/5 rounded-2xl p-6 flex-grow shadow-inner">
                    <p class="text-neutral-400 font-sans font-medium leading-loose whitespace-pre-wrap text-sm"><?= setting('donate.bank_details', "Bank Name: Faith Bank\nAccount: 1234567890\nBranch: Main Branch") ?></p>
                </div>
            </div>

            <!-- Mobile Money -->
            <div class="bg-obsidian-800/40 p-10 lg:p-12 rounded-[2.5rem] shadow-glass border border-white/5 text-center group hover:border-white/20 transition-all duration-700 hover:-translate-y-2 gs-reveal-up relative overflow-hidden flex flex-col delay-200">
                <div class="w-24 h-24 rounded-[1.5rem] bg-white/5 border border-white/10 text-neutral-400 flex items-center justify-center mx-auto mb-8 group-hover:bg-white group-hover:text-black transition-colors duration-500 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black uppercase text-white mb-6 tracking-normal leading-none group-hover:text-white/80 transition-colors">Mobile Money</h3>
                <div class="bg-obsidian-950/50 border border-white/5 rounded-2xl p-6 flex-grow shadow-inner">
                    <p class="text-neutral-400 font-sans font-medium leading-loose whitespace-pre-wrap text-sm"><?= setting('donate.momo_details', "MTN MoMo: 055 123 4567\nVodafone Cash: 020 123 4567\nName: Bridge Ministries") ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-24 bg-obsidian-950 relative overflow-hidden border-t border-white/5 gs-reveal-section">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-r from-accent/10 to-blue-600/10 opacity-50"></div>
    </div>
    
    <div class="max-w-[112.5rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10 gs-reveal-up w-[90%]">
        <div class="bg-obsidian-800/50 backdrop-blur-xl border border-white/10 p-12 md:p-20 rounded-[3rem] flex flex-col md:flex-row items-center justify-between gap-12 text-center md:text-left shadow-glass relative overflow-hidden">
            <!-- Glass Accents -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-accent/20 blur-[100px] rounded-full"></div>
            
            <div class="relative z-10">
                <h2 class="text-3xl md:text-5xl font-display font-black tracking-normal uppercase text-white mb-6">Have Questions About Giving?</h2>
                <p class="text-neutral-400 font-medium text-lg max-w-xl leading-relaxed">Our finance team is here to help with annual giving statements, asset transfers, and general inquiries.</p>
            </div>
            
            <div class="relative z-10 flex-shrink-0">
                <?php render_button_primary(['text' => 'Contact Finance Team', 'url' => 'contact.php?subject=Giving', 'style' => 'outline']); ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
