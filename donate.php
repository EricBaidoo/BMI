<?php
$pageTitle = 'Give Online | Bridge Ministries International';
$pageDescription = 'Partner with us financially to spread the Gospel and empower communities globally.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/settings.php';

include 'includes/header.php';
?>

<!-- CUSTOM ANIMATION STYLES -->
<style>
.reveal { opacity: 0; transform: translateY(40px); transition: all 1s cubic-bezier(0.16, 1, 0.3, 1); }
.reveal.revealed { opacity: 1; transform: translateY(0); }
.delay-100 { transition-delay: 100ms; }
.delay-200 { transition-delay: 200ms; }
</style>

<!-- HERO SECTION -->
<div class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#030303] overflow-hidden min-h-[50vh] flex items-center justify-center">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img loading="lazy" src="<?= setting('donate.hero_bg_image', 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?q=80&w=1200&auto=format&fit=crop') ?>" alt="Donate Background" class="w-full h-full object-cover opacity-20 mix-blend-luminosity grayscale" onerror="this.src='https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?q=80&w=1200&auto=format&fit=crop';">
        <div class="absolute inset-0 bg-gradient-to-b from-[#030303]/90 via-[#030303]/80 to-[#0a0a0c]"></div>
    </div>

    <!-- Abstract Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[50rem] h-[50rem] bg-indigo-600/10 blur-[120px] rounded-full mix-blend-screen pointer-events-none"></div>

    <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto text-center reveal">
        <div class="inline-flex items-center gap-6 mb-8">
            <div class="h-px w-16 bg-white/20"></div>
            <span class="text-white/50 font-sans font-bold text-[0.625rem] tracking-[0.4em] uppercase">Partnership</span>
            <div class="h-px w-16 bg-white/20"></div>
        </div>
        <h1 class="text-5xl md:text-7xl lg:text-9xl font-display font-black text-transparent bg-clip-text bg-gradient-to-br from-white via-neutral-300 to-neutral-600 mb-6 tracking-normal uppercase leading-[0.9]">
            <?= setting('donate.hero_title', '<span class="italic font-light">Give</span> Online') ?>
        </h1>
        <p class="text-xl md:text-2xl text-neutral-400 max-w-3xl mx-auto font-medium leading-relaxed mb-12">
            <?= setting('donate.hero_subtitle', 'Your generosity helps us build the church, preach the Gospel, and make a lasting impact in communities around the world.') ?>
        </p>
        
        <a href="#give-now" class="inline-flex items-center justify-center bg-white text-black hover:bg-amber-500 px-12 py-6 font-bold uppercase tracking-[0.2em] text-sm rounded-full transition-all hover:-translate-y-1 shadow-[0_0_30px_rgba(255,255,255,0.1)] hover:shadow-[0_0_40px_rgba(245,158,11,0.3)]">
            Give Securely Now
            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
</div>

