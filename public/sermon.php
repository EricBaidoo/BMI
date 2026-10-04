<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$sermon = null;
$error = null;

if ($id <= 0) {
    header('Location: sermons');
    exit;
}

try {
    $pdo = db_connect();
    $stmt = $pdo->prepare("SELECT * FROM sermons WHERE id = ?");
    $stmt->execute([$id]);
    $sermon = $stmt->fetch();
} catch (Throwable $e) {
    log_exception($e, 'sermon');
    $error = "Unable to fetch sermon details.";
}

if (!$sermon) {
    // A real 404 (or 503 if the database failed) so search engines drop dead links.
    http_response_code($error ? 503 : 404);
    $noIndex = true;
    $pageTitle = 'Sermon Not Found | Bridge Ministries International';
} else {
    $pageTitle = (string) $sermon['title'] . ' | Bridge Ministries International';
    $pageDescription = mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags((string) $sermon['content']))), 0, 160);
    $ogType = 'article';
    if (!empty($sermon['sermon_image'])) {
        $ogImage = (string) $sermon['sermon_image'];
    }
    if (($sermon['media_type'] ?? '') === 'video' && !empty($sermon['media_url'])) {
        $thumb = !empty($sermon['sermon_image']) ? (preg_match('~^https?://~i', $sermon['sermon_image']) ? $sermon['sermon_image'] : $siteUrl . '/' . ltrim($sermon['sermon_image'], '/')) : null;
        $structuredData = [array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => (string) $sermon['title'],
            'description' => $pageDescription ?: (string) $sermon['title'],
            'uploadDate' => !empty($sermon['sermon_date']) ? date('c', strtotime($sermon['sermon_date'])) : null,
            'thumbnailUrl' => $thumb,
            'contentUrl' => (string) $sermon['media_url'],
        ])];
    }
}

// Helper to convert youtube/facebook watch URLs to embed URLs
function getEmbedUrl($url) {
    if (strpos($url, 'youtube.com/watch') !== false) {
        parse_str(parse_url($url, PHP_URL_QUERY), $vars);
        if (isset($vars['v'])) {
            return 'https://www.youtube.com/embed/' . $vars['v'];
        }
    } elseif (strpos($url, 'youtu.be/') !== false) {
        $path = parse_url($url, PHP_URL_PATH);
        return 'https://www.youtube.com/embed' . $path;
    } elseif (strpos($url, 'facebook.com') !== false && (strpos($url, '/videos/') !== false || strpos($url, '/watch') !== false)) {
        return 'https://www.facebook.com/plugins/video.php?href=' . urlencode($url) . '&show_text=false&width=auto';
    }
    return $url; // Return original if not youtube/facebook, or write other embed handlers if needed
}

include __DIR__ . '/../includes/header.php';
?>

