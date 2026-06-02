<?php
$pageTitle = 'Give Online | Bridge Ministries International';
$pageDescription = 'Partner with us financially to spread the Gospel and empower communities globally.';

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
        <img src="<?= setting('donate.hero_bg_image', 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?q=80&w=1200&auto=format&fit=crop') ?>" alt="Donate Background" class="w-full h-full object-cover opacity-10" onerror="this.src='https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?q=80&w=1200&auto=format&fit=crop';">
        <div class="absolute inset-0 bg-gradient-to-t from-[#06080f] via-[#06080f]/80 to-transparent"></div>
    </div>

    <!-- Abstract Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[50rem] h-[50rem] bg-[#c49a45]/10 blur-[7.5rem] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-[#c49a45]/30 to-transparent"></div>

    <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto text-center">
        <h1 class="text-5xl md:text-7xl font-display font-black text-white tracking-tight mb-6">
            <?= setting('donate.hero_title', 'Give Online') ?>
        </h1>
        <p class="text-xl text-slate-300 max-w-2xl mx-auto font-medium mb-10">
            <?= setting('donate.hero_subtitle', 'Your generosity helps us build the church, preach the Gospel, and make a lasting impact in communities around the world.') ?>
        </p>
        
        <a href="#give-now" class="inline-flex items-center justify-center px-8 py-4 bg-[#c49a45] hover:bg-[#d4ac57] text-white font-bold  transition-all duration-300 hover:-translate-y-1 shadow-[0_4px_20px_rgba(196,154,69,0.3)] text-lg">
            Give Securely Now
        </a>
    </div>
</div>

<!-- WAYS TO GIVE SECTION -->
<div class="py-24 bg-white relative">
    <div class="w-[90%] max-w-[112.5rem] mx-auto">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl md:text-5xl font-display font-black text-slate-900 tracking-tight mb-6">3 Ways to Give</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Online -->
            <div class="bg-slate-50 p-10  border border-slate-100 text-center shadow-sm hover:shadow-md transition-shadow group">
                <div class="w-16 h-16  bg-white border border-slate-100 text-[#c49a45] flex items-center justify-center mx-auto mb-6 shadow-sm group-hover:bg-[#c49a45] group-hover:text-white transition-colors duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Give Online</h3>
                <p class="text-slate-600 font-medium leading-relaxed mb-6">
                    Simple and secure. Give a single gift, or schedule recurring giving using your checking account, debit, or credit card.
                </p>
                <a href="#give-now" class="text-[#c49a45] font-bold hover:text-[#d4ac57] flex items-center justify-center">
                    Give Online <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <!-- Bank Transfer -->
            <div class="bg-slate-50 p-10  border border-slate-100 text-center shadow-sm hover:shadow-md transition-shadow group">
                <div class="w-16 h-16  bg-white border border-slate-100 text-[#c49a45] flex items-center justify-center mx-auto mb-6 shadow-sm group-hover:bg-[#c49a45] group-hover:text-white transition-colors duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Bank Transfer</h3>
                <p class="text-slate-600 font-medium leading-relaxed mb-6 whitespace-pre-wrap"><?= setting('donate.bank_details', "Bank Name: Faith Bank\nAccount: 1234567890\nBranch: Main Branch") ?></p>
            </div>

            <!-- Mobile Money -->
            <div class="bg-slate-50 p-10  border border-slate-100 text-center shadow-sm hover:shadow-md transition-shadow group">
                <div class="w-16 h-16  bg-white border border-slate-100 text-[#c49a45] flex items-center justify-center mx-auto mb-6 shadow-sm group-hover:bg-[#c49a45] group-hover:text-white transition-colors duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Mobile Money</h3>
                <p class="text-slate-600 font-medium leading-relaxed mb-6 whitespace-pre-wrap"><?= setting('donate.momo_details', "MTN MoMo: 055 123 4567\nVodafone Cash: 020 123 4567\nName: Bridge Ministries") ?></p>
            </div>
        </div>
    </div>
</div>

<!-- GIVE NOW WIDGET (Placeholder) -->
<div id="give-now" class="py-24 bg-slate-900 relative overflow-hidden">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        
        <h2 class="text-3xl md:text-5xl font-display font-black text-white tracking-tight mb-6">Secure Giving Portal</h2>
        <p class="text-lg text-slate-400 font-medium mb-12">
            Select an amount below or enter a custom amount to proceed to our secure checkout.
        </p>

        <div class="bg-white p-8 md:p-12  shadow-2xl">
            <!-- Simulated Giving UI -->
            <div class="mb-8">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Choose Amount</h3>
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <button class="py-4 border-2 border-slate-200  font-bold text-xl text-slate-700 hover:border-[#c49a45] hover:text-[#c49a45] transition-colors focus:border-[#c49a45] focus:bg-[#c49a45]/5 focus:text-[#c49a45]">$50</button>
                    <button class="py-4 border-2 border-[#c49a45] bg-[#c49a45]/5  font-bold text-xl text-[#c49a45] transition-colors">$100</button>
                    <button class="py-4 border-2 border-slate-200  font-bold text-xl text-slate-700 hover:border-[#c49a45] hover:text-[#c49a45] transition-colors focus:border-[#c49a45] focus:bg-[#c49a45]/5 focus:text-[#c49a45]">$250</button>
                </div>
                <div class="relative">
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xl">$</span>
                    <input type="number" placeholder="Custom Amount" class="w-full bg-slate-50 border-2 border-slate-200  py-4 pl-12 pr-6 font-bold text-xl text-slate-900 focus:outline-none focus:border-[#c49a45] transition-colors">
                </div>
            </div>

            <button class="w-full bg-[#c49a45] hover:bg-[#d4ac57] text-white font-bold text-xl py-5  shadow-lg hover:-translate-y-1 transition-all duration-300 mb-6">
                Continue to Payment
            </button>
            
            <div class="flex items-center justify-center gap-2 text-slate-400 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Encrypted & Secure Transaction</span>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
