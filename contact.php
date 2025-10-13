<?php

require_once 'config/config.php';

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contacto - <?php echo SITE_NAME; ?></title>

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

                        <a class="nav-link active" href="contact.php">

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



    <!-- Contact Section -->

    <section class="section" style="padding-top: 120px;">

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

                                        <p class="text-muted mb-0">Ambato, Ecuador</p>

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

                                        <p class="text-muted mb-0">+593 983015307</p>

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

                                        <p class="text-muted mb-0">info@alquimiatech.com</p>

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

                            

                            <form id="contactForm">

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

                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.7964691869!2d-78.60053978945571!3d-1.2967735986854487!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91d3833075ab6891%3A0xc5bed5e18459cf30!2sALQUIMIA%20ESENCIAL!5e0!3m2!1ses!2snl!4v1752552300301!5m2!1ses!2snl"
                                    width="100%"
                                    height="400"
                                    style="border:0;"
                                    allowfullscreen=""
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
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

                            <a href="https://wa.me/593983015307?text=Hola%2C%20necesito%20ayuda%20con%20AlquimiaTechnologic" 

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

                        <li><i class="fas fa-map-marker-alt me-2"></i>Ambato, Ecuador</li>

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

        

        // Contact form handling

        document.getElementById('contactForm').addEventListener('submit', function(e) {

            e.preventDefault();

            

            // Simulate form submission

            const formData = new FormData(this);

            const data = {};

            formData.forEach((value, key) => {

                data[key] = value;

            });

            

            // Show success message

            alert('¡Mensaje enviado correctamente! Te responderemos pronto.');

            this.reset();

        });

    </script>

</body>

</html> 