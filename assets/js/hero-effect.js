// Minimal, resilient hero image click effect
(function () {
  function bindHeroEffect() {
    const container = document.querySelector('.hero-image');
    if (!container) return;
    const img = container.querySelector('img');
    if (!img || img.dataset.heroEffectBound === 'true') return;

    const cleanup = () => {
      container.querySelectorAll('.hero-image-burst, .hero-image-portal, .hero-image-spark-container')
        .forEach(el => el.remove());
    };

    const trigger = () => {
      // Restart CSS animation
      img.classList.remove('hero-image-effect');
      void img.offsetWidth;
      img.classList.add('hero-image-effect');

      // Optional supplemental visuals
      const burst = document.createElement('div');
      burst.className = 'hero-image-burst active';
      const portal = document.createElement('div');
      portal.className = 'hero-image-portal active';
      container.appendChild(burst);
      container.appendChild(portal);
      setTimeout(cleanup, 2200);
    };

    img.style.cursor = 'pointer';
    img.addEventListener('click', function (e) { e.preventDefault(); trigger(); });
    img.addEventListener('animationend', function (e) {
      if (e.animationName === 'heroImageReappear') {
        img.classList.remove('hero-image-effect');
        cleanup();
      }
    });

    img.dataset.heroEffectBound = 'true';
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindHeroEffect);
  } else {
    bindHeroEffect();
  }
})();

