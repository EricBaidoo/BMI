<?php
$pageTitle = 'Livestream | Bridge Ministries International';
$pageDescription = 'Watch BMI services live and replay recent recordings.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/csrf.php';

// For now, we will use the setting from the .env or backend as a fallback.
$liveEmbedUrl = setting('live.embed_url', '');

// Auto-convert standard YouTube links to embed format
if (str_contains($liveEmbedUrl, 'youtube.com/watch?v=')) {
    $liveEmbedUrl = str_replace('youtube.com/watch?v=', 'youtube.com/embed/', $liveEmbedUrl);
    $liveEmbedUrl = explode('&', $liveEmbedUrl)[0]; // strip extra parameters
} elseif (str_contains($liveEmbedUrl, 'youtu.be/')) {
    $liveEmbedUrl = str_replace('youtu.be/', 'youtube.com/embed/', $liveEmbedUrl);
    $liveEmbedUrl = explode('?', $liveEmbedUrl)[0]; // strip extra parameters
} elseif (str_contains($liveEmbedUrl, 'facebook.com/') && !str_contains($liveEmbedUrl, 'plugins/video.php')) {
    // Auto-convert standard Facebook video/live links to embed format
    $encodedUrl = urlencode($liveEmbedUrl);
    $liveEmbedUrl = "https://www.facebook.com/plugins/video.php?href={$encodedUrl}&show_text=0";
}

include 'includes/header.php';
?>

<!-- CUSTOM ANIMATION STYLES -->
<!-- LIVE PLATFORM LAYOUT -->
<div class="flex flex-col lg:flex-row h-[calc(100dvh-5rem)] md:h-[calc(100vh-6rem)] mt-20 md:mt-24 bg-[#030303] overflow-hidden relative">
    
    <!-- Ambient Background Glows -->
    <div class="absolute top-0 right-1/4 w-[50rem] h-[50rem] bg-red-600/10 blur-[150px] rounded-full pointer-events-none mix-blend-screen z-0"></div>

    <!-- LEFT SIDE: VIDEO PLAYER AREA -->
    <div class="flex-none lg:flex-grow flex flex-col relative overflow-hidden bg-transparent z-10">
        
        <!-- Video / Offline State Container -->
        <div class="w-full flex items-center justify-center p-0 lg:p-10 flex-grow relative">
            <!-- Backlight for player -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[80%] h-[80%] bg-indigo-600/20 blur-[100px] pointer-events-none mix-blend-screen hidden lg:block"></div>

            <!-- Video Player Container -->
            <div class="relative w-full lg:max-w-6xl mx-auto bg-black lg:rounded-[2.5rem] overflow-hidden aspect-video shadow-[0_0_50px_rgba(0,0,0,0.8)] border-y border-white/10 lg:border border-white/10 lg:ring-1 lg:ring-white/5 gs-reveal-up group">
                <?php if ($liveEmbedUrl !== ''): ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none z-10"></div>
                    <iframe id="main-player" src="<?php echo htmlspecialchars($liveEmbedUrl); ?>" class="absolute inset-0 w-full h-full z-0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                <?php else: ?>
                    <div id="offline-overlay" class="absolute inset-0 flex flex-col items-center justify-center bg-[#050505]">
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent z-10"></div>
                        <?php
                        $offlineThumbnail = setting('live.offline_thumbnail', '');
                        if ($offlineThumbnail !== ''): ?>
                            <img loading="lazy" src="<?php echo rtrim((string)$siteUrl, '/'); ?>/<?php echo htmlspecialchars($offlineThumbnail); ?>" class="absolute inset-0 w-full h-full object-cover opacity-60 mix-blend-luminosity grayscale" alt="Stream Offline">
                        <?php else: ?>
                            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10"></div>
                        <?php endif; ?>
                        
                        <div class="relative z-20 text-center flex flex-col items-center">
                            <div class="w-20 h-20 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mb-8 text-white/30 shadow-inner">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                            <h2 class="text-4xl md:text-5xl font-display font-black text-white mb-4 tracking-normal uppercase">Stream is <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-red-600">Offline</span></h2>
                            <p class="text-neutral-400 font-medium text-lg max-w-md text-center px-4">We are not currently broadcasting. Please check the schedule for our next live service.</p>
                        </div>
                    </div>
                    <iframe id="main-player" src="" class="hidden absolute inset-0 w-full h-full bg-[#000000]" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                <?php endif; ?>

                <!-- REAL-TIME PROMPT OVERLAY -->
                <div id="interactive-prompt-container" class="absolute bottom-8 left-1/2 -translate-x-1/2 z-30 hidden prompt-active w-[90%] max-w-lg bg-black/60 backdrop-blur-2xl border border-white/20 p-6 rounded-[2rem] shadow-2xl">
                    <!-- Content injected via AJAX -->
                </div>
            </div>
        </div>
        
        <!-- Church Name / Below Video -->
        <div class="px-6 py-5 bg-white/[0.02] backdrop-blur-xl flex-shrink-0 border-t border-white/10 relative z-20">
            <h1 class="text-lg md:text-2xl font-display font-black uppercase text-white tracking-widest flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse hidden md:block"></span>
                Bridge Ministries International
            </h1>
        </div>
    </div>

    <!-- RIGHT SIDE: SIDEBAR -->
    <div class="w-full lg:w-[30rem] flex-grow lg:flex-shrink-0 bg-[#0a0a0c] flex flex-col min-h-0 lg:h-full border-l border-white/10 z-20 relative shadow-2xl">
        <!-- Sidebar subtle glow -->
        <div class="absolute inset-0 bg-gradient-to-b from-white/[0.02] to-transparent pointer-events-none"></div>

        <!-- Header -->
        <style>
