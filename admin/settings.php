<?php
require_once __DIR__ . '/../config/config.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Simular configuración actual
$settings = [
    'site_name' => 'AlquimiaTechnologic',
    'site_description' => 'Especialistas en software personalizado, aceites esenciales y figuras artesanales',
    'hero_image_url' => 'assets/images/placeholder.jpg',
    'contact_email' => 'kevinmoyolema13@gmail.com',
    'contact_phone' => '+593 983015307',
    'whatsapp_number' => '+593 983015307',
    'address' => 'Ecuador',
    'google_maps_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.7964691869!2d-78.60053978945571!3d-1.2967735986854487!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91d3833075ab6891%3A0xc5bed5e18459cf30!2sALQUIMIA%20ESENCIAL!5e0!3m2!1ses!2snl!4v1752552300301!5m2!1ses!2snl',
    'facebook_url' => 'https://facebook.com/alquimiatechnologic',
    'instagram_url' => 'https://instagram.com/alquimiatechnologic',
    'whatsapp_url' => 'https://wa.me/593983015307',
    'logo_url' => '',
    'favicon_url' => '',
    'maintenance_mode' => false,
    'allow_registration' => true,
    'email_notifications' => true
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - Admin <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3>
                    <i class="fas fa-flask"></i>
                    Admin Panel
                </h3>
            </div>
            
            <ul class="list-unstyled components">
                <li>
                    <a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="products.php">
                        <i class="fas fa-box"></i>
                        Productos
                    </a>
                </li>
                <li>
                    <a href="categories.php">
                        <i class="fas fa-tags"></i>
                        Categorías
                    </a>
                </li>
                <li>
                    <a href="orders.php">
                        <i class="fas fa-shopping-cart"></i>
                        Pedidos
                    </a>
                </li>
                <li>
                    <a href="users.php">
                        <i class="fas fa-users"></i>
                        Usuarios
                    </a>
                </li>
                <li>
                    <a href="messages.php">
                        <i class="fas fa-envelope"></i>
                        Mensajes
                    </a>
                </li>
                <li>
                    <a href="team.php">
                        <i class="fas fa-users-cog"></i>
                        Equipo
                    </a>
                </li>
                <li class="active">
                    <a href="settings.php">
                        <i class="fas fa-cog"></i>
                        Configuración
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <a href="../index.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-eye me-2"></i>Ver Sitio
                </a>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i>Salir
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                <?php echo $_SESSION['user_name']; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">Mi Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../auth/logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="h3 mb-0">Configuración del Sistema</h1>
                            <button class="btn btn-primary" onclick="saveSettings()">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Settings Tabs -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                            <i class="fas fa-cog me-2"></i>General
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                                            <i class="fas fa-address-book me-2"></i>Contacto
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">
                                            <i class="fas fa-share-alt me-2"></i>Redes Sociales
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
                                            <i class="fas fa-server me-2"></i>Sistema
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="settingsTabContent">
                                    <!-- General Settings -->
                                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                                        <form id="generalForm">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nombre del Sitio</label>
                                                        <input type="text" class="form-control" name="site_name" value="<?php echo $settings['site_name']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Descripción del Sitio</label>
                                                        <textarea class="form-control" name="site_description" rows="3"><?php echo $settings['site_description']; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Logo</label>
                                                        <input type="text" class="form-control mb-2" name="logo_url" id="logoUrl" value="<?php echo $settings['logo_url']; ?>" placeholder="URL o subir imagen">
                                                        <div class="upload-section">
                                                            <!-- Botón para seleccionar logo -->
                                                            <div class="mb-2">
                                                                <input type="file" id="logoFileUpload" name="logo_file" accept="image/*" class="form-control" style="display: none;">
                                                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('logoFileUpload').click()">
                                                                    <i class="fas fa-folder-open me-2"></i>Seleccionar Logo
                                                                </button>
                                                            </div>
                                                            
                                                            <!-- Preview de logo seleccionado -->
                                                            <div id="logoSelectedImagePreview" class="mb-3" style="display: none;">
                                                                <h6>Logo Seleccionado:</h6>
                                                                <div class="selected-images-container"></div>
                                                                <button type="button" class="btn btn-success mt-2" onclick="uploadLogo()">
                                                                    <i class="fas fa-cloud-upload-alt me-2"></i>Subir Logo
                                                                </button>
                                                            </div>
                                                            
                                                            <!-- Lista de logos subidos -->
                                                            <div id="logoImagePreview" class="image-preview mt-2"></div>
                                                        </div>
                                                        <input type="hidden" id="logoImagesJson">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Favicon</label>
                                                        <input type="text" class="form-control mb-2" name="favicon_url" id="faviconUrl" value="<?php echo $settings['favicon_url']; ?>" placeholder="URL o subir imagen">
                                                        <div class="upload-section">
                                                            <!-- Botón para seleccionar favicon -->
                                                            <div class="mb-2">
                                                                <input type="file" id="faviconFileUpload" name="favicon_file" accept="image/*" class="form-control" style="display: none;">
                                                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('faviconFileUpload').click()">
                                                                    <i class="fas fa-folder-open me-2"></i>Seleccionar Favicon
                                                                </button>
                                                            </div>
                                                            
                                                            <!-- Preview de favicon seleccionado -->
                                                            <div id="faviconSelectedImagePreview" class="mb-3" style="display: none;">
                                                                <h6>Favicon Seleccionado:</h6>
                                                                <div class="selected-images-container"></div>
                                                                <button type="button" class="btn btn-success mt-2" onclick="uploadFavicon()">
                                                                    <i class="fas fa-cloud-upload-alt me-2"></i>Subir Favicon
                                                                </button>
                                                            </div>
                                                            
                                                            <!-- Lista de favicons subidos -->
                                                            <div id="faviconImagePreview" class="image-preview mt-2"></div>
                                                        </div>
                                                    <input type="hidden" id="faviconImagesJson">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Imagen del Hero</label>
                                                    <input type="text" class="form-control mb-2" name="hero_image_url" id="heroImageUrl" value="<?php echo $settings['hero_image_url']; ?>" placeholder="URL o subir imagen">
                                                    <div class="upload-section">
                                                        <div class="mb-2">
                                                            <input type="file" id="heroFileUpload" name="hero_file" accept="image/*" class="form-control" style="display: none;">
                                                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('heroFileUpload').click()">
                                                                <i class="fas fa-folder-open me-2"></i>Seleccionar Imagen
                                                            </button>
                                                        </div>
                                                        <div id="heroSelectedImagePreview" class="mb-3" style="display: none;">
                                                            <h6>Imagen Seleccionada:</h6>
                                                            <div class="selected-images-container"></div>
                                                            <button type="button" class="btn btn-success mt-2" onclick="uploadHeroImage()">
                                                                <i class="fas fa-cloud-upload-alt me-2"></i>Subir Imagen
                                                            </button>
                                                        </div>
                                                        <div id="heroImagePreview" class="image-preview mt-2"></div>
                                                    </div>
                                                    <input type="hidden" id="heroImagesJson">
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>

                                    <!-- Contact Settings -->
                                    <div class="tab-pane fade" id="contact" role="tabpanel">
                                        <form id="contactForm">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Email de Contacto</label>
                                                        <input type="email" class="form-control" name="contact_email" value="<?php echo $settings['contact_email']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Teléfono de Contacto</label>
                                                        <input type="tel" class="form-control" name="contact_phone" value="<?php echo $settings['contact_phone']; ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Número de WhatsApp</label>
                                                        <input type="tel" class="form-control" name="whatsapp_number" value="<?php echo $settings['whatsapp_number']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Dirección</label>
                                                        <input type="text" class="form-control" name="address" value="<?php echo $settings['address']; ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Google Maps Embed URL</label>
                                                <textarea class="form-control" name="google_maps_embed" rows="3"><?php echo $settings['google_maps_embed']; ?></textarea>
                                                <small class="text-muted">URL completa del iframe de Google Maps</small>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Social Media Settings -->
                                    <div class="tab-pane fade" id="social" role="tabpanel">
                                        <form id="socialForm">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Facebook URL</label>
                                                        <input type="url" class="form-control" name="facebook_url" value="<?php echo $settings['facebook_url']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Instagram URL</label>
                                                        <input type="url" class="form-control" name="instagram_url" value="<?php echo $settings['instagram_url']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">WhatsApp URL</label>
                                                        <input type="url" class="form-control" name="whatsapp_url" value="<?php echo $settings['whatsapp_url']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">YouTube URL</label>
                                                        <input type="url" class="form-control" name="youtube_url" value="">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Twitter URL</label>
                                                        <input type="url" class="form-control" name="twitter_url" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">LinkedIn URL</label>
                                                        <input type="url" class="form-control" name="linkedin_url" value="">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- System Settings -->
                                    <div class="tab-pane fade" id="system" role="tabpanel">
                                        <form id="systemForm">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode" <?php echo $settings['maintenance_mode'] ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="maintenanceMode">
                                                                Modo Mantenimiento
                                                            </label>
                                                        </div>
                                                        <small class="text-muted">Activa el modo mantenimiento para realizar actualizaciones</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="allow_registration" id="allowRegistration" <?php echo $settings['allow_registration'] ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="allowRegistration">
                                                                Permitir Registro de Usuarios
                                                            </label>
                                                        </div>
                                                        <small class="text-muted">Permite que nuevos usuarios se registren</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotifications" <?php echo $settings['email_notifications'] ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="emailNotifications">
                                                                Notificaciones por Email
                                                            </label>
                                                        </div>
                                                        <small class="text-muted">Enviar notificaciones por email</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="debug_mode" id="debugMode">
                                                            <label class="form-check-label" for="debugMode">
                                                                Modo Debug
                                                            </label>
                                                        </div>
                                                        <small class="text-muted">Activa el modo debug para desarrollo</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Zona Horaria</label>
                                                        <select class="form-select" name="timezone">
                                                            <option value="America/Guayaquil" selected>America/Guayaquil (Ecuador)</option>
                                                            <option value="UTC">UTC</option>
                                                            <option value="America/New_York">America/New_York</option>
                                                            <option value="Europe/Madrid">Europe/Madrid</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Idioma por Defecto</label>
                                                        <select class="form-select" name="default_language">
                                                            <option value="es" selected>Español</option>
                                                            <option value="en">English</option>
                                                            <option value="fr">Français</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Backup Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-database me-2"></i>
                                    Respaldo y Mantenimiento
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="d-grid">
                                            <button class="btn btn-outline-primary" onclick="createBackup()">
                                                <i class="fas fa-download me-2"></i>Crear Respaldo
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-grid">
                                            <button class="btn btn-outline-success" onclick="clearCache()">
                                                <i class="fas fa-broom me-2"></i>Limpiar Caché
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-grid">
                                            <button class="btn btn-outline-warning" onclick="optimizeDatabase()">
                                                <i class="fas fa-tools me-2"></i>Optimizar Base de Datos
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    
    <script>
        const csrfToken = '<?php echo generateCSRFToken(); ?>';
        
        // Inicializar sistema de subida cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Inicializando sistema de subida para configuraciones...');
            initializeFileUpload();
            setupSettingsImageSelection();
        });
        
        // Configurar selección de imágenes para configuraciones
        function setupSettingsImageSelection() {
            const logoFileUpload = document.getElementById('logoFileUpload');
            const faviconFileUpload = document.getElementById('faviconFileUpload');
            const heroFileUpload = document.getElementById('heroFileUpload');
            
            if (logoFileUpload) {
                logoFileUpload.addEventListener('change', function(e) {
                    handleSettingsImageSelection(e.target.files, 'logoSelectedImagePreview');
                });
            }
            
            if (faviconFileUpload) {
                faviconFileUpload.addEventListener('change', function(e) {
                    handleSettingsImageSelection(e.target.files, 'faviconSelectedImagePreview');
                });
            }

            if (heroFileUpload) {
                heroFileUpload.addEventListener('change', function(e) {
                    handleSettingsImageSelection(e.target.files, 'heroSelectedImagePreview');
                });
            }
        }
        
        // Manejar selección de imagen para configuraciones
        function handleSettingsImageSelection(files, previewId) {
            const preview = document.getElementById(previewId);
            const container = preview.querySelector('.selected-images-container');
            
            if (files.length === 0) {
                preview.style.display = 'none';
                return;
            }
            
            preview.style.display = 'block';
            container.innerHTML = '';
            
            const file = files[0]; // Solo una imagen
            const reader = new FileReader();
            reader.onload = function(e) {
                const item = document.createElement('div');
                item.className = 'selected-image-item';
                item.style.cssText = 'display: inline-block; margin: 5px; text-align: center;';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;';
                img.alt = file.name;
                
                const name = document.createElement('div');
                name.textContent = file.name.length > 20 ? file.name.substring(0, 20) + '...' : file.name;
                name.style.cssText = 'font-size: 12px; margin-top: 5px; color: #666;';
                
                item.appendChild(img);
                item.appendChild(name);
                container.appendChild(item);
            };
            reader.readAsDataURL(file);
        }
        
        // Subir logo
        function uploadLogo() {
            const logoFileUpload = document.getElementById('logoFileUpload');
            
            if (!logoFileUpload || !logoFileUpload.files.length) {
                alert('Por favor selecciona un logo');
                return;
            }
            
            const formData = new FormData();
            formData.append('images[]', logoFileUpload.files[0]);
            formData.append('csrf_token', csrfToken);
            formData.append('folder', 'settings');
            
            const preview = document.getElementById('logoImagePreview');
            
            // Mostrar progreso
            const progressBar = document.createElement('div');
            progressBar.className = 'upload-progress';
            progressBar.innerHTML = '<div class="upload-progress-bar" style="width: 0%"></div>';
            preview.appendChild(progressBar);
            
            fetch('../admin/upload_handler_simple.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                progressBar.remove();
                
                if (data.success) {
                    preview.innerHTML = '';
                    data.files.forEach(file => {
                        const item = createImagePreviewItem(file.thumbnail, file.original);
                        preview.appendChild(item);
                    });
                    
                    // Actualizar campo de URL
                    document.getElementById('logoUrl').value = data.files[0].original;
                    updateImagesJson();
                    
                    // Limpiar selección
                    logoFileUpload.value = '';
                    document.getElementById('logoSelectedImagePreview').style.display = 'none';
                    
                    showNotification('Logo subido correctamente', 'success');
                } else {
                    const msg = data.errors && data.errors.length ? data.errors.join('; ') : data.message;
                    showNotification('Error al subir logo: ' + msg, 'error');
                }
            })
            .catch(error => {
                progressBar.remove();
                showNotification('Error al subir logo: ' + error.message, 'error');
                console.error('Upload error:', error);
            });
        }
        
        // Subir favicon
        function uploadFavicon() {
            const faviconFileUpload = document.getElementById('faviconFileUpload');
            
            if (!faviconFileUpload || !faviconFileUpload.files.length) {
                alert('Por favor selecciona un favicon');
                return;
            }
            
            const formData = new FormData();
            formData.append('images[]', faviconFileUpload.files[0]);
            formData.append('csrf_token', csrfToken);
            formData.append('folder', 'settings');
            
            const preview = document.getElementById('faviconImagePreview');
            
            // Mostrar progreso
            const progressBar = document.createElement('div');
            progressBar.className = 'upload-progress';
            progressBar.innerHTML = '<div class="upload-progress-bar" style="width: 0%"></div>';
            preview.appendChild(progressBar);
            
            fetch('../admin/upload_handler_simple.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                progressBar.remove();
                
                if (data.success) {
                    preview.innerHTML = '';
                    data.files.forEach(file => {
                        const item = createImagePreviewItem(file.thumbnail, file.original);
                        preview.appendChild(item);
                    });
                    
                    // Actualizar campo de URL
                    document.getElementById('faviconUrl').value = data.files[0].original;
                    updateImagesJson();
                    
                    // Limpiar selección
                    faviconFileUpload.value = '';
                    document.getElementById('faviconSelectedImagePreview').style.display = 'none';
                    
                    showNotification('Favicon subido correctamente', 'success');
                } else {
                    const msg = data.errors && data.errors.length ? data.errors.join('; ') : data.message;
                    showNotification('Error al subir favicon: ' + msg, 'error');
                }
            })
            .catch(error => {
                progressBar.remove();
                showNotification('Error al subir favicon: ' + error.message, 'error');
            console.error('Upload error:', error);
        });
        }

        // Subir imagen del hero
        function uploadHeroImage() {
            const heroFileUpload = document.getElementById('heroFileUpload');

            if (!heroFileUpload || !heroFileUpload.files.length) {
                alert('Por favor selecciona una imagen');
                return;
            }

            const formData = new FormData();
            formData.append('images[]', heroFileUpload.files[0]);
            formData.append('csrf_token', csrfToken);
            formData.append('folder', 'settings');

            const preview = document.getElementById('heroImagePreview');

            const progressBar = document.createElement('div');
            progressBar.className = 'upload-progress';
            progressBar.innerHTML = '<div class="upload-progress-bar" style="width: 0%"></div>';
            preview.appendChild(progressBar);

            fetch('../admin/upload_handler_simple.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                progressBar.remove();

                if (data.success) {
                    preview.innerHTML = '';
                    data.files.forEach(file => {
                        const item = createImagePreviewItem(file.thumbnail, file.original);
                        preview.appendChild(item);
                    });

                    document.getElementById('heroImageUrl').value = data.files[0].original;
                    updateImagesJson();

                    heroFileUpload.value = '';
                    document.getElementById('heroSelectedImagePreview').style.display = 'none';

                    showNotification('Imagen subida correctamente', 'success');
                } else {
                    const msg = data.errors && data.errors.length ? data.errors.join('; ') : data.message;
                    showNotification('Error al subir imagen: ' + msg, 'error');
                }
            })
            .catch(error => {
                progressBar.remove();
                showNotification('Error al subir imagen: ' + error.message, 'error');
                console.error('Upload error:', error);
            });
        }
        
        // Settings management functions
        function saveSettings() {
            // Collect all form data
            const generalForm = document.getElementById('generalForm');
            const contactForm = document.getElementById('contactForm');
            const socialForm = document.getElementById('socialForm');
            const systemForm = document.getElementById('systemForm');
            
            const formData = new FormData();
            
            // Add form data from all tabs
            [generalForm, contactForm, socialForm, systemForm].forEach(form => {
                const data = new FormData(form);
                for (let [key, value] of data.entries()) {
                    formData.append(key, value);
                }
            });
            
            // Simulate saving settings
            alert('Configuración guardada correctamente');
            
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                Configuración guardada correctamente
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.row'));
            
            // Remove alert after 3 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
        
        function createBackup() {
            // Simulate creating backup
            alert('Respaldo creado correctamente');
        }
        
        function clearCache() {
            // Simulate clearing cache
            alert('Caché limpiado correctamente');
        }
        
        function optimizeDatabase() {
            // Simulate database optimization
            alert('Base de datos optimizada correctamente');
        }
        
        // Auto-save functionality
        let autoSaveTimer;
        document.querySelectorAll('input, textarea, select').forEach(element => {
            element.addEventListener('change', () => {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    console.log('Auto-saving settings...');
                }, 2000);
            });
        });
    </script>
</body>
</html> 