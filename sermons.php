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
    $sermonsError = 'Sermons are temporarily unavailable.';
}

$totalPages = (int) ceil(max(1, $total) / $perPage);

include 'includes/header.php';
?>

<!-- HERO SECTION -->
<section class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-slate-900 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="<?= setting('sermons.hero_bg_image', 'https://images.unsplash.com/photo-1543165365-07232ed12fad?q=80&w=1200&auto=format&fit=crop') ?>" alt="Sermons Background" class="w-full h-full object-cover opacity-20 mix-blend-luminosity">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/90 via-slate-900/80 to-slate-900"></div>
    </div>
    
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10 text-center">
        <div class="inline-flex items-center gap-4 mb-6">
            <div class="h-px w-12 bg-[#c49a45]"></div>
            <span class="text-[#c49a45] font-bold text-sm tracking-widest uppercase">Archive</span>
            <div class="h-px w-12 bg-[#c49a45]"></div>
        </div>
        <h1 class="text-4xl md:text-7xl font-display font-black text-white mb-6 tracking-tight leading-tight">
            <?= setting('sermons.hero_title', 'Watch, Listen, <br/>and <span class="text-[#c49a45]">Grow.</span>') ?>
        </h1>
        <p class="text-xl text-slate-300 max-w-3xl mx-auto font-light leading-relaxed">
            <?= setting('sermons.hero_subtitle', 'Browse messages by date, speaker, and topic. Whether you missed a service or want to revisit a teaching, start here.') ?>
        </p>
    </div>
</section>

