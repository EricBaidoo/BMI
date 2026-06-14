<?php
$pageTitle = 'Livestream | Bridge Ministries International';
$pageDescription = 'Watch BMI services live and replay recent recordings.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/settings.php';

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

<!-- LIVE PLATFORM LAYOUT -->
<div class="flex flex-col lg:flex-row h-[calc(100vh-5rem)] md:h-[calc(100vh-6rem)] mt-20 md:mt-24 bg-[#0f0f11] overflow-hidden">
    
    <!-- LEFT SIDE: VIDEO PLAYER AREA -->
    <div class="flex-grow flex flex-col relative overflow-hidden bg-black">
        
        <!-- Video / Offline State Container (Centers the 16:9 content) -->
        <div class="flex-grow flex items-center justify-center p-4 lg:p-8">
            <!-- Video Player Container -->
            <div class="relative w-full bg-slate-900 rounded-xl overflow-hidden shadow-2xl aspect-video ring-1 ring-white/10">
                <?php if ($liveEmbedUrl !== ''): ?>
                    <iframe id="main-player" src="<?php echo htmlspecialchars($liveEmbedUrl); ?>" class="absolute inset-0 w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                <?php else: ?>
                    <div id="offline-overlay" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900">
                        <?php
                        $offlineThumbnail = setting('live.offline_thumbnail', '');
                        if ($offlineThumbnail !== ''): ?>
                            <img src="/BMI/<?php echo htmlspecialchars($offlineThumbnail); ?>" class="absolute inset-0 w-full h-full object-cover" alt="Stream Offline">
                        <?php else: ?>
                            <svg class="w-16 h-16 text-slate-700 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <h2 class="text-2xl font-bold text-white mb-3 tracking-tight">Stream is Offline</h2>
                            <p class="text-slate-400 text-sm md:text-base max-w-sm text-center px-4">We are not currently broadcasting. Please check the schedule for our next live service.</p>
                        <?php endif; ?>
                    </div>
                    <iframe id="main-player" src="" class="hidden absolute inset-0 w-full h-full bg-slate-900" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Church Name / Below Video -->
        <div class="px-6 py-4 bg-[#0a0a0c] flex-shrink-0 border-t border-white/5">
            <h1 class="text-lg md:text-xl font-bold text-white tracking-wide">Bridge Ministries International</h1>
        </div>
    </div>

    <!-- RIGHT SIDE: SIDEBAR -->
    <div class="w-full lg:w-[25rem] flex-shrink-0 bg-[#f8f9fa] flex flex-col h-[50vh] lg:h-full border-l border-slate-200 z-20">
        
        <!-- Tab Contents Container -->
        <div class="flex-grow overflow-y-auto p-4 md:p-5 relative bg-[#f8f9fa]" id="sidebar-content">
            
            <!-- SCHEDULE TAB CONTENT -->
            <div id="tab-schedule" class="space-y-6 block">
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
                    <div class="bg-white border border-slate-100  p-5 shadow-[0_2px_10px_rgba(0,0,0,0.04)] relative overflow-hidden" id="up-next-card" data-timestamp="<?php echo $upNext['timestamp']; ?>">
                        <div class="absolute top-0 left-0 w-1 h-full bg-slate-900"></div>
                        <p class="font-black text-xl text-slate-900 leading-none"><?php echo htmlspecialchars($upNext['time_formatted']); ?></p>
                        <p class="text-[0.8125rem] text-slate-500 font-medium mt-1 mb-5"><?php echo htmlspecialchars($upNext['name']); ?></p>
                        
                        <div class="flex items-center justify-center gap-4 md:gap-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xl font-black text-slate-900" id="countdown-days">0</span>
                                <span class="text-[0.5625rem] font-bold text-slate-400 uppercase tracking-widest mt-1">Days</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-xl font-black text-slate-900" id="countdown-hours">00</span>
                                <span class="text-[0.5625rem] font-bold text-slate-400 uppercase tracking-widest mt-1">Hours</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-xl font-black text-slate-900" id="countdown-mins">00</span>
                                <span class="text-[0.5625rem] font-bold text-slate-400 uppercase tracking-widest mt-1">Mins</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-8">
                    <p class="text-slate-500 text-sm font-medium">No upcoming scheduled events.</p>
                </div>
                <?php endif; ?>

                <!-- Future Events List -->
                <?php foreach ($groupedEvents as $dateStr => $dayEvents): ?>
                <div>
                    <p class="text-[0.6875rem] font-bold text-slate-400 uppercase tracking-wider mb-2 pl-1 mt-6"><?php echo htmlspecialchars($dateStr); ?></p>
                    <div class="space-y-2">
                        <?php foreach ($dayEvents as $event): ?>
                        <div class="bg-white border border-slate-100  p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                            <p class="font-bold text-[0.9375rem] text-slate-900"><?php echo htmlspecialchars($event['time_formatted']); ?></p>
                            <p class="text-xs text-slate-500 font-medium mt-0.5"><?php echo htmlspecialchars($event['name']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
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
                <a href="contact" class="block bg-white border border-slate-200  p-5 shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md transition-shadow group">
                    <div class="flex items-start gap-4">
                        <div class="text-slate-900 flex-shrink-0 mt-0.5 relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <div class="absolute -right-2 -bottom-1 bg-white rounded-full">
                                <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>
                        </div>
                        <div class="flex-grow">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-[0.9375rem] text-slate-900 group-hover:text-teal-700 transition-colors">Submit Prayer Request</h3>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-teal-700 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                            <p class="text-[0.8125rem] text-slate-600 mt-2 leading-relaxed">Let us know how we can pray for you and our team will reach out via email with a prayer and helpful next steps.</p>
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
                    <p class="text-slate-500 text-sm font-medium">No past services available.</p>
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
                        <button onclick="playPastService('<?php echo htmlspecialchars($replayUrl); ?>')" class="w-full text-left block bg-white border border-slate-200 p-4 shadow-[0_2px_10px_rgba(0,0,0,0.02)] hover:shadow-md transition-shadow group">
                            <div class="flex items-start gap-4">
                                <div class="text-slate-400 group-hover:text-red-600 transition-colors flex-shrink-0 mt-0.5">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-[0.9375rem] text-slate-900 group-hover:text-red-600 transition-colors"><?php echo htmlspecialchars($replay['title']); ?></h3>
                                    <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider"><?php echo date('F j, Y', strtotime($replay['date'])); ?></p>
                                </div>
                            </div>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>

        <!-- Bottom Navigation Tabs -->
        <div class="flex items-center justify-between border-t border-slate-200 bg-white px-2 py-3 flex-shrink-0 shadow-[0_-4px_10px_rgba(0,0,0,0.02)]">
            <button onclick="switchTab('pray')" id="btn-pray" class="flex flex-col items-center justify-center w-1/3 text-slate-400 hover:text-slate-900 transition-colors">
                <div class="flex flex-col items-center border-b-[0.1875rem] border-transparent pb-1 -mb-1 px-2" id="border-pray">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="text-[0.5625rem] font-bold uppercase tracking-wider">Pray</span>
                </div>
            </button>
            <button onclick="switchTab('schedule')" id="btn-schedule" class="flex flex-col items-center justify-center w-1/3 text-slate-900 transition-colors">
                <div class="flex flex-col items-center border-b-[0.1875rem] border-slate-900 pb-1 -mb-1 px-2" id="border-schedule">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-[0.5625rem] font-bold uppercase tracking-wider">Schedule</span>
                </div>
            </button>
            <button onclick="switchTab('past')" id="btn-past" class="flex flex-col items-center justify-center w-1/3 text-slate-400 hover:text-slate-900 transition-colors">
                <div class="flex flex-col items-center border-b-[0.1875rem] border-transparent pb-1 -mb-1 px-2" id="border-past">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[0.5625rem] font-bold uppercase tracking-wider">Past</span>
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
    document.getElementById('tab-past').classList.add('hidden');
    
    // Reset buttons
    document.getElementById('btn-pray').classList.replace('text-slate-900', 'text-slate-400');
    document.getElementById('btn-schedule').classList.replace('text-slate-900', 'text-slate-400');
    document.getElementById('btn-past').classList.replace('text-slate-900', 'text-slate-400');
    document.getElementById('border-pray').classList.replace('border-slate-900', 'border-transparent');
    document.getElementById('border-schedule').classList.replace('border-slate-900', 'border-transparent');
    document.getElementById('border-past').classList.replace('border-slate-900', 'border-transparent');

    // Show active tab
    document.getElementById('tab-' + tab).classList.remove('hidden');
    document.getElementById('btn-' + tab).classList.replace('text-slate-400', 'text-slate-900');
    document.getElementById('border-' + tab).classList.replace('border-transparent', 'border-slate-900');
}

function playPastService(url) {
    const iframe = document.getElementById('main-player');
    if (iframe) {
        iframe.src = url;
        iframe.classList.remove('hidden');
        
        // Hide the offline overlay if it exists
        const offlineOverlay = document.getElementById('offline-overlay');
        if (offlineOverlay) offlineOverlay.classList.add('hidden');
        
        // Scroll to top to focus on player
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}
</script>
    </div>
</div>