/* Custom Scrollbar for sidebar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.1); 
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.2); 
}
</style>
<div class="px-8 py-6 border-b border-white/10 flex items-center justify-between relative z-10 bg-black/20 backdrop-blur-md">
            <h2 id="sidebar-title" class="font-display font-black uppercase tracking-widest text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Notes
            </h2>
        </div>

        <!-- Tab Contents Container -->
        <div class="flex-grow overflow-y-auto p-6 md:p-8 relative z-10 custom-scrollbar" id="sidebar-content">
            
            <!-- NOTES TAB CONTENT (Interactive) -->
            <div id="tab-notes" class="block space-y-6">
                <div id="live-notes-container">
                    <div class="text-center py-12 opacity-50">
                        <svg class="w-12 h-12 mx-auto mb-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="font-sans font-medium">Waiting for live notes...</p>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-white/10 flex gap-4">
                    <button id="btn-email-notes" class="flex-1 bg-white/10 hover:bg-white/20 text-white px-4 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Email Notes
                    </button>
                    <button id="btn-print-notes" class="flex-1 bg-white/10 hover:bg-white/20 text-white px-4 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print
                    </button>
                </div>
            </div>

            <!-- SCHEDULE TAB CONTENT -->
            <div id="tab-schedule" class="hidden space-y-10">
                <?php
                $scheduleJson = setting('live.schedule', '[]');
                $events = json_decode($scheduleJson, true) ?: [];

                $now = time();
                $upcomingEvents = [];

                foreach ($events as $event) {
                    $type = $event['type'] ?? 'date';
                    $time = $event['time'] ?? '';
                    if (empty($time)) continue;

                    if ($type === 'weekly') {
                        $day = $event['day'] ?? 'Sunday';
                        // Check if the event is happening today
                        $todayEventTime = strtotime("today $time");
                        $todayName = date('l');
                        $endOfToday = strtotime('tomorrow') - 1;
                        
                        if ($todayName === $day) {
                            // Check if today's occurrence has already completely finished (2 hours past start time)
                            if (($todayEventTime + (2 * 3600)) > $now) {
                                $eventTime = $todayEventTime;
                            } else {
                                // It finished today, so show next week's occurrence
                                $eventTime = strtotime("next $day $time");
                            }
                        } else {
                            // Find the next occurrence
                            $eventTime = strtotime("next $day $time");
                        }
                    } else {
                        if (empty($event['date'])) continue;
                        $eventTime = strtotime($event['date'] . ' ' . $time);
                    }
                    
                    // Check if it was explicitly ended today
                    $lastEnded = $event['last_ended'] ?? '';
                    if ($type === 'weekly' && $lastEnded === date('Y-m-d', $now)) {
                        // The user hit 'End Stream' today, so push this weekly occurrence to next week
                        $eventTime = strtotime("next $day $time");
                    }
                    
                    // Keep events on schedule until 2 hours after they start (in case stream starts late)
                    $twoHoursAfterStart = $eventTime + (2 * 3600);
                    if ($twoHoursAfterStart > $now) {
                        $upcomingEvents[] = [
                            'name' => $event['name'] ?? '',
                            'timestamp' => $eventTime,
                            'date_formatted' => date('l, F j', $eventTime),
                            'time_formatted' => date('g:iA', $eventTime)
                        ];
                    }
                }

                // Sort by timestamp
                usort($upcomingEvents, function($a, $b) {
                    return $a['timestamp'] <=> $b['timestamp'];
                });

                $upNext = count($upcomingEvents) > 0 ? $upcomingEvents[0] : null;

                // Group remaining events by date
                $groupedEvents = [];
                $first = true;
                foreach ($upcomingEvents as $event) {
                    if ($first) {
                        $first = false;
                        continue;
                    }
                    $dateKey = $event['date_formatted'];
                    if (!isset($groupedEvents[$dateKey])) {
                        $groupedEvents[$dateKey] = [];
                    }
                    $groupedEvents[$dateKey][] = $event;
                }
                ?>
                
                <?php if ($upNext): ?>
                <!-- Up Next Card -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Up Next</h3>
                    <div class="bg-[#050505] border border-white/10 rounded-[2rem] p-6 shadow-2xl relative overflow-hidden group" id="up-next-card" data-timestamp="<?php echo $upNext['timestamp']; ?>">
                        <div class="absolute inset-0 bg-gradient-to-r from-amber-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500 rounded-l-[2rem]"></div>
                        
                        <p class="font-display font-black text-3xl text-white leading-none tracking-normal mb-2"><?php echo htmlspecialchars($upNext['time_formatted']); ?></p>
                        <p class="text-sm text-neutral-400 font-medium mb-8"><?php echo htmlspecialchars($upNext['name']); ?></p>
                        
                        <div class="flex items-center justify-between gap-2 md:gap-4 text-center bg-white/5 border border-white/10 rounded-[1.25rem] p-4 shadow-inner">
                            <div class="flex flex-col items-center flex-1">
                                <span class="text-2xl font-black text-white" id="countdown-days">0</span>
                                <span class="text-[0.625rem] font-bold text-white/40 uppercase tracking-[0.2em] mt-1">Days</span>
                            </div>
                            <div class="w-px h-8 bg-white/10"></div>
                            <div class="flex flex-col items-center flex-1">
                                <span class="text-2xl font-black text-white" id="countdown-hours">00</span>
                                <span class="text-[0.625rem] font-bold text-white/40 uppercase tracking-[0.2em] mt-1">Hrs</span>
                            </div>
                            <div class="w-px h-8 bg-white/10"></div>
                            <div class="flex flex-col items-center flex-1">
                                <span class="text-2xl font-black text-white" id="countdown-mins">00</span>
                                <span class="text-[0.625rem] font-bold text-white/40 uppercase tracking-[0.2em] mt-1">Min</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-12 bg-white/5 border border-white/10 rounded-[2rem]">
                    <p class="text-neutral-400 font-medium">No upcoming scheduled events.</p>
                </div>
                <?php endif; ?>

                <!-- Future Events List -->
                <div class="space-y-8">
                    <?php foreach ($groupedEvents as $dateStr => $dayEvents): ?>
                    <div>
                        <p class="text-xs font-bold text-white/40 uppercase tracking-widest mb-4 flex items-center gap-3">
                            <?php echo htmlspecialchars($dateStr); ?>
                            <span class="flex-grow h-px bg-white/10"></span>
                        </p>
                        <div class="space-y-3">
                            <?php foreach ($dayEvents as $event): ?>
                            <div class="bg-[#050505] border border-white/5 rounded-2xl p-5 hover:border-amber-500/30 transition-colors duration-300">
                                <p class="font-display font-black text-xl text-white tracking-normal mb-1"><?php echo htmlspecialchars($event['time_formatted']); ?></p>
                                <p class="text-sm text-neutral-400 font-medium"><?php echo htmlspecialchars($event['name']); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const card = document.getElementById('up-next-card');
                if (!card) return;
                
                const targetTimestamp = parseInt(card.getAttribute('data-timestamp')) * 1000;
                const daysEl = document.getElementById('countdown-days');
                const hoursEl = document.getElementById('countdown-hours');
                const minsEl = document.getElementById('countdown-mins');
                
                function updateCountdown() {
                    const now = new Date().getTime();
                    const distance = targetTimestamp - now;
                    
                    if (distance < 0) {
                        daysEl.innerText = "0";
                        hoursEl.innerText = "00";
                        minsEl.innerText = "00";
                        return;
                    }
                    
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    
                    daysEl.innerText = days;
                    hoursEl.innerText = hours.toString().padStart(2, '0');
                    minsEl.innerText = minutes.toString().padStart(2, '0');
                }
                
                updateCountdown();
                setInterval(updateCountdown, 60000); // update every minute
            });
            </script>

            <!-- PRAY TAB CONTENT -->
            <div id="tab-pray" class="hidden space-y-4">
                
                <!-- Submit Prayer Request -->
                <a href="contact" class="block bg-[#000000] border border-white/10 rounded-[1.5rem] p-5 shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:border-amber-500/50 transition-all group">
                    <div class="flex items-start gap-4">
                        <div class="text-white flex-shrink-0 mt-0.5 relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-grow">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-[0.9375rem] text-white group-hover:text-amber-500 transition-colors">Submit Prayer Request</h3>
                                <svg class="w-4 h-4 text-white/40 group-hover:text-amber-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                            <p class="text-[0.8125rem] text-white/60 mt-2 leading-relaxed">Let us know how we can pray for you and our team will reach out.</p>
                        </div>
                    </div>
                </a>

            </div>
            
            <!-- PAST SERVICES TAB CONTENT -->
            <div id="tab-past" class="hidden space-y-4">
                <?php
                $pastJson = setting('live.past_services', '[]');
                $replays = json_decode($pastJson, true) ?: [];
                if (count($replays) === 0):
                ?>
                <div class="text-center py-8">
                    <p class="text-white/50 text-sm font-medium">No past services available.</p>
                </div>
                <?php else: ?>
                    <?php foreach ($replays as $replay): 
                        $replayUrl = $replay['url'];
                        if (str_contains($replayUrl, 'youtube.com/watch?v=')) {
                            $replayUrl = str_replace('youtube.com/watch?v=', 'youtube.com/embed/', $replayUrl);
                            $replayUrl = explode('&', $replayUrl)[0];
                        } elseif (str_contains($replayUrl, 'youtu.be/')) {
                            $replayUrl = str_replace('youtu.be/', 'youtube.com/embed/', $replayUrl);
                            $replayUrl = explode('?', $replayUrl)[0];
                        } elseif (str_contains($replayUrl, 'facebook.com/') && !str_contains($replayUrl, 'plugins/video.php')) {
                            $encodedUrl = urlencode($replayUrl);
                            $replayUrl = "https://www.facebook.com/plugins/video.php?href={$encodedUrl}&show_text=0";
                        }
                    ?>
                        <button onclick="playPastService('<?php echo htmlspecialchars($replayUrl); ?>')" class="w-full text-left block bg-[#000000] border border-white/10 rounded-2xl p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:border-red-500/50 transition-all group">
                            <div class="flex items-start gap-4">
                                <div class="text-white/40 group-hover:text-red-600 transition-colors flex-shrink-0 mt-0.5">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-[0.9375rem] text-white group-hover:text-red-600 transition-colors"><?php echo htmlspecialchars($replay['title']); ?></h3>
                                    <p class="text-xs text-white/50 font-medium mt-1 uppercase tracking-wider"><?php echo date('F j, Y', strtotime($replay['date'])); ?></p>
                                </div>
                            </div>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>

        <!-- Bottom Navigation Tabs -->
        <div class="flex items-center justify-between border-t border-white/10 bg-[#000000] px-2 py-3 flex-shrink-0 shadow-[0_-4px_10px_rgba(0,0,0,0.02)]">
            <!-- Notes Tab (Active by Default) -->
            <button onclick="switchTab('notes')" id="btn-notes" class="flex flex-col items-center justify-center w-1/4 text-white transition-colors">
                <div class="flex flex-col items-center border-b-[0.1875rem] border-white pb-1 -mb-1 px-2" id="border-notes">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span class="text-[0.5625rem] font-bold uppercase tracking-wider">Notes</span>
                </div>
            </button>
            <button onclick="switchTab('pray')" id="btn-pray" class="flex flex-col items-center justify-center w-1/4 text-white/40 hover:text-white transition-colors">
                <div class="flex flex-col items-center border-b-[0.1875rem] border-transparent pb-1 -mb-1 px-2" id="border-pray">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="text-[0.5625rem] font-bold uppercase tracking-wider">Pray</span>
                </div>
            </button>
            <button onclick="switchTab('schedule')" id="btn-schedule" class="flex flex-col items-center justify-center w-1/4 text-white/40 transition-colors">
                <div class="flex flex-col items-center border-b-[0.1875rem] border-transparent pb-1 -mb-1 px-2" id="border-schedule">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-[0.5625rem] font-bold uppercase tracking-wider">Schedule</span>
                </div>
            </button>
            <button onclick="switchTab('past')" id="btn-past" class="flex flex-col items-center justify-center w-1/4 text-white/40 hover:text-white transition-colors">
                <div class="flex flex-col items-center border-b-[0.1875rem] border-transparent pb-1 -mb-1 px-2" id="border-past">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[0.5625rem] font-bold uppercase tracking-wider">Past</span>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
// --- TAB LOGIC ---
const tabTitles = {
    'notes': '<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Notes',
    'pray': '<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg> Pray',
    'schedule': '<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Schedule',
    'past': '<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Past'
};

function switchTab(tab) {
    document.getElementById('sidebar-title').innerHTML = tabTitles[tab];

    // Hide all tabs
    document.getElementById('tab-notes').classList.add('hidden');
    document.getElementById('tab-pray').classList.add('hidden');
    document.getElementById('tab-schedule').classList.add('hidden');
    document.getElementById('tab-past').classList.add('hidden');
    
    // Reset buttons
    document.getElementById('btn-notes').classList.replace('text-white', 'text-white/40');
    document.getElementById('btn-pray').classList.replace('text-white', 'text-white/40');
    document.getElementById('btn-schedule').classList.replace('text-white', 'text-white/40');
    document.getElementById('btn-past').classList.replace('text-white', 'text-white/40');
    
    document.getElementById('border-notes').classList.replace('border-white', 'border-transparent');
    document.getElementById('border-pray').classList.replace('border-white', 'border-transparent');
    document.getElementById('border-schedule').classList.replace('border-white', 'border-transparent');
    document.getElementById('border-past').classList.replace('border-white', 'border-transparent');

    // Show active tab
    document.getElementById('tab-' + tab).classList.remove('hidden');
    document.getElementById('btn-' + tab).classList.replace('text-white/40', 'text-white');
    document.getElementById('border-' + tab).classList.replace('border-transparent', 'border-white');
}

function playPastService(url) {
    const iframe = document.getElementById('main-player');
    if (iframe) {
        iframe.src = url;
        iframe.classList.remove('hidden');
        const offlineOverlay = document.getElementById('offline-overlay');
        if (offlineOverlay) offlineOverlay.classList.add('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// --- INTERACTIVE ENGINE (POLLING LOGIC) ---
document.addEventListener('DOMContentLoaded', () => {
    let lastPromptHtml = '';
    let lastNotesHtml = '';
    const pollInterval = 15000; // 15 seconds (+ up to 5s random offset)
    
    function fetchLiveState() {
        fetch('api/live_state')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update Prompt Overlay
                    const promptContainer = document.getElementById('interactive-prompt-container');
                    if (data.current_prompt_html && data.current_prompt_html.trim() !== '') {
                        if (data.current_prompt_html !== lastPromptHtml) {
                            promptContainer.innerHTML = data.current_prompt_html;
                            promptContainer.classList.remove('hidden');
                            // Re-trigger animation
                            promptContainer.classList.remove('prompt-active');
                            void promptContainer.offsetWidth; // trigger reflow
                            promptContainer.classList.add('prompt-active');
                            lastPromptHtml = data.current_prompt_html;
                        }
                    } else {
                        promptContainer.classList.add('hidden');
                        lastPromptHtml = '';
                    }

                    // Update Notes
                    const notesContainer = document.getElementById('live-notes-container');
                    if (data.current_notes_html && data.current_notes_html.trim() !== '') {
                        if (data.current_notes_html !== lastNotesHtml) {
                            // Save user input values before replacing DOM
                            const oldInputs = notesContainer.querySelectorAll('.notes-input, .notes-textarea');
                            const savedValues = [];
                            oldInputs.forEach(input => savedValues.push(input.value));
                            
                            notesContainer.innerHTML = data.current_notes_html;
                            
                            // Restore user input values
                            const newInputs = notesContainer.querySelectorAll('.notes-input, .notes-textarea');
                            newInputs.forEach((input, index) => {
                                if (savedValues[index]) {
                                    input.value = savedValues[index];
                                }
                            });
                            
                            lastNotesHtml = data.current_notes_html;
                        }
                    } else if (lastNotesHtml !== '') {
                        notesContainer.innerHTML = `
                        <div class="text-center py-12 opacity-50">
                            <p class="font-sans font-medium">No active notes right now.</p>
                        </div>`;
                        lastNotesHtml = '';
                    }
                }
            })
            .catch(err => console.error('Live State Sync Error:', err));
    }

    // Initial fetch, then poll only while the tab is visible. A random offset spreads
    // thousands of viewers' requests out instead of hitting the server at the same moment.
    fetchLiveState();
    const schedule = () => setTimeout(() => {
        if (!document.hidden) fetchLiveState();
        schedule();
    }, pollInterval + Math.floor(Math.random() * 5000));
    schedule();
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) fetchLiveState();
    });
    // Print Logic
    const btnPrint = document.getElementById('btn-print-notes');
    if (btnPrint) {
        btnPrint.addEventListener('click', () => {
            const notesContent = document.getElementById('live-notes-container').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Sermon Notes - Bridge Ministries</title>
                    <style>
                        body { font-family: sans-serif; padding: 20px; color: black; background: white; }
                        h1, h2, h3 { color: #333; }
                        .notes-input, .notes-textarea { 
                            border: none; border-bottom: 1px solid #ccc; width: 100%; 
                            font-family: inherit; font-size: inherit; margin: 10px 0;
                        }
                    </style>
                </head>
                <body>
                    <h2>Sermon Notes</h2>
                    ${notesContent}
                    <script>
                        // Copy values from parent window
                        const originalInputs = window.opener.document.querySelectorAll('#live-notes-container .notes-input, #live-notes-container .notes-textarea');
                        const newInputs = document.querySelectorAll('.notes-input, .notes-textarea');
                        newInputs.forEach((input, i) => {
                            if (originalInputs[i]) {
                                input.value = originalInputs[i].value;
                                // Convert input to text for printing
                                const text = document.createElement('span');
                                text.style.textDecoration = 'underline';
                                text.innerText = input.value || '________________';
                                input.parentNode.replaceChild(text, input);
                            }
                        });
                        window.print();
                        window.close();
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        });
    }

    // Email Logic
    const btnEmail = document.getElementById('btn-email-notes');
    if (btnEmail) {
        btnEmail.addEventListener('click', () => {
            const email = prompt("Enter your email address to receive these notes:");
            if (!email) return;

            // The server builds the email from its own copy of the notes; send only the typed answers
            const answers = document.querySelectorAll('#live-notes-container .notes-input, #live-notes-container .notes-textarea');

            const btnText = btnEmail.innerHTML;
            btnEmail.innerHTML = "Sending...";
            btnEmail.disabled = true;

            const formData = new FormData();
            formData.append('action', 'email_notes');
            formData.append('email', email);
            formData.append('csrf_token', <?= json_encode(csrf_token()) ?>);
            answers.forEach(input => formData.append('answers[]', input.value));

            fetch('api/live_state', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Notes sent successfully!');
                } else {
                    alert('Failed to send notes: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => {
                alert('An error occurred while sending.');
                console.error(err);
            })
            .finally(() => {
                btnEmail.innerHTML = btnText;
                btnEmail.disabled = false;
            });
        });
    }
});
</script>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
