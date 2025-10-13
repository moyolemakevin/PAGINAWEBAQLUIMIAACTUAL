<?php
require_once 'config/config.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';

$product = new Product();
$category = new Category();

// Obtener productos destacados
$featuredProducts = $product->getFeaturedProducts(8);
foreach ($featuredProducts as &$fp) {
    $fp['image'] = Product::getImagePath($fp);
}
unset($fp);
$categories = $category->getAllCategories();
$heroImage = getSiteSetting('hero_image_url', 'assets/images/placeholder.jpg');
// Convertir a URL absoluta si la ruta es relativa
if ($heroImage && strpos($heroImage, 'http') !== 0) {
    $heroImage = rtrim(SITE_URL, '/') . '/' . ltrim($heroImage, '/');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Transformando tu día a día</title>
    <meta name="description" content="En AlquimiaTechnologic nos especializamos en ofrecerte productos y servicios de alta calidad que transformarán tu día a día.">
    
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-boxes me-1"></i>Productos
                        </a>
                        <ul class="dropdown-menu shadow-lg border-0">
                            <?php foreach ($categories as $cat): ?>
                                <li><a class="dropdown-item" href="category.php?slug=<?php echo $cat['slug']; ?>">
                                    <i class="fas fa-tag me-2"></i><?php echo $cat['name']; ?>
                                </a></li>
                            <?php endforeach; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="products.php">
                                <i class="fas fa-th-large me-2"></i>Ver todos
                            </a></li>
                        </ul>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="particles" id="particles"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content" data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="hero-title mb-4">
                        Transforma tu <span class="text-danger">Mundo</span> con
                        <span class="text-warning">AlquimiaTechnologic</span>
                    </h1>
                    <p class="hero-subtitle mb-4">
                        🚀 Descubre productos únicos y servicios innovadores que elevarán tu estilo de vida. 
                        Desde tecnología de vanguardia hasta aceites esenciales premium, 
                        tenemos todo lo que necesitas para vivir mejor.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="products.php" class="btn btn-primary btn-lg hover-lift">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Explorar Productos
                        </a>
                        <a href="#categories" class="btn btn-outline-light btn-lg hover-lift">
                            <i class="fas fa-play me-2"></i>
                            Ver Categorías
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="row mt-5">
                        <div class="col-4 text-center">
                            <div class="text-white">
                                <h3 class="fw-bold mb-1">500+</h3>
                                <small class="opacity-75">Productos</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="text-white">
                                <h3 class="fw-bold mb-1">1000+</h3>
                                <small class="opacity-75">Clientes</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="text-white">
                                <h3 class="fw-bold mb-1">24/7</h3>
                                <small class="opacity-75">Soporte</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 hero-image text-center" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <img src="<?php echo htmlspecialchars($heroImage); ?>"
                         alt="Tecnología y Innovación" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-shield-alt text-success fs-1"></i>
                        </div>
                        <h5 class="fw-bold">Compra Segura</h5>
                        <p class="text-muted">Pagos 100% seguros y protegidos</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-headset text-info fs-1"></i>
                        </div>
                        <h5 class="fw-bold">Soporte 24/7</h5>
                        <p class="text-muted">Atención al cliente siempre disponible</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-undo text-warning fs-1"></i>
                        </div>
                        <h5 class="fw-bold">Devoluciones</h5>
                        <p class="text-muted">30 días para devoluciones</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="section" id="categories">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-gradient" data-aos="fade-up">
                        Nuestras Categorías
                    </h2>
                    <p class="text-center text-muted mb-5" data-aos="fade-up" data-aos-delay="100">
                        Explora nuestra amplia gama de productos diseñados para mejorar tu vida
                    </p>
                </div>
            </div>
            
            <div class="row">
                <?php 
                $categoryIcons = [
                    'software' => 'fas fa-laptop-code',
                    'aceites' => 'fas fa-leaf',
                    'figuras' => 'fas fa-palette',
                    'suscripciones' => 'fas fa-crown'
                ];
                $categoryColors = [
                    'software' => 'blue-gradient',
                    'aceites' => 'green-gradient',
                    'figuras' => 'purple-gradient',
                    'suscripciones' => 'orange-gradient'
                ];
                $delay = 100;
                ?>
                
                <?php foreach ($categories as $index => $cat): ?>
                    <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="<?php echo $delay; ?>">
                        <div class="category-card h-100 text-center p-4 hover-lift">
                            <div class="category-icon mx-auto mb-3" style="background: var(--<?php echo $categoryColors[$cat['slug']] ?? 'primary-gradient'; ?>);">
                                <i class="<?php echo $categoryIcons[$cat['slug']] ?? 'fas fa-cube'; ?>"></i>
                            </div>
                            <h4 class="fw-bold mb-3"><?php echo $cat['name']; ?></h4>
                            <p class="text-muted mb-4"><?php echo $cat['description']; ?></p>
                            <a href="category.php?slug=<?php echo $cat['slug']; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-right me-2"></i>
                                Explorar
                            </a>
                        </div>
                    </div>
                    <?php $delay += 100; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="section bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-gradient" data-aos="fade-up">
                        Productos Destacados
                    </h2>
                    <p class="text-center text-muted mb-5" data-aos="fade-up" data-aos-delay="100">
                        Descubre los productos más populares y mejor valorados por nuestros clientes
                    </p>
                </div>
            </div>
            
            <div class="row">
                <?php 
                $productDelay = 100;
                foreach ($featuredProducts as $product): 
                ?>
                    <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $productDelay; ?>">
                        <div class="product-card h-100 hover-lift">
                            <div class="product-image">
                                <img src="<?php echo $product['image'] ?: 'assets/images/placeholder.jpg'; ?>"
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     class="img-fluid"
                                     loading="lazy"
                                     onerror="this.src='assets/images/placeholder.jpg';">
                                <?php $finalPrice = (isset($product['discount_percentage']) && $product['discount_percentage'] > 0) ? $product['price'] * (1 - $product['discount_percentage']/100) : $product['price']; ?>
                                <div class="product-overlay">
                                    <button class="btn btn-light btn-sm me-2 hover-glow" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $finalPrice; ?>, '<?php echo $product['image'] ?: 'assets/images/placeholder.jpg'; ?>')">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm hover-glow">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                                <?php if (isset($product['discount_percentage']) && $product['discount_percentage'] > 0): ?>
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                        -<?php echo $product['discount_percentage']; ?>%
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                <p class="card-text"><?php echo substr($product['description'], 0, 100); ?>...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price">
                                        <?php if (isset($product['discount_percentage']) && $product['discount_percentage'] > 0): ?>
                                            <span class="text-decoration-line-through text-muted me-2">
                                                $<?php echo number_format($product['price'], 0, ',', '.'); ?>
                                            </span>
                                            <span class="text-success fw-bold">
                                                $<?php echo number_format($product['price'] * (1 - $product['discount_percentage'] / 100), 0, ',', '.'); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="fw-bold">$<?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star text-warning"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <button class="btn btn-primary w-100 mt-3 hover-lift" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $finalPrice; ?>, '<?php echo $product['image'] ?: 'assets/images/placeholder.jpg'; ?>')">
                                    <i class="fas fa-cart-plus me-2"></i>
                                    Agregar al Carrito
                                </button>
                                <a href="https://wa.me/593983015307?text=Hola%2C%20quiero%20comprar%20el%20producto:%20<?php echo urlencode($product['name']); ?>%20-%20$<?php echo number_format((isset($product['discount_percentage']) && $product['discount_percentage'] > 0) ? $product['price'] * (1 - $product['discount_percentage'] / 100) : $product['price'], 0, ',', '.'); ?>%20-%20AlquimiaTechnologic"
                                   class="btn btn-success w-100 mt-2 hover-lift" target="_blank">
                                    <i class="fab fa-whatsapp me-2"></i>
                                    Comprar por WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php $productDelay += 100; ?>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5" data-aos="fade-up">
                <a href="products.php" class="btn btn-primary btn-lg hover-lift">
                    <i class="fas fa-th-large me-2"></i>
                    Ver Todos los Productos
                </a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="section bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h2 class="fw-bold mb-3">
                        <i class="fas fa-envelope-open me-3"></i>
                        ¡Mantente Conectado!
                    </h2>
                    <p class="mb-4">
                        Suscríbete a nuestro newsletter y recibe ofertas exclusivas, 
                        nuevos productos y contenido especial directamente en tu bandeja de entrada.
                    </p>
                    <div class="d-flex gap-3">
                        <i class="fas fa-check-circle text-warning"></i>
                        <span>Ofertas exclusivas</span>
                    </div>
                    <div class="d-flex gap-3 mt-2">
                        <i class="fas fa-check-circle text-warning"></i>
                        <span>Primero en enterarte de nuevos productos</span>
                    </div>
                    <div class="d-flex gap-3 mt-2">
                        <i class="fas fa-check-circle text-warning"></i>
                        <span>Contenido premium gratuito</span>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="bg-glass p-4 rounded-custom">
                        <h4 class="mb-3">Suscríbete Ahora</h4>
                        <form id="newsletterForm">
                            <div class="mb-3">
                                <input type="email" class="form-control form-control-lg" 
                                       placeholder="Tu email aquí..." required>
                            </div>
                            <button type="submit" class="btn btn-warning btn-lg w-100 hover-lift">
                                <i class="fas fa-paper-plane me-2"></i>
                                Suscribirme Gratis
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                        <li><i class="fas fa-map-marker-alt me-2"></i>Ecuador</li>
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
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Particles effect
        function createParticles() {
            const particles = document.getElementById('particles');
            const particleCount = 50;

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

        // Newsletter form
        document.getElementById('newsletterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            // Simulate API call
            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Suscribiendo...';
            button.disabled = true;
            
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check me-2"></i>¡Suscrito!';
                button.classList.remove('btn-warning');
                button.classList.add('btn-success');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-warning');
                    button.disabled = false;
                    this.reset();
                }, 2000);
            }, 2000);
        });

        // Initialize particles
        createParticles();

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html> 