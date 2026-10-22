/* Progressive enhancement: every collection and product remains a real link. */
(() => {
    const showcase = document.querySelector('.experience-showcase');
    if (!showcase) return;
    const selectors = [...showcase.querySelectorAll('[data-category-choice]')];
    selectors.forEach(button => {
        button.addEventListener('click', () => {
            const category = JSON.parse(button.dataset.categoryChoice);
            selectors.forEach(selector => selector.setAttribute('aria-pressed', String(selector === button)));
            showcase.dataset.category = category.slug;
            showcase.querySelector('[data-category-icon]').className = category.icon;
            showcase.querySelector('[data-category-name]').textContent = category.name;
            showcase.querySelector('[data-category-summary]').textContent = category.summary;
            const link = showcase.querySelector('[data-category-link]');
            link.href = category.url;
            link.setAttribute('aria-label', `Explorar ${category.name}`);
            if (!motion.matches) {
                const preview = showcase.querySelector('.category-preview');
                preview.classList.remove('preview-changing');
                void preview.offsetWidth;
                preview.classList.add('preview-changing');
            }
        });
    });

    const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const pointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    const stage = showcase.querySelector('.brand-visual');
    let frame = 0;
    showcase.addEventListener('pointermove', event => {
        if (motion.matches || !pointer.matches || frame) return;
        frame = requestAnimationFrame(() => {
            const rect = showcase.getBoundingClientRect();
            stage.style.setProperty('--tilt-x', `${((event.clientX - rect.left) / rect.width - .5) * 8}deg`);
            stage.style.setProperty('--tilt-y', `${((event.clientY - rect.top) / rect.height - .5) * -6}deg`);
            frame = 0;
        });
    }, { passive: true });
    const reset = () => {
        cancelAnimationFrame(frame);
        frame = 0;
        stage.style.removeProperty('--tilt-x');
        stage.style.removeProperty('--tilt-y');
    };
    showcase.addEventListener('pointerleave', reset);
    motion.addEventListener('change', reset);

    // Reveal on entry without hiding any content when scripts are unavailable.
    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                if (!motion.matches) {
                    entry.target.classList.add('reveal-enter');
                    entry.target.addEventListener('animationend', () => entry.target.classList.remove('reveal-enter'), { once: true });
                }
                revealObserver.unobserve(entry.target);
            });
        }, { threshold: .12 });
        document.querySelectorAll('.experience-section-heading, .category-tile, .discovery-card, .experience-steps article, .experience-contact-panel').forEach(el => revealObserver.observe(el));
    }

    document.querySelectorAll('.category-tile').forEach(tile => {
        let tileFrame = 0;
        tile.addEventListener('pointermove', event => {
            if (motion.matches || !pointer.matches || tileFrame) return;
            tileFrame = requestAnimationFrame(() => {
                const rect = tile.getBoundingClientRect();
                tile.style.setProperty('--pointer-x', `${event.clientX - rect.left}px`);
                tile.style.setProperty('--pointer-y', `${event.clientY - rect.top}px`);
                tileFrame = 0;
            });
        }, { passive: true });
    });

    const progress = document.querySelector('[data-reading-progress]');
    let scrollFrame = 0;
    const updateProgress = () => {
        const total = document.documentElement.scrollHeight - window.innerHeight;
        progress.style.transform = `scaleX(${total > 0 ? Math.min(1, Math.max(0, window.scrollY / total)) : 0})`;
        scrollFrame = 0;
    };
    if (progress) {
        const schedule = () => { if (!scrollFrame) scrollFrame = requestAnimationFrame(updateProgress); };
        window.addEventListener('scroll', schedule, { passive: true });
        window.addEventListener('resize', schedule, { passive: true });
        updateProgress();
    }

    const carousel = document.querySelector('[data-spotlight]');
    if (!carousel) return;
    const slides = [...carousel.querySelectorAll('[data-spotlight-slide]')];
    const dots = [...carousel.querySelectorAll('[data-spotlight-dot]')];
    if (slides.length < 2) return;
    const controls = carousel.querySelector('[data-spotlight-controls]');
    const playButton = carousel.querySelector('[data-spotlight-play]');
    const liveRegion = carousel.querySelector('[data-spotlight-slides]');
    controls.hidden = false;
    let current = 0;
    let manualPause = false;
    let hovered = false;
    let visible = false;
    let timer = null;
    const setSlide = index => {
        current = (index + slides.length) % slides.length;
        slides.forEach((slide, i) => { slide.hidden = i !== current; });
        dots.forEach((dot, i) => dot.setAttribute('aria-current', String(i === current)));
        carousel.querySelector('[data-spotlight-count]').textContent = `${String(current + 1).padStart(2, '0')} / ${String(slides.length).padStart(2, '0')}`;
        const nextImage = slides[(current + 1) % slides.length].querySelector('img');
        if (nextImage) nextImage.loading = 'eager';
    };
    const syncRotation = () => {
        clearInterval(timer);
        timer = null;
        const stopped = manualPause || hovered || !visible || document.hidden || motion.matches;
        playButton.hidden = motion.matches;
        playButton.setAttribute('aria-label', manualPause ? 'Reanudar carrusel' : 'Pausar carrusel');
        playButton.querySelector('i').className = `fas fa-${manualPause ? 'play' : 'pause'}`;
        liveRegion.setAttribute('aria-live', stopped ? 'polite' : 'off');
        if (!stopped) timer = setInterval(() => setSlide(current + 1), 7000);
    };
    const choose = index => {
        manualPause = true;
        syncRotation();
        setSlide(index);
    };
    dots.forEach((dot, i) => dot.addEventListener('click', () => choose(i)));
    carousel.querySelector('[data-spotlight-prev]').addEventListener('click', () => choose(current - 1));
    carousel.querySelector('[data-spotlight-next]').addEventListener('click', () => choose(current + 1));
    playButton.addEventListener('click', () => { manualPause = !manualPause; syncRotation(); });
    carousel.addEventListener('pointerenter', event => { if (event.pointerType === 'mouse') { hovered = true; syncRotation(); } });
    carousel.addEventListener('pointerleave', () => { hovered = false; syncRotation(); });
    carousel.addEventListener('focusin', event => {
        if (event.target !== playButton && !playButton.contains(event.target)) manualPause = true;
        syncRotation();
    });
    document.addEventListener('visibilitychange', syncRotation);
    motion.addEventListener('change', syncRotation);
    if ('IntersectionObserver' in window) {
        const carouselObserver = new IntersectionObserver(entries => {
            visible = entries[0].isIntersecting;
            document.body.classList.toggle('spotlight-in-view', visible);
            syncRotation();
        }, { threshold: .25 });
        carouselObserver.observe(carousel);
    } else { visible = true; }
    setSlide(0);
    syncRotation();
})();
