/* Promotional cards use only public catalog data already rendered by the server. */
(() => {
    const promotion = document.querySelector('[data-product-promo]');
    if (!promotion) return;
    const storageKey = 'moyoProductPromotionsDismissed';
    try { if (sessionStorage.getItem(storageKey) === '1') return; } catch (_) { /* Optional storage. */ }
    const products = [...document.querySelectorAll('[data-spotlight-slide]')].map(slide => ({
        sold: slide.dataset.productSold === 'true',
        name: slide.querySelector('h2 a').textContent.trim(),
        url: slide.querySelector('h2 a').href,
        image: slide.querySelector('.spotlight-image img').src,
        price: slide.querySelector('.spotlight-price > span').textContent.trim()
    }));
    if (!products.length) return;
    const chatgptIndex = products.findIndex(product => /chat\s*gpt/i.test(product.name));
    if (chatgptIndex > 0) products.unshift(...products.splice(chatgptIndex, 1));
    const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const firstDelay = 2000;
    const visibleDuration = 7000;
    const betweenCards = 8000;
    const preload = product => { const image = new Image(); image.src = product.image; };
    preload(products[0]);
    let index = 0;
    let shown = 0;
    let timer = null;
    let dismissed = false;
    const queue = delay => {
        clearTimeout(timer);
        if (dismissed || document.hidden || shown >= (motion.matches ? 1 : 4)) return;
        timer = setTimeout(show, delay);
    };
    const close = () => {
        dismissed = true;
        clearTimeout(timer);
        promotion.hidden = true;
        try { sessionStorage.setItem(storageKey, '1'); } catch (_) { /* Optional storage. */ }
    };
    const hideLater = () => {
        clearTimeout(timer);
        if (promotion.matches(':hover') || promotion.contains(document.activeElement)) {
            timer = setTimeout(hideLater, 1500);
            return;
        }
        promotion.hidden = true;
        queue(betweenCards);
    };
    function show() {
        if (dismissed || document.hidden) return;
        if (document.activeElement.matches('input, textarea, select')) { queue(8000); return; }
        const product = products[index % products.length];
        index++;
        shown++;
        const link = promotion.querySelector('[data-promo-link]');
        link.href = product.url;
        link.setAttribute('aria-label', `Ver ${product.name}`);
        promotion.querySelector('[data-promo-image]').src = product.image;
        promotion.querySelector('[data-promo-label]').textContent = product.sold ? 'Producto vendido' : 'Producto destacado';
        promotion.querySelector('[data-promo-icon]').className = product.sold ? 'fas fa-circle-check' : 'fas fa-star';
        promotion.querySelector('[data-promo-name]').textContent = product.name;
        promotion.querySelector('[data-promo-price]').textContent = product.price;
        promotion.querySelector('[data-promo-note]').textContent = product.sold ? 'Un cliente compró este producto.' : 'Consulta disponibilidad y detalles.';
        promotion.hidden = false;
        preload(products[index % products.length]);
        if (!motion.matches) timer = setTimeout(hideLater, visibleDuration);
    }
    promotion.querySelector('[data-promo-close]').addEventListener('click', close);
    promotion.addEventListener('keydown', event => {
        if (event.key === 'Escape') { event.preventDefault(); close(); }
    });
    document.addEventListener('visibilitychange', () => {
        clearTimeout(timer);
        promotion.hidden = true;
        if (!document.hidden) queue(firstDelay);
    });
    motion.addEventListener('change', () => {
        clearTimeout(timer);
        if (promotion.hidden) queue(firstDelay);
        else if (!motion.matches) timer = setTimeout(hideLater, visibleDuration);
    });
    queue(firstDelay);
})();
