<?php
require_once __DIR__ . '/config/bootstrap.php';

$product = new Product();
$category = new Category();

$slug = isset($_GET['slug']) ? cleanInput($_GET['slug']) : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$currentCategory = $category->getCategoryBySlug($slug, true);
if (!$currentCategory) {
    header('Location: products.php');
    exit;
}

$products = $product->getProductsByCategory($currentCategory['id'], $limit, $offset, true);
foreach ($products as &$p) {
    $p['image'] = Product::getImagePath($p);
}
unset($p);

$totalProducts = $product->getProductCountByCategory($currentCategory['id'], true);
$totalPages = (int) ceil($totalProducts / $limit);
$categoryUi = getCategoryPresentation($currentCategory['slug'] ?? '', $currentCategory['name'] ?? 'Categoria');
$catalogWhatsappUrl = 'https://wa.me/593983015307?text=' . rawurlencode('Hola, quiero informacion sobre la categoria ' . ($currentCategory['name'] ?? 'del catalogo') . ' de Moyoalquimia');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentCategory['name'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(($currentCategory['name'] ?? 'Categoria') . ' en el catalogo de ' . SITE_NAME, ENT_QUOTES, 'UTF-8'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo assetUrl('assets/css/style.css'); ?>" rel="stylesheet">
    <link href="<?php echo assetUrl('assets/css/experience.css'); ?>" rel="stylesheet">
    <link rel="icon" href="<?php echo assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')); ?>">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script>const SITE_URL = '<?php echo SITE_URL; ?>';</script>
</head>
<body class="catalog-page category-page">
    <?php
    $currentPage = 'category';
    include __DIR__ . '/partials/navbar.php';
    ?>
<main id="main-content">

    <section class="hero-section hero-section--compact catalog-hero catalog-hero--category">
        <div class="particles" id="particles"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center hero-content">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="index.php" class="text-white">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="products.php" class="text-white">Catalogo</a></li>
                            <li class="breadcrumb-item active text-warning" aria-current="page"><?php echo htmlspecialchars($currentCategory['name'], ENT_QUOTES, 'UTF-8'); ?></li>
                        </ol>
                    </nav>
                    <h1 class="hero-title mb-4" data-aos="fade-up">
                        <?php echo htmlspecialchars($currentCategory['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </h1>
                    <div class="d-flex flex-wrap justify-content-center gap-3 hero-actions" data-aos="fade-up" data-aos-delay="250">
                        <a href="products.php" class="btn btn-outline-light btn-lg hover-lift">
                            <i class="fas fa-arrow-left me-2"></i>
                            Ver todo el catalogo
                        </a>
                        <a href="<?php echo htmlspecialchars($catalogWhatsappUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-warning btn-lg hover-lift" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp me-2"></i>
                            Pedir asesoria
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row mb-4 align-items-end">
                <div class="col-12">
                    <h2 class="text-gradient fw-bold mb-2" data-aos="fade-right">
                        Seleccion de <?php echo htmlspecialchars($currentCategory['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                </div>
            </div>

            <div class="row" id="products-container">
                <?php if (empty($products)): ?>
                    <div class="col-12 text-center py-5" data-aos="fade-up">
                        <div class="mb-4">
                            <i class="<?php echo htmlspecialchars($categoryUi['icon'], ENT_QUOTES, 'UTF-8'); ?> text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-muted mb-3">No hay productos activos en esta categoria</h3>
                        <p class="text-muted mb-4">Pronto agregaremos nuevas opciones a esta linea del catalogo.</p>
                        <a href="products.php" class="btn btn-primary hover-lift">
                            <i class="fas fa-arrow-left me-2"></i>Volver al catalogo
                        </a>
                    </div>
                <?php else: ?>
                    <?php $delay = 100; ?>
                    <?php foreach ($products as $prod): ?>
                        <?php $finalPrice = (isset($prod['discount_percentage']) && $prod['discount_percentage'] > 0) ? $prod['price'] * (1 - $prod['discount_percentage'] / 100) : $prod['price']; ?>
                        <div class="col-6 col-lg-3 mb-4 product-item" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                            <div class="product-card h-100 hover-lift">
                                <div class="product-image">
                                    <img src="<?php echo htmlspecialchars($prod['image'] ?: 'assets/images/placeholder.jpg', ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="<?php echo htmlspecialchars($prod['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                         class="img-fluid"
                                         loading="lazy"
                                         onerror="this.src='assets/images/placeholder.jpg';">
                                    <div class="product-overlay">
                                        <button class="btn btn-light btn-sm me-2 hover-glow" onclick="addToCart(<?php echo (int) $prod['id']; ?>, '<?php echo addslashes($prod['name']); ?>', <?php echo $finalPrice; ?>, '<?php echo $prod['image'] ?: 'assets/images/placeholder.jpg'; ?>')" title="Agregar al carrito">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm me-2 hover-glow" onclick="quickView(<?php echo (int) $prod['id']; ?>)" title="Vista rapida">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-light btn-sm hover-glow" onclick="toggleWishlist(<?php echo (int) $prod['id']; ?>)" title="Agregar a favoritos">
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
                                        <span class="badge catalog-category-badge">
                                            <?php echo htmlspecialchars($currentCategory['name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </div>
                                    <h5 class="card-title mb-2"><a href="product.php?id=<?php echo (int) $prod['id']; ?>"><?php echo htmlspecialchars($prod['name'], ENT_QUOTES, 'UTF-8'); ?></a></h5>

                                    <div class="catalog-card-price">
                                        <div class="price">
                                            <?php if (isset($prod['discount_percentage']) && $prod['discount_percentage'] > 0): ?>
                                                <span class="text-decoration-line-through text-muted me-2">
                                                    $<?php echo number_format($prod['price'], 2, ',', '.'); ?>
                                                </span>
                                                <span class="text-success fw-bold">
                                                    $<?php echo number_format($prod['price'] * (1 - $prod['discount_percentage'] / 100), 2, ',', '.'); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="fw-bold text-primary">$<?php echo number_format($prod['price'], 2, ',', '.'); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="catalog-card-actions">
                                        <button class="btn btn-primary btn-sm hover-lift" onclick="addToCart(<?php echo (int) $prod['id']; ?>, '<?php echo addslashes($prod['name']); ?>', <?php echo $finalPrice; ?>, '<?php echo $prod['image'] ?: 'assets/images/placeholder.jpg'; ?>')">
                                            <i class="fas fa-cart-plus me-2"></i>
                                            Agregar
                                        </button>
                                        <div class="d-flex gap-2">
                                            <a href="https://wa.me/593983015307?text=Hola%2C%20quiero%20comprar%20el%20producto:%20<?php echo urlencode($prod['name']); ?>%20-%20$<?php echo number_format((isset($prod['discount_percentage']) && $prod['discount_percentage'] > 0) ? $prod['price'] * (1 - $prod['discount_percentage'] / 100) : $prod['price'], 2, ',', '.'); ?>%20-%20Moyoalquimia"
                                               class="btn btn-success btn-sm hover-lift flex-fill"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               title="Comprar por WhatsApp" aria-label="Comprar por WhatsApp">
                                                <i class="fab fa-whatsapp me-1"></i>
                                                <span class="catalog-whatsapp-label">WhatsApp</span>
                                            </a>
                                            <button class="btn btn-outline-primary btn-sm hover-lift" onclick="quickView(<?php echo (int) $prod['id']; ?>)" title="Vista rapida">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $delay += 50; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Navegacion de productos" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?slug=<?php echo urlencode($slug); ?>&page=<?php echo $page - 1; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?slug=<?php echo urlencode($slug); ?>&page=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?slug=<?php echo urlencode($slug); ?>&page=<?php echo $page + 1; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </section>

    <div class="modal fade" id="quickViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Vista rapida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="quickViewContent"></div>
            </div>
        </div>
    </div>

</main>
<?php include __DIR__ . '/partials/footer.php'; ?>

<?php include __DIR__ . '/partials/scripts.php'; ?>

<script>
function createParticles() {
    const skipParticles = (window.matchMedia && window.matchMedia('(max-width: 767px)').matches)
        || (navigator.connection && navigator.connection.saveData);
    if (skipParticles) return;
    const particles = document.getElementById('particles');
    if (!particles) {
        return;
    }
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

            const whatsapp = `https://wa.me/593983015307?text=Hola%2C%20quiero%20comprar%20el%20producto:%20${encodeURIComponent(data.name)}%20-%20$${Number(data.final_price).toLocaleString()}%20-%20Moyoalquimia`;

            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6 text-center">
                        <img src="${data.image}" class="img-fluid rounded" alt="${data.name}">
                    </div>
                    <div class="col-md-6">
                        <h4>${data.name}</h4>
                        <p class="text-muted">${data.description}</p>
                        <div class="price mb-3">${priceHtml}</div>
                        <button class="btn btn-primary w-100 mb-2" onclick="addToCart(${data.id}, '${data.name.replace(/'/g, "\\'")}', ${data.final_price}, '${data.image}')">
                            <i class="fas fa-cart-plus me-2"></i>Agregar al carrito
                        </button>
                        <a href="${whatsapp}" class="btn btn-success w-100" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp me-2"></i>Comprar por WhatsApp
                        </a>
                    </div>
                </div>`;
        })
        .catch(() => {
            content.innerHTML = '<p class="text-danger">Error al cargar el producto.</p>';
        });
}

function toggleWishlist(productId) {
    console.log('Toggle wishlist for product:', productId);
}

document.addEventListener('DOMContentLoaded', function() {
    if (window.AOS) {
        AOS.init();
    }

    createParticles();

    document.querySelectorAll('.pulse').forEach(badge => {
        badge.style.animation = 'pulse 2s infinite';
    });
});
</script>
</body>
</html>
