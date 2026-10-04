<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ministries');
    exit;
}

try {
    $pdo = db_connect();
    $stmt = $pdo->prepare('SELECT * FROM weekly_services WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $ministry = $stmt->fetch();
    
    if (!$ministry) {
        header('Location: ministries');
        exit;
    }
} catch (Throwable $e) {
    log_exception($e, 'ministry_detail');
    header('Location: ministries');
    exit;
}

$pageTitle = (string) $ministry['title'] . ' | Bridge Ministries International';
$pageDescription = htmlspecialchars($ministry['description']);

include __DIR__ . '/../includes/header.php';

$heroImage = empty($ministry['image_url']) ? 'https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=2000&auto=format&fit=crop' : (strpos($ministry['image_url'], 'http') === 0 ? htmlspecialchars($ministry['image_url']) : '/BMI/' . htmlspecialchars($ministry['image_url']));
$themeColor = htmlspecialchars($ministry['theme_color'] ?? 'cyan');

// Map Tailwind colors for UI accents based on theme_color
$colorClasses = [
    'cyan' => 'text-cyan-400 bg-cyan-400/10 border-cyan-400/20',
    'purple' => 'text-purple-400 bg-purple-400/10 border-purple-400/20',
    'orange' => 'text-orange-400 bg-orange-400/10 border-orange-400/20',
    'teal' => 'text-teal-400 bg-teal-400/10 border-teal-400/20',
    'blue' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
    'red' => 'text-red-400 bg-red-400/10 border-red-400/20',
    'green' => 'text-green-400 bg-green-400/10 border-green-400/20',
    'yellow' => 'text-yellow-400 bg-yellow-400/10 border-yellow-400/20',
    'pink' => 'text-pink-400 bg-pink-400/10 border-pink-400/20',
];
$themeClass = $colorClasses[$themeColor] ?? $colorClasses['cyan'];
$textColor = explode(' ', $themeClass)[0];
?>

<!-- CUSTOM ANIMATION STYLES -->
<!-- HERO SECTION -->
<section class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#030303] overflow-hidden min-h-[60vh] flex items-center justify-center gs-reveal-section">
    <div class="absolute inset-0 z-0">
        <img loading="lazy" src="<?= $heroImage ?>" alt="<?= htmlspecialchars($ministry['title']) ?> Background" class="w-full h-full object-cover opacity-30 mix-blend-luminosity grayscale">
        <div class="absolute inset-0 bg-gradient-to-b from-[#030303]/90 via-[#030303]/60 to-[#0a0a0c]"></div>
    </div>
    
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10 text-center gs-reveal-up">
        <div class="inline-flex items-center gap-6 mb-6">
            <div class="h-px w-16 bg-white/20"></div>
            <a href="ministries" class="text-white/50 hover:text-white font-sans font-bold text-[0.625rem] tracking-[0.4em] uppercase transition-colors">
                &larr; Back to Ministries
            </a>
            <div class="h-px w-16 bg-white/20"></div>
        </div>
        
        <h1 class="text-5xl md:text-7xl lg:text-8xl font-display font-black tracking-normal uppercase text-white mb-6 leading-[1.0]">
            <?= htmlspecialchars($ministry['title']) ?>
        </h1>
        
        <?php if (!empty($ministry['subtitle'])): ?>
        <p class="text-xl md:text-3xl text-neutral-300 font-display font-bold italic mb-6">
            <?= htmlspecialchars($ministry['subtitle']) ?>
        </p>
        <?php endif; ?>
        
        <p class="text-lg md:text-xl text-neutral-400 max-w-3xl mx-auto font-medium leading-relaxed">
            <?= htmlspecialchars($ministry['description']) ?>
        </p>
    </div>
</section>

<!-- CONTENT SECTION -->
<section class="py-24 md:py-32 bg-[#0a0a0c] relative overflow-hidden gs-reveal-section">
    <div class="w-[90%] max-w-[112.5rem] mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-24">
            
            <!-- Left Column: Long Description -->
            <div class="lg:col-span-8 gs-reveal-left">
                <?php if (!empty($ministry['long_description'])): ?>
                    <div class="prose prose-invert prose-lg prose-headings:font-display prose-headings:font-bold prose-headings:uppercase prose-p:font-sans prose-p:text-neutral-400 prose-p:leading-relaxed max-w-none">
                        <?= safe_html($ministry['long_description']) ?>
                    </div>
                <?php else: ?>
                    <div class="prose prose-invert prose-lg prose-p:font-sans prose-p:text-neutral-400 prose-p:leading-relaxed max-w-none">
                        <p><?= nl2br(htmlspecialchars($ministry['description'])) ?></p>
                        <p class="mt-8 italic text-neutral-600">More details about this ministry will be added soon.</p>
                    </div>
                <?php endif; ?>

                <?php
                // Check if any gallery images exist
                $gallery = [];
                for ($i = 1; $i <= 3; $i++) {
                    $g = $ministry['gallery_image_'.$i] ?? '';
                    if (!empty($g)) {
                        $gallery[] = strpos($g, 'http') === 0 ? htmlspecialchars($g) : '/BMI/' . htmlspecialchars($g);
                    }
                }
                
                if (count($gallery) > 0):
                ?>
                <div class="mt-16 pt-12 border-t border-white/10 gs-reveal-up">
                    <h3 class="font-display font-bold text-2xl text-white uppercase mb-8 pb-4 border-b border-white/10 inline-block">Gallery</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($gallery as $index => $img): 
                            // Make the first image span 2 columns if there's an odd number, or just display them normally.
                            $colSpanClass = (count($gallery) === 3 && $index === 0) ? 'md:col-span-2' : '';
                            // Different heights for visual interest
                            $heightClass = (count($gallery) === 3 && $index === 0) ? 'h-[25rem] md:h-[30rem]' : 'h-[20rem] md:h-[24rem]';
                        ?>
                            <div class="<?= $colSpanClass ?> rounded-3xl overflow-hidden group shadow-2xl relative bg-[#111]">
                                <img loading="lazy" decoding="async" src="<?= $img ?>" alt="Ministry Gallery Image" class="w-full <?= $heightClass ?> object-cover transition-transform duration-700 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors duration-700"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Details & CTA -->
            <div class="lg:col-span-4 gs-reveal-right">
                <div class="bg-[#111] border border-white/10 rounded-[2rem] p-8 md:p-10 sticky top-32">
                    
                    <h3 class="font-display font-bold text-2xl text-white uppercase mb-8 pb-4 border-b border-white/10">Ministry Details</h3>
                    
                    <div class="space-y-8">
                        <?php if (!empty($ministry['leader_name'])): ?>
                        <div>
                            <span class="block text-[0.625rem] font-sans font-bold tracking-[0.2em] text-neutral-500 uppercase mb-2">Leader</span>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 <?= $textColor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="text-white font-sans font-medium text-lg"><?= htmlspecialchars($ministry['leader_name']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($ministry['time_info'])): ?>
                        <div>
                            <span class="block text-[0.625rem] font-sans font-bold tracking-[0.2em] text-neutral-500 uppercase mb-2">Meeting Schedule</span>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 <?= $textColor ?> mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-white font-sans font-medium text-lg leading-snug"><?= htmlspecialchars($ministry['time_info']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-12 pt-8 border-t border-white/10">
                        <h4 class="font-display font-bold text-lg text-white mb-4">Get Connected</h4>
                        <p class="text-neutral-400 text-sm mb-6">Interested in joining or learning more? Reach out to us and we'll connect you with the leader.</p>
                        <a href="contact" class="flex items-center justify-center gap-3 w-full bg-white text-black hover:bg-neutral-200 px-6 py-4 rounded-xl font-sans font-bold uppercase tracking-widest text-xs transition-colors">
                            Contact Us
                        </a>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px"
    });

    document.querySelectorAll('.gs-reveal-up, .gs-reveal-left, .gs-reveal-right').forEach((el) => {
        observer.observe(el);
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
