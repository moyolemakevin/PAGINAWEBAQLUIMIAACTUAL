// Visual effects and animations
function initializeAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                element.classList.add('animate-in');
                element.style.animationDelay = element.getAttribute('data-aos-delay') || '0ms';
                observer.unobserve(element);
            }
        });
    }, observerOptions);

    const elementsToAnimate = document.querySelectorAll('[data-aos], .product-card, .category-card, .feature-box');
    elementsToAnimate.forEach(element => {
        observer.observe(element);
    });

    const cards = document.querySelectorAll('.product-card, .category-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.15)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '';
        });
    });
}

function initializeScrollEffects() {
    const skip = (window.matchMedia && window.matchMedia('(max-width: 767px)').matches)
        || (navigator.connection && navigator.connection.saveData);
    if (skip) return;
    let ticking = false;

    function updateScrollEffects() {
        const scrolled = window.pageYOffset;
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            if (scrolled > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        ticking = false;
    }

    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateScrollEffects);
            ticking = true;
        }
    }, { passive: true });
}

function applyNativeLazyLoading() {
    document.querySelectorAll('img:not([loading])').forEach(img => {
        if (img.closest('.hero-section') || img.closest('.hero-image')) {
            return;
        }
        if (img.hasAttribute('data-no-lazy')) {
            return;
        }
        img.setAttribute('loading', 'lazy');
    });
}

function initializeLazyLoading() {
    applyNativeLazyLoading();

    const lazyImages = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;

                img.style.filter = 'blur(5px)';
                img.style.transition = 'filter 0.3s';

                img.src = img.dataset.src;
                img.addEventListener('load', () => {
                    img.style.filter = 'blur(0)';
                    img.classList.add('loaded');
                });

                observer.unobserve(img);
            }
        });
    });

    lazyImages.forEach(img => imageObserver.observe(img));
}

function initializeParallax() {
    const skip = (window.matchMedia && window.matchMedia('(max-width: 767px)').matches)
        || (navigator.connection && navigator.connection.saveData);
    if (skip) return;
    const parallaxElements = document.querySelectorAll('.parallax');
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;

        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
    }, { passive: true });
}

function initializeCounters() {
    const counters = document.querySelectorAll('.counter');

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.textContent, 10);
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;

                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current);
                }, 16);

                counterObserver.unobserve(counter);
            }
        });
    });

    counters.forEach(counter => counterObserver.observe(counter));
}

function initializeTypingEffect() {
    const typingElements = document.querySelectorAll('.typing-effect');
    typingElements.forEach(element => {
        const text = element.textContent;
        element.textContent = '';

        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    typeWriter();
                    observer.unobserve(entry.target);
                }
            });
        });

        observer.observe(element);
    });
}

