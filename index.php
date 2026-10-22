<?php
require_once __DIR__ . '/config/bootstrap.php';

$product = new Product();
$category = new Category();

$featuredProducts = $product->getFeaturedProducts(8, true);
foreach ($featuredProducts as &$fp) {
    $fp['image'] = Product::getImagePath($fp);
}
unset($fp);

$categories = $category->getCategoriesWithProductCount(true);
$heroImage = assetUrl(getSafeSiteImageSetting('hero_image_url', 'assets/images/placeholder.jpg'));
$catalogWhatsappUrl = 'https://wa.me/593983015307?text=Hola%2C%20quiero%20asesoria%20sobre%20el%20catalogo%20de%20Moyoalquimia';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Catalogo digital y productos destacados</title>
    <meta name="description" content="Catalogo permanente de software, aceites esenciales, maceteros de yeso y suscripciones digitales en Moyoalquimia.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="<?php echo assetUrl('assets/css/style.css'); ?>" rel="stylesheet">
    <link rel="icon" href="<?php echo assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')); ?>">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="home-page">
    <?php
    $currentPage = 'index';
    $showCategoryDropdown = true;
    include __DIR__ . '/partials/navbar.php';
    ?>
<main id="main-content">

    <section class="hero-section">
        <div class="particles" id="particles"></div>
        <div class="container">
            <div class="row align-items-center g-5 hero-row">
                <div class="col-12 col-lg-6 hero-content" data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="hero-title mb-4">
                        Transforma tu Mundo con
                        <span class="hero-title-line">
                            Moyoalquimia
                        </span>
                    </h1>
                    <p class="hero-subtitle mb-4">
                        &#128640; Descubre productos &uacute;nicos y servicios innovadores que elevar&aacute;n tu estilo de vida.
                        Desde tecnolog&iacute;a de vanguardia hasta aceites esenciales premium, tenemos todo lo que
                        necesitas para vivir mejor.
                    </p>
                    <div class="d-flex flex-wrap gap-3 hero-actions">
                        <a href="products.php" class="btn btn-primary btn-lg hover-lift">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Ver catalogo
                        </a>
                        <a href="<?php echo htmlspecialchars($catalogWhatsappUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-light btn-lg hover-lift" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp me-2"></i>
                            Hablar por WhatsApp
                        </a>
                    </div>
                </div>

                <div class="col-12 col-lg-6 hero-image text-center" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <img src="<?php echo htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="Imagen principal de Moyoalquimia"
                         class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="categories">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-gradient" data-aos="fade-up">
                        Categorias principales
                    </h2>
                </div>
            </div>

            <div class="row">
                <?php $delay = 100; ?>
                <?php foreach ($categories as $cat): ?>
                    <?php
                    $categorySlug = $cat['slug'] ?? '';
                    $categoryUrl = 'category.php?slug=' . urlencode($categorySlug);
                    $categoryUi = getCategoryPresentation($categorySlug, $cat['name'] ?? 'Categoria');
                    ?>
                    <div class="col-lg-3 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="<?php echo $delay; ?>">
                        <div class="category-card h-100 text-center p-4 hover-lift"
                             data-category-url="<?php echo htmlspecialchars($categoryUrl, ENT_QUOTES, 'UTF-8'); ?>"
                             tabindex="0">
                            <div class="category-icon mx-auto mb-3" style="background: var(--<?php echo htmlspecialchars($categoryUi['gradient'], ENT_QUOTES, 'UTF-8'); ?>);">
                                <i class="<?php echo htmlspecialchars($categoryUi['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                            </div>
                            <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                            <a href="<?php echo htmlspecialchars($categoryUrl, ENT_QUOTES, 'UTF-8'); ?>"
                               class="btn btn-outline-primary"
                               data-category-link="true">
                                <i class="fas fa-arrow-right me-2"></i>
                                Ver categoria
                            </a>
                        </div>
                    </div>
                    <?php $delay += 100; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-gradient" data-aos="fade-up">
                        Productos destacados
                    </h2>
                </div>
            </div>

            <div class="row">
                <?php $productDelay = 100; ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <?php $finalPrice = (isset($product['discount_percentage']) && $product['discount_percentage'] > 0) ? $product['price'] * (1 - $product['discount_percentage'] / 100) : $product['price']; ?>
                    <div class="col-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $productDelay; ?>">
                        <div class="product-card h-100 hover-lift">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($product['image'] ?: 'assets/images/placeholder.jpg', ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                     class="img-fluid"
                                     loading="lazy"
                                     onerror="this.src='assets/images/placeholder.jpg';">
                                <div class="product-overlay">
                                    <button class="btn btn-light btn-sm me-2 hover-glow" onclick="addToCart(<?php echo (int) $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $finalPrice; ?>, '<?php echo $product['image'] ?: 'assets/images/placeholder.jpg'; ?>')">
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
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars(substr((string) $product['description'], 0, 100), ENT_QUOTES, 'UTF-8'); ?>...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price">
                                        <?php if (isset($product['discount_percentage']) && $product['discount_percentage'] > 0): ?>
                                            <span class="text-decoration-line-through text-muted me-2">
                                                $<?php echo number_format($product['price'], 2, ',', '.'); ?>
                                            </span>
                                            <span class="text-success fw-bold">
                                                $<?php echo number_format($product['price'] * (1 - $product['discount_percentage'] / 100), 2, ',', '.'); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="fw-bold">$<?php echo number_format($product['price'], 2, ',', '.'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star text-warning"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button class="btn btn-primary btn-sm flex-fill hover-lift" onclick="addToCart(<?php echo (int) $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $finalPrice; ?>, '<?php echo $product['image'] ?: 'assets/images/placeholder.jpg'; ?>')">
                                        <i class="fas fa-cart-plus me-1"></i>
                                        Carrito
                                    </button>
                                    <a href="https://wa.me/593983015307?text=Hola%2C%20quiero%20comprar%20el%20producto:%20<?php echo urlencode($product['name']); ?>%20-%20$<?php echo number_format((isset($product['discount_percentage']) && $product['discount_percentage'] > 0) ? $product['price'] * (1 - $product['discount_percentage'] / 100) : $product['price'], 2, ',', '.'); ?>%20-%20Moyoalquimia"
                                       class="btn btn-success btn-sm flex-fill hover-lift"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       title="Comprar por WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $productDelay += 100; ?>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="products.php" class="btn btn-primary btn-lg hover-lift">
                    <i class="fas fa-th-large me-2"></i>
                    Ver todo el catalogo
                </a>
            </div>
        </div>
    </section>

    <section class="section bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h2 class="fw-bold mb-3">
                        <i class="fas fa-envelope-open me-3"></i>
                        Mantente conectado
                    </h2>
                    <p class="mb-4">
                        Suscribete a nuestro newsletter y recibe ofertas exclusivas, nuevos productos y
                        contenido especial directamente en tu bandeja de entrada.
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
                        <span>Suscripciones premium y novedades</span>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="bg-glass p-4 rounded-custom">
                        <h4 class="mb-3">Suscribete ahora</h4>
                        <form id="newsletterForm">
                            <div class="mb-3">
                                <input type="email" class="form-control form-control-lg" placeholder="Tu email aqui..." required>
                            </div>
                            <button type="submit" class="btn btn-warning btn-lg w-100 hover-lift">
                                <i class="fas fa-paper-plane me-2"></i>
                                Suscribirme gratis
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
<?php include __DIR__ . '/partials/footer.php'; ?>

<?php include __DIR__ . '/partials/scripts.php'; ?>
<script src="<?php echo assetUrl('assets/js/hero-effect.js'); ?>" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.AOS) {
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    }

    function createParticles() {
        const skipParticles = (window.matchMedia && window.matchMedia('(max-width: 767px)').matches)
            || (navigator.connection && navigator.connection.saveData);
        if (skipParticles) return;
        const particles = document.getElementById('particles');
        if (!particles) {
            return;
        }
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

    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Suscribiendo...';
            button.disabled = true;

            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check me-2"></i>Suscrito';
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
    }

    createParticles();

    document.querySelectorAll('.category-card[data-category-url]').forEach(card => {
        const categoryUrl = card.getAttribute('data-category-url');
        if (!categoryUrl) {
            return;
        }
        card.addEventListener('click', (event) => {
            if (event.target.closest('a')) {
                return;
            }
            window.location.href = categoryUrl;
        });
        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                window.location.href = categoryUrl;
            }
        });
    });
});
</script>
</body>
</html>
