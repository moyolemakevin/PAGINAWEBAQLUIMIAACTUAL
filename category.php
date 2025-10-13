<?php
require_once 'config/config.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';

$product = new Product();
$category = new Category();

// Obtener parámetros
$slug = isset($_GET['slug']) ? cleanInput($_GET['slug']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Obtener categoría y productos
$currentCategory = $category->getCategoryBySlug($slug);
if (!$currentCategory) {
    header('Location: products.php');
    exit;
}

$products = $product->getProductsByCategory($currentCategory['id'], $limit, $offset);
foreach ($products as &$p) {
    $p['image'] = Product::getImagePath($p);
}
unset($p);
$totalProducts = $product->getProductCountByCategory($currentCategory['id']);
$totalPages = ceil($totalProducts / $limit);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $currentCategory['name']; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script>const SITE_URL = '<?php echo SITE_URL; ?>';</script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-flask me-2"></i>
                AlquimiaTechnologic
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-boxes me-1"></i>Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            <i class="fas fa-info-circle me-1"></i>Nosotros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                            <i class="fas fa-envelope me-1"></i>Contacto
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="btn btn-outline-light btn-sm" id="darkModeToggle" title="Modo oscuro">
                            <i class="fas fa-moon"></i>
                        </button>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link position-relative" href="cart.php">
                            <i class="fas fa-shopping-cart fs-5"></i>
                            <span class="badge bg-warning text-dark position-absolute" id="cart-count">0</span>
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo $_SESSION['user_name']; ?>
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
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/register.php">
                                <i class="fas fa-user-plus me-1"></i>Registrarse
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Category Header -->
    <section class="hero-section" style="min-height: 40vh; padding: 120px 0 60px;">
        <div class="particles" id="particles"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center hero-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php" class="text-white">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="products.php" class="text-white">Productos</a></li>
                            <li class="breadcrumb-item active text-warning" aria-current="page"><?php echo $currentCategory['name']; ?></li>
                        </ol>
                    </nav>
                    <h1 class="hero-title mb-4" data-aos="fade-up">
                        <?php echo $currentCategory['name']; ?>
                    </h1>
                    <p class="hero-subtitle mb-4" data-aos="fade-up" data-aos-delay="200">
                        <?php echo $currentCategory['description']; ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="section">
        <div class="container">
            <!-- Results Header -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="text-gradient fw-bold" data-aos="fade-right">
                        <?php echo $currentCategory['name']; ?>
                    </h2>
                    <p class="text-muted">
                        <i class="fas fa-box me-2"></i>
                        <?php echo count($products); ?> productos encontrados
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="products.php" class="btn btn-outline-primary hover-lift">
                        <i class="fas fa-arrow-left me-2"></i>
                        Ver Todos los Productos
                    </a>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row" id="products-container">
                <?php if (empty($products)): ?>
                    <div class="col-12 text-center py-5" data-aos="fade-up">
                        <div class="mb-4">
                            <i class="fas fa-box-open text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-muted mb-3">No hay productos en esta categoría</h3>
                        <p class="text-muted mb-4">Pronto agregaremos más productos a esta categoría</p>
                        <a href="products.php" class="btn btn-primary hover-lift">
                            <i class="fas fa-arrow-left me-2"></i>Ver Todos los Productos
                        </a>
                    </div>
                <?php else: ?>
                    <?php 
                    $delay = 100;
                    foreach ($products as $prod): 
                    ?>
                        <div class="col-lg-3 col-md-6 mb-4 product-item" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                            <div class="product-card h-100 hover-lift">
                                <div class="product-image">
                                    <img src="<?php echo $prod['image'] ?: 'assets/images/placeholder.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($prod['name']); ?>" 
                                         class="img-fluid">
                                    <?php $finalPrice = (isset($prod['discount_percentage']) && $prod['discount_percentage'] > 0) ? $prod['price'] * (1 - $prod['discount_percentage']/100) : $prod['price']; ?>
                                    <div class="product-overlay">
                                        <button class="btn btn-light btn-sm me-2 hover-glow" onclick="addToCart(<?php echo $prod['id']; ?>, '<?php echo addslashes($prod['name']); ?>', <?php echo $finalPrice; ?>, '<?php echo $prod['image'] ?: 'assets/images/placeholder.jpg'; ?>')" title="Agregar al carrito">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm me-2 hover-glow" onclick="quickView(<?php echo $prod['id']; ?>)" title="Vista rápida">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm hover-glow" onclick="toggleWishlist(<?php echo $prod['id']; ?>)" title="Agregar a favoritos">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </div>
                                    <?php if (isset($prod['discount_percentage']) && $prod['discount_percentage'] > 0): ?>
                                        <span class="badge bg-danger position-absolute top-0 start-0 m-2 pulse">
                                            -<?php echo $prod['discount_percentage']; ?>%
                                        </span>
                                    <?php endif; ?>
                                    <?php if (isset($prod['is_featured']) && $prod['is_featured']): ?>
                                        <span class="badge bg-warning position-absolute top-0 end-0 m-2">
                                            <i class="fas fa-star"></i> Destacado
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <span class="badge bg-primary opacity-75 text-white">
                                            <?php echo $currentCategory['name']; ?>
                                        </span>
                                    </div>
                                    <h5 class="card-title mb-2"><?php echo htmlspecialchars($prod['name']); ?></h5>
                                    <p class="card-text text-muted small mb-3">
                                        <?php echo substr(htmlspecialchars($prod['description']), 0, 100); ?>...
                                    </p>
                                    
                                    <!-- Rating -->
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rating me-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <small class="text-muted">(4.8)</small>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="price">
                                            <?php if (isset($prod['discount_percentage']) && $prod['discount_percentage'] > 0): ?>
                                                <span class="text-decoration-line-through text-muted me-2">
                                                    $<?php echo number_format($prod['price'], 0, ',', '.'); ?>
                                                </span>
                                                <span class="text-success fw-bold">
                                                    $<?php echo number_format($prod['price'] * (1 - $prod['discount_percentage'] / 100), 0, ',', '.'); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="fw-bold text-primary">$<?php echo number_format($prod['price'], 0, ',', '.'); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary hover-lift" onclick="addToCart(<?php echo $prod['id']; ?>, '<?php echo addslashes($prod['name']); ?>', <?php echo $finalPrice; ?>, '<?php echo $prod['image'] ?: 'assets/images/placeholder.jpg'; ?>')">
                                            <i class="fas fa-cart-plus me-2"></i>
                                            Agregar al Carrito
                                        </button>
                                        <a href="https://wa.me/593983015307?text=Hola%2C%20quiero%20comprar%20el%20producto:%20<?php echo urlencode($prod['name']); ?>%20-%20$<?php echo number_format((isset($prod['discount_percentage']) && $prod['discount_percentage'] > 0) ? $prod['price'] * (1 - $prod['discount_percentage'] / 100) : $prod['price'], 0, ',', '.'); ?>%20-%20AlquimiaTechnologic"
                                           class="btn btn-success btn-sm hover-lift" target="_blank">
                                            <i class="fab fa-whatsapp me-2"></i>
                                            Comprar por WhatsApp
                                        </a>
                                        <button class="btn btn-outline-primary btn-sm hover-lift" onclick="quickView(<?php echo $prod['id']; ?>)">
                                            <i class="fas fa-eye me-2"></i>
                                            Vista Rápida
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $delay += 50; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Navegación de productos" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $page - 1; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $page + 1; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </section>

    <!-- Quick View Modal -->
    <div class="modal fade" id="quickViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Vista Rápida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="quickViewContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light">
        <div class="container">
            <div class="row py-5">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-flask me-2"></i>
                        AlquimiaTechnologic
                    </h5>
                    <p class="text-muted">
                        Transformando tu día a día con productos únicos y servicios innovadores 
                        que elevan tu estilo de vida.
                    </p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Productos</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">Software</a></li>
                        <li><a href="#" class="text-muted">Aceites Esenciales</a></li>
                        <li><a href="#" class="text-muted">Figuras de Yeso</a></li>
                        <li><a href="#" class="text-muted">Suscripciones</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Empresa</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">Sobre Nosotros</a></li>
                        <li><a href="#" class="text-muted">Contacto</a></li>
                        <li><a href="#" class="text-muted">Blog</a></li>
                        <li><a href="#" class="text-muted">Careers</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Soporte</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">Centro de Ayuda</a></li>
                        <li><a href="#" class="text-muted">Política de Privacidad</a></li>
                        <li><a href="#" class="text-muted">Términos</a></li>
                        <li><a href="#" class="text-muted">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Contacto</h6>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-map-marker-alt me-2"></i>Anbato, Ecuador</li>
                        <li><i class="fas fa-phone me-2"></i>+593 983015307</li>
                        <li><i class="fas fa-envelope me-2"></i>kevinmoyolema13@gmail.com</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2025 AlquimiaTechnologic. Desarrollado por AlquimiaTechnologic. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">Hecho con <i class="fas fa-heart text-danger"></i> en Ecuador</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        AOS.init();
        
        // Particles effect
        function createParticles() {
            const particles = document.getElementById('particles');
            const particleCount = 20;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.width = Math.random() * 4 + 2 + 'px';
                particle.style.height = particle.style.width;
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                particles.appendChild(particle);
            }
        }

        // Quick view function
        function quickView(productId) {
            const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
            const content = document.getElementById('quickViewContent');
            content.innerHTML = '<div class="text-center py-5"><div class="spinner-border" role="status"></div></div>';
            modal.show();

            fetch('quick_view.php?id=' + productId)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        content.innerHTML = '<p class="text-danger">Error al cargar el producto.</p>';
                        return;
                    }

                    const priceHtml = data.discount_percentage > 0
                        ? `<span class="text-decoration-line-through text-muted me-2">$${Number(data.price).toLocaleString()}</span>
                           <span class="h4 text-primary">$${Number(data.final_price).toLocaleString()}</span>`
                        : `<span class="h4 text-primary">$${Number(data.price).toLocaleString()}</span>`;

                    const whatsapp = `https://wa.me/593983015307?text=Hola%2C%20quiero%20comprar%20el%20producto:%20${encodeURIComponent(data.name)}%20-%20$${Number(data.final_price).toLocaleString()}%20-%20AlquimiaTechnologic`;

                    content.innerHTML = `
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <img src="${data.image}" class="img-fluid rounded" alt="${data.name}">
                            </div>
                            <div class="col-md-6">
                                <h4>${data.name}</h4>
                                <p class="text-muted">${data.description}</p>
                                <div class="price mb-3">${priceHtml}</div>
                                <button class="btn btn-primary w-100 mb-2" onclick="addToCart(${data.id}, '${data.name.replace(/'/g, "\'")}', ${data.final_price}, '${data.image}')">
                                    <i class="fas fa-cart-plus me-2"></i>Agregar al Carrito
                                </button>
                                <a href="${whatsapp}" class="btn btn-success w-100" target="_blank">
                                    <i class="fab fa-whatsapp me-2"></i>Comprar por WhatsApp
                                </a>
                            </div>
                        </div>`;
                })
                .catch(() => {
                    content.innerHTML = '<p class="text-danger">Error al cargar el producto.</p>';
                });
        }

        // Wishlist toggle
        function toggleWishlist(productId) {
            console.log('Toggle wishlist for product:', productId);
        }

        // Initialize particles
        createParticles();

        // Add pulse animation to discount badges
        document.querySelectorAll('.pulse').forEach(badge => {
            badge.style.animation = 'pulse 2s infinite';
        });
    </script>
</body>
</html> 