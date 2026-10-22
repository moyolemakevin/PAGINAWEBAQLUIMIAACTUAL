<?php
// Paid orders are the only source of sales claims; featured products fill the rest.
$spotlightProducts = [];
try {
    $spotlightDb = (new Database())->getConnection();
    $spotlightQuery = $spotlightDb->query("SELECT oi.product_id, SUM(oi.quantity) AS units
        FROM order_items oi JOIN orders o ON o.id = oi.order_id
        WHERE o.payment_status = 'paid' AND o.status != 'cancelled'
        GROUP BY oi.product_id HAVING SUM(oi.quantity) > 0
        ORDER BY units DESC, oi.product_id ASC LIMIT 4");
    foreach ($spotlightQuery->fetchAll(PDO::FETCH_ASSOC) as $sold) {
        $soldProduct = $productModel->getProductById((int) $sold['product_id'], true);
        if ($soldProduct) {
            $soldProduct['spotlight_label'] = 'Entre los más vendidos';
            $soldProduct['spotlight_note'] = 'Según los pedidos pagados registrados en nuestra tienda.';
            $spotlightProducts[(int) $soldProduct['id']] = $soldProduct;
        }
    }
} catch (PDOException $e) {
    error_log('Product spotlight: sales ranking unavailable');
}
foreach ($featuredProducts as $featured) {
    if (count($spotlightProducts) >= 4) break;
    if (!isset($spotlightProducts[(int) $featured['id']])) {
        $featured['spotlight_label'] = (float) ($featured['discount_percentage'] ?? 0) > 0 ? 'Precio con descuento' : 'Selección Moyoalquimia';
        $featured['spotlight_note'] = 'Producto destacado de nuestro catálogo.';
        $spotlightProducts[(int) $featured['id']] = $featured;
    }
}
$spotlightProducts = array_values($spotlightProducts);
if (!$spotlightProducts) return;
?>
<section class="product-spotlight" aria-label="Escaparate de productos" aria-roledescription="carrusel" data-spotlight>
    <div class="container">
        <div class="spotlight-heading"><p class="experience-eyebrow"><span class="status-dot"></span> EN EL ESCAPARATE</p><a href="products.php">Ver catálogo <span aria-hidden="true">↗</span></a></div>
        <div class="spotlight-frame">
            <div class="spotlight-slides" aria-live="off" data-spotlight-slides>
            <?php foreach ($spotlightProducts as $spotlightIndex => $spotlightItem):
                $spotlightImage = Product::getImagePath($spotlightItem) ?: 'assets/images/placeholder.jpg';
                $spotlightDiscount = (float) ($spotlightItem['discount_percentage'] ?? 0);
                $spotlightPrice = (float) $spotlightItem['price'] * (1 - $spotlightDiscount / 100);
                $spotlightUrl = 'product.php?id=' . (int) $spotlightItem['id'];
                $spotlightDescription = trim(strip_tags((string) ($spotlightItem['short_description'] ?? '')));
                $spotlightDescription = trim(preg_replace('/[?？]{2,}/u', '', $spotlightDescription));
                if ($spotlightDescription === '') {
                    $spotlightDescription = 'Conoce sus características y consulta los detalles antes de realizar tu pedido.';
                }
            ?>
                <article class="spotlight-slide" data-spotlight-slide role="group" aria-roledescription="diapositiva" aria-label="<?= $spotlightIndex + 1 ?> de <?= count($spotlightProducts) ?>" <?= $spotlightIndex ? 'hidden' : '' ?>>
                    <a href="<?= $escape($spotlightUrl) ?>" class="spotlight-image" aria-label="Ver <?= $escape($spotlightItem['name']) ?>">
                        <span class="spotlight-image-grid" aria-hidden="true"></span>
                        <img src="<?= $escape(assetUrl($spotlightImage)) ?>" alt="<?= $escape($spotlightItem['name']) ?>" width="480" height="480" loading="lazy" decoding="async">
                        <span class="spotlight-image-tag"><i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i> VER PRODUCTO</span>
                    </a>
                    <div class="spotlight-copy">
                        <p class="spotlight-badge"><i class="fas <?= str_contains($spotlightItem['spotlight_label'], 'vendidos') ? 'fa-chart-line' : 'fa-star' ?>" aria-hidden="true"></i> <?= $escape($spotlightItem['spotlight_label']) ?></p>
                        <span class="spotlight-category"><?= $escape($spotlightItem['category_name'] ?? 'Catálogo Moyoalquimia') ?></span>
                        <h2><a href="<?= $escape($spotlightUrl) ?>"><?= $escape($spotlightItem['name']) ?></a></h2>
                        <p class="spotlight-description"><?= $escape($spotlightDescription) ?></p>
                        <div class="spotlight-price"><?php if ($spotlightDiscount > 0): ?><del>$<?= number_format((float) $spotlightItem['price'], 2) ?></del><?php endif; ?><span>$<?= number_format($spotlightPrice, 2) ?></span><?php if ($spotlightDiscount > 0): ?><small>−<?= $escape($spotlightDiscount) ?>%</small><?php endif; ?></div>
                        <div class="spotlight-actions"><a href="<?= $escape($spotlightUrl) ?>" class="experience-button experience-button-primary">Conocer producto <i class="fas fa-arrow-right" aria-hidden="true"></i></a><button type="button" class="spotlight-cart add-to-cart" data-product-id="<?= (int) $spotlightItem['id'] ?>" data-product-name="<?= $escape($spotlightItem['name']) ?>" data-product-price="<?= $escape($spotlightPrice) ?>" data-product-image="<?= $escape($spotlightImage) ?>" aria-label="Añadir <?= $escape($spotlightItem['name']) ?> al carrito"><i class="fas fa-cart-plus" aria-hidden="true"></i></button></div>
                        <p class="spotlight-note"><?= $escape($spotlightItem['spotlight_note']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
            </div>
            <?php if (count($spotlightProducts) > 1): ?>
            <div class="spotlight-controls" hidden data-spotlight-controls>
                <div class="spotlight-dots" role="group" aria-label="Seleccionar producto">
                    <?php foreach ($spotlightProducts as $spotlightIndex => $spotlightItem): ?><button type="button" data-spotlight-dot="<?= $spotlightIndex ?>" aria-label="Mostrar <?= $escape($spotlightItem['name']) ?>" aria-current="<?= $spotlightIndex === 0 ? 'true' : 'false' ?>"><span></span></button><?php endforeach; ?>
                </div>
                <div class="spotlight-navigation"><span data-spotlight-count>01 / <?= str_pad((string) count($spotlightProducts), 2, '0', STR_PAD_LEFT) ?></span><button type="button" data-spotlight-prev aria-label="Producto anterior"><i class="fas fa-arrow-left" aria-hidden="true"></i></button><button type="button" data-spotlight-play aria-label="Pausar carrusel"><i class="fas fa-pause" aria-hidden="true"></i></button><button type="button" data-spotlight-next aria-label="Producto siguiente"><i class="fas fa-arrow-right" aria-hidden="true"></i></button></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