function initializeHeroImageEffect() {
    const container = document.querySelector('.hero-image');
    if (!container) return;

    const heroImage = container.querySelector('img');
    if (!heroImage) return;

    const cleanup = () => {
        container
            .querySelectorAll('.hero-image-burst, .hero-image-portal, .hero-image-spark-container')
            .forEach(el => el.remove());
    };

    const createSparkContainer = () => {
        const sparkContainer = document.createElement('div');
        sparkContainer.className = 'hero-image-spark-container';

        const sparkCount = 7;
        for (let i = 0; i < sparkCount; i++) {
            const spark = document.createElement('span');
            spark.className = 'hero-image-spark';

            const angle = (Math.PI * 2 * i) / sparkCount;
            const distance = 140 + Math.random() * 80;
            const tx = Math.cos(angle) * distance;
            const ty = Math.sin(angle) * distance;

            spark.style.setProperty('--tx', `${tx}px`);
            spark.style.setProperty('--ty', `${ty}px`);
            spark.style.setProperty('--delay', `${0.1 + i * 0.05}s`);
            spark.style.setProperty('--hue', `${200 + i * 18}`);

            sparkContainer.appendChild(spark);
        }

        return sparkContainer;
    };

    const handleAnimationEnd = event => {
        if (event.animationName !== 'heroImageReappear') return;
        heroImage.classList.remove('hero-image-effect', 'hero-image-animating');
        cleanup();
    };

    heroImage.addEventListener('animationend', handleAnimationEnd);

    const triggerEffect = () => {
        cleanup();

        heroImage.classList.remove('hero-image-effect', 'hero-image-animating');
        // Force reflow so the animation can restart even if triggered consecutively
        void heroImage.offsetWidth;

        heroImage.classList.add('hero-image-animating', 'hero-image-effect');

        const burst = document.createElement('div');
        burst.className = 'hero-image-burst';

        const portal = document.createElement('div');
        portal.className = 'hero-image-portal';

        const sparks = createSparkContainer();

        container.appendChild(burst);
        container.appendChild(portal);
        container.appendChild(sparks);

        requestAnimationFrame(() => {
            burst.classList.add('active');
            portal.classList.add('active');
        });

        setTimeout(() => {
            burst.remove();
            portal.remove();
            sparks.remove();
        }, 2200);
    };

    heroImage.style.cursor = 'pointer';
    heroImage.setAttribute('role', 'button');
    heroImage.setAttribute('tabindex', '0');
    heroImage.setAttribute('aria-label', 'Animar imagen principal');
    heroImage.style.touchAction = 'manipulation';

    heroImage.addEventListener('click', event => {
        event.preventDefault();
        triggerEffect();
    });

    heroImage.addEventListener('keyup', event => {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            triggerEffect();
        }
    });

    container.addEventListener('click', event => {
        if (event.target !== container) return;
        triggerEffect();
    });

    container.querySelectorAll('[data-trigger="hero-image-effect"]').forEach(triggerEl => {
        triggerEl.addEventListener('click', triggerEffect);
    });
}

function initializeHeroTitleTypingEffect() {
    const reducedMotionQuery = window.matchMedia
        ? window.matchMedia('(prefers-reduced-motion: reduce)')
        : null;
    if (reducedMotionQuery && reducedMotionQuery.matches) {
        return;
    }
    const title = document.querySelector('.hero-title-typing');
    if (!title || title.dataset.typingInitialized === 'true') return;
    title.dataset.typingInitialized = 'true';

    const originalNodes = Array.from(title.childNodes);
    title.innerHTML = '';
    let delay = 0;
    const delayStep = 0.045;

    const buildAnimatedContent = (node) => {
        if (node.nodeType === Node.TEXT_NODE) {
            const text = node.textContent || '';
            if (!text.length) {
                return document.createDocumentFragment();
            }
            const fragment = document.createDocumentFragment();
            for (const char of text) {
                if (char === '\n' || char === '\r') continue;
                const span = document.createElement('span');
                span.className = 'hero-title-char';
                if (char === ' ') {
                    span.innerHTML = '&nbsp;';
                    delay += delayStep * 0.4;
                } else {
                    span.textContent = char;
                    delay += delayStep;
                }
                span.style.setProperty('--char-delay', `${delay.toFixed(3)}s`);
                fragment.appendChild(span);
            }
            return fragment;
        }
        if (node.nodeType === Node.ELEMENT_NODE) {
            const clone = node.cloneNode(false);
            node.childNodes.forEach(child => {
                const builtChild = buildAnimatedContent(child);
                if (builtChild) {
                    clone.appendChild(builtChild);
                }
            });
            return clone;
        }
        return document.createDocumentFragment();
    };

    originalNodes.forEach(node => {
        const built = buildAnimatedContent(node);
        if (built) {
            title.appendChild(built);
        }
    });

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            title.classList.add('hero-title-typing--visible');
        });
    });
}

// Inject custom keyframes used by UI animations
(function injectCustomAnimations() {
    const customAnimations = document.createElement('style');
    customAnimations.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        @keyframes ripple {
            to { transform: scale(2); opacity: 0; }
        }

        @keyframes floatUp {
            0% { transform: translateY(0) scale(1); opacity: 1; }
            50% { transform: translateY(-50px) scale(1.1); opacity: 0.8; }
            100% { transform: translateY(-100px) scale(0.8); opacity: 0; }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .animate-in {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .navbar.scrolled {
            background: rgba(99, 102, 241, 0.98) !important;
            backdrop-filter: blur(20px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
    `;
    document.head.appendChild(customAnimations);
})();
