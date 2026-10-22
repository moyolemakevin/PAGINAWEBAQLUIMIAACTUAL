/* Native disclosures keep footer links usable with keyboard and without scripts. */
(() => {
    const groups = [...document.querySelectorAll('[data-footer-group]')];
    if (!groups.length) return;
    const mobile = window.matchMedia('(max-width: 767px)');
    const sync = () => groups.forEach(group => {
        group.open = !mobile.matches;
        group.querySelector('summary').tabIndex = mobile.matches ? 0 : -1;
    });
    groups.forEach(group => group.querySelector('summary').addEventListener('click', event => {
        if (!mobile.matches) event.preventDefault();
    }));
    mobile.addEventListener('change', sync);
    sync();
})();
