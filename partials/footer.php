<?php
$siteName = getSiteSetting('site_name', SITE_NAME);
$siteDescription = getSiteSetting(
    'site_description',
    'Transformando tu dia a dia con productos unicos y servicios innovadores que elevan tu estilo de vida.'
);
$contactEmail = getSiteSetting('contact_email', ADMIN_EMAIL);
$contactPhone = getSiteSetting('contact_phone', '');
$address = getSiteSetting('address', 'Ecuador');
$facebookUrl = getSiteSetting('facebook_url', '');
$instagramUrl = getSiteSetting('instagram_url', '');
$tiktokUrl = getSiteSetting('tiktok_url', '');
$whatsappUrl = getSiteSetting('whatsapp_url', '');
?>

<footer class="bg-dark text-light">
  <div class="container">
    <div class="row py-5">
      <div class="col-lg-4 mb-4">
        <h5 class="fw-bold mb-3">
          <i class="fas fa-flask me-2"></i>
          <?php echo htmlspecialchars($siteName); ?>
        </h5>
        <p class="text-muted">
          <?php echo htmlspecialchars($siteDescription); ?>
        </p>
        <div class="social-links">
          <?php if (!empty($facebookUrl)): ?>
            <a href="<?php echo htmlspecialchars($facebookUrl, ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
          <?php endif; ?>
          <?php if (!empty($instagramUrl)): ?>
            <a href="<?php echo htmlspecialchars($instagramUrl, ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a>
          <?php endif; ?>
          <?php if (!empty($tiktokUrl)): ?>
            <a href="<?php echo htmlspecialchars($tiktokUrl, ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-tiktok"></i></a>
          <?php endif; ?>
          <?php if (!empty($whatsappUrl)): ?>
            <a href="<?php echo htmlspecialchars($whatsappUrl, ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i></a>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 mb-4">
        <h6 class="fw-bold mb-3">Productos</h6>
        <ul class="list-unstyled">
          <li><a href="category.php?slug=software-personalizado" class="text-muted">Software</a></li>
          <li><a href="category.php?slug=aceites-esenciales" class="text-muted">Aceites Esenciales</a></li>
          <li><a href="category.php?slug=figuras-yeso" class="text-muted">Maceteros de Yeso</a></li>
          <li><a href="category.php?slug=suscripciones-premium" class="text-muted">Suscripciones Premium</a></li>
        </ul>
      </div>
      <div class="col-lg-2 col-md-6 mb-4">
        <h6 class="fw-bold mb-3">Empresa</h6>
        <ul class="list-unstyled">
          <li><a href="about.php" class="text-muted">Sobre Nosotros</a></li>
          <li><a href="contact.php" class="text-muted">Contacto</a></li>
        </ul>
      </div>
      <div class="col-lg-2 col-md-6 mb-4">
        <h6 class="fw-bold mb-3">Soporte</h6>
        <ul class="list-unstyled">
          <li><a href="help.php" class="text-muted">Centro de Ayuda</a></li>
          <li><a href="privacy.php" class="text-muted">Politica de Privacidad</a></li>
          <li><a href="terms.php" class="text-muted">Terminos</a></li>
          <li><a href="faq.php" class="text-muted">FAQ</a></li>
        </ul>
      </div>
      <div class="col-lg-2 col-md-6 mb-4">
        <h6 class="fw-bold mb-3">Contacto</h6>
        <ul class="list-unstyled text-muted">
          <li><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($address); ?></li>
          <li><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($contactPhone !== '' ? $contactPhone : 'No disponible'); ?></li>
          <li><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($contactEmail); ?></li>
        </ul>
      </div>
    </div>
    <hr class="my-4">
    <div class="row justify-content-center">
      <div class="col-12 text-center">
        <p class="text-center text-muted mb-0">
          Moyoalquimia
        </p>
      </div>
    </div>
  </div>
</footer>

<a href="<?php echo htmlspecialchars($whatsappUrl ?: 'https://wa.me/593983015307', ENT_QUOTES, 'UTF-8'); ?>"
   class="whatsapp-float"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="Hablar por WhatsApp">
  <i class="fab fa-whatsapp"></i>
</a>
