<?php
$currentPage = $currentPage ?? '';
$showCategoryDropdown = $showCategoryDropdown ?? false;
$categories = $categories ?? [];
$productsActive = in_array($currentPage, ['products', 'category', 'product'], true);
$authEnabled = !isPublicAuthDisabled();
?>
<script>
  (function () {
    var isDark = true;
    try {
      isDark = localStorage.getItem('darkMode') !== 'disabled';
    } catch (e) {
      // Keep the default theme if storage is unavailable.
    }
    document.body.classList.toggle('dark-mode', isDark);
    document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
  })();
</script>
<a class="skip-link" href="#main-content">Saltar al contenido</a>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
      <i class="fas fa-flask me-2"></i>
      <span class="site-name"><?php echo SITE_NAME; ?></span>
    </a>

    <div class="navbar-quick-actions d-lg-none ms-auto me-2" aria-label="Accesos rapidos">
      <button class="btn btn-outline-light btn-sm mobile-theme-toggle" type="button" data-theme-toggle title="Modo oscuro" aria-label="Cambiar tema">
        <i class="fas fa-moon"></i>
      </button>
      <a class="mobile-action-link mobile-cart-link <?php echo $currentPage === 'cart' ? 'active' : ''; ?>" href="cart.php" aria-label="Carrito">
        <i class="fas fa-shopping-cart"></i>
        <span class="badge bg-warning text-dark mobile-cart-badge" data-cart-count>0</span>
      </a>
      <?php if (isLoggedIn()): ?>
        <a class="mobile-action-link <?php echo in_array($currentPage, ['profile', 'orders'], true) ? 'active' : ''; ?>" href="<?php echo isAdmin() ? 'admin/dashboard.php' : 'profile.php'; ?>" aria-label="Mi cuenta">
          <i class="fas fa-user-circle"></i>
        </a>
      <?php endif; ?>
    </div>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" href="index.php">
            <i class="fas fa-home me-1"></i>Inicio
          </a>
        </li>
        <?php if ($showCategoryDropdown && !empty($categories)): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo $productsActive ? 'active' : ''; ?>" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-boxes me-1"></i>Productos
            </a>
            <ul class="dropdown-menu shadow-lg border-0">
              <?php foreach ($categories as $cat): ?>
                <li>
                  <a class="dropdown-item" href="category.php?slug=<?php echo urlencode($cat['slug']); ?>">
                    <i class="fas fa-tag me-2"></i><?php echo htmlspecialchars($cat['name']); ?>
                  </a>
                </li>
              <?php endforeach; ?>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item" href="products.php">
                  <i class="fas fa-th-large me-2"></i>Ver todos
                </a>
              </li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $productsActive ? 'active' : ''; ?>" href="products.php">
              <i class="fas fa-boxes me-1"></i>Productos
            </a>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link <?php echo $currentPage === 'about' ? 'active' : ''; ?>" href="about.php">
            <i class="fas fa-info-circle me-1"></i>Nosotros
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>" href="contact.php">
            <i class="fas fa-envelope me-1"></i>Contacto
          </a>
        </li>
      </ul>

      <ul class="navbar-nav">
        <li class="nav-item d-none d-lg-block">
          <button class="btn btn-outline-light btn-sm" id="darkModeToggle" data-theme-toggle type="button" title="Modo oscuro" aria-label="Cambiar tema">
            <i class="fas fa-moon"></i>
          </button>
        </li>
        <li class="nav-item me-3">
          <a class="nav-link position-relative <?php echo $currentPage === 'cart' ? 'active' : ''; ?>" href="cart.php" aria-label="Carrito">
            <i class="fas fa-shopping-cart fs-5"></i>
            <span class="badge bg-warning text-dark position-absolute cart-badge" id="cart-count" data-cart-count>0</span>
          </a>
        </li>
        <?php if (isLoggedIn()): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-user-circle me-1"></i>
              <?php echo htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </a>
            <ul class="dropdown-menu shadow-lg border-0">
              <li><a class="dropdown-item" href="profile.php">
                <i class="fas fa-user me-2"></i>Mi Perfil
              </a></li>
              <li><a class="dropdown-item" href="orders.php">
                <i class="fas fa-shopping-bag me-2"></i>Mis Pedidos
              </a></li>
              <?php if (isAdmin()): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="admin/dashboard.php">
                  <i class="fas fa-tachometer-alt me-2"></i>Panel Admin
                </a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="auth/logout.php">
                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesion
              </a></li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
