<?php

require_once __DIR__ . '/config/bootstrap.php';

$contactEmail = getSiteSetting('contact_email', ADMIN_EMAIL);

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FAQ - <?php echo SITE_NAME; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link href="<?php echo assetUrl('assets/css/style.css'); ?>" rel="stylesheet">

    <link rel="icon" href="<?php echo assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')); ?>">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

</head>

<body>

    <?php
$currentPage = 'faq';
include __DIR__ . '/partials/navbar.php';
?>
<main id="main-content">

    <section class="section bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="section-title text-gradient" data-aos="fade-up">
                    Preguntas Frecuentes
                </h1>
                <p class="lead" data-aos="fade-up" data-aos-delay="200">
                    Resolvemos las dudas mas comunes de nuestros clientes.
                </p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-9" data-aos="fade-up">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqOneBody">
                                    Como realizo una compra?
                                </button>
                            </h2>
                            <div id="faqOneBody" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Agrega productos al carrito, confirma tu informacion y finaliza el pedido.
                                    Recibiras un correo con el detalle.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwoBody">
                                    Que metodos de pago aceptan?
                                </button>
                            </h2>
                            <div id="faqTwoBody" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Puedes pagar con los metodos disponibles en el checkout. Si necesitas una opcion
                                    especial, contactanos.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqThreeBody">
                                    Cuanto demora el envio?
                                </button>
                            </h2>
                            <div id="faqThreeBody" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    El tiempo depende de tu ubicacion. Te compartimos un estimado al confirmar el pedido.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqFourBody">
                                    Como descargo mi producto digital?
                                </button>
                            </h2>
                            <div id="faqFourBody" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Una vez confirmado el pago, recibiras un enlace de descarga o instrucciones de acceso.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqFive">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqFiveBody">
                                    Puedo solicitar un cambio o devolucion?
                                </button>
                            </h2>
                            <div id="faqFiveBody" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Si. Escribe a nuestro soporte para revisar tu caso y validar la garantia.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqSix">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqSixBody">
                                    Como contacto al equipo?
                                </button>
                            </h2>
                            <div id="faqSixBody" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Puedes escribirnos a <?php echo htmlspecialchars($contactEmail); ?> o desde el formulario
                                    de contacto del sitio.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">
                            Si necesitas mas ayuda, escribe a <?php echo htmlspecialchars($contactEmail); ?>.
                        </p>
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
