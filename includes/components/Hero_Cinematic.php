<?php
/**
 * Cinematic Hero Component (Swiper Slider)
 * 
 * @param array $args
 * - slides (array of slide arrays)
 *   - title, subtitle, bg_image, button_text, button_url
 */
function render_hero_cinematic($args = []) {
    $slides = $args['slides'] ?? [];
    if (empty($slides)) {
        // Fallback slide
        $slides = [[
            'title' => 'Welcome',
            'subtitle' => '',
            'bg_image' => '',
            'button_text' => '',
            'button_url' => '#'
        ]];
    }
    ?>
    <section class="relative w-full h-screen min-h-[50rem] bg-[#0A0A0B] overflow-hidden">
        <!-- Swiper -->
        <div class="swiper hero-swiper w-full h-full">
            <div class="swiper-wrapper h-full">
                <?php foreach ($slides as $index => $slide): 
                    $title = $slide['title'] ?? '';
                    $subtitle = $slide['subtitle'] ?? '';
                    $bg_image = $slide['bg_image'] ?? '';
                    $button_text = $slide['button_text'] ?? '';
                    $button_url = $slide['button_url'] ?? '#';
                    $is_video = preg_match('/\.(mp4|webm)$/i', $bg_image);
                ?>
                <div class="swiper-slide relative w-full h-full flex flex-col pt-[25vh] sm:pt-[28vh] md:pt-[32vh]">
                    <!-- Media Background -->
                    <div class="absolute inset-0 z-0">
                        <?php if ($is_video): ?>
                            <video src="<?php echo htmlspecialchars($bg_image); ?>" class="w-full h-full object-cover opacity-80" autoplay loop muted playsinline data-swiper-parallax="-20%"></video>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($bg_image); ?>" alt="" class="w-full h-full object-cover opacity-80" data-swiper-parallax="-20%">
                        <?php endif; ?>
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0A0A0B] via-[#0A0A0B]/40 to-black/20"></div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 text-center px-6 max-w-[112.5rem] w-[90%] mx-auto" data-swiper-parallax="-50%" data-swiper-parallax-opacity="0">
                        <?php if ($subtitle): ?>
                            <span class="block font-sans font-bold text-white/70 tracking-[0.25em] uppercase text-xs md:text-sm mb-6 max-w-4xl mx-auto">
                                <?php echo htmlspecialchars($subtitle); ?>
                            </span>
                        <?php endif; ?>
                        
                        <h1 class="font-display font-black text-5xl md:text-[5rem] lg:text-[7.5rem] xl:text-[8.5rem] tracking-tighter text-white/95 leading-[0.85] uppercase drop-shadow-2xl">
                            <?php echo $title; ?>
                        </h1>

                        <?php if ($button_text): ?>
                            <div class="mt-10">
                                <?php render_button_primary(['text' => $button_text, 'url' => $button_url, 'style' => 'light']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
            
            <!-- Scroll indicator -->
            <div class="absolute bottom-12 left-1/2 -translate-x-1/2 z-20 flex flex-col items-center animate-float pointer-events-none">
                <span class="text-[0.6rem] font-bold text-white/50 uppercase tracking-widest mb-2">Scroll</span>
                <div class="w-[1px] h-12 bg-gradient-to-b from-white/50 to-transparent"></div>
            </div>
        </div>
    </section>

    <!-- Initialize Swiper -->
    <script>
        function initHeroCarousel() {
            if (typeof Swiper !== 'undefined') {
                new Swiper('.hero-swiper', {
                    effect: 'fade',
                    fadeEffect: { crossFade: true },
                    parallax: true,
                    speed: 1500,
                    loop: true,
                    autoplay: {
                        delay: 6000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                });
            }
        }
        document.addEventListener('DOMContentLoaded', initHeroCarousel);
    </script>
    <style>
        .hero-swiper .swiper-pagination-bullet {
            background: rgba(255, 255, 255, 0.5);
            width: 8px;
            height: 8px;
            opacity: 1;
            transition: all 0.3s ease;
        }
        .hero-swiper .swiper-pagination-bullet-active {
            background: #f59e0b; /* amber-500 */
            width: 24px;
            border-radius: 4px;
        }
        .swiper-slide-active [data-swiper-parallax-opacity] {
            opacity: 1 !important;
            transition: opacity 1.5s ease;
        }
    </style>
    <?php
}
