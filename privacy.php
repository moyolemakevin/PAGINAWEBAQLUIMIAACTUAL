<?php

require_once __DIR__ . '/config/bootstrap.php';

$contactEmail = getSiteSetting('contact_email', ADMIN_EMAIL);

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Politica de Privacidad - <?php echo SITE_NAME; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link href="<?php echo assetUrl('assets/css/style.css'); ?>" rel="stylesheet">

    <link rel="icon" href="<?php echo assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')); ?>">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

</head>

<body>

    <?php
$currentPage = 'privacy';
include __DIR__ . '/partials/navbar.php';
?>
<main id="main-content">

    <section class="section bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="section-title text-gradient" data-aos="fade-up">
                    Politica de Privacidad
                </h1>
                <p class="lead" data-aos="fade-up" data-aos-delay="200">
                    Cuidamos tus datos y los usamos solo para brindarte un mejor servicio.
                </p>
                <p class="text-muted" data-aos="fade-up" data-aos-delay="300">
                    Ultima actualizacion: <?php echo date('d/m/Y'); ?>
                </p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10" data-aos="fade-up">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4 p-md-5">
                            <h5 class="fw-bold mb-3">1. Informacion que recopilamos</h5>
                            <ul class="text-muted">
                                <li>Datos de contacto: nombre, correo, telefono.</li>
                                <li>Datos de compra: productos, precios y estado de pedido.</li>
                                <li>Datos de navegacion: direccion IP, navegador y paginas visitadas.</li>
                            </ul>

                            <h5 class="fw-bold mb-3 mt-4">2. Como usamos la informacion</h5>
                            <ul class="text-muted">
                                <li>Procesar pedidos y entregar productos o servicios.</li>
                                <li>Enviar confirmaciones y notificaciones importantes.</li>
                                <li>Mejorar la experiencia del sitio y nuestro soporte.</li>
                            </ul>

                            <h5 class="fw-bold mb-3 mt-4">3. Cookies y tecnologias similares</h5>
                            <p class="text-muted">
                                Utilizamos cookies para mantener tu sesion activa y medir el uso del sitio.
                                Puedes desactivarlas desde tu navegador, pero algunas funciones podrian limitarse.
                            </p>

                            <h5 class="fw-bold mb-3 mt-4">4. Seguridad</h5>
                            <p class="text-muted">
                                Aplicamos medidas tecnicas y organizativas para proteger tu informacion.
                                No compartimos datos con terceros sin tu consentimiento, salvo requerimientos legales.
                            </p>

                            <h5 class="fw-bold mb-3 mt-4">5. Derechos del usuario</h5>
                            <p class="text-muted">
                                Puedes solicitar acceso, actualizacion o eliminacion de tus datos escribiendo a
                                <?php echo htmlspecialchars($contactEmail); ?>.
                            </p>

                            <h5 class="fw-bold mb-3 mt-4">6. Contacto</h5>
                            <p class="text-muted mb-0">
                                Si tienes dudas sobre esta politica, escribenos a
                                <?php echo htmlspecialchars($contactEmail); ?>.
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
