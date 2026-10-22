<?php

require_once __DIR__ . '/config/bootstrap.php';

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

    <link href="<?php echo assetUrl('assets/css/style.css'); ?>" rel="stylesheet">

    <link rel="icon" href="<?php echo assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')); ?>">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

</head>

<body>

    <?php
$currentPage = 'about';
include __DIR__ . '/partials/navbar.php';
?>
<main id="main-content">



    <!-- Hero Section -->

    <section class="hero-section hero-section--compact">

        <div class="particles" id="particles"></div>

        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-8 mx-auto text-center hero-content">

                    <h1 class="hero-title mb-4" data-aos="fade-up">
                        Sobre
                        <span class="hero-title-line">
                            <span class="text-warning">Moyoalquimia</span>
                        </span>
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

                        Fundada en 2024, Moyoalquimia nació de la visión de crear un puente 

                        entre la tecnología moderna y los productos naturales tradicionales.

                    </p>

                    <p class="mb-4">

                        Nos especializamos en ofrecer soluciones tecnológicas personalizadas, 

                        aceites esenciales premium, figuras artesanales únicas y servicios 

                        digitales orientados a productividad, bienestar y acompañamiento técnico.

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
                         alt="Nuestra Historia" class="img-fluid rounded-custom shadow-lg" loading="lazy">
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
                        Conoce a las personas que hacen posible la magia de Moyoalquimia
                    </p>
                </div>
            </div>
            <div class="row">
                <?php foreach ($teamMembers as $idx => $member): ?>
                    <?php if (isset($member['visible']) && !$member['visible']) continue; ?>
                    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo ($idx + 1) * 100; ?>">
                        <div class="card text-center border-0 shadow-lg">
                            <div class="card-body p-4">
                                <img src="<?php echo htmlspecialchars(getSafeImagePath($member['image'] ?? '', 'assets/images/placeholder.jpg')); ?>"
                                     alt="<?php echo htmlspecialchars($member['role']); ?>" class="rounded-circle mb-3"
                                     style="width: 120px; height: 120px; object-fit: cover;" loading="lazy">
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



    </main>
<?php include __DIR__ . '/partials/footer.php'; ?>



    <!-- Scripts -->
    <?php include __DIR__ . '/partials/scripts.php'; ?>

    

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const isMobile = window.matchMedia && window.matchMedia('(max-width: 575.98px)').matches;
        if (window.AOS && !isMobile) {
            AOS.init({ once: true });
        }
        // Particles effect
        function createParticles() {
            const skipParticles = (window.matchMedia && window.matchMedia('(max-width: 767px)').matches)
                || (navigator.connection && navigator.connection.saveData);
            if (skipParticles) return;
            const particles = document.getElementById('particles');
            if (!particles) {
                return;
            }
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
    });
</script>

</body>

</html> 
