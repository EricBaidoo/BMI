<?php
$pageTitle = 'Sermons | Bridge Ministries International';
$pageDescription = 'Browse messages by date, speaker, and topic from Bridge Ministries International.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$perPage = 12;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$searchQuery = trim($_GET['q'] ?? '');
$topicFilter = trim($_GET['topic'] ?? '');

$sermons = [];
$total = 0;
$sermonsError = null;
$allTopics = [];

try {
    $pdo = db_connect();
    
    // Fetch unique topics for the filter dropdown
    $allTopics = $pdo->query('SELECT DISTINCT topic FROM sermons WHERE topic IS NOT NULL AND topic != \'\' ORDER BY topic ASC')->fetchAll(PDO::FETCH_COLUMN);

    $where = [];
    $params = [];
    
    if ($searchQuery !== '') {
        $where[] = '(title LIKE :q OR speaker LIKE :q OR content LIKE :q)';
        $params[':q'] = '%' . $searchQuery . '%';
    }
    
    if ($topicFilter !== '') {
        $where[] = 'topic = :t';
        $params[':t'] = $topicFilter;
    }
    
    $whereSql = '';
    if (!empty($where)) {
        $whereSql = 'WHERE ' . implode(' AND ', $where);
    }

    $totalStmt = $pdo->prepare('SELECT COUNT(*) FROM sermons ' . $whereSql);
    $totalStmt->execute($params);
    $total = (int) $totalStmt->fetchColumn();
    
    $sql = 'SELECT id, title, speaker, sermon_date, topic, media_type, media_url, content, sermon_image
            FROM sermons
            ' . $whereSql . '
            ORDER BY sermon_date DESC, id DESC
            LIMIT :lim OFFSET :off';
            
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $sermons = $stmt->fetchAll();
} catch (Throwable $e) {
    log_exception($e, 'sermons');
    $sermonsError = 'Sermons are temporarily unavailable.';
}

$totalPages = (int) ceil(max(1, $total) / $perPage);

include 'includes/header.php';

// Render Cinematic Hero
render_hero_cinematic([
    'title' => setting('sermons.hero_title', 'Watch, Listen, <br/><i class="font-light text-amber-500">Grow.</i>'),
    'subtitle' => setting('sermons.hero_subtitle', 'The Media Hub'),
    'bg_image' => setting('sermons.hero_bg_image', 'https://images.unsplash.com/photo-1543165365-07232ed12fad?q=80&w=1920&auto=format&fit=crop'),
    'is_video' => false
]);
?>

