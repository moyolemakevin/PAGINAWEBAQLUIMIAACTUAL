<?php

require_once __DIR__ . '/config/bootstrap.php';

$contactEmail = getSiteSetting('contact_email', ADMIN_EMAIL);
$contactPhone = getSiteSetting('contact_phone', '');
$whatsappNumber = getSiteSetting('whatsapp_number', $contactPhone);
$address = getSiteSetting('address', 'Ecuador');
$mapsEmbed = getSiteSetting(
    'google_maps_embed',
    'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.7964691869!2d-78.60053978945571!3d-1.2967735986854487!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91d3833075ab6891%3A0xc5bed5e18459cf30!2sALQUIMIA%20ESENCIAL!5e0!3m2!1ses!2snl!4v1752552300301!5m2!1ses!2snl'
);
$whatsappUrl = trim((string)getSiteSetting('whatsapp_url', ''));
$whatsappText = rawurlencode('Hola, necesito ayuda con ' . SITE_NAME);
$whatsappLink = '#';
if ($whatsappUrl !== '') {
    $whatsappLink = $whatsappUrl;
} elseif ($whatsappNumber !== '') {
    $digits = preg_replace('/\D+/', '', $whatsappNumber);
    if ($digits !== '') {
        $whatsappLink = 'https://wa.me/' . $digits . '?text=' . $whatsappText;
    }
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contacto - <?php echo SITE_NAME; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link href="<?php echo assetUrl('assets/css/style.css'); ?>" rel="stylesheet">

    <link rel="icon" href="<?php echo assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')); ?>">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

</head>

<body>

    <?php
$currentPage = 'contact';
include __DIR__ . '/partials/navbar.php';
?>
<main id="main-content">



    <!-- Contact Section -->

    <section class="section">

        <div class="container">

            <div class="row">

                <div class="col-12 text-center mb-5">

                    <h1 class="section-title text-gradient" data-aos="fade-up">

                        <i class="fas fa-envelope me-3"></i>

                        Contáctanos

                    </h1>

                    <p class="lead" data-aos="fade-up" data-aos-delay="200">

                        ¿Tienes preguntas, sugerencias o quieres hacer un pedido especial? ¡Estamos para ayudarte!

                    </p>

                </div>

            </div>



            <div class="row">

                <!-- Contact Information -->

                <div class="col-lg-4 mb-4" data-aos="fade-right">

                    <div class="card border-0 shadow-lg h-100">

                        <div class="card-body p-4">

                            <h4 class="fw-bold mb-4">

                                <i class="fas fa-info-circle text-primary me-2"></i>

                                Información de Contacto

                            </h4>

                            

                            <div class="contact-item mb-4">

                                <div class="d-flex align-items-center mb-3">

                                    <div class="contact-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">

                                        <i class="fas fa-map-marker-alt"></i>

                                    </div>

                                    <div>

                                        <h6 class="fw-bold mb-1">Ubicación</h6>

                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($address, ENT_QUOTES); ?></p>

                                    </div>

                                </div>

                            </div>

                            

                            <div class="contact-item mb-4">

                                <div class="d-flex align-items-center mb-3">

                                    <div class="contact-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">

                                        <i class="fab fa-whatsapp"></i>

                                    </div>

                                    <div>

                                        <h6 class="fw-bold mb-1">WhatsApp</h6>

                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($whatsappNumber !== '' ? $whatsappNumber : ($contactPhone !== '' ? $contactPhone : 'No disponible'), ENT_QUOTES); ?></p>

                                    </div>

                                </div>

                            </div>

                            

                            <div class="contact-item mb-4">

                                <div class="d-flex align-items-center mb-3">

                                    <div class="contact-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">

                                        <i class="fas fa-envelope"></i>

                                    </div>

                                    <div>

                                        <h6 class="fw-bold mb-1">Email</h6>

                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($contactEmail, ENT_QUOTES); ?></p>

                                    </div>

                                </div>

                            </div>

                            

                            <div class="contact-item">

                                <div class="d-flex align-items-center mb-3">

                                    <div class="contact-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">

                                        <i class="fas fa-clock"></i>

                                    </div>

                                    <div>

                                        <h6 class="fw-bold mb-1">Horarios</h6>

                                        <p class="text-muted mb-0">Lun - Vie: 9:00 - 18:00</p>

                                        <p class="text-muted mb-0">Sáb: 9:00 - 14:00</p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Contact Form -->

                <div class="col-lg-8" data-aos="fade-left">

                    <div class="card border-0 shadow-lg">

                        <div class="card-body p-4">

                            <h4 class="fw-bold mb-4">

                                <i class="fas fa-paper-plane text-primary me-2"></i>

                                Envíanos un Mensaje

                            </h4>

                            

                            <div id="contactAlert" class="alert d-none" role="alert"></div>

                            <form id="contactForm">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken(), ENT_QUOTES); ?>">
                                <input type="hidden" name="form_time" value="<?php echo time(); ?>">
                                <input type="text" name="website" value="" autocomplete="off" tabindex="-1" aria-hidden="true" style="display:none">

                                <div class="row">

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-bold">Nombre Completo</label>

                                        <input type="text" class="form-control" name="name" required>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-bold">Email</label>

                                        <input type="email" class="form-control" name="email" required>

                                    </div>

                                </div>

                                

                                <div class="row">

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-bold">Telefono</label>

                                        <input type="tel" class="form-control" name="phone" placeholder="Ej: +593 987654321" autocomplete="tel" inputmode="tel" maxlength="30">

                                    </div>

                                </div>

                                

                                <div class="mb-3">

                                    <label class="form-label fw-bold">Asunto</label>

                                    <input type="text" class="form-control" name="subject" required>

                                </div>

                                

                                <div class="mb-3">

                                    <label class="form-label fw-bold">Mensaje</label>

                                    <textarea class="form-control" name="message" rows="5" required placeholder="Cuéntanos cómo podemos ayudarte..."></textarea>

                                </div>

                                

                                <div class="d-grid">

                                    <button type="submit" class="btn btn-primary btn-lg hover-lift">

                                        <i class="fas fa-paper-plane me-2"></i>

                                        Enviar Mensaje

                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>



            <!-- Map Section -->

            <div class="row mt-5">

                <div class="col-12" data-aos="fade-up">

                    <div class="card border-0 shadow-lg">

                        <div class="card-body p-0">

                            <div class="map-container" style="height: 400px;">
                                <?php if (!empty($mapsEmbed)): ?>
                                    <iframe
                                        src="<?php echo htmlspecialchars($mapsEmbed, ENT_QUOTES); ?>"
                                        width="100%"
                                        height="400"
                                        style="border:0;"
                                        allowfullscreen=""
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade">
                                    </iframe>
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                        <p class="mb-0">Mapa no disponible</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!-- WhatsApp CTA -->

            <div class="row mt-5">

                <div class="col-12 text-center" data-aos="fade-up">

                    <div class="card border-0 shadow-lg bg-success text-white">

                        <div class="card-body p-5">

                            <h3 class="fw-bold mb-3">

                                <i class="fab fa-whatsapp me-3"></i>

                                ¿Necesitas Ayuda Inmediata?

                            </h3>

                            <p class="lead mb-4">

                                ¡Chatea con nosotros en WhatsApp y obtén respuestas instantáneas!

                            </p>

                            <a href="<?php echo htmlspecialchars($whatsappLink, ENT_QUOTES); ?>" 

                               class="btn btn-light btn-lg hover-lift" target="_blank">

                                <i class="fab fa-whatsapp me-2"></i>

                                Chatear por WhatsApp

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>



    </main>
