<?php
require_once __DIR__ . '/config/bootstrap.php';

$product = new Product();
$category = new Category();

// Obtener parámetros de búsqueda y filtros
$search = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Obtener productos según filtros
if ($search) {
    $products = $product->searchProducts($search, $limit, true);
} elseif ($categoryFilter) {
    if (!$category->getCategoryById($categoryFilter, true)) {
        header('Location: products.php');
        exit;
    }

    $products = $product->getProductsByCategory($categoryFilter, $limit, 0, true);
} else {
    $products = $product->getAllProducts($limit, $offset, true);
}

foreach ($products as &$p) {
    $p['image'] = Product::getImagePath($p);
}
unset($p);

$categories = $category->getCategoriesWithProductCount(true);
$catalogWhatsappUrl = 'https://wa.me/593983015307?text=Hola%2C%20quiero%20asesoria%20sobre%20el%20catalogo%20de%20Moyoalquimia';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Explora nuestra amplia gama de productos únicos: software personalizado, aceites esenciales, figuras artesanales y servicios digitales.">
    
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo assetUrl('assets/css/style.css'); ?>" rel="stylesheet">
    <link href="<?php echo assetUrl('assets/css/experience.css'); ?>" rel="stylesheet">

    <link rel="icon" href="<?php echo assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')); ?>">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script>const SITE_URL = '<?php echo SITE_URL; ?>';</script>
</head>
<body class="catalog-page products-page">
    <?php
