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
})();