<!-- WAYS TO GIVE SECTION -->
<div class="py-24 md:py-32 bg-[#0a0a0c] relative overflow-hidden">
    <!-- Ambient Glows -->
    <div class="absolute top-0 right-0 w-[50rem] h-[50rem] bg-indigo-600/5 blur-[150px] rounded-full mix-blend-screen pointer-events-none z-0"></div>

    <div class="w-[95%] max-w-[112.5rem] mx-auto relative z-10">
        
        <div class="text-center max-w-3xl mx-auto mb-20 reveal">
            <h2 class="text-4xl md:text-5xl lg:text-7xl font-display font-black text-white uppercase tracking-normal mb-6 leading-[1.0]">3 Ways to <i class="text-amber-500 font-light">Give</i></h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Online -->
            <div class="bg-[#050505] p-10 lg:p-12 rounded-[2.5rem] shadow-2xl border border-white/5 text-center group hover:border-amber-500/30 transition-all duration-700 hover:-translate-y-2 reveal relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-t from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                
                <div class="w-24 h-24 rounded-[1.5rem] bg-white/5 border border-white/10 text-neutral-400 flex items-center justify-center mx-auto mb-8 group-hover:bg-amber-500 group-hover:text-black transition-colors duration-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black uppercase text-white mb-4 tracking-normal leading-none group-hover:text-amber-500 transition-colors">Give Online</h3>
                <p class="text-neutral-400 font-medium leading-relaxed mb-8 flex-grow">
                    Simple and secure. Give a single gift, or schedule recurring giving using your checking account, debit, or credit card.
                </p>
                <a href="#give-now" class="inline-flex items-center justify-center font-sans font-bold uppercase tracking-[0.2em] text-xs text-white bg-white/5 border border-white/10 px-6 py-4 rounded-full hover:bg-amber-500 hover:text-black hover:border-amber-500 transition-all duration-300 w-full group/link">
                    Give Online 
                    <svg class="w-5 h-5 ml-2 transform group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <!-- Bank Transfer -->
            <div class="bg-[#050505] p-10 lg:p-12 rounded-[2.5rem] shadow-2xl border border-white/5 text-center group hover:border-white/10 transition-all duration-700 hover:-translate-y-2 reveal delay-100 relative overflow-hidden flex flex-col">
                <div class="w-24 h-24 rounded-[1.5rem] bg-white/5 border border-white/10 text-neutral-400 flex items-center justify-center mx-auto mb-8 group-hover:bg-white group-hover:text-black transition-colors duration-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black uppercase text-white mb-6 tracking-normal leading-none group-hover:text-white/80 transition-colors">Bank Transfer</h3>
                <div class="bg-black/50 border border-white/5 rounded-2xl p-6 flex-grow">
                    <p class="text-neutral-400 font-medium leading-loose whitespace-pre-wrap text-sm"><?= setting('donate.bank_details', "Bank Name: Faith Bank\nAccount: 1234567890\nBranch: Main Branch") ?></p>
                </div>
            </div>

            <!-- Mobile Money -->
            <div class="bg-[#050505] p-10 lg:p-12 rounded-[2.5rem] shadow-2xl border border-white/5 text-center group hover:border-white/10 transition-all duration-700 hover:-translate-y-2 reveal delay-200 relative overflow-hidden flex flex-col">
                <div class="w-24 h-24 rounded-[1.5rem] bg-white/5 border border-white/10 text-neutral-400 flex items-center justify-center mx-auto mb-8 group-hover:bg-white group-hover:text-black transition-colors duration-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-3xl font-display font-black uppercase text-white mb-6 tracking-normal leading-none group-hover:text-white/80 transition-colors">Mobile Money</h3>
                <div class="bg-black/50 border border-white/5 rounded-2xl p-6 flex-grow">
                    <p class="text-neutral-400 font-medium leading-loose whitespace-pre-wrap text-sm"><?= setting('donate.momo_details', "MTN MoMo: 055 123 4567\nVodafone Cash: 020 123 4567\nName: Bridge Ministries") ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- GIVE NOW WIDGET -->
<?php if (false): // Hidden pending Paystack integration ?>
<div id="give-now" class="py-24 bg-black relative overflow-hidden">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        
        <h2 class="text-4xl md:text-5xl lg:text-7xl font-display font-black text-white uppercase tracking-normal mb-6 leading-[1.0]">Secure Giving Portal</h2>
        <p class="text-lg text-white/60 font-sans font-medium mb-12 max-w-2xl mx-auto">
            Select an amount below or enter a custom amount to proceed to our secure checkout.
        </p>

        <div class="bg-[#111111] p-8 md:p-14 rounded-[2.5rem] shadow-2xl border border-white/5 reveal">
            <!-- Simulated Giving UI -->
            <div class="mb-10">
                <h3 class="text-xs font-bold text-white/40 font-sans uppercase tracking-widest mb-6">Choose Amount</h3>
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <button class="py-5 border-2 border-white/10 rounded-2xl font-sans font-bold text-2xl text-white/40 hover:border-amber-500/50 hover:text-white transition-colors focus:border-amber-500 focus:bg-amber-500/10 focus:text-amber-500">$50</button>
                    <button class="py-5 border-2 border-amber-500 bg-amber-500/10 rounded-2xl font-sans font-bold text-2xl text-amber-500 transition-colors">$100</button>
                    <button class="py-5 border-2 border-white/10 rounded-2xl font-sans font-bold text-2xl text-white/40 hover:border-amber-500/50 hover:text-white transition-colors focus:border-amber-500 focus:bg-amber-500/10 focus:text-amber-500">$250</button>
                </div>
                <div class="relative">
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-white/40 font-sans font-bold text-2xl">$</span>
                    <input type="number" placeholder="Custom Amount" class="w-full bg-black border-2 border-white/10 rounded-2xl py-5 pl-12 pr-6 font-sans font-bold text-2xl text-white focus:outline-none focus:border-amber-500 transition-colors">
                </div>
            </div>

            <button class="w-full bg-white hover:bg-neutral-200 text-black font-sans font-bold uppercase tracking-widest text-sm py-5 rounded-2xl hover:-translate-y-1 transition-all duration-300 mb-8">
                Continue to Payment
            </button>
            
            <div class="flex items-center justify-center gap-3 text-white/40 text-xs font-sans font-bold uppercase tracking-widest">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Encrypted & Secure Transaction</span>
            </div>
        </div>

    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
