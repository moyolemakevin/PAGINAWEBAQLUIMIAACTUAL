// Lightweight, robust dark mode toggle initializer
// Ensures the toggle works even if other scripts fail
(function () {
  function setIcon(toggle, isDark) {
    if (!toggle) return;
    toggle.innerHTML = isDark
      ? '<i class="fas fa-sun" aria-hidden="true"></i><span class="theme-label">Modo claro</span>'
      : '<i class="fas fa-moon" aria-hidden="true"></i><span class="theme-label">Modo oscuro</span>';
    toggle.setAttribute('aria-pressed', String(isDark));
    toggle.setAttribute('title', isDark ? 'Modo claro' : 'Modo oscuro');
    toggle.setAttribute('aria-label', isDark ? 'Activar modo claro' : 'Activar modo oscuro');
  }

  function updateAllToggles(toggles, isDark) {
    toggles.forEach(function (toggle) {
      setIcon(toggle, isDark);
    });
  }

  function applyThemeFromStorage(toggles) {
    try {
      const saved = localStorage.getItem('darkMode');
      const enable = saved !== 'disabled';
      document.body.classList.toggle('dark-mode', enable);
      document.documentElement.style.colorScheme = enable ? 'dark' : 'light';
      updateAllToggles(toggles, enable);
    } catch (e) {
      // Keep the default dark theme when storage is unavailable.
      document.body.classList.add('dark-mode');
      document.documentElement.style.colorScheme = 'dark';
      updateAllToggles(toggles, true);
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
        document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
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