<!-- CUSTOM ANIMATION STYLES -->
<div class="pt-20 md:pt-24 bg-[#0a0a0c] min-h-screen relative overflow-hidden">
    <!-- Ambient Background Glows -->
    <div class="absolute top-0 right-1/4 w-[50rem] h-[50rem] bg-amber-600/5 blur-[150px] rounded-full pointer-events-none mix-blend-screen z-0"></div>
    
    <?php if ($error || !$sermon): ?>
        <div class="max-w-3xl mx-auto px-4 py-32 text-center relative z-10 gs-reveal-up">
            <h1 class="text-5xl md:text-7xl font-display font-black text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-red-600 mb-6 uppercase tracking-normal">Sermon Not Found</h1>
            <p class="text-xl text-neutral-400 mb-10 font-medium"><?php echo $error ?? "We couldn't find the message you were looking for."; ?></p>
            <a href="sermons" class="inline-block bg-white text-black px-10 py-5 rounded-full font-bold uppercase tracking-widest text-sm hover:bg-amber-500 hover:text-white transition-all shadow-[0_0_30px_rgba(255,255,255,0.1)] hover:shadow-[0_0_40px_rgba(245,158,11,0.3)] hover:-translate-y-1">Return to Archive</a>
        </div>
    <?php else: 
        $imageUrl = !empty($sermon['sermon_image']) ? $sermon['sermon_image'] : 'https://images.unsplash.com/photo-1543165365-07232ed12fad?q=80&w=1200&auto=format&fit=crop';
        $sermonDate = date('F j, Y', strtotime((string)$sermon['sermon_date']));
        $hasMedia = !empty($sermon['media_url']);
        $embedUrl = $hasMedia ? getEmbedUrl($sermon['media_url']) : '';
    ?>

    <!-- SERMON HERO (Cinematic Player Area) -->
    <section class="relative bg-[#030303] border-b border-white/5 pt-10 pb-16 relative z-10 gs-reveal-section">
        <!-- Intense Backlight for Player -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[60%] h-[60%] bg-indigo-500/10 blur-[100px] pointer-events-none mix-blend-screen"></div>

        <div class="w-[95%] max-w-7xl mx-auto gs-reveal-up">
            <div class="inline-flex items-center gap-4 mb-8">
                <a href="sermons" class="text-white/40 hover:text-white transition-colors flex items-center gap-2 font-sans font-bold text-xs uppercase tracking-widest">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Archive
                </a>
            </div>

            <?php if ($sermon['media_type'] === 'video' && $hasMedia && (strpos($embedUrl, 'youtube.com/embed') !== false || strpos($embedUrl, 'facebook.com/plugins') !== false)): ?>
                <div class="w-full aspect-video rounded-[2rem] bg-black relative z-10 shadow-[0_0_50px_rgba(0,0,0,0.8)] border border-white/10 overflow-hidden ring-1 ring-white/5 group">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none z-10"></div>
                    <iframe src="<?php echo htmlspecialchars(privacy_embed_url($embedUrl)); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full absolute inset-0 z-0 bg-black"></iframe>
                </div>
            <?php else: ?>
                <div class="w-full aspect-[21/9] rounded-[2rem] bg-black relative z-10 shadow-[0_0_50px_rgba(0,0,0,0.8)] border border-white/10 overflow-hidden ring-1 ring-white/5">
                    <img loading="lazy" src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars((string)$sermon['title']); ?>" class="absolute inset-0 w-full h-full object-cover opacity-60">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#030303] via-[#030303]/60 to-transparent"></div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CONTENT SECTION -->
    <section class="py-16 md:py-24 relative z-20 gs-reveal-section">
        <div class="w-[90%] max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                <!-- Main Details -->
                <div class="lg:col-span-8 gs-reveal-right">
                    <div class="flex items-center gap-3 mb-6 flex-wrap">
                        <span class="px-4 py-1.5 bg-white/5 text-white/70 text-xs font-bold uppercase tracking-widest border border-white/10 rounded-full shadow-inner"><?php echo $sermonDate; ?></span>
                        <?php if (!empty($sermon['topic'])): ?>
                            <span class="text-amber-500/80 text-xs font-bold uppercase tracking-widest flex items-center gap-2 bg-amber-500/10 px-4 py-1.5 rounded-full border border-amber-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <?php echo htmlspecialchars($sermon['topic']); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-display font-black text-white mb-8 leading-[1.0] tracking-normal uppercase">
                        <?php echo htmlspecialchars((string)$sermon['title']); ?>
                    </h1>
                    
                    <div class="flex items-center gap-5 mb-12 border-b border-white/5 pb-10">
                        <div class="w-14 h-14 rounded-full bg-white/5 flex items-center justify-center text-white/40 border border-white/10 shadow-inner">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-white/40 uppercase tracking-widest font-bold mb-1">Speaker</p>
                            <p class="text-xl font-display font-black text-white uppercase tracking-normal"><?php echo htmlspecialchars((string)$sermon['speaker']); ?></p>
                        </div>
                    </div>

                    <?php if ($sermon['media_type'] === 'audio' && $hasMedia): ?>
                        <div class="mb-12 bg-[#050505] p-8 border border-white/5 rounded-[2rem] shadow-2xl relative overflow-hidden group">
                            <div class="absolute inset-0 bg-gradient-to-r from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-white/50 mb-6 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                </div>
                                Listen to Audio
                            </h3>
                            <!-- Custom styled audio player using standard element -->
                            <audio controls class="w-full rounded-full focus:outline-none shadow-inner" style="filter: invert(100%) hue-rotate(180deg) brightness(1.2);">
                                <source src="<?php echo htmlspecialchars($sermon['media_url']); ?>">
                                Your browser does not support the audio element.
                            </audio>
                            <p class="text-xs text-white/30 mt-4 font-medium">If the audio does not play, you can <a href="<?php echo htmlspecialchars($sermon['media_url']); ?>" target="_blank" class="text-amber-500 hover:underline">download it here</a>.</p>
                        </div>
                    <?php endif; ?>

                    <?php if ($sermon['media_type'] === 'video' && $hasMedia && strpos($embedUrl, 'youtube.com/embed') === false): ?>
                        <div class="mb-12 bg-[#050505] p-8 border border-white/5 rounded-[2rem] shadow-2xl relative overflow-hidden group">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-white/50 mb-6 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </div>
                                Watch External Video
                            </h3>
                            <a href="<?php echo htmlspecialchars($sermon['media_url']); ?>" target="_blank" rel="noopener" class="inline-flex items-center justify-center bg-white text-black hover:bg-blue-500 hover:text-white px-8 py-4 rounded-full font-bold uppercase tracking-widest text-xs transition-all shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_30px_rgba(59,130,246,0.3)]">
                                Open Video Link <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="prose prose-lg prose-invert prose-amber max-w-none">
                        <h2 class="text-3xl font-display font-black text-white mb-8 tracking-normal uppercase">Message Notes</h2>
                        <?php if (!empty($sermon['content'])): ?>
                            <div class="text-neutral-300 leading-loose font-medium text-lg">
                                <?php echo nl2br(htmlspecialchars((string)$sermon['content'])); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-white/30 italic font-medium">No notes have been provided for this message.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar (Glassmorphic) -->
                <div class="lg:col-span-4 gs-reveal-left delay-100">
                    <div class="bg-white/[0.02] backdrop-blur-3xl p-10 border border-white/5 rounded-[2.5rem] sticky top-32 shadow-2xl relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-500/10 blur-[50px] rounded-full pointer-events-none"></div>

                        <h3 class="text-xl font-display font-black text-white mb-8 uppercase tracking-normal">Share Message</h3>
                        
                        <div class="flex flex-col gap-4 mb-10">
                            <?php $currentUrl = urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>
                            
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $currentUrl; ?>" target="_blank" class="flex items-center gap-4 bg-[#050505] p-4 rounded-2xl border border-white/5 hover:border-blue-500/50 hover:bg-blue-500/10 text-white/60 hover:text-white transition-all group">
                                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.312h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
                                </div>
                                <span class="font-bold uppercase tracking-widest text-xs">Share on Facebook</span>
                            </a>
                            
                            <a href="https://twitter.com/intent/tweet?url=<?php echo $currentUrl; ?>&text=<?php echo urlencode('Check out this message: ' . $sermon['title']); ?>" target="_blank" class="flex items-center gap-4 bg-[#050505] p-4 rounded-2xl border border-white/5 hover:border-sky-500/50 hover:bg-sky-500/10 text-white/60 hover:text-white transition-all group">
                                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center group-hover:bg-sky-500 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                </div>
                                <span class="font-bold uppercase tracking-widest text-xs">Share on Twitter</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
