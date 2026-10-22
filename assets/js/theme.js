// Lightweight, robust dark mode toggle initializer
// Ensures the toggle works even if other scripts fail
(function () {
  function setIcon(toggle, isDark) {
    if (!toggle) return;
    toggle.innerHTML = isDark
      ? '<i class="fas fa-sun"></i>'
      : '<i class="fas fa-moon"></i>';
    toggle.setAttribute('aria-pressed', String(isDark));
    toggle.setAttribute('title', isDark ? 'Modo claro' : 'Modo oscuro');
  }

  function updateAllToggles(toggles, isDark) {
    toggles.forEach(function (toggle) {
      setIcon(toggle, isDark);
    });
  }

  function applyThemeFromStorage(toggles) {
    try {
      const saved = localStorage.getItem('darkMode');
      const enable = saved === 'enabled';
      document.body.classList.toggle('dark-mode', enable);
      updateAllToggles(toggles, enable);
    } catch (e) {
      // Fallback to light mode if storage fails
      document.body.classList.remove('dark-mode');
      updateAllToggles(toggles, false);
    }
  }

  function initThemeToggle() {
    const toggles = Array.prototype.slice.call(document.querySelectorAll('[data-theme-toggle]'));
    if (!toggles.length) return;

    applyThemeFromStorage(toggles);

    toggles.forEach(function (toggle) {
      if (toggle.dataset.bound === 'true') return;

      toggle.addEventListener('click', function () {
        const isDark = !document.body.classList.contains('dark-mode');
        document.body.classList.toggle('dark-mode', isDark);
        try {
          localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
        } catch (e) {
          // ignore storage errors
        }
        updateAllToggles(toggles, isDark);
      });

      toggle.dataset.bound = 'true';
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initThemeToggle);
  } else {
    initThemeToggle();
  }
})();
