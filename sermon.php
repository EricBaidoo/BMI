<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$sermon = null;
$error = null;

if ($id <= 0) {
    header('Location: sermons.php');
    exit;
}

try {
    $pdo = db_connect();
    $stmt = $pdo->prepare("SELECT * FROM sermons WHERE id = ?");
    $stmt->execute([$id]);
    $sermon = $stmt->fetch();
} catch (Throwable $e) {
    $error = "Unable to fetch sermon details.";
}

if (!$sermon) {
    $pageTitle = 'Sermon Not Found | Bridge Ministries International';
} else {
    $pageTitle = htmlspecialchars((string)$sermon['title']) . ' | Bridge Ministries International';
    $pageDescription = substr(strip_tags((string)$sermon['content']), 0, 160);
}

// Helper to convert youtube watch URLs to embed URLs
function getEmbedUrl($url) {
    if (strpos($url, 'youtube.com/watch') !== false) {
        parse_str(parse_url($url, PHP_URL_QUERY), $vars);
        if (isset($vars['v'])) {
            return 'https://www.youtube.com/embed/' . $vars['v'];
        }
    } elseif (strpos($url, 'youtu.be/') !== false) {
        $path = parse_url($url, PHP_URL_PATH);
        return 'https://www.youtube.com/embed' . $path;
    }
    return $url; // Return original if not youtube, or write other embed handlers if needed
}

include 'includes/header.php';
?>

<div class="pt-20 md:pt-24 bg-slate-50 min-h-screen">
    <?php if ($error || !$sermon): ?>
        <div class="max-w-3xl mx-auto px-4 py-32 text-center">
            <h1 class="text-4xl font-display font-black text-slate-900 mb-6">Sermon Not Found</h1>
            <p class="text-lg text-slate-600 mb-8"><?php echo $error ?? "We couldn't find the message you were looking for."; ?></p>
            <a href="sermons.php" class="inline-block bg-[#c49a45] text-white px-8 py-4 font-bold hover:bg-[#d4ac57] transition-colors">Return to Sermons</a>
        </div>
    <?php else: 
        $imageUrl = !empty($sermon['sermon_image']) ? $sermon['sermon_image'] : 'https://images.unsplash.com/photo-1543165365-07232ed12fad?q=80&w=1200&auto=format&fit=crop';
        $sermonDate = date('F j, Y', strtotime((string)$sermon['sermon_date']));
        $hasMedia = !empty($sermon['media_url']);
        $embedUrl = $hasMedia ? getEmbedUrl($sermon['media_url']) : '';
    ?>

    <!-- SERMON HERO -->
    <section class="relative bg-slate-900 border-b border-slate-800">
        <?php if ($sermon['media_type'] === 'video' && $hasMedia && strpos($embedUrl, 'youtube.com/embed') !== false): ?>
            <div class="w-full max-w-6xl mx-auto aspect-video bg-black relative z-10 shadow-2xl">
                <iframe src="<?php echo htmlspecialchars($embedUrl); ?>?autoplay=0&rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full absolute inset-0"></iframe>
            </div>
        <?php else: ?>
            <div class="h-[40vh] min-h-[20rem] relative overflow-hidden">
                <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars((string)$sermon['title']); ?>" class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-luminosity">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-slate-900/30"></div>
            </div>
        <?php endif; ?>
    </section>

    <!-- CONTENT -->
    <section class="py-16 md:py-24 relative -mt-10 z-20">
        <div class="w-[90%] max-w-[112.5rem] mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                
                <!-- Main Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white p-8 md:p-12 border border-slate-200 shadow-xl">
                        <div class="flex items-center gap-3 mb-6 flex-wrap">
                            <span class="px-3 py-1 bg-slate-50 text-[#c49a45] text-xs font-bold uppercase tracking-wider border border-slate-200"><?php echo $sermonDate; ?></span>
                            <?php if (!empty($sermon['topic'])): ?>
                                <span class="text-slate-600 text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                                    <span class="w-1 h-1 rounded-full bg-[#c49a45]"></span>
                                    <?php echo htmlspecialchars($sermon['topic']); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <h1 class="text-4xl md:text-5xl font-display font-black text-slate-900 mb-6 leading-tight">
                            <?php echo htmlspecialchars((string)$sermon['title']); ?>
                        </h1>
                        
                        <div class="flex items-center gap-4 mb-10 border-b border-slate-100 pb-8">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 uppercase tracking-widest font-bold mb-1">Speaker</p>
                                <p class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars((string)$sermon['speaker']); ?></p>
                            </div>
                        </div>

                        <?php if ($sermon['media_type'] === 'audio' && $hasMedia): ?>
                            <div class="mb-10 bg-slate-50 p-6 border border-slate-200">
                                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-[#c49a45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                    Listen to Audio
                                </h3>
                                <!-- Simple HTML5 Audio Player -->
                                <audio controls class="w-full">
                                    <source src="<?php echo htmlspecialchars($sermon['media_url']); ?>">
                                    Your browser does not support the audio element.
                                </audio>
                                <p class="text-xs text-slate-400 mt-2">If the audio does not play, you can <a href="<?php echo htmlspecialchars($sermon['media_url']); ?>" target="_blank" class="text-[#c49a45] hover:underline">access it here</a>.</p>
                            </div>
                        <?php endif; ?>

                        <?php if ($sermon['media_type'] === 'video' && $hasMedia && strpos($embedUrl, 'youtube.com/embed') === false): ?>
                            <div class="mb-10 bg-slate-50 p-6 border border-slate-200">
                                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-[#c49a45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    Watch Video
                                </h3>
                                <a href="<?php echo htmlspecialchars($sermon['media_url']); ?>" target="_blank" rel="noopener" class="inline-flex items-center justify-center bg-[#c49a45] text-white px-6 py-3 font-bold uppercase tracking-widest text-sm hover:bg-[#d4ac57] transition-colors">
                                    Open Video Link
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="prose prose-lg prose-slate max-w-none">
                            <h2 class="text-2xl font-display font-black text-slate-900 mb-6">Sermon Notes</h2>
                            <?php if (!empty($sermon['content'])): ?>
                                <div class="text-slate-700 leading-loose">
                                    <?php echo nl2br(htmlspecialchars((string)$sermon['content'])); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-slate-500 italic">No notes have been provided for this message.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white p-8 border border-slate-200 shadow-xl sticky top-32">
                        <h3 class="text-xl font-display font-black text-slate-900 mb-6">Share This Message</h3>
                        
                        <div class="flex gap-4 mb-8">
                            <!-- Basic social share buttons using current URL -->
                            <?php $currentUrl = urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $currentUrl; ?>" target="_blank" class="w-12 h-12 bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-600 hover:text-[#c49a45] hover:border-[#c49a45] transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.312h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo $currentUrl; ?>&text=<?php echo urlencode('Check out this message: ' . $sermon['title']); ?>" target="_blank" class="w-12 h-12 bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-600 hover:text-[#c49a45] hover:border-[#c49a45] transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                        </div>

                        <a href="sermons" class="block w-full bg-slate-100 text-slate-600 text-center px-6 py-4 font-bold hover:bg-slate-200 transition-all border border-slate-200 uppercase tracking-widest text-sm">
                            Back to Library
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