$currentPage = 'products';
include __DIR__ . '/partials/navbar.php';
?>
<main id="main-content">

    <!-- Hero Section -->
    <section class="hero-section hero-section--compact catalog-hero">
        <div class="particles" id="particles"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center hero-content">
                    <h1 class="hero-title mb-4" data-aos="fade-up">
                        Descubre Productos <span class="text-warning">Únicos</span>
                    </h1>

                    <!-- Search Bar -->
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-10 col-lg-8">
                            <form method="GET" action="products.php" class="position-relative catalog-search-form">
                                <div class="input-group input-group-lg">
                                    <input type="text"
                                           name="search"
                                           class="form-control border-0 shadow-lg"
                                           placeholder="¿Qué estás buscando hoy?"
                                           value="<?php echo htmlspecialchars($search); ?>"
                                           style="border-radius: 50px 0 0 50px; padding-left: 2rem;">
                                    <button class="btn btn-warning px-4"
                                            type="submit"
                                            style="border-radius: 0 50px 50px 0;">
                                        <i class="fas fa-search me-2"></i>Buscar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="section bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bg-white rounded-custom shadow-lg p-4 mb-4 catalog-filter-shell" data-aos="fade-up">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-3 mb-md-0">
                                    <i class="fas fa-filter me-2 text-primary"></i>
                                    Filtrar por Categoría
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex flex-wrap gap-2 catalog-filter-chips">
                                    <a href="products.php" class="btn <?php echo !$categoryFilter ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm hover-lift">
                                        <i class="fas fa-th-large me-1"></i>Todos
                                    </a>
                                    <?php foreach ($categories as $cat): ?>
                                        <a href="products.php?category=<?php echo $cat['id']; ?>" 
                                           class="btn <?php echo $categoryFilter == $cat['id'] ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm hover-lift">
                                            <?php echo $cat['name']; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="section">
        <div class="container">
            <!-- Results Header -->
            <div class="row mb-4 catalog-results-header">
                <div class="col-md-6">
                    <h2 class="text-gradient fw-bold" data-aos="fade-right">
                        <?php if ($search): ?>
                            Resultados para: "<?php echo htmlspecialchars($search); ?>"
                        <?php elseif ($categoryFilter): ?>
                            <?php 
                            $selectedCategory = array_filter($categories, function($cat) use ($categoryFilter) {
                                return $cat['id'] == $categoryFilter;
                            });
                            $selectedCategory = reset($selectedCategory);
                            echo $selectedCategory['name'] ?? 'Categoría';
                            ?>
                        <?php else: ?>
                            Todos los Productos
                        <?php endif; ?>
                    </h2>
                    <p class="text-muted">
                        <i class="fas fa-box me-2"></i>
                        <?php echo count($products); ?> productos encontrados
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="btn-group" role="group" data-aos="fade-left">
                        <button type="button" class="btn btn-outline-primary active" id="grid-view" aria-label="Ver productos en cuadrícula" aria-pressed="true">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="list-view" aria-label="Ver productos en lista" aria-pressed="false">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row catalog-grid" id="products-container">
                <?php if (empty($products)): ?>
                    <div class="col-12 text-center py-5" data-aos="fade-up">
                        <div class="mb-4">
                            <i class="fas fa-search text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-muted mb-3">No se encontraron productos</h3>
                        <p class="text-muted mb-4">Intenta con otros términos de búsqueda o explora nuestras categorías</p>
                        <a href="products.php" class="btn btn-primary hover-lift">
                            <i class="fas fa-arrow-left me-2"></i>Ver Todos los Productos
                        </a>
                    </div>
                <?php else: ?>
                    <?php 
                    $delay = 100;
                    foreach ($products as $prod): 
                    ?>
                        <div class="col-6 col-lg-3 mb-4 product-item" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                            <div class="product-card h-100 hover-lift">
                                <div class="product-image">
                                    <img src="<?php echo $prod['image'] ?: 'assets/images/placeholder.jpg'; ?>"
                                         alt="<?php echo htmlspecialchars($prod['name']); ?>"
                                         class="img-fluid"
                                         loading="lazy"
                                         onerror="this.src='assets/images/placeholder.jpg';">
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
                                        <span class="badge catalog-category-badge">
                                            <?php 
                                            $productCategory = array_filter($categories, function($cat) use ($prod) {
                                                return $cat['id'] == $prod['category_id'];
                                            });
                                            $productCategory = reset($productCategory);
                                            echo $productCategory['name'] ?? 'General';
                                            ?>
                                        </span>
                                    </div>
                                    <h5 class="card-title mb-2"><a href="product.php?id=<?php echo (int) $prod['id']; ?>"><?php echo htmlspecialchars($prod['name']); ?></a></h5>
                                    
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
                                        <button class="btn btn-primary btn-sm hover-lift" onclick="addToCart(<?php echo $prod['id']; ?>, '<?php echo addslashes($prod['name']); ?>', <?php echo $finalPrice; ?>, '<?php echo $prod['image'] ?: 'assets/images/placeholder.jpg'; ?>')">
                                            <i class="fas fa-cart-plus me-2"></i>
                                            Agregar
                                        </button>
                                        <div class="d-flex gap-2">
                                            <a href="https://wa.me/593983015307?text=Hola%2C%20quiero%20comprar%20el%20producto:%20<?php echo urlencode($prod['name']); ?>%20-%20$<?php echo number_format((isset($prod['discount_percentage']) && $prod['discount_percentage'] > 0) ? $prod['price'] * (1 - $prod['discount_percentage'] / 100) : $prod['price'], 2, ',', '.'); ?>%20-%20Moyoalquimia"
                                               class="btn btn-success btn-sm hover-lift flex-fill" target="_blank" title="Comprar por WhatsApp" aria-label="Comprar por WhatsApp">
                                                <i class="fab fa-whatsapp me-1"></i>
                                                <span class="catalog-whatsapp-label">WhatsApp</span>
                                            </a>
                                            <button class="btn btn-outline-primary btn-sm hover-lift" onclick="quickView(<?php echo $prod['id']; ?>)" title="Vista rápida">
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

            <!-- Load More Button -->
            <?php if (count($products) >= $limit): ?>
                <div class="text-center mt-5" data-aos="fade-up">
                    <button class="btn btn-outline-primary btn-lg hover-lift" id="loadMore">
                        <i class="fas fa-plus me-2"></i>
                        Cargar Más Productos
                    </button>
                </div>
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

    <!-- Newsletter Section -->
    <section class="section bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="fw-bold mb-3" data-aos="fade-up">
                        <i class="fas fa-bell me-3"></i>
                        ¡No te pierdas nuestras ofertas!
                    </h2>
                    <p class="mb-4" data-aos="fade-up" data-aos-delay="200">
                        Suscríbete y recibe notificaciones sobre nuevos productos, descuentos exclusivos y contenido especial.
                    </p>
                    <form class="row g-3 justify-content-center" data-aos="fade-up" data-aos-delay="400">
                        <div class="col-md-6">
                            <input type="email" class="form-control form-control-lg" placeholder="Tu email aquí...">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-warning btn-lg w-100 hover-lift">
                                <i class="fas fa-paper-plane me-2"></i>
                                Suscribirse
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    </main>
<?php include __DIR__ . '/partials/footer.php'; ?>

    <!-- Scripts -->
    <?php include __DIR__ . '/partials/scripts.php'; ?>
    
    <script>
        // Particles effect
        function createParticles() {
            const skipParticles = (window.matchMedia && window.matchMedia('(max-width: 767px)').matches)
                || (navigator.connection && navigator.connection.saveData);
            if (skipParticles) return;
            const particles = document.getElementById('particles');
            if (!particles) {
                return;
            }
            const particleCount = 30;

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
            // Simulate wishlist toggle
            console.log('Toggle wishlist for product:', productId);
        }
        
        // Load more functionality
        (function() {
            const loadMoreBtn = document.getElementById('loadMore');
            if (!loadMoreBtn) {
                return;
            }

            let currentPage = <?php echo max(1, $page); ?>;
            const limit = <?php echo $limit; ?>;
            const searchTerm = <?php echo json_encode($search); ?>;
            const categoryId = <?php echo (int)$categoryFilter; ?>;
            let isLoading = false;
            const originalLabel = loadMoreBtn.innerHTML;

            loadMoreBtn.addEventListener('click', function() {
                if (isLoading) {
                    return;
                }

                isLoading = true;
                loadMoreBtn.disabled = true;
                loadMoreBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Cargando...';

                const params = new URLSearchParams({
                    page: currentPage + 1,
                    limit: limit
                });
                if (searchTerm) {
                    params.append('search', searchTerm);
                }
                if (categoryId) {
                    params.append('category', categoryId);
                }

                fetch('load_more_products.php?' + params.toString())
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Error desconocido');
                        }

                        if (Array.isArray(data.products) && data.products.length > 0) {
                            appendProducts(data.products);
                            currentPage += 1;

                            if (!data.has_more) {
                                loadMoreBtn.remove();
                                return;
                            }

                            loadMoreBtn.disabled = false;
                            loadMoreBtn.innerHTML = originalLabel;
                        } else {
                            loadMoreBtn.remove();
                        }
                    })
                    .catch(error => {
                        console.error('Load more error:', error);
                        if (typeof showNotification === 'function') {
                            showNotification('No pudimos cargar más productos. Inténtalo nuevamente.', 'danger');
                        }
                        if (loadMoreBtn.isConnected) {
                            loadMoreBtn.disabled = false;
                            loadMoreBtn.innerHTML = originalLabel;
                        }
                    })
                    .finally(() => {
                        isLoading = false;
                    });
            });

            function appendProducts(items) {
                const container = document.getElementById('products-container');
                if (!container) {
                    return;
                }

                const listToggle = document.getElementById('list-view');
                const isListView = listToggle && listToggle.classList.contains('active');
                const existingItems = container.querySelectorAll('.product-item').length;

                items.forEach((product, index) => {
                    const col = document.createElement('div');
                    col.className = isListView ? 'col-12 mb-4 product-item' : 'col-6 col-lg-3 mb-4 product-item';
                    col.setAttribute('data-aos', 'fade-up');
                    col.setAttribute('data-aos-delay', String(100 + (existingItems + index) * 50));

                    const safeImage = sanitizeImage(product.image);
                    const price = Number.isFinite(Number(product.price)) ? Number(product.price) : 0;
                    const discountPrice = product.discount_price !== null && product.discount_price !== undefined
                        ? Number(product.discount_price)
                        : null;
                    const discountPercentage = calculateDiscountPercentage(price, discountPrice, product.discount_percentage);
                    const finalPriceBase = discountPrice !== null ? discountPrice : price;
                    const finalPriceRounded = Number.isFinite(finalPriceBase) ? Number(finalPriceBase.toFixed(2)) : 0;

                    const name = product.name || 'Producto';
                    const categoryName = product.category_name || 'General';

                    const discountBadge = discountPercentage > 0
                        ? '<span class="badge bg-danger position-absolute top-0 start-0 m-2 pulse">-' + discountPercentage + '%</span>'
                        : '';
                    const featuredBadge = Number(product.is_featured) === 1
                        ? '<span class="badge bg-warning position-absolute top-0 end-0 m-2"><i class="fas fa-star"></i> Destacado</span>'
                        : '';

                    const nameJs = JSON.stringify(name);
                    const imageJs = JSON.stringify(safeImage);
                    const whatsappLink = buildWhatsappLink(name, finalPriceRounded);

                    const priceHtml = discountPercentage > 0
                        ? '<span class="text-decoration-line-through text-muted me-2">' + formatCurrency(price) + '</span>' +
                          '<span class="text-success fw-bold">' + formatCurrency(finalPriceRounded) + '</span>'
                        : '<span class="fw-bold text-primary">' + formatCurrency(price) + '</span>';

                    col.innerHTML = `
                        <div class="product-card h-100 hover-lift">
                            <div class="product-image">
                                <img src="${escapeHtml(safeImage)}"
                                     alt="${escapeHtml(name)}"
                                     class="img-fluid"
                                     loading="lazy"
                                     onerror="this.src='assets/images/placeholder.jpg';">
                                <div class="product-overlay">
                                    <button class="btn btn-light btn-sm me-2 hover-glow" onclick='addToCart(${product.id}, ${nameJs}, ${finalPriceRounded}, ${imageJs})' title="Agregar al carrito">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm me-2 hover-glow" onclick="quickView(${product.id})" title="Vista rápida">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm hover-glow" onclick="toggleWishlist(${product.id})" title="Agregar a favoritos">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                                ${discountBadge}
                                ${featuredBadge}
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <span class="badge catalog-category-badge">${escapeHtml(categoryName)}</span>
                                </div>
                                <h5 class="card-title mb-2"><a href="product.php?id=${Number(product.id)}">${escapeHtml(name)}</a></h5>
                                <div class="catalog-card-price">
                                    <div class="price">${priceHtml}</div>
                                </div>
                                <div class="catalog-card-actions">
                                    <button class="btn btn-primary btn-sm hover-lift" onclick='addToCart(${product.id}, ${nameJs}, ${finalPriceRounded}, ${imageJs})'>
                                        <i class="fas fa-cart-plus me-2"></i>Agregar
                                    </button>
                                    <div class="d-flex gap-2">
                                        <a href="${whatsappLink}"
                                           class="btn btn-success btn-sm hover-lift flex-fill" target="_blank" title="Comprar por WhatsApp" aria-label="Comprar por WhatsApp">
                                            <i class="fab fa-whatsapp me-1"></i><span class="catalog-whatsapp-label">WhatsApp</span>
                                        </a>
                                        <button class="btn btn-outline-primary btn-sm hover-lift" onclick="quickView(${product.id})" title="Vista rápida">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    container.appendChild(col);
                });

                if (window.AOS && typeof window.AOS.refreshHard === 'function') {
                    window.AOS.refreshHard();
                } else if (window.AOS && typeof window.AOS.refresh === 'function') {
                    window.AOS.refresh();
                }
            }

            function sanitizeImage(imagePath) {
                if (typeof imagePath !== 'string' || imagePath.trim() === '') {
                    return 'assets/images/placeholder.jpg';
                }
                return imagePath;
            }

            function calculateDiscountPercentage(price, discountPrice, fallbackPercentage) {
                if (discountPrice !== null && price > 0) {
                    return Math.round((1 - (discountPrice / price)) * 100);
                }
                if (typeof fallbackPercentage === 'number') {
                    return Math.round(fallbackPercentage);
                }
                return 0;
            }

            function buildWhatsappLink(name, price) {
                const message = 'Hola, quiero comprar el producto: ' + name + ' - ' + formatCurrency(price) + ' - Moyoalquimia';
                return 'https://wa.me/593983015307?text=' + encodeURIComponent(message);
            }

            function escapeHtml(value) {
                return String(value || '').replace(/[&<>"']/g, function(match) {
                    const entities = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    };
                    return entities[match] || match;
                });
            }

            function formatCurrency(value) {
                return '$' + Number(value || 0).toLocaleString('es-EC', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        })();
        
        document.addEventListener('DOMContentLoaded', function() {
            if (window.AOS) {
                AOS.init({
                    duration: 800,
                    once: true,
                    offset: 100
                });
            }

            const heroTitle = document.querySelector('.products-page .hero-title');
            if (heroTitle) {
                <?php if ($search): ?>
                heroTitle.textContent = 'Resultados para "<?php echo addslashes($search); ?>"';
                <?php elseif ($categoryFilter): ?>
                heroTitle.textContent = '<?php echo addslashes($selectedCategory['name'] ?? 'Categoria'); ?>';
                <?php else: ?>
                heroTitle.textContent = 'Catalogo completo';
                <?php endif; ?>
            }

            const heroSubtitle = document.querySelector('.products-page .hero-subtitle');
            if (heroSubtitle) {
                <?php if ($search): ?>
                heroSubtitle.textContent = 'Revisa una vista clara de tus coincidencias para comparar precios, abrir vista rapida y comprar sin salir del catalogo.';
                <?php elseif ($categoryFilter): ?>
                <?php $selectedCategoryUi = getCategoryPresentation($selectedCategory['slug'] ?? '', $selectedCategory['name'] ?? 'Categoria'); ?>
                heroSubtitle.textContent = '<?php echo addslashes($selectedCategoryUi['summary']); ?>';
                <?php else: ?>
                heroSubtitle.textContent = 'Explora software, aceites esenciales, maceteros de yeso y suscripciones digitales dentro de un catalogo ordenado y permanente.';
                <?php endif; ?>
            }

            const searchInput = document.querySelector('.products-page .catalog-search-form input[name="search"]');
            if (searchInput) {
                searchInput.placeholder = 'Que estas buscando hoy?';
            }

            const filterTitle = document.querySelector('.products-page .catalog-filter-shell h5');
            if (filterTitle) {
                filterTitle.innerHTML = '<i class="fas fa-filter me-2 text-primary"></i>Filtrar por categoria';
            }

            const modalTitle = document.querySelector('#quickViewModal .modal-title');
            if (modalTitle) {
                modalTitle.textContent = 'Vista rapida';
            }

            const loadMoreLabel = document.querySelector('#loadMore');
            if (loadMoreLabel) {
                loadMoreLabel.innerHTML = '<i class="fas fa-plus me-2"></i>Cargar mas productos';
            }

            const newsletterTitle = document.querySelector('.products-page .section.bg-primary.text-white h2');
            if (newsletterTitle) {
                newsletterTitle.innerHTML = '<i class="fas fa-bell me-3"></i>No te pierdas nuestras ofertas';
            }

            const newsletterText = document.querySelector('.products-page .section.bg-primary.text-white p');
            if (newsletterText) {
                newsletterText.textContent = 'Suscribete y recibe notificaciones sobre nuevos productos, descuentos exclusivos y contenido especial.';
            }

            const newsletterInput = document.querySelector('.products-page .section.bg-primary.text-white input[type="email"]');
            if (newsletterInput) {
                newsletterInput.placeholder = 'Tu email aqui...';
            }

            // Initialize particles
            createParticles();

            // View toggle
            const gridView = document.getElementById('grid-view');
            const listView = document.getElementById('list-view');
            if (gridView && listView) {
                gridView.addEventListener('click', function() {
                    document.querySelectorAll('.product-item').forEach(item => {
                        item.className = 'col-6 col-lg-3 mb-4 product-item';
                    });
                    gridView.classList.add('active');
                    listView.classList.remove('active');
                    gridView.setAttribute('aria-pressed', 'true');
                    listView.setAttribute('aria-pressed', 'false');
                });

                listView.addEventListener('click', function() {
                    document.querySelectorAll('.product-item').forEach(item => {
                        item.className = 'col-12 mb-4 product-item';
                    });
                    listView.classList.add('active');
                    gridView.classList.remove('active');
                    listView.setAttribute('aria-pressed', 'true');
                    gridView.setAttribute('aria-pressed', 'false');
                });
            }

            // Add pulse animation to discount badges
            document.querySelectorAll('.pulse').forEach(badge => {
                badge.style.animation = 'pulse 2s infinite';
            });
        });
    </script>
</body>
</html> 
