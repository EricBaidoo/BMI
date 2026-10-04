<?php
/**
 * Cinematic Hero Component (Swiper Slider)
 *
 * Pass either a list of slides, or one slide's fields directly:
 *   render_hero_cinematic(['slides' => [...]]);
 *   render_hero_cinematic(['title' => '...', 'subtitle' => '...', 'bg_image' => '...', 'button_text' => '...', 'button_url' => '...']);
 *
 * Slide fields: title, subtitle, bg_image (image or .mp4/.webm), poster (image shown before/instead of video),
 * button_text, button_url.
 *
 * Accessibility and speed:
 *  - Only the first slide's title is the page <h1>; the first image loads immediately, the rest lazily.
 *  - Videos never autoplay for visitors who prefer reduced motion or have Save-Data on.
 *  - A pause button stops the slideshow and any video (WCAG 2.2.2).
 */
function render_hero_cinematic($args = []) {
    $slides = $args['slides'] ?? [];
    if (empty($slides) && (!empty($args['title']) || !empty($args['bg_image']))) {
        $slides = [[
            'title' => $args['title'] ?? '',
            'subtitle' => $args['subtitle'] ?? '',
            'bg_image' => $args['bg_image'] ?? '',
            'poster' => $args['poster'] ?? '',
            'button_text' => $args['button_text'] ?? '',
            'button_url' => $args['button_url'] ?? '#',
        ]];
    }
    if (empty($slides)) {
        $slides = [[
            'title' => setting('site.name', 'Bridge Ministries International'),
            'subtitle' => '',
            'bg_image' => '',
            'button_text' => '',
            'button_url' => '#'
        ]];
    }
    $multiple = count($slides) > 1;
    $hasVideo = false;
    ?>
    <section class="relative w-full h-screen min-h-[50rem] bg-[#0A0A0B] overflow-hidden" aria-roledescription="<?php echo $multiple ? 'carousel' : 'banner'; ?>" aria-label="Featured">
        <!-- Swiper -->
        <div class="swiper hero-swiper w-full h-full" data-multiple="<?php echo $multiple ? '1' : '0'; ?>">
            <div class="swiper-wrapper h-full">
                <?php foreach ($slides as $index => $slide):
                    $title = $slide['title'] ?? '';
                    $subtitle = $slide['subtitle'] ?? '';
                    $bg_image = safe_url($slide['bg_image'] ?? '');
                    $poster = safe_url($slide['poster'] ?? '');
                    $button_text = $slide['button_text'] ?? '';
                    $button_url = safe_url($slide['button_url'] ?? '#') ?: '#';
                    $is_video = (bool) preg_match('/\.(mp4|webm)$/i', $bg_image);
                    $hasVideo = $hasVideo || $is_video;
                    $first = $index === 0;
                ?>
                <div class="swiper-slide relative w-full h-full flex flex-col pt-[25vh] sm:pt-[28vh] md:pt-[32vh]" <?php echo $multiple ? 'role="group" aria-roledescription="slide" aria-label="' . ($index + 1) . ' of ' . count($slides) . '"' : ''; ?>>
                    <!-- Media Background -->
                    <div class="absolute inset-0 z-0">
                        <?php if ($is_video): ?>
                            <video class="hero-video w-full h-full object-cover opacity-80" data-src="<?php echo htmlspecialchars($bg_image); ?>" <?php echo $poster !== '' ? 'poster="' . htmlspecialchars($poster) . '"' : ''; ?> loop muted playsinline preload="none" aria-hidden="true" data-swiper-parallax="-20%"></video>
                        <?php elseif ($bg_image !== ''): ?>
                            <img src="<?php echo htmlspecialchars($bg_image); ?>" alt="" class="w-full h-full object-cover opacity-80" data-swiper-parallax="-20%"
                                 <?php echo $first ? 'fetchpriority="high" decoding="async"' : 'loading="lazy" decoding="async"'; ?>>
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

                        <?php $tag = $first ? 'h1' : 'h2'; ?>
                        <<?php echo $tag; ?> class="font-display font-black text-5xl md:text-[5rem] lg:text-[7.5rem] xl:text-[8.5rem] tracking-tighter text-white/95 leading-[0.85] uppercase drop-shadow-2xl">
                            <?php echo safe_html($title); ?>
                        </<?php echo $tag; ?>>

                        <?php if ($button_text): ?>
                            <div class="mt-10">
                                <?php render_button_primary(['text' => $button_text, 'url' => $button_url, 'style' => 'light']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($multiple): ?>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
            <?php endif; ?>

            <?php if ($multiple || $hasVideo): ?>
            <button type="button" class="hero-pause absolute bottom-12 right-[5%] z-20 w-11 h-11 rounded-full border border-white/30 bg-black/30 text-white flex items-center justify-center hover:bg-white hover:text-black transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-accent"
                    aria-label="Pause animation" aria-pressed="false">
                <svg class="hero-pause-icon w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>
                <svg class="hero-play-icon w-4 h-4 hidden" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
            </button>
            <?php endif; ?>

            <!-- Scroll indicator -->
            <div class="absolute bottom-12 left-1/2 -translate-x-1/2 z-20 flex flex-col items-center animate-float pointer-events-none" aria-hidden="true">
                <span class="text-[0.6rem] font-bold text-white/50 uppercase tracking-widest mb-2">Scroll</span>
                <div class="w-[1px] h-12 bg-gradient-to-b from-white/50 to-transparent"></div>
            </div>
        </div>
    </section>

    <script>
        function initHeroCarousel() {
            var el = document.querySelector('.hero-swiper');
            if (!el || el.dataset.ready === '1') return;
            el.dataset.ready = '1';

            var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var saveData = navigator.connection && navigator.connection.saveData;
            var multiple = el.dataset.multiple === '1';
            var videos = el.querySelectorAll('video.hero-video');

            // Load and play background videos only when motion and data use are welcome.
            if (!reduceMotion && !saveData) {
                videos.forEach(function (v) {
                    v.src = v.dataset.src;
                    v.play().catch(function () {});
                });
            }

            var swiper = null;
            if (multiple && typeof Swiper !== 'undefined') {
                swiper = new Swiper(el, {
                    effect: 'fade',
                    fadeEffect: { crossFade: true },
                    parallax: !reduceMotion,
                    speed: reduceMotion ? 0 : 1500,
                    loop: true,
                    autoplay: reduceMotion ? false : { delay: 6000, disableOnInteraction: false },
                    pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
                    a11y: { enabled: true },
                });
            }

            var btn = el.querySelector('.hero-pause');
            if (!btn) return;
            var paused = reduceMotion;
            var render = function () {
                btn.setAttribute('aria-pressed', paused ? 'true' : 'false');
                btn.setAttribute('aria-label', paused ? 'Play animation' : 'Pause animation');
                btn.querySelector('.hero-pause-icon').classList.toggle('hidden', paused);
                btn.querySelector('.hero-play-icon').classList.toggle('hidden', !paused);
            };
            render();
            btn.addEventListener('click', function () {
                paused = !paused;
                if (swiper && swiper.autoplay) {
                    paused ? swiper.autoplay.stop() : swiper.autoplay.start();
                }
                videos.forEach(function (v) {
                    if (paused) { v.pause(); } else { if (!v.src) v.src = v.dataset.src; v.play().catch(function () {}); }
                });
                render();
            });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHeroCarousel);
        } else {
            initHeroCarousel();
        }
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
        .swiper-slide-active [data-swiper-parallax-opacity],
        .hero-swiper[data-multiple="0"] [data-swiper-parallax-opacity] {
            opacity: 1 !important;
            transition: opacity 1.5s ease;
        }
    </style>
    <?php
}
