<?php

require_once 'config/config.php';

$teamFile = __DIR__ . '/data/team.json';
$teamMembers = [];
if (file_exists($teamFile)) {
    $json = file_get_contents($teamFile);
    $data = json_decode($json, true);
    if (is_array($data)) {
        $teamMembers = $data;
    }
}
?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nosotros - <?php echo SITE_NAME; ?></title>

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

                        <a class="nav-link active" href="about.php">

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



    <!-- Hero Section -->

    <section class="hero-section" style="min-height: 60vh; padding: 120px 0 80px;">

        <div class="particles" id="particles"></div>

        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-8 mx-auto text-center hero-content">

                    <h1 class="hero-title mb-4" data-aos="fade-up">

                        Sobre <span class="text-warning">AlquimiaTechnologic</span>

                    </h1>

                    <p class="hero-subtitle mb-4" data-aos="fade-up" data-aos-delay="200">

                        🌟 Somos una empresa innovadora que combina la tecnología más avanzada 

                        con productos naturales de la más alta calidad para transformar tu vida.

                    </p>

                </div>

            </div>

        </div>

    </section>



    <!-- About Section -->

    <section class="section">

        <div class="container">

            <div class="row align-items-center mb-5">

                <div class="col-lg-6" data-aos="fade-right">

                    <h2 class="section-title text-gradient mb-4">Nuestra Historia</h2>

                    <p class="lead mb-4">

                        Fundada en 2024, AlquimiaTechnologic nació de la visión de crear un puente 

                        entre la tecnología moderna y los productos naturales tradicionales.

                    </p>

                    <p class="mb-4">

                        Nos especializamos en ofrecer soluciones tecnológicas personalizadas, 

                        aceites esenciales premium, figuras artesanales únicas y suscripciones 

                        a las mejores plataformas digitales.

                    </p>

                    <div class="row">

                        <div class="col-6">

                            <div class="text-center">

                                <h3 class="text-primary fw-bold">500+</h3>

                                <p class="text-muted">Productos</p>

                            </div>

                        </div>

                        <div class="col-6">

                            <div class="text-center">

                                <h3 class="text-success fw-bold">1000+</h3>

                                <p class="text-muted">Clientes Satisfechos</p>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-lg-6" data-aos="fade-left">
                    <img src="https://images.unsplash.com/photo-1559136555-9303baea8ebd?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         alt="Nuestra Historia" class="img-fluid rounded-custom shadow-lg">
                </div>
            </div>

        </div>

    </section>



    <!-- Mission & Vision -->

    <section class="section bg-light">

        <div class="container">

            <div class="row">

                <div class="col-lg-6 mb-4" data-aos="fade-up">

                    <div class="card h-100 border-0 shadow-lg">

                        <div class="card-body text-center p-5">

                            <div class="mb-4">

                                <i class="fas fa-bullseye text-primary" style="font-size: 3rem;"></i>

                            </div>

                            <h3 class="card-title mb-3">Nuestra Misión</h3>

                            <p class="card-text">

                                Transformar la vida de nuestros clientes a través de productos 

                                y servicios innovadores que combinen la mejor tecnología con 

                                elementos naturales de alta calidad.

                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="200">

                    <div class="card h-100 border-0 shadow-lg">

                        <div class="card-body text-center p-5">

                            <div class="mb-4">

                                <i class="fas fa-eye text-success" style="font-size: 3rem;"></i>

                            </div>

                            <h3 class="card-title mb-3">Nuestra Visión</h3>

                            <p class="card-text">

                                Ser la empresa líder en la transformación digital y el bienestar 

                                natural, creando soluciones únicas que mejoren la calidad de vida 

                                de nuestros clientes.

                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- Values Section -->

    <section class="section">

        <div class="container">

            <div class="row">

                <div class="col-12 text-center mb-5">

                    <h2 class="section-title text-gradient" data-aos="fade-up">Nuestros Valores</h2>

                </div>

            </div>

            <div class="row">

                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">

                    <div class="text-center">

                        <div class="mb-3">

                            <i class="fas fa-heart text-danger" style="font-size: 2.5rem;"></i>

                        </div>

                        <h5 class="fw-bold">Calidad</h5>

                        <p class="text-muted">Ofrecemos solo productos de la más alta calidad</p>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">

                    <div class="text-center">

                        <div class="mb-3">

                            <i class="fas fa-handshake text-primary" style="font-size: 2.5rem;"></i>

                        </div>

                        <h5 class="fw-bold">Confianza</h5>

                        <p class="text-muted">Construimos relaciones duraderas con nuestros clientes</p>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">

                    <div class="text-center">

                        <div class="mb-3">

                            <i class="fas fa-lightbulb text-warning" style="font-size: 2.5rem;"></i>

                        </div>

                        <h5 class="fw-bold">Innovación</h5>

                        <p class="text-muted">Siempre buscamos nuevas formas de mejorar</p>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">

                    <div class="text-center">

                        <div class="mb-3">

                            <i class="fas fa-leaf text-success" style="font-size: 2.5rem;"></i>

                        </div>

                        <h5 class="fw-bold">Sostenibilidad</h5>

                        <p class="text-muted">Cuidamos el medio ambiente en todo lo que hacemos</p>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- Team Section -->

    <section class="section bg-light">

        <div class="container">

            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title text-gradient" data-aos="fade-up">Nuestro Equipo</h2>
                    <p class="lead" data-aos="fade-up" data-aos-delay="200">
                        Conoce a las personas que hacen posible la magia de AlquimiaTechnologic
                    </p>
                </div>
            </div>
            <div class="row">
                <?php foreach ($teamMembers as $idx => $member): ?>
                    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo ($idx + 1) * 100; ?>">
                        <div class="card text-center border-0 shadow-lg">
                            <div class="card-body p-4">
                                <img src="<?php echo htmlspecialchars($member['image']); ?>"
                                     alt="<?php echo htmlspecialchars($member['role']); ?>" class="rounded-circle mb-3"
                                     style="width: 120px; height: 120px; object-fit: cover;">
                                <h5 class="card-title"><?php echo htmlspecialchars($member['name']); ?></h5>
                                <p class="text-muted"><?php echo htmlspecialchars($member['role']); ?></p>
                                <p class="card-text"><?php echo htmlspecialchars($member['description']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- CTA Section -->

    <section class="section bg-primary text-white">

        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-8 mx-auto text-center">

                    <h2 class="fw-bold mb-3" data-aos="fade-up">

                        ¿Listo para Transformar tu Vida?

                    </h2>

                    <p class="mb-4" data-aos="fade-up" data-aos-delay="200">

                        Descubre nuestros productos únicos y servicios innovadores 

                        que cambiarán tu perspectiva del mundo.

                    </p>

                    <div class="d-flex flex-wrap justify-content-center gap-3" data-aos="fade-up" data-aos-delay="400">

                        <a href="products.php" class="btn btn-warning btn-lg hover-lift">

                            <i class="fas fa-shopping-bag me-2"></i>

                            Explorar Productos

                        </a>

                        <a href="contact.php" class="btn btn-outline-light btn-lg hover-lift">

                            <i class="fas fa-envelope me-2"></i>

                            Contáctanos

                        </a>

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

        

        // Particles effect

        function createParticles() {

            const particles = document.getElementById('particles');

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



        createParticles();

    </script>

</body>

</html> 