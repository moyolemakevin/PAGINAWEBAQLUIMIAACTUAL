<?php

require_once __DIR__ . '/config/bootstrap.php';

$contactEmail = getSiteSetting('contact_email', ADMIN_EMAIL);

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Terminos y Condiciones - <?php echo SITE_NAME; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link href="<?php echo assetUrl('assets/css/style.css'); ?>" rel="stylesheet">

    <link rel="icon" href="<?php echo assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')); ?>">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

</head>

<body>

    <?php
$currentPage = 'terms';
include __DIR__ . '/partials/navbar.php';
?>
<main id="main-content">

    <section class="section bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="section-title text-gradient" data-aos="fade-up">
                    Terminos y Condiciones
                </h1>
                <p class="lead" data-aos="fade-up" data-aos-delay="200">
                    Recomendamos leer esta informacion antes de usar el sitio.
                </p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10" data-aos="fade-up">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4 p-md-5">
                            <h5 class="fw-bold mb-3">1. Aceptacion de los terminos</h5>
                            <p class="text-muted">
                                Al acceder y utilizar este sitio aceptas estos terminos y sus futuras actualizaciones.
                            </p>

                            <h5 class="fw-bold mb-3 mt-4">2. Uso del sitio</h5>
                            <p class="text-muted">
                                El usuario se compromete a usar el sitio de forma legal y responsable.
                                No se permite el uso indebido de informacion ni la alteracion del servicio.
                            </p>

                            <h5 class="fw-bold mb-3 mt-4">3. Productos y servicios</h5>
                            <p class="text-muted">
                                Las descripciones, precios y disponibilidad pueden cambiar sin previo aviso.
                                Nos reservamos el derecho de limitar cantidades o cancelar pedidos por causas justificadas.
                            </p>

                            <h5 class="fw-bold mb-3 mt-4">4. Pagos y facturacion</h5>
                            <p class="text-muted">
                                Los pagos deben completarse segun los metodos disponibles al momento de la compra.
                                La facturacion se emitira con los datos proporcionados por el cliente.
                            </p>

                            <h5 class="fw-bold mb-3 mt-4">5. Envios y devoluciones</h5>
                            <p class="text-muted">
                                Los tiempos de entrega son estimados. Si deseas realizar un cambio o devolucion,
                                contactanos para validar condiciones y plazos.
                            </p>

                            <h5 class="fw-bold mb-3 mt-4">6. Propiedad intelectual</h5>
                            <p class="text-muted">
                                El contenido del sitio, marcas y materiales son propiedad de <?php echo SITE_NAME; ?>.
                                Queda prohibida su reproduccion sin autorizacion.
                            </p>

                            <h5 class="fw-bold mb-3 mt-4">7. Limitacion de responsabilidad</h5>
                            <p class="text-muted">
                                No nos responsabilizamos por interrupciones temporales del servicio o demoras ajenas a nuestro control.
                            </p>

                            <h5 class="fw-bold mb-3 mt-4">8. Contacto</h5>
                            <p class="text-muted mb-0">
                                Para dudas sobre estos terminos escribe a <?php echo htmlspecialchars($contactEmail); ?>.
                            </p>
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
