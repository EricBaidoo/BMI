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
/* ============================================================
   INITIALIZATION
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    try {
        initGSAP();
    } catch (e) {
        console.error("GSAP Initialization failed:", e);
        // Fallback: force show text if GSAP fails
        document.querySelectorAll('.opacity-0').forEach(el => el.style.opacity = 1);
    }
    initCustomCursor();
});

// Re-initialize on Swup page transitions
document.addEventListener('swup:pageView', function () {
    ScrollTrigger.getAll().forEach(t => t.kill()); // Kill old triggers
    initGSAP();
    
    // Reattach cursor listeners
    const cursor = document.querySelector('.custom-cursor');
    if (cursor) {
        const hoverElements = document.querySelectorAll('a, button, input, .horizontal-panel, [role="button"]');
        hoverElements.forEach(el => {
            el.addEventListener('mouseenter', () => cursor.classList.add('hover'));
            el.addEventListener('mouseleave', () => cursor.classList.remove('hover'));
        });
    }
});