<?php include __DIR__ . '/partials/footer.php'; ?>



    <!-- Scripts -->
    <?php include __DIR__ . '/partials/scripts.php'; ?>

    

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.AOS) {
            AOS.init();
        }

        // Contact form handling
        const contactForm = document.getElementById('contactForm');
        const alertBox = document.getElementById('contactAlert');
        const submitButton = contactForm ? contactForm.querySelector('button[type="submit"]') : null;

        function showContactAlert(message, type) {
            if (!alertBox) return;
            alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
            alertBox.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
            alertBox.textContent = message;
        }

        function setSubmitState(isSubmitting) {
            if (!submitButton) return;
            if (isSubmitting) {
                submitButton.dataset.originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviando...';
                submitButton.disabled = true;
            } else {
                submitButton.innerHTML = submitButton.dataset.originalText || submitButton.innerHTML;
                submitButton.disabled = false;
            }
        }

        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                setSubmitState(true);

                fetch('contact_submit.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showContactAlert(data.message || 'Mensaje enviado correctamente', 'success');
                        this.reset();
                    } else {
                        showContactAlert(data.message || 'No se pudo enviar el mensaje', 'error');
                    }
                })
                .catch(error => {
                    showContactAlert('Error al enviar el mensaje: ' + error.message, 'error');
                })
                .finally(() => {
                    setSubmitState(false);
                });
            });
        }
    });
</script>

</body>

</html> 

