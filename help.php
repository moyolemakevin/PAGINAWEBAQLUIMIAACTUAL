<?php

require_once __DIR__ . '/config/bootstrap.php';

$contactEmail = getSiteSetting('contact_email', ADMIN_EMAIL);
$contactPhone = getSiteSetting('contact_phone', '');
$whatsappNumber = getSiteSetting('whatsapp_number', $contactPhone);
$whatsappUrl = trim((string)getSiteSetting('whatsapp_url', ''));
$whatsappLink = '#';
if ($whatsappUrl !== '') {
    $whatsappLink = $whatsappUrl;
} elseif ($whatsappNumber !== '') {
    $digits = preg_replace('/\D+/', '', $whatsappNumber);
    if ($digits !== '') {
        $whatsappLink = 'https://wa.me/' . $digits;
    }
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Centro de Ayuda - <?php echo SITE_NAME; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link href="<?php echo assetUrl('assets/css/style.css'); ?>" rel="stylesheet">

    <link rel="icon" href="<?php echo assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')); ?>">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

</head>

<body>

    <?php
$currentPage = 'help';
include __DIR__ . '/partials/navbar.php';
?>
<main id="main-content">

    <section class="section bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="section-title text-gradient" data-aos="fade-up">
                    Centro de Ayuda
                </h1>
                <p class="lead" data-aos="fade-up" data-aos-delay="200">
                    Encuentra respuestas rapidas sobre compras, pagos y soporte tecnico.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <h5 class="mb-0">Compras</h5>
                            </div>
                            <p class="text-muted mb-0">
                                Guia de compra, carrito, pedidos y confirmaciones.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <h5 class="mb-0">Pagos</h5>
                            </div>
                            <p class="text-muted mb-0">
                                Metodos de pago, facturacion y comprobantes.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <h5 class="mb-0">Envios</h5>
                            </div>
                            <p class="text-muted mb-0">
                                Tiempos de entrega, seguimiento y cobertura.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <h5 class="mb-0">Soporte tecnico</h5>
                            </div>
                            <p class="text-muted mb-0">
                                Asistencia para software, implementacion y soporte tecnico.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h5 class="mb-0">Garantias</h5>
                            </div>
                            <p class="text-muted mb-0">
                                Politicas de cambio, devolucion y soporte postventa.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h5 class="mb-0">Cuenta</h5>
                            </div>
                            <p class="text-muted mb-0">
                                Registro, acceso, datos de perfil y seguridad.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-lg-7" data-aos="fade-right">
                    <h2 class="mb-3">Necesitas ayuda personalizada?</h2>
                    <p class="text-muted">
                        Nuestro equipo esta listo para ayudarte. Responderemos lo mas rapido posible
                        con una solucion clara y directa.
                    </p>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($contactEmail); ?>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($contactPhone !== '' ? $contactPhone : 'No disponible'); ?>
                        </li>
                        <?php if ($whatsappLink !== '#'): ?>
                            <li>
                                <i class="fab fa-whatsapp me-2"></i>
                                <a href="<?php echo htmlspecialchars($whatsappLink, ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer">Escribir por WhatsApp</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-lg-5" data-aos="fade-left">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Recomendaciones rapidas</h5>
                            <ul class="list-unstyled text-muted mb-0">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Revisa tu bandeja de spam.</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Confirma el numero de pedido.</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Indica el producto y la fecha.</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Adjunta capturas si aplica.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
<?php include __DIR__ . '/partials/footer.php'; ?>

    <?php include __DIR__ . '/partials/scripts.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.AOS) {
            AOS.init();
        }
    });
    </script>

</body>

</html>