<!-- SERMONS LIST -->
<section class="py-24 md:py-32 bg-[#111113] relative overflow-hidden gs-reveal-section">

    <div class="w-[95%] max-w-[112.5rem] mx-auto relative z-10">
        
        <!-- LIVESTREAM BANNER -->
        <?php if (setting('live.is_streaming_now') == '1'): ?>
        <div class="mb-16 relative overflow-hidden bg-white/[0.02] backdrop-blur-3xl rounded-[3rem] shadow-glass border border-white/10 gs-reveal-up">
            <div class="absolute inset-0 bg-gradient-to-r from-red-600/20 to-transparent"></div>
            <div class="relative p-12 md:p-16 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left z-10">
                <div class="flex-grow">
                    <div class="inline-flex items-center gap-3 mb-6 bg-red-950/60 backdrop-blur-md text-red-400 text-xs font-bold uppercase tracking-widest px-5 py-2 rounded-full border border-red-500/20 shadow-inner">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                        Join Us Live
                    </div>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-display font-black uppercase text-white mb-4 tracking-normal">Experience the Glory, <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-red-600">Wherever You Are.</span></h2>
                    <p class="text-neutral-400 text-lg font-medium max-w-2xl">Can't make it in person? Watch the powerful message and worship from anywhere in the world right now.</p>
                </div>
                <div class="flex-shrink-0 w-full md:w-auto mt-8 md:mt-0">
                    <?php render_button_primary(['text' => 'Watch Live Stream', 'url' => 'livestream.php', 'style' => 'solid']); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Search / Filter UI -->
        <form method="GET" action="sermons" class="mb-16 bg-[#0A0A0B] rounded-[2rem] p-6 md:p-8 flex flex-col md:flex-row gap-6 items-center justify-between gs-reveal-up">
            <div class="flex-grow w-full md:w-auto relative group">
                <svg class="w-5 h-5 text-white/40 absolute left-6 top-1/2 transform -translate-y-1/2 group-focus-within:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search messages, speakers, topics..." class="w-full bg-[#161619] border-none rounded-2xl text-white pl-14 pr-6 py-4 font-sans font-bold text-lg focus:outline-none focus:ring-1 focus:ring-amber-500/50 transition-colors placeholder-white/20">
            </div>
            <div class="w-full md:w-auto flex flex-col sm:flex-row gap-4 relative">
                <select name="topic" class="w-full sm:w-64 bg-[#161619] border-none rounded-2xl text-white/60 px-6 py-4 font-sans font-bold focus:outline-none focus:ring-1 focus:ring-amber-500/50 transition-colors appearance-none cursor-pointer">
                    <option value="">All Topics</option>
                    <?php foreach ($allTopics as $t): ?>
                        <option value="<?php echo htmlspecialchars($t); ?>" <?php echo $topicFilter === $t ? 'selected' : ''; ?>><?php echo htmlspecialchars($t); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="absolute inset-y-0 right-[9.5rem] sm:right-[11rem] flex items-center pointer-events-none text-white/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <button type="submit" class="bg-white text-black px-10 py-4 rounded-2xl font-sans font-bold uppercase tracking-widest text-xs hover:bg-amber-500 hover:text-white transition-all">Filter</button>
            </div>
        </form>

        <?php if ($sermonsError): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-8 rounded-[2rem] text-center font-bold font-sans gs-reveal-up">
                <?php echo htmlspecialchars($sermonsError); ?>
            </div>
        <?php elseif (empty($sermons)): ?>
            <div class="text-center py-24 gs-reveal-up">
                <div class="max-w-2xl mx-auto bg-obsidian-800/50 rounded-[3rem] p-16 border border-white/5 shadow-glass flex flex-col items-center justify-center">
                    <div class="w-24 h-24 bg-white/5 border border-white/10 flex items-center justify-center mb-8 text-white/30 rounded-full">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                    <p class="text-neutral-400 text-xl font-sans font-medium">No messages found matching your criteria. Please try a different search.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 xl:gap-12">
                <?php foreach ($sermons as $index => $sermon): 
                    $dateText = date('M d, Y', strtotime((string) $sermon['sermon_date']));
                    $link = 'sermon.php?id=' . (int)$sermon['id'];
                ?>
                    <a href="<?php echo $link; ?>" class="group relative bg-[#0A0A0B] hover:bg-[#161619] transition-colors duration-700 hover:-translate-y-2 flex flex-col h-full overflow-hidden block gs-reveal-up">
                        
                        <!-- Thumbnail Area -->
                        <div class="aspect-[16/10] relative bg-black overflow-hidden mb-6">
                            <?php if (!empty($sermon['sermon_image'])): ?>
                                <img src="<?php echo htmlspecialchars($sermon['sermon_image']); ?>" alt="<?php echo htmlspecialchars((string) $sermon['title']); ?>" class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-105" loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1543165365-07232ed12fad?q=80&w=800&auto=format&fit=crop" alt="Sermon Flyer" class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-105 opacity-80" loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                            <?php endif; ?>
                            
                            <!-- Play Icon Overlay -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-500 scale-90 group-hover:scale-100">
                                <div class="w-20 h-20 rounded-full bg-white/10 backdrop-blur-md text-white flex items-center justify-center">
                                    <svg class="w-8 h-8 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="px-6 pb-6 flex flex-col flex-grow relative z-10">
                            <div class="flex items-center gap-3 mb-4 flex-wrap">
                                <span class="bg-white/5 border border-white/10 rounded-full text-white/70 px-4 py-1.5 text-[0.65rem] font-bold uppercase tracking-widest"><?php echo htmlspecialchars($dateText); ?></span>
                                <?php if (!empty($sermon['topic'])): ?>
                                    <span class="text-white/40 text-[0.65rem] font-bold uppercase tracking-widest flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                                        <?php echo htmlspecialchars($sermon['topic']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="text-2xl font-display font-black text-white/95 mb-2 group-hover:text-amber-500 transition-colors duration-500 leading-tight line-clamp-2 uppercase tracking-normal"><?php echo htmlspecialchars((string) $sermon['title']); ?></h3>
                            
                            <p class="text-neutral-500 font-medium text-sm mt-auto uppercase tracking-widest border-t border-white/5 pt-4">Speaker: <span class="text-white/80"><?php echo htmlspecialchars($sermon['speaker']); ?></span></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- PAGINATION -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-16 flex justify-center gap-2 gs-reveal-up">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="w-12 h-12 flex items-center justify-center rounded-full bg-accent text-black font-bold text-lg"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>" class="w-12 h-12 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-white/10 hover:text-accent transition-colors font-bold text-lg border border-white/10"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-32 bg-[#0A0A0B] relative overflow-hidden gs-reveal-section border-t border-white/[0.03]">
    <div class="max-w-[112.5rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-[90%] gs-reveal-up">
        <div class="bg-transparent p-12 md:p-20 flex flex-col lg:flex-row items-center justify-between gap-12 text-center lg:text-left">
            <div>
                <h2 class="text-4xl lg:text-6xl font-display font-black tracking-normal uppercase text-white/95 mb-6 leading-none">Never Miss a Message</h2>
                <p class="text-white/40 text-xl font-sans font-medium max-w-2xl">Join us in person or watch our services live online every week.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-6 flex-shrink-0">
                <a href="livestream" class="bg-white text-black hover:bg-amber-500 hover:text-white px-10 py-5 rounded-full font-sans font-bold uppercase tracking-widest text-xs transition-colors inline-flex items-center justify-center">
                    Watch Live
                </a>
                <a href="visit" class="bg-transparent border border-white/20 text-white hover:border-amber-500 hover:text-amber-500 px-10 py-5 rounded-full font-sans font-bold uppercase tracking-widest text-xs transition-colors inline-flex items-center justify-center">
                    Plan a Visit
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
