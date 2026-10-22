<?php
require_once __DIR__ . '/config/bootstrap.php';
$productModel = new Product();
$categoryModel = new Category();
$featuredProducts = $productModel->getFeaturedProducts(8, true);
$categories = $categoryModel->getCategoriesWithProductCount(true);
$heroImage = assetUrl(getSafeSiteImageSetting('hero_image_url', 'assets/images/placeholder.jpg'));
$escape = static function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$whatsappUrl = 'https://wa.me/593983015307?text=' . rawurlencode('Hola, quiero asesoría sobre el catálogo de Moyoalquimia');
$catalogCategories = [];
foreach ($categories as $cat) {
    $presentation = getCategoryPresentation($cat['slug'] ?? '', $cat['name']);
    $catalogCategories[] = array_merge($cat, $presentation, ['url' => 'category.php?slug=' . rawurlencode($cat['slug'])]);
}
$firstCategory = $catalogCategories[0] ?? ['name' => 'Moyoalquimia', 'icon' => 'fas fa-flask', 'summary' => 'Tecnología, bienestar y creatividad para transformar tu día.', 'url' => 'products.php'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moyoalquimia — Tecnología, bienestar y creatividad</title>
    <meta name="description" content="Software a medida, aceites esenciales, maceteros de yeso y suscripciones digitales. Consulta nuestro catálogo y recibe asesoría directa en Moyoalquimia.">
    <meta name="theme-color" content="#080d17">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= assetUrl('assets/css/style.css') ?>" rel="stylesheet">
    <link href="<?= assetUrl('assets/css/experience.css') ?>" rel="stylesheet">
    <link rel="icon" href="<?= assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')) ?>">
</head>
<body class="home-page experience-home">
<?php
$currentPage = 'index';
$showCategoryDropdown = true;
include __DIR__ . '/partials/navbar.php';
?>
<main id="main-content">
    <div class="reading-progress" aria-hidden="true"><span data-reading-progress></span></div>
    <section class="experience-hero" aria-labelledby="experience-title">
        <div class="experience-grid" aria-hidden="true"></div>
        <div class="container">
            <div class="experience-hero-layout">
                <div class="experience-intro">
                    <p class="experience-eyebrow"><span class="status-dot"></span> PRODUCTOS Y SERVICIOS · MOYOALQUIMIA</p>
                    <h1 id="experience-title">Innovación.<br>Bienestar.<br><span>Creatividad.</span></h1>
                    <p class="experience-lead">Software a medida, aceites esenciales, maceteros de yeso y suscripciones digitales. Encuentra lo que necesitas, con asesoría directa.</p>
                    <div class="experience-actions">
                        <a class="experience-button experience-button-primary" href="products.php">Explorar el catálogo <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        <a class="experience-text-link" href="#categorias">Ver categorías <span aria-hidden="true">↗</span></a>
                    </div>
                    <div class="experience-signature"><span class="signature-line"></span> Atención personalizada desde Ecuador.</div>
                </div>
                <div class="experience-showcase" data-category="<?= $escape($firstCategory['slug'] ?? '') ?>">
                    <div class="brand-visual">
                        <span class="brand-scan" aria-hidden="true"></span>
                        <img src="<?= $escape($heroImage) ?>" alt="Moyoalquimia: software, aceites esenciales, figuras de yeso y suscripciones digitales" width="1024" height="1024" fetchpriority="high" decoding="async">
                        <span class="brand-chip brand-chip-top" aria-hidden="true"><i class="fas fa-code"></i> Software a medida</span>
                        <span class="brand-chip brand-chip-bottom" aria-hidden="true"><i class="fas fa-leaf"></i> Bienestar natural</span>
                    </div>
                    <div class="category-preview">
                        <span class="category-preview-icon"><i class="<?= $escape($firstCategory['icon']) ?>" data-category-icon aria-hidden="true"></i></span>
                        <div aria-live="polite" aria-atomic="true"><span class="category-preview-kicker">EXPLORA POR CATEGORÍA</span><h2 data-category-name><?= $escape($firstCategory['name']) ?></h2><p data-category-summary><?= $escape($firstCategory['summary']) ?></p></div>
                        <a href="<?= $escape($firstCategory['url']) ?>" data-category-link aria-label="Explorar <?= $escape($firstCategory['name']) ?>"><i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    </div>
                    <?php if (count($catalogCategories) > 1): ?>
                    <div class="category-selectors" role="group" aria-label="Elige una categoría">
                        <?php foreach ($catalogCategories as $categoryIndex => $catalogCategory): ?>
                        <button type="button" class="category-selector" aria-pressed="<?= $categoryIndex === 0 ? 'true' : 'false' ?>" data-category-choice="<?= $escape(json_encode(['name' => $catalogCategory['name'], 'summary' => $catalogCategory['summary'], 'icon' => $catalogCategory['icon'], 'url' => $catalogCategory['url'], 'slug' => $catalogCategory['slug']], JSON_UNESCAPED_UNICODE)) ?>"><i class="<?= $escape($catalogCategory['icon']) ?>" aria-hidden="true"></i><span><?= $escape($catalogCategory['name']) ?></span></button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="experience-hero-bottom"><span>PRODUCTOS Y SERVICIOS</span><a href="#categorias">VER CATEGORÍAS <i class="fas fa-arrow-down" aria-hidden="true"></i></a><span>ASESORÍA POR WHATSAPP</span></div>
        </div>
    </section>

    <?php include __DIR__ . '/partials/product-spotlight.php'; ?>

    <section class="experience-categories" id="categorias" aria-labelledby="categories-title">
        <div class="container">
            <div class="experience-section-heading"><div><p class="experience-eyebrow">01 / NUESTRAS CATEGORÍAS</p><h2 id="categories-title">Lo que necesitas,<br><span>en un solo lugar.</span></h2></div><p>Soluciones digitales, bienestar y decoración.<br class="d-none d-md-block"> Elige una categoría para conocer nuestros productos.</p></div>
            <div class="categories-mosaic">
                <?php foreach ($catalogCategories as $categoryIndex => $catalogCategory): ?>
                <a class="category-tile category-tile-<?= $escape($catalogCategory['slug']) ?>" href="<?= $escape($catalogCategory['url']) ?>">
                    <div class="category-tile-top"><span><?= str_pad((string) ($categoryIndex + 1), 2, '0', STR_PAD_LEFT) ?> / <?= $escape($catalogCategory['eyebrow']) ?></span><span class="category-tile-arrow" aria-hidden="true">↗</span></div>
                    <div class="category-art" aria-hidden="true"><div class="category-art-orbit"></div><i class="<?= $escape($catalogCategory['icon']) ?>"></i><span class="category-art-point"></span></div>
                    <div class="category-tile-content"><h3><?= $escape($catalogCategory['name']) ?></h3><p><?= $escape($catalogCategory['summary']) ?></p><span class="category-tile-cta">Explorar colección <i class="fas fa-arrow-right" aria-hidden="true"></i></span></div>
                </a>
                <?php endforeach; ?>
                <?php if (!$catalogCategories): ?><p>Estamos preparando nuevas colecciones. <a href="<?= $escape($whatsappUrl) ?>">Escríbenos para conocer más.</a></p><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="experience-featured" aria-labelledby="featured-title">
        <div class="container">
            <div class="experience-section-heading"><div><p class="experience-eyebrow">02 / PRODUCTOS DESTACADOS</p><h2 id="featured-title">Una selección<br><span>para empezar.</span></h2></div><a class="experience-text-link" href="products.php">Ver todo el catálogo <span aria-hidden="true">↗</span></a></div>
            <div class="experience-product-grid">
                <?php foreach ($featuredProducts as $item):
                    $image = Product::getImagePath($item) ?: 'assets/images/placeholder.jpg';
                    $discount = (float) ($item['discount_percentage'] ?? 0);
                    $finalPrice = (float) $item['price'] * (1 - $discount / 100);
                    $itemUrl = 'product.php?id=' . (int) $item['id'];
                ?>
                <article class="discovery-card">
                    <a class="discovery-image" href="<?= $escape($itemUrl) ?>" aria-label="Ver <?= $escape($item['name']) ?>">
                        <img src="<?= $escape(assetUrl($image)) ?>" alt="<?= $escape($item['name']) ?>" loading="lazy" decoding="async" width="420" height="420">
                        <?php if ($discount > 0): ?><span class="discovery-discount">−<?= $escape($discount) ?>%</span><?php endif; ?>
                        <span class="discovery-image-arrow" aria-hidden="true">↗</span>
                    </a>
                    <div class="discovery-body"><h3><a href="<?= $escape($itemUrl) ?>"><?= $escape($item['name']) ?></a></h3><div class="discovery-bottom"><div class="discovery-price"><?php if ($discount > 0): ?><del>$<?= number_format((float) $item['price'], 2) ?></del><?php endif; ?><span>$<?= number_format($finalPrice, 2) ?></span></div><button type="button" class="discovery-add add-to-cart" data-product-id="<?= (int) $item['id'] ?>" data-product-name="<?= $escape($item['name']) ?>" data-product-price="<?= $escape($finalPrice) ?>" data-product-image="<?= $escape($image) ?>" aria-label="Añadir <?= $escape($item['name']) ?> al carrito"><i class="fas fa-plus" aria-hidden="true"></i></button></div></div>
                </article>
                <?php endforeach; ?>
                <?php if (!$featuredProducts): ?><p>Explora nuestras categorías o <a href="<?= $escape($whatsappUrl) ?>">consulta los productos disponibles</a>.</p><?php endif; ?>
            </div>
        </div>
    </section>

    <section class="experience-how" aria-labelledby="how-title"><div class="container"><div class="experience-section-heading"><div><p class="experience-eyebrow">03 / CÓMO COMPRAR</p><h2 id="how-title">Compra con asesoría.<br><span>En tres pasos.</span></h2></div><p>Consulta disponibilidad y condiciones.<br>Te ayudamos a elegir antes de comprar.</p></div><div class="experience-steps">
        <article><span>01</span><i class="fas fa-compass" aria-hidden="true"></i><h3>Elige tu producto</h3><p>Consulta las categorías, compara los productos y añade tus favoritos al carrito.</p></article>
        <article><span>02</span><i class="fab fa-whatsapp" aria-hidden="true"></i><h3>Conversemos</h3><p>Resuelve tus dudas por WhatsApp y consulta disponibilidad, entrega o activación.</p></article>
        <article><span>03</span><i class="fas fa-box-open" aria-hidden="true"></i><h3>Coordina tu pedido</h3><p>Confirma con nosotros el pago y la entrega o activación, según el producto que elijas.</p></article>
    </div></div></section>

    <section class="experience-contact" aria-labelledby="contact-title"><div class="container"><div class="experience-contact-panel"><div><p class="experience-eyebrow"><span class="status-dot"></span> ASESORÍA DIRECTA · MOYOALQUIMIA</p><h2 id="contact-title">¿En qué podemos<br><span>ayudarte?</span></h2><p>Pregunta por nuestros productos, suscripciones o software a medida.<br>Consulta precios, disponibilidad y opciones de entrega.</p></div><a href="<?= $escape($whatsappUrl) ?>" target="_blank" rel="noopener noreferrer" class="experience-button experience-button-primary"><i class="fab fa-whatsapp" aria-hidden="true"></i> Consultar por WhatsApp <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i></a><span class="contact-orbit" aria-hidden="true"></span></div></div></section>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>
<?php include __DIR__ . '/partials/scripts.php'; ?>
<script src="<?= assetUrl('assets/js/experience.js') ?>" defer></script>
<script src="<?= assetUrl('assets/js/product-promotions.js') ?>" defer></script>
</body>
</html>
