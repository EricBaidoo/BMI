/**
 * BMI — Main JavaScript (GSAP Powered)
 */

/* ============================================================
   CUSTOM CURSOR
   ============================================================ */
function initCustomCursor() {
    const cursor = document.createElement('div');
    cursor.classList.add('custom-cursor');
    document.body.appendChild(cursor);
    document.documentElement.classList.add('has-custom-cursor');

    document.addEventListener('mousemove', (e) => {
        cursor.style.left = e.clientX + 'px';
        cursor.style.top = e.clientY + 'px';
    });

    const hoverElements = document.querySelectorAll('a, button, input, .horizontal-panel, [role="button"]');
    hoverElements.forEach(el => {
        el.addEventListener('mouseenter', () => cursor.classList.add('hover'));
        el.addEventListener('mouseleave', () => cursor.classList.remove('hover'));
    });
}

/* ============================================================
   GSAP ANIMATIONS
   ============================================================ */
function initGSAP() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
        console.warn("GSAP not loaded. Running fallback.");
        document.documentElement.classList.remove('js-anim');
        document.querySelectorAll('.opacity-0').forEach(el => {
            el.classList.remove('opacity-0');
            el.style.opacity = 1;
        });
        return;
    }
    
    gsap.registerPlugin(ScrollTrigger);

    // 1. Hero Animations
    const heroTitle = document.querySelector('.gs-hero-title');
    const heroText = document.querySelector('.gs-hero-text');
    const heroBtn = document.querySelector('.gs-hero-btn');
    const heroScroll = document.querySelector('.gs-hero-scroll');
    const heroBg = document.querySelector('.gs-hero-bg img, .gs-hero-bg video');
    
    if (heroTitle) {
        const tl = gsap.timeline();
        
        if (heroBg) {
            gsap.fromTo(heroBg, 
                { scale: 1.1, filter: 'blur(10px)' }, 
                { scale: 1, filter: 'blur(0px)', duration: 2, ease: 'power3.out' }
            );
        }

        if (heroText) {
            tl.fromTo(heroText, 
                { y: 30, opacity: 0 }, 
                { y: 0, opacity: 1, duration: 1, ease: 'power3.out' }
            );
        }
        
        tl.fromTo(heroTitle,
            { y: 50, opacity: 0, rotationX: -20 },
            { y: 0, opacity: 1, rotationX: 0, duration: 1.2, ease: 'power4.out' },
            heroText ? "-=0.6" : 0
        );

        if (heroBtn) {
            tl.fromTo(heroBtn,
                { y: 30, opacity: 0 },
                { y: 0, opacity: 1, duration: 0.8, ease: 'power3.out' },
                "-=0.8"
            );
        }

        if (heroScroll) {
            tl.fromTo(heroScroll,
                { opacity: 0 },
                { opacity: 1, duration: 1 },
                "-=0.4"
            );
        }
    }

    // 2. Global Reveal Animations (ScrollTrigger)
    // Disabled temporarily because elements were getting stuck at opacity 0
    const revealSections = document.querySelectorAll('.gs-reveal-section');
    revealSections.forEach(section => {
        const revealUps = section.querySelectorAll('.gs-reveal-up, .gs-reveal-left, .gs-reveal-right, .gs-reveal-opacity');
        if (revealUps.length) {
            gsap.fromTo(revealUps, 
                { y: 60, autoAlpha: 0 },
                { 
                    y: 0, 
                    autoAlpha: 1, 
                    duration: 1, 
                    stagger: 0.1, 
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: section,
                        start: "top 80%",
                    }
                }
            );
        }
    });

    // 3. Horizontal Scroll Section (Ministries)
    const horizontalSection = document.getElementById('ministries-horizontal');
    const horizontalContainer = document.querySelector('.horizontal-scroll-container');
    
    if (horizontalSection && horizontalContainer) {
        // Calculate the amount to scroll horizontally
        const scrollAmount = horizontalContainer.scrollWidth - window.innerWidth + 100;
        
        if (scrollAmount > 0 && window.innerWidth > 768) {
            gsap.to(horizontalContainer, {
                x: -scrollAmount,
                ease: "none",
                scrollTrigger: {
                    trigger: horizontalSection,
                    pin: true,
                    scrub: 1,
                    start: "center center",
                    end: () => "+=" + scrollAmount
                }
            });
        }
    }

    // 4. Parallax Backgrounds
    const parallaxBgs = document.querySelectorAll('.gs-parallax-bg');
    parallaxBgs.forEach(bg => {
        gsap.to(bg, {
            yPercent: 30,
            ease: "none",
            scrollTrigger: {
                trigger: bg.parentElement,
                start: "top bottom",
                end: "bottom top",
                scrub: true
            }
        });
    });
}