<!-- SERMONS LIST -->
<section class="py-24 md:py-32 bg-slate-50 relative overflow-hidden border-t border-slate-200">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        
        <!-- LIVESTREAM BANNER -->
        <div class="mb-12 relative overflow-hidden bg-gradient-to-r from-red-600 to-red-800 lg:rounded-2xl shadow-[0_0.625rem_2.5rem_rgba(220,38,38,0.2)] group border border-red-500/50">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10"></div>
            <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-black/20 to-transparent"></div>
            <div class="relative p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left z-10">
                <div class="flex-grow">
                    <div class="inline-flex items-center gap-2 mb-3 bg-red-950/40 text-red-100 text-[0.65rem] font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-red-400/30">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                        </span>
                        Join Us Live
                    </div>
                    <h2 class="text-3xl md:text-4xl font-display font-black text-white mb-3">Experience the Service Online</h2>
                    <p class="text-red-100/90 text-lg md:text-xl font-medium max-w-2xl">Can't make it in person? Watch the powerful message and worship from anywhere in the world.</p>
                </div>
                <div class="flex-shrink-0 w-full md:w-auto">
                    <a href="livestream.php" class="inline-flex items-center justify-center w-full md:w-auto bg-white text-red-700 hover:bg-slate-50 px-8 py-4 font-bold text-lg shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                        Watch Live Stream
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Search / Filter UI -->
        <form method="GET" action="sermons.php" class="mb-12 bg-white border border-slate-200 shadow-sm p-6 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:w-auto relative">
                <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="<?php echo e($searchQuery); ?>" placeholder="Search messages..." class="w-full bg-slate-50 border border-slate-200 text-slate-900 pl-12 pr-4 py-3 focus:outline-none focus:border-[#c49a45] transition-colors  placeholder-slate-400">
            </div>
            <div class="w-full md:w-auto flex gap-4">
                <select name="topic" class="w-full md:w-48 bg-slate-50 border border-slate-200 text-slate-600 px-4 py-3 focus:outline-none focus:border-[#c49a45] transition-colors  appearance-none">
                    <option value="">All Topics</option>
                    <?php foreach ($allTopics as $t): ?>
                        <option value="<?php echo e($t); ?>" <?php echo $topicFilter === $t ? 'selected' : ''; ?>><?php echo e($t); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bg-[#c49a45] text-white px-6 py-3 font-bold uppercase tracking-widest text-sm hover:bg-[#d4ac57] transition-colors ">Filter</button>
            </div>
        </form>

        <?php if ($sermonsError): ?>
            <div class="bg-red-900/20 border border-red-900/50 text-red-400 p-6  text-center">
                <?php echo e($sermonsError); ?>
            </div>
        <?php elseif (empty($sermons)): ?>
            <div class="text-center py-10">
                <div class="max-w-2xl mx-auto bg-white p-12 border border-slate-200 shadow-sm flex flex-col items-center justify-center">
                    <div class="w-20 h-20 bg-slate-50 border border-slate-200 flex items-center justify-center mb-6 text-slate-400 rounded-full">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                    <p class="text-slate-600 text-lg mb-8 font-light">No sermons have been published yet. Please check back soon.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 xl:gap-10">
                <?php foreach ($sermons as $sermon): 
                    $dateText = date('M d, Y', strtotime((string) $sermon['sermon_date']));
                    $link = 'sermon.php?id=' . (int)$sermon['id'];
                ?>
                    <a href="<?php echo $link; ?>" class="group relative bg-white border border-slate-200 hover:border-[#c49a45] transition-all duration-300 hover:-translate-y-1 flex flex-col h-full overflow-hidden shadow-sm hover:shadow-[0_20px_40px_rgba(196,154,69,0.15)]">
                        
                        <!-- Thumbnail Area -->
                        <div class="aspect-[16/10] relative bg-slate-100 overflow-hidden mb-6">
                            <?php if (!empty($sermon['sermon_image'])): ?>
                                <img src="<?php echo htmlspecialchars($sermon['sermon_image']); ?>" alt="<?php echo htmlspecialchars((string) $sermon['title']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out" loading="lazy">
                                <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors duration-300"></div>
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1543165365-07232ed12fad?q=80&w=800&auto=format&fit=crop" alt="Sermon Flyer" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out opacity-80" loading="lazy">
                                <div class="absolute inset-0 bg-slate-900/50 group-hover:bg-slate-900/30 transition-colors duration-300"></div>
                            <?php endif; ?>
                            
                            <!-- Action Icon -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-16 h-16 rounded-full bg-red-600 text-white flex items-center justify-center transform group-hover:scale-110 group-hover:bg-red-700 transition-all duration-300 shadow-[0_0_20px_rgba(220,38,38,0.4)] group-hover:shadow-[0_0_30px_rgba(220,38,38,0.6)]">
                                    <?php if ($sermon['media_type'] === 'audio'): ?>
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                    <?php elseif ($sermon['media_type'] === 'text'): ?>
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <?php else: ?>
                                        <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="px-4 pb-4 flex flex-col flex-grow relative z-10">
                            <div class="flex items-center gap-3 mb-4 flex-wrap">
                                <span class="px-3 py-1 bg-slate-50 text-[#c49a45] text-[0.625rem] sm:text-xs font-bold uppercase tracking-wider border border-slate-200"><?php echo htmlspecialchars($dateText); ?></span>
                                <?php if (!empty($sermon['topic'])): ?>
                                    <span class="text-slate-600 text-[0.625rem] sm:text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-[#c49a45]"></span>
                                        <?php echo htmlspecialchars($sermon['topic']); ?>
                                    </span>
                                <?php endif; ?>
                                <span class="text-slate-500 text-[0.625rem] sm:text-xs font-bold uppercase tracking-widest ml-auto"><?php echo htmlspecialchars($sermon['speaker']); ?></span>
                            </div>
                            
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 mb-3 group-hover:text-[#c49a45] transition-colors leading-tight line-clamp-2"><?php echo htmlspecialchars((string) $sermon['title']); ?></h3>
                            
                            <?php if (!empty($sermon['content'])): ?>
                                <p class="text-slate-600 text-sm mb-4 line-clamp-3 leading-relaxed"><?php echo e(excerpt($sermon['content'], 20)); ?></p>
                            <?php endif; ?>

                            <p class="text-[#c49a45] text-sm mt-auto flex items-center gap-2 group-hover:text-slate-900 font-bold transition-colors uppercase tracking-widest">
                                <?php 
                                    if ($sermon['media_type'] === 'audio') echo 'Listen to Message';
                                    elseif ($sermon['media_type'] === 'text') echo 'Read Message';
                                    else echo 'Watch Message';
                                ?> 
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-16 flex items-center justify-center gap-2 text-sm font-bold" aria-label="Pagination">
                    <?php 
                        $queryParams = $_GET;
                        for ($i = 1; $i <= $totalPages; $i++): 
                            $queryParams['page'] = $i;
                            $pageUrl = 'sermons.php?' . http_build_query($queryParams);
                    ?>
                        <?php if ($i === $page): ?>
                            <span class="w-10 h-10 flex items-center justify-center  bg-[#c49a45] text-white shadow-md"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($pageUrl); ?>" class="w-10 h-10 flex items-center justify-center  border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-24 bg-slate-900 relative overflow-hidden border-t border-slate-800">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="bg-slate-950 border border-slate-800 p-10 md:p-14 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
            <div>
                <h2 class="text-3xl font-display font-bold text-white mb-3">Never Miss a Message</h2>
                <p class="text-slate-400 text-lg max-w-xl">Join us in person or watch our services live online every week.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 flex-shrink-0">
                <a href="live" class="inline-flex items-center justify-center bg-[#c49a45] text-white hover:bg-[#d4ac57] text-sm font-bold uppercase tracking-widest px-8 py-4 transition-all shadow-[0_8px_30px_rgba(196,154,69,0.3)] hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    Watch Live
                </a>
                <a href="visit" class="inline-flex items-center justify-center bg-transparent border border-slate-600 text-white hover:bg-slate-800 hover:border-slate-500 text-sm font-bold uppercase tracking-widest px-8 py-4 transition-all">
                    Plan a Visit
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
