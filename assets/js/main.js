document.addEventListener('DOMContentLoaded', function () {
    var menuButton = document.getElementById('mobileMenuButton');
    var mobileMenu = document.getElementById('mobileMenu');

    if (menuButton && mobileMenu) {
        menuButton.addEventListener('click', function () {
            var isHidden = mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            menuButton.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        });
    }

    var slider = document.getElementById('heroSlider');
    if (slider) {
        var slides = slider.querySelectorAll('.hero-slide');
        var dotsWrap = document.getElementById('heroDots');
        var prevBtn = document.getElementById('heroPrev');
        var nextBtn = document.getElementById('heroNext');
        var heading = document.getElementById('heroHeading');
        var subheading = document.getElementById('heroSubheading');
        var count = document.getElementById('heroCount');
        var textPanel = document.querySelector('.hero-text-panel');
        var slideIndex = 0;
        var timer = null;
        var autoDelayMs = 5000;

        if (!slides.length) {
            return;
        }

        function renderDots() {
            if (!dotsWrap) {
                return;
            }
            dotsWrap.innerHTML = '';
            slides.forEach(function (_, idx) {
                var dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'hero-dot' + (idx === slideIndex ? ' active' : '');
                dot.setAttribute('aria-label', 'Go to slide ' + (idx + 1));
                dot.addEventListener('click', function () {
                    slideIndex = idx;
                    showSlide();
                    restartAuto();
                });
                dotsWrap.appendChild(dot);
            });
        }

        function showSlide() {
            slides.forEach(function (slide, idx) {
                slide.classList.toggle('active', idx === slideIndex);
            });

            if (heading) {
                heading.textContent = slides[slideIndex].getAttribute('data-title') || '';
            }

            if (subheading) {
                subheading.textContent = slides[slideIndex].getAttribute('data-subtitle') || '';
            }

            if (count) {
                count.textContent = (slideIndex + 1) + ' / ' + slides.length;
            }

            if (textPanel) {
                textPanel.classList.remove('hero-text-animate');
                void textPanel.offsetWidth;
                textPanel.classList.add('hero-text-animate');
            }

            renderDots();
        }

        function nextSlide() {
            slideIndex = (slideIndex + 1) % slides.length;
            showSlide();
        }

        function prevSlide() {
            slideIndex = (slideIndex - 1 + slides.length) % slides.length;
            showSlide();
        }

        function startAuto() {
            if (slides.length > 1) {
                timer = window.setInterval(nextSlide, autoDelayMs);
            }
        }

        function stopAuto() {
            window.clearInterval(timer);
            timer = null;
        }

        function restartAuto() {
            stopAuto();
            startAuto();
        }

        if (nextBtn && slides.length > 1) {
            nextBtn.addEventListener('click', function () {
                nextSlide();
                restartAuto();
            });
        }

        if (prevBtn && slides.length > 1) {
            prevBtn.addEventListener('click', function () {
                prevSlide();
                restartAuto();
            });
        }

        showSlide();
        startAuto();

        slider.addEventListener('mouseenter', stopAuto);
        slider.addEventListener('mouseleave', startAuto);
        slider.addEventListener('focusin', stopAuto);
        slider.addEventListener('focusout', startAuto);

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stopAuto();
            } else {
                startAuto();
            }
        });
    }

    var revealItems = document.querySelectorAll('.section-card, .page-hero .max-w-6xl');
    revealItems.forEach(function (item) {
        item.classList.add('reveal-item');
    });

    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        revealItems.forEach(function (item) {
            observer.observe(item);
        });
    } else {
        revealItems.forEach(function (item) {
            item.classList.add('in-view');
        });
    }
});