/* ============================================================
   SMART HEADER (Hide on scroll down, show on scroll up)
   ============================================================ */
function initSmartHeader() {
    const header = document.getElementById('site-header');
    if (!header) return;

    // Remove any GSAP scroll triggers that might conflict
    const toggleHeaderBackground = () => {
        if (window.scrollY > 50) {
            header.classList.remove('header-transparent');
            header.classList.add('header-solid');
            // Also adjust padding for a tighter look when scrolled
            header.classList.remove('py-8');
            header.classList.add('py-4');
        } else {
            header.classList.remove('header-solid');
            header.classList.add('header-transparent');
            // Revert padding
            header.classList.remove('py-4');
            header.classList.add('py-8');
        }
    };

    window.addEventListener('scroll', toggleHeaderBackground, { passive: true });
    // Run once on load to set initial state
    toggleHeaderBackground();
}

/* ============================================================
   INITIALIZATION
   ============================================================ */
const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const hasFinePointer = window.matchMedia && window.matchMedia('(pointer: fine)').matches;

/* ============================================================
   BACKGROUND VIDEOS (video.bg-video with data-src)
   Downloaded and played only when motion and data use are welcome; each gets a pause button.
   ============================================================ */
function initBackgroundVideos() {
    const saveData = navigator.connection && navigator.connection.saveData;
    document.querySelectorAll('video.bg-video[data-src]').forEach(video => {
        if (prefersReducedMotion || saveData) return;
        video.src = video.dataset.src;
        video.play().catch(() => {});

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'absolute bottom-6 right-6 z-20 w-11 h-11 rounded-full border border-white/30 bg-black/30 text-white flex items-center justify-center hover:bg-white hover:text-black transition-colors';
        const setState = paused => {
            btn.setAttribute('aria-label', paused ? 'Play background video' : 'Pause background video');
            btn.innerHTML = paused
                ? '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>'
                : '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>';
        };
        setState(false);
        btn.addEventListener('click', () => {
            if (video.paused) { video.play().catch(() => {}); setState(false); }
            else { video.pause(); setState(true); }
        });
        // Attach to the banner (the video's layer sits below the page content).
        (video.parentElement.parentElement || video.parentElement).appendChild(btn);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initSmartHeader();
    initBackgroundVideos();

    if (prefersReducedMotion) {
        // Show everything immediately: no scroll reveals, parallax or pinned scrolling.
        document.documentElement.classList.add('reduce-motion');
        document.querySelectorAll('.opacity-0').forEach(el => el.style.opacity = 1);
    } else {
        try {
            initGSAP();
        } catch (e) {
            console.error("GSAP Initialization failed:", e);
            // Fallback: force show text if GSAP fails
            document.documentElement.classList.remove('js-anim');
            document.querySelectorAll('.opacity-0').forEach(el => el.style.opacity = 1);
        }
    }

    // The custom cursor only makes sense with a mouse, and not when motion is reduced.
    if (hasFinePointer && !prefersReducedMotion) {
        initCustomCursor();
    }
});
