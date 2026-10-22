// Main JavaScript entry point

document.addEventListener('DOMContentLoaded', function() {
    const prefersReducedMotion = window.matchMedia
        && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const smallViewport = window.matchMedia && window.matchMedia('(max-width: 575.98px)').matches;
    const saveData = navigator.connection && navigator.connection.saveData;
    const lowMemory = navigator.deviceMemory && navigator.deviceMemory <= 2;
    const skipAnimations = prefersReducedMotion || smallViewport || saveData || lowMemory;

    if (typeof initializeCart === 'function') {
        initializeCart();
    }

    if (typeof initializeEventListeners === 'function') {
        initializeEventListeners();
    }

    if (typeof initializeLazyLoading === 'function') {
        initializeLazyLoading();
    }

    if (!skipAnimations && !document.body.classList.contains('experience-home')) {
        if (typeof initializeAnimations === 'function') {
            initializeAnimations();
        }
        if (typeof initializeScrollEffects === 'function') {
            initializeScrollEffects();
        }
        if (typeof initializeParallax === 'function') {
            initializeParallax();
        }
        if (typeof initializeCounters === 'function') {
            initializeCounters();
        }
        if (typeof initializeTypingEffect === 'function') {
            initializeTypingEffect();
        }
        if (typeof initializeHeroImageEffect === 'function') {
            initializeHeroImageEffect();
        }
        if (typeof initializeHeroTitleTypingEffect === 'function') {
            initializeHeroTitleTypingEffect();
        }
    }

    if (typeof hideLoadingScreen === 'function') {
        hideLoadingScreen();
    }
});
