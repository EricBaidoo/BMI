<?php
$pageTitle = 'Livestream | Bridge Ministries International';
$pageDescription = 'Watch BMI services live and replay recent recordings.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/settings.php';

// For now, we will use the setting from the .env or backend as a fallback.
$liveEmbedUrl = ''; // Temporarily hardcoded to empty string to test offline graphic


include 'includes/header.php';
?>

<!-- LIVE PLATFORM LAYOUT -->
<div class="flex flex-col lg:flex-row h-[calc(100vh-5rem)] md:h-[calc(100vh-6rem)] mt-20 md:mt-24 bg-[#0f0f11] overflow-hidden">
    
    <!-- LEFT SIDE: VIDEO PLAYER AREA -->
    <div class="flex-grow flex flex-col relative overflow-hidden bg-black">
        
        <!-- Video / Offline State Container (Centers the 16:9 content) -->
        <div class="flex-grow flex items-center justify-center p-4 lg:p-8">
            <div class="w-full aspect-video max-h-full relative shadow-2xl bg-[#1a1a1f] overflow-hidden rounded-md">
                
                <?php if ($liveEmbedUrl !== ''): ?>
                    <iframe src="<?php echo htmlspecialchars($liveEmbedUrl); ?>" class="absolute inset-0 w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                <?php else: ?>
                    <!-- FOOLPROOF NEON SIGN OFFLINE GRAPHIC -->
                    <style>
                        .offline-bg {
                            background-color: #06080f;
                            background-image: 
                                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
                            background-size: 40px 40px;
                        }
                        .neon-box {
                            border: 3px solid #facc15;
                            border-radius: 16px;
                            box-shadow: 0 0 15px rgba(250, 204, 21, 0.5), inset 0 0 15px rgba(250, 204, 21, 0.5);
                            padding: 2.5rem 3.5rem;
                            position: relative;
                            z-index: 10;
                            background-color: rgba(6, 8, 15, 0.8);
                        }
                        .neon-inner-box {
                            position: absolute;
                            top: 8px; left: 8px; right: 8px; bottom: 8px;
                            border: 1px solid rgba(250, 204, 21, 0.4);
                            border-radius: 10px;
                            pointer-events: none;
                        }
                        .neon-text-cyan {
                            color: #22d3ee;
                            text-shadow: 0 0 8px #22d3ee, 0 0 16px #22d3ee;
                            font-size: 2rem;
                            font-weight: 600;
                            letter-spacing: 0.15em;
                            margin-bottom: 0.5rem;
                        }
                        .neon-text-pink {
                            color: #ec4899;
                            text-shadow: 0 0 10px #ec4899, 0 0 25px #ec4899, 0 0 40px #ec4899;
                            font-size: 5.5rem;
                            font-weight: 900;
                            letter-spacing: 0.1em;
                            line-height: 1;
                        }
                        .center-glow {
                            position: absolute;
                            top: 50%; left: 50%;
                            transform: translate(-50%, -50%);
                            width: 300px; height: 300px;
                            background: rgba(236, 72, 153, 0.15);
                            filter: blur(80px);
                            border-radius: 50%;
                        }
                    </style>
                    <div class="absolute inset-0 offline-bg flex flex-col items-center justify-center overflow-hidden font-display">
                        
                        <div class="center-glow"></div>

                        <div class="neon-box text-center">
                            <div class="neon-inner-box"></div>
                            
                            <p class="neon-text-cyan uppercase m-0">
                                SORRY WE ARE
                            </p>
                            
                            <h2 class="neon-text-pink uppercase m-0">
                                OFFLINE
                            </h2>
                        </div>

                        <p class="absolute bottom-10 text-slate-500 text-sm tracking-widest uppercase font-medium m-0">
                            Check the schedule for our next service
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Church Name / Below Video -->
        <div class="px-6 py-4 bg-[#0a0a0c] flex-shrink-0 border-t border-white/5">
            <h1 class="text-lg md:text-xl font-bold text-white tracking-wide">Bridge Ministries International</h1>
        </div>
    </div>

    <!-- RIGHT SIDE: SIDEBAR -->
    <div class="w-full lg:w-[400px] flex-shrink-0 bg-[#f8f9fa] flex flex-col h-[50vh] lg:h-full border-l border-slate-200 z-20">
        
        <!-- Tab Contents Container -->
        <div class="flex-grow overflow-y-auto p-4 md:p-5 relative bg-[#f8f9fa]" id="sidebar-content">
            
            <!-- SCHEDULE TAB CONTENT -->
            <div id="tab-schedule" class="space-y-6 block">
                <!-- Up Next Card -->
                <div>
                    <div class="bg-white border border-slate-100 rounded-xl p-5 shadow-[0_2px_10px_rgba(0,0,0,0.04)] relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-slate-900"></div>
                        <p class="font-black text-xl text-slate-900 leading-none">6:00PM</p>
                        <p class="text-[13px] text-slate-500 font-medium mt-1 mb-5">Restful Living</p>
                        
                        <div class="flex items-center justify-center gap-4 md:gap-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xl font-black text-slate-900">2</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Days</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-xl font-black text-slate-900">20</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Hours</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-xl font-black text-slate-900">51</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Mins</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Future Events List -->
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 pl-1">Wednesday, June 3</p>
                    <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                        <p class="font-bold text-[15px] text-slate-900">6:00PM</p>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Restful Living</p>
                    </div>
                </div>
                
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 pl-1 mt-6">Sunday, June 7</p>
                    <div class="space-y-2">
                        <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                            <p class="font-bold text-[15px] text-slate-900">8:30AM</p>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">Celebration Service</p>
                        </div>
                        <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                            <p class="font-bold text-[15px] text-slate-900">5:00PM</p>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">Evening Gathering</p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 pl-1 mt-6">Wednesday, June 10</p>
                    <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                        <p class="font-bold text-[15px] text-slate-900">6:00PM</p>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Restful Living</p>
                    </div>
                </div>
            </div>

            <!-- PRAY TAB CONTENT -->
            <div id="tab-pray" class="hidden space-y-4">
                
                <!-- Submit Prayer Request -->
                <a href="contact" class="block bg-white border border-slate-200 rounded-xl p-5 shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md transition-shadow group">
                    <div class="flex items-start gap-4">
                        <div class="text-slate-900 flex-shrink-0 mt-0.5 relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <div class="absolute -right-2 -bottom-1 bg-white rounded-full">
                                <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>
                        </div>
                        <div class="flex-grow">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-[15px] text-slate-900 group-hover:text-teal-700 transition-colors">Submit Prayer Request</h3>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-teal-700 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                            <p class="text-[13px] text-slate-600 mt-2 leading-relaxed">Let us know how we can pray for you and our team will reach out via email with a prayer and helpful next steps.</p>
                        </div>
                    </div>
                </a>

            </div>
            
        </div>

        <!-- Bottom Navigation Tabs -->
        <div class="flex items-center justify-between border-t border-slate-200 bg-white px-2 py-3 flex-shrink-0 shadow-[0_-4px_10px_rgba(0,0,0,0.02)]">
            <button onclick="switchTab('pray')" id="btn-pray" class="flex flex-col items-center justify-center w-1/2 text-slate-400 hover:text-slate-900 transition-colors">
                <div class="flex flex-col items-center border-b-[3px] border-transparent pb-1 -mb-1 px-2" id="border-pray">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="text-[9px] font-bold uppercase tracking-wider">Pray</span>
                </div>
            </button>
            <button onclick="switchTab('schedule')" id="btn-schedule" class="flex flex-col items-center justify-center w-1/2 text-slate-900 transition-colors">
                <div class="flex flex-col items-center border-b-[3px] border-slate-900 pb-1 -mb-1 px-2" id="border-schedule">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-[9px] font-bold uppercase tracking-wider">Schedule</span>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    // Hide all tabs
    document.getElementById('tab-pray').classList.add('hidden');
    document.getElementById('tab-schedule').classList.add('hidden');
    
    // Reset buttons
    document.getElementById('btn-pray').classList.replace('text-slate-900', 'text-slate-400');
    document.getElementById('btn-schedule').classList.replace('text-slate-900', 'text-slate-400');
    document.getElementById('border-pray').classList.replace('border-slate-900', 'border-transparent');
    document.getElementById('border-schedule').classList.replace('border-slate-900', 'border-transparent');

    // Show active tab
    document.getElementById('tab-' + tab).classList.remove('hidden');
    document.getElementById('btn-' + tab).classList.replace('text-slate-400', 'text-slate-900');
    document.getElementById('border-' + tab).classList.replace('border-transparent', 'border-slate-900');
}
</script>
    </div>
</div>
