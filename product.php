<?php
require_once 'config/config.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';

$product = new Product();
$category = new Category();

// Obtener parámetros
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$slug = isset($_GET['slug']) ? cleanInput($_GET['slug']) : '';

// Obtener producto
$productData = $product->getProductById($id);
if (!$productData) {
    header('Location: products.php');
    exit;
}

// Procesar imágenes del producto asegurando compatibilidad con distintos formatos
$productImages = [];
if (!empty($productData['images'])) {
    $decoded = json_decode($productData['images'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        foreach ($decoded as $img) {
            if (is_array($img)) {
                $productImages[] = $img['original'] ?? ($img['thumbnail'] ?? '');
            } elseif (is_string($img)) {
                $productImages[] = $img;
            }
        }
        // Limpiar valores vacíos
        $productImages = array_values(array_filter($productImages));
    }
}

// Fallback si no hay imágenes válidas
if (empty($productImages)) {
    $productImages[] = Product::getImagePath($productData);
}

$mainImage = $productImages[0] ?? 'assets/images/placeholder.jpg';

// Calcular precio final teniendo en cuenta descuentos
$finalPrice = ($productData['discount_percentage'] > 0)
    ? $productData['price'] * (1 - $productData['discount_percentage'] / 100)
    : $productData['price'];

// Obtener categoría del producto
$productCategory = $category->getCategoryById($productData['category_id']);

// Obtener productos relacionados
$relatedProducts = $product->getRelatedProducts($id, $productData['category_id'], 4);
foreach ($relatedProducts as &$rp) {
    $rp['image'] = Product::getImagePath($rp);
}
unset($rp);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($productData['name']); ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($productData['description']); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
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

    <!-- Product Detail Section -->
    <section class="section" style="padding-top: 120px;">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="products.php">Productos</a></li>
                    <?php if ($productCategory): ?>
                        <li class="breadcrumb-item">
                            <a href="category.php?slug=<?php echo $productCategory['slug']; ?>">
                                <?php echo $productCategory['name']; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($productData['name']); ?></li>
                </ol>
            </nav>

            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6 mb-4" data-aos="fade-right">
                    <div class="product-gallery">
                        <div class="main-image mb-3">
                            <img src="<?php echo htmlspecialchars($mainImage); ?>"
                                 alt="<?php echo htmlspecialchars($productData['name']); ?>"
                                 class="img-fluid rounded-custom shadow-lg"
                                 id="mainImage"
                                 loading="lazy"
                                 onerror="this.src='assets/images/placeholder.jpg';">
                        </div>
                        <div class="thumbnail-images d-flex gap-2">
                            <?php if (!empty($productImages)): ?>
                                <?php foreach ($productImages as $index => $imgUrl): ?>
                                    <img src="<?php echo htmlspecialchars($imgUrl); ?>"
                                         alt="Thumbnail"
                                         class="img-thumbnail thumbnail-img <?php echo $index === 0 ? 'active' : ''; ?>"
                                         loading="lazy"
                                         onclick="changeImage(this)"
                                         onerror="this.src='assets/images/placeholder.jpg';">
                                <?php endforeach; ?>
                            <?php else: ?>
                                <img src="<?php echo htmlspecialchars($mainImage); ?>"
                                     alt="Thumbnail" class="img-thumbnail thumbnail-img active"
                                     loading="lazy"
                                     onclick="changeImage(this)"
                                     onerror="this.src='assets/images/placeholder.jpg';">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="product-info">
                        <!-- Category Badge -->
                        <?php if ($productCategory): ?>
                            <div class="mb-3">
                                <span class="badge bg-primary opacity-75 text-white">
                                    <?php echo $productCategory['name']; ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <!-- Product Title -->
                        <h1 class="h2 fw-bold mb-3"><?php echo htmlspecialchars($productData['name']); ?></h1>

                        <!-- Rating -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="rating me-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star text-warning"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="text-muted me-3">(4.8)</span>
                            <span class="text-muted">128 reseñas</span>
                        </div>

                        <!-- Price -->
                        <div class="price-section mb-4">
                            <?php if ($productData['discount_percentage'] > 0): ?>
                                <div class="d-flex align-items-center">
                                    <span class="h3 text-decoration-line-through text-muted me-3">
                                        $<?php echo number_format($productData['price'], 0, ',', '.'); ?>
                                    </span>
                                    <span class="h2 text-success fw-bold">
                                        $<?php echo number_format($productData['price'] * (1 - $productData['discount_percentage'] / 100), 0, ',', '.'); ?>
                                    </span>
                                    <span class="badge bg-danger ms-3">
                                        -<?php echo $productData['discount_percentage']; ?>%
                                    </span>
                                </div>
                            <?php else: ?>
                                <span class="h2 text-primary fw-bold">
                                    $<?php echo number_format($productData['price'], 0, ',', '.'); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Description -->
                        <div class="description mb-4">
                            <h5 class="fw-bold mb-2">Descripción</h5>
                            <p class="text-muted">
                                <?php echo nl2br(htmlspecialchars($productData['description'])); ?>
                            </p>
                        </div>

                        <!-- Features -->
                        <div class="features mb-4">
                            <h5 class="fw-bold mb-3">Características</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Calidad premium garantizada
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Garantía de 30 días
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Soporte técnico especializado
                                </li>
                            </ul>
                        </div>

                        <!-- Quantity and Actions -->
                        <div class="actions mb-4">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Cantidad</label>
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary" onclick="updateQuantity(-1)">-</button>
                                        <input type="number" class="form-control text-center" value="1" min="1" id="quantity">
                                        <button class="btn btn-outline-secondary" onclick="updateQuantity(1)">+</button>
                                    </div>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold">Stock</label>
                                    <div class="d-flex align-items-center">
                                        <span class="text-success me-2">
                                            <i class="fas fa-circle"></i>
                                        </span>
                                        <span class="text-muted">Disponible</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg hover-lift" onclick="addToCartWithQuantity(<?php echo $productData['id']; ?>, '<?php echo addslashes($productData['name']); ?>', <?php echo $finalPrice; ?>, '<?php echo $mainImage; ?>')">
                                    <i class="fas fa-cart-plus me-2"></i>
                                    Agregar al Carrito
                                </button>
                                <a href="https://wa.me/593983015307?text=Hola%2C%20quiero%20comprar%20el%20producto:%20<?php echo urlencode($productData['name']); ?>%20-%20$<?php echo number_format($productData['discount_percentage'] > 0 ? $productData['price'] * (1 - $productData['discount_percentage'] / 100) : $productData['price'], 0, ',', '.'); ?>%20-%20AlquimiaTechnologic" 
                                   class="btn btn-success btn-lg hover-lift" target="_blank">
                                    <i class="fab fa-whatsapp me-2"></i>
                                    Comprar por WhatsApp
                                </a>
                                <button class="btn btn-outline-primary hover-lift" onclick="toggleWishlist(<?php echo $productData['id']; ?>)">
                                    <i class="fas fa-heart me-2"></i>
                                    Agregar a Favoritos
                                </button>
                            </div>
                        </div>

                        <!-- Shipping Info -->
                        <div class="shipping-info p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <i class="fas fa-shield-alt text-success me-2"></i>
                                    <span class="text-muted">Garantía 30 días</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="fas fa-undo text-info me-2"></i>
                                    <span class="text-muted">Devolución fácil</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="fas fa-headset text-warning me-2"></i>
                                    <span class="text-muted">Soporte 24/7</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details Tabs -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-transparent border-0">
                            <ul class="nav nav-tabs card-header-tabs" id="productTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                                        <i class="fas fa-info-circle me-2"></i>Detalles
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                                        <i class="fas fa-star me-2"></i>Reseñas
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab">
                                        <i class="fas fa-truck me-2"></i>Envío
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="productTabsContent">
                                <div class="tab-pane fade show active" id="details" role="tabpanel">
                                    <h5 class="fw-bold mb-3">Detalles del Producto</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><strong>Categoría:</strong> <?php echo $productCategory ? $productCategory['name'] : 'Sin categoría'; ?></li>
                                                <li class="mb-2"><strong>SKU:</strong> <?php echo $productData['id']; ?></li>
                                                <li class="mb-2"><strong>Peso:</strong> 500g</li>
                                                <li class="mb-2"><strong>Dimensiones:</strong> 10 x 15 x 5 cm</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><strong>Material:</strong> Premium</li>
                                                <li class="mb-2"><strong>Color:</strong> Varios</li>
                                                <li class="mb-2"><strong>Garantía:</strong> 30 días</li>
                                                <li class="mb-2"><strong>Origen:</strong> Ecuador</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="reviews" role="tabpanel">
                                    <h5 class="fw-bold mb-3">Reseñas de Clientes</h5>
                                    <div class="review-item mb-3 p-3 border rounded">
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>
                                                <strong>María González</strong>
                                                <div class="rating">
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                </div>
                                            </div>
                                            <small class="text-muted">Hace 2 días</small>
                                        </div>
                                        <p class="mb-0">Excelente producto, calidad superior y envío rápido. ¡Muy recomendado!</p>
                                    </div>
                                    <div class="review-item mb-3 p-3 border rounded">
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>
                                                <strong>Carlos Rodríguez</strong>
                                                <div class="rating">
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-warning"></i>
                                                    <i class="fas fa-star text-muted"></i>
                                                </div>
                                            </div>
                                            <small class="text-muted">Hace 1 semana</small>
                                        </div>
                                        <p class="mb-0">Buen producto, cumple con las expectativas. El servicio al cliente es muy bueno.</p>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="shipping" role="tabpanel">
                                    <h5 class="fw-bold mb-3">Información de Envío</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold">Opciones de Envío</h6>
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-truck text-primary me-2"></i>
                                                    <strong>Envío Estándar:</strong> Gratis (3-5 días)
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-shipping-fast text-success me-2"></i>
                                                    <strong>Envío Express:</strong> $15.000 (1-2 días)
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold">Zonas de Cobertura</h6>
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Todo Ecuador
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Envío internacional disponible
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <?php if (!empty($relatedProducts)): ?>
                <div class="row mt-5">
                    <div class="col-12">
                        <h3 class="section-title text-gradient mb-4" data-aos="fade-up">
                            <i class="fas fa-thumbs-up me-3"></i>
                            Productos Relacionados
                        </h3>
                        <div class="row">
                            <?php foreach ($relatedProducts as $related): ?>
                                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up">
                                    <div class="product-card h-100 hover-lift">
                                        <div class="product-image">
                                            <img src="<?php echo $related['image'] ?: 'assets/images/placeholder.jpg'; ?>"
                                                 alt="<?php echo htmlspecialchars($related['name']); ?>"
                                                 class="img-fluid"
                                                 loading="lazy"
                                                 onerror="this.src='assets/images/placeholder.jpg';">
                                            <div class="product-overlay">
                                                <button class="btn btn-light btn-sm me-2 hover-glow" onclick="addToCart(<?php echo $related['id']; ?>, '<?php echo addslashes($related['name']); ?>', <?php echo ($related['discount_percentage'] > 0 ? $related['price'] * (1 - $related['discount_percentage'] / 100) : $related['price']); ?>, '<?php echo $related['image'] ?: 'assets/images/placeholder.jpg'; ?>')" title="Agregar al carrito">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </button>
                                                <a href="product.php?id=<?php echo $related['id']; ?>" class="btn btn-light btn-sm hover-glow" title="Ver producto">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="card-title"><?php echo htmlspecialchars($related['name']); ?></h6>
                                            <div class="price mb-2">
                                                <?php if ($related['discount_percentage'] > 0): ?>
                                                    <span class="text-decoration-line-through text-muted me-2">
                                                        $<?php echo number_format($related['price'], 0, ',', '.'); ?>
                                                    </span>
                                                    <span class="text-success fw-bold">
                                                        $<?php echo number_format($related['price'] * (1 - $related['discount_percentage'] / 100), 0, ',', '.'); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="fw-bold text-primary">$<?php echo number_format($related['price'], 0, ',', '.'); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <button class="btn btn-primary btn-sm w-100" onclick="addToCart(<?php echo $related['id']; ?>, '<?php echo addslashes($related['name']); ?>', <?php echo ($related['discount_percentage'] > 0 ? $related['price'] * (1 - $related['discount_percentage'] / 100) : $related['price']); ?>, '<?php echo $related['image'] ?: 'assets/images/placeholder.jpg'; ?>')">
                                                <i class="fas fa-cart-plus me-2"></i>
                                                Agregar al Carrito
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
        
        // Image gallery
        function changeImage(thumbnail) {
            const mainImage = document.getElementById('mainImage');
            mainImage.src = thumbnail.src.replace('w=200', 'w=800');
            
            // Update active thumbnail
            document.querySelectorAll('.thumbnail-img').forEach(img => {
                img.classList.remove('active');
            });
            thumbnail.classList.add('active');
        }
        
        // Quantity functions
        function updateQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            const newQuantity = Math.max(1, parseInt(quantityInput.value) + change);
            quantityInput.value = newQuantity;
        }
        
        // Wishlist toggle
        function toggleWishlist(productId) {
            console.log('Toggle wishlist for product:', productId);
            showNotification('Producto agregado a favoritos', 'success');
        }
        
        // Wrapper to use global addToCart with selected quantity
        function addToCartWithQuantity(productId, name, price, image) {
            const quantity = parseInt(document.getElementById('quantity').value) || 1;
            if (typeof window.addToCart === 'function') {
                window.addToCart(productId, name, price, image, quantity);
            }
        }
    </script>
</body>
</html>