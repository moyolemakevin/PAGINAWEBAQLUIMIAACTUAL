/* Keep floating promotions and WhatsApp clear of the page's usable controls. */
(() => {
    const main = document.querySelector('main');
    const floats = [...document.querySelectorAll('.product-promotion, .whatsapp-float')];
    if (!main || !floats.length) return;
    let frame = 0;
    let refreshControls = true;
    let controls = [];
    const state = new WeakMap();
    floats.forEach(element => state.set(element, { blocked: false, visibility: element.style.visibility, aria: element.getAttribute('aria-hidden'), inert: element.inert }));
    const update = () => {
        frame = 0;
        if (refreshControls) {
            controls = [...main.querySelectorAll('a[href], button, input, select, textarea, [tabindex="0"], .category-tile-cta')].filter(el => !el.closest('.product-promotion'));
            refreshControls = false;
        }
        const bounds = controls.flatMap(element => {
            if (!element.getClientRects().length) return [];
            const rect = element.getBoundingClientRect();
            // A large linked photo or tile is content; protect its explicit actions instead.
            if (element.matches('a') && rect.height > 100) return [];
            return rect.width && rect.height && rect.bottom > 0 && rect.top < innerHeight ? [rect] : [];
        });
        const decisions = floats.map(element => {
            const rect = element.getBoundingClientRect();
            const blocked = !element.hidden && rect.width > 0 && bounds.some(control =>
                rect.left < control.right + 6 && rect.right > control.left - 6 &&
                rect.top < control.bottom + 6 && rect.bottom > control.top - 6);
            return { element, blocked };
        });
        decisions.forEach(({ element, blocked }) => {
            const previous = state.get(element);
            if (previous.blocked === blocked) return;
            previous.blocked = blocked;
            element.style.visibility = blocked ? 'hidden' : previous.visibility;
            element.inert = blocked || previous.inert;
            if (blocked) element.setAttribute('aria-hidden', 'true');
            else if (previous.aria === null) element.removeAttribute('aria-hidden');
            else element.setAttribute('aria-hidden', previous.aria);
        });
    };
    const schedule = () => { if (!frame) frame = requestAnimationFrame(update); };
    window.addEventListener('scroll', schedule, { passive: true });
    window.addEventListener('resize', schedule, { passive: true });
    document.addEventListener('load', schedule, true);
    const observer = new MutationObserver(() => { refreshControls = true; schedule(); });
    observer.observe(main, { childList: true, subtree: true, attributes: true, attributeFilter: ['hidden'] });
    if ('ResizeObserver' in window) {
        const resizeObserver = new ResizeObserver(schedule);
        floats.forEach(element => resizeObserver.observe(element));
    }
    if (document.fonts) document.fonts.ready.then(schedule);
    schedule();
})();
