/**
 * BMI — Main JavaScript
 * Lightweight cinematic interactions (no AOS dependency).
 */

/* ============================================================
   SCROLL REVEAL — IntersectionObserver-based
   ============================================================ */
function initScrollReveal() {
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
    if (!revealElements.length) return;

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        revealElements.forEach(function (el) {
            observer.observe(el);
        });
    } else {
        // Fallback: show everything
        revealElements.forEach(function (el) {
            el.classList.add('revealed');
        });
    }
}

/* ============================================================
   ANIMATED COUNTERS
   ============================================================ */
function initCounters() {
    const counters = document.querySelectorAll('.counter');
    if (!counters.length) return;

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                const el = entry.target;
                if (el.dataset.counted) return;
                el.dataset.counted = 'true';

                const target = parseInt(el.dataset.target, 10) || 0;
                const duration = 2000;
                const start = performance.now();

                function step(now) {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    // Ease out cubic
                    const eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.floor(eased * target);
                    if (progress < 1) {
                        requestAnimationFrame(step);
                    } else {
                        el.textContent = target;
                    }
                }
                requestAnimationFrame(step);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(function (el) {
        observer.observe(el);
    });
}

/* ============================================================
   PARALLAX BACKGROUNDS
   ============================================================ */
function initParallax() {
    const parallaxElements = document.querySelectorAll('.parallax-bg');
    if (!parallaxElements.length) return;

    function onScroll() {
        const scrollY = window.pageYOffset;
        parallaxElements.forEach(function (el) {
            const rect = el.getBoundingClientRect();
            const speed = parseFloat(el.dataset.speed) || 0.3;
            if (rect.bottom > 0 && rect.top < window.innerHeight) {
                const offset = scrollY * speed;
                el.style.transform = 'translateY(' + offset + 'px) scale(1.1)';
            }
        });
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

/* ============================================================
   HERO CAROUSEL (index.php)
   ============================================================ */
function initHeroCarousel() {
    var slides = document.querySelectorAll('.carousel-slide');
    if (!slides.length || slides.length < 2) return;

    var currentIndex = 0;
    var timer = null;
    var autoDelayMs = 6000;

    function showSlide(index) {
        slides.forEach(function (slide, i) {
            if (i === index) {
                slide.classList.remove('opacity-0', 'z-10');
                slide.classList.add('opacity-100', 'z-20');
            } else {
                slide.classList.add('opacity-0', 'z-10');
                slide.classList.remove('opacity-100', 'z-20');
            }
        });
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % slides.length;
        showSlide(currentIndex);
    }

    function startAuto() {
        timer = setInterval(nextSlide, autoDelayMs);
    }

    function stopAuto() {
        clearInterval(timer);
        timer = null;
    }

    // Expose global goToSlide for inline onclick
    window.goToSlide = function (index) {
        currentIndex = index;
        showSlide(currentIndex);
        stopAuto();
        startAuto();
    };

    showSlide(0);
    startAuto();

    // Pause on hover
    var heroSection = document.getElementById('hero-carousel');
    if (heroSection) {
        heroSection.addEventListener('mouseenter', stopAuto);
        heroSection.addEventListener('mouseleave', startAuto);
    }

    // Pause when tab is hidden
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) { stopAuto(); } else { startAuto(); }
    });
}

/* ============================================================
   SMART HEADER (Hide on scroll down, show on scroll up)
   ============================================================ */
function initSmartHeader() {
    const header = document.getElementById('site-header');
    if (!header) return;

    let lastScroll = 0;
    
    // Remove Alpine-based classes if any are clinging
    header.classList.remove('header-solid', 'header-transparent', 'py-4', 'py-8');
    
    function onScroll() {
        const currentScroll = window.pageYOffset;
        
        // At the very top
        if (currentScroll <= 50) {
            header.classList.remove('header-hidden', 'header-solid', 'py-4');
            header.classList.add('header-visible', 'header-transparent', 'py-8');
        } 
        // Scrolling Down
        else if (currentScroll > lastScroll && currentScroll > 200) {
            header.classList.remove('header-visible');
            header.classList.add('header-hidden');
        } 
        // Scrolling Up
        else if (currentScroll < lastScroll) {
            header.classList.remove('header-hidden', 'header-transparent', 'py-8');
            header.classList.add('header-visible', 'header-solid', 'py-4');
        }
        
        lastScroll = currentScroll;
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    // Run once on load
    onScroll();
}

/* ============================================================
   INITIALIZATION
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    initScrollReveal();
    initCounters();
    initParallax();
    initHeroCarousel();
    initSmartHeader();
});

// Re-initialize on Swup page transitions
document.addEventListener('swup:pageView', function () {
    initScrollReveal();
    initCounters();
    initParallax();
    initHeroCarousel();
    initSmartHeader();
});
