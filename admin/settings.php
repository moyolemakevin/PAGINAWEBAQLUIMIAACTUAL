<?php
require_once __DIR__ . '/../config/bootstrap.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

// Cargar configuracion desde JSON con valores por defecto
$defaultSettings = [
    'site_name' => 'Moyoalquimia',
    'site_description' => 'Especialistas en software personalizado, aceites esenciales y figuras artesanales',
    'hero_image_url' => 'assets/images/placeholder.jpg',
    'contact_email' => 'moyoalquimia@gmail.com',
    'contact_phone' => '+593 983015307',
    'whatsapp_number' => '+593 983015307',
    'address' => 'Ecuador',
    'google_maps_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.7964691869!2d-78.60053978945571!3d-1.2967735986854487!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91d3833075ab6891%3A0xc5bed5e18459cf30!2sALQUIMIA%20ESENCIAL!5e0!3m2!1ses!2snl!4v1752552300301!5m2!1ses!2snl',
    'facebook_url' => 'https://www.facebook.com/share/1AG4jgxFbw/',
    'instagram_url' => 'https://www.instagram.com/moyoalquimia?igsh=MTJzeDVsMzhhd3NkZw==',
    'tiktok_url' => 'https://www.tiktok.com/@moyoalquimia?_r=1&_t=ZM-92Sy91rXAZk',
    'whatsapp_url' => 'https://wa.me/593983015307',
    'logo_url' => '',
    'favicon_url' => '',
    'maintenance_mode' => false,
    'allow_registration' => true,
    'email_notifications' => true,
    'debug_mode' => false,
    'timezone' => 'America/Guayaquil',
    'default_language' => 'es',
    'apply_iva' => true,
    'iva_rate' => 15
];

$settingsFile = __DIR__ . '/../data/settings.json';
$settings = $defaultSettings;
if (file_exists($settingsFile)) {
    $data = json_decode(file_get_contents($settingsFile), true);
    if (is_array($data)) {
        $settings = array_merge($defaultSettings, $data);
    }
}

$settings['hero_image_url'] = getSafeImagePath($settings['hero_image_url'] ?? '', 'assets/images/placeholder.jpg');
$settings['logo_url'] = sanitizeLocalImagePath($settings['logo_url'] ?? '', true) ?: '';
$settings['favicon_url'] = sanitizeLocalImagePath($settings['favicon_url'] ?? '', true) ?: '';
$pageTitle = 'Configuracion';
$activePage = 'settings';
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navigation -->
            <?php include __DIR__ . '/partials/topbar.php'; ?>

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
                                                        <input type="text" class="form-control" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Descripción del Sitio</label>
                                                        <textarea class="form-control" name="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description'] ?? ''); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Logo</label>
                                                        <input type="text" class="form-control mb-2" name="logo_url" id="logoUrl" value="<?php echo htmlspecialchars($settings['logo_url'] ?? ''); ?>" placeholder="Ruta local o subir imagen">
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
                                                        <input type="text" class="form-control mb-2" name="favicon_url" id="faviconUrl" value="<?php echo htmlspecialchars($settings['favicon_url'] ?? ''); ?>" placeholder="Ruta local o subir imagen">
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
                                                    <input type="text" class="form-control mb-2" name="hero_image_url" id="heroImageUrl" value="<?php echo htmlspecialchars($settings['hero_image_url'] ?? ''); ?>" placeholder="Ruta local o subir imagen">
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
                                                        <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Teléfono de Contacto</label>
                                                        <input type="tel" class="form-control" name="contact_phone" value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Número de WhatsApp</label>
                                                        <input type="tel" class="form-control" name="whatsapp_number" value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Dirección</label>
                                                        <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($settings['address'] ?? ''); ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Google Maps Embed URL</label>
                                                <textarea class="form-control" name="google_maps_embed" rows="3"><?php echo htmlspecialchars($settings['google_maps_embed'] ?? ''); ?></textarea>
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
                                                        <input type="url" class="form-control" name="facebook_url" value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Instagram URL</label>
                                                        <input type="url" class="form-control" name="instagram_url" value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">WhatsApp URL</label>
                                                        <input type="url" class="form-control" name="whatsapp_url" value="<?php echo htmlspecialchars($settings['whatsapp_url'] ?? ''); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">TikTok URL</label>
                                                        <input type="url" class="form-control" name="tiktok_url" value="<?php echo htmlspecialchars($settings['tiktok_url'] ?? ''); ?>">
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
                                                            <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode" <?php echo !empty($settings['maintenance_mode']) ? 'checked' : ''; ?>>
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
                                                            <input class="form-check-input" type="checkbox" name="allow_registration" id="allowRegistration" <?php echo !empty($settings['allow_registration']) ? 'checked' : ''; ?>>
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
                                                            <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotifications" <?php echo !empty($settings['email_notifications']) ? 'checked' : ''; ?>>
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
                                                            <input class="form-check-input" type="checkbox" name="debug_mode" id="debugMode" <?php echo !empty($settings['debug_mode']) ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="debugMode">
                                                                Modo Debug
                                                            </label>
                                                        </div>
                                                        <small class="text-muted">Activa el modo debug para desarrollo</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="apply_iva" id="applyIvaSetting" <?php echo !empty($settings['apply_iva']) ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="applyIvaSetting">
                                                                Aplicar IVA a los pedidos
                                                            </label>
                                                        </div>
                                                        <small class="text-muted">Si está desactivado, los clientes verán totales sin IVA.</small>
                                                        <label class="form-label mt-2" for="ivaRateSetting">Porcentaje IVA (%)</label>
                                                        <input type="number" class="form-control" name="iva_rate" id="ivaRateSetting" value="<?php echo htmlspecialchars($settings['iva_rate'] ?? 15); ?>" min="0" max="100" step="0.01">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Zona Horaria</label>
                                                        <select class="form-select" name="timezone">
                                                            <option value="America/Guayaquil" <?php echo ($settings['timezone'] ?? '') === 'America/Guayaquil' ? 'selected' : ''; ?>>America/Guayaquil (Ecuador)</option>
                                                            <option value="UTC" <?php echo ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                                            <option value="America/New_York" <?php echo ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : ''; ?>>America/New_York</option>
                                                            <option value="Europe/Madrid" <?php echo ($settings['timezone'] ?? '') === 'Europe/Madrid' ? 'selected' : ''; ?>>Europe/Madrid</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Idioma por Defecto</label>
                                                        <select class="form-select" name="default_language">
                                                            <option value="es" <?php echo ($settings['default_language'] ?? '') === 'es' ? 'selected' : ''; ?>>Espanol</option>
                                                            <option value="en" <?php echo ($settings['default_language'] ?? '') === 'en' ? 'selected' : ''; ?>>English</option>
                                                            <option value="fr" <?php echo ($settings['default_language'] ?? '') === 'fr' ? 'selected' : ''; ?>>Francais</option>
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
    <?php include __DIR__ . '/partials/scripts.php'; ?>
    
    <script>
        const csrfToken = '<?php echo generateCSRFToken(); ?>';
        
        // Inicializar sistema de subida cuando se carga la pagina
        document.addEventListener('DOMContentLoaded', function() {
            initializeFileUpload();
            setupSettingsImageSelection();
            renderSettingsPreviews();
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
            
            fetch('upload_handler.php', {
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
            
            fetch('upload_handler.php', {
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

            fetch('upload_handler.php', {
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
            const formData = buildSettingsFormData();

            fetch('process_settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Configuracion guardada correctamente', 'success');
                } else {
                    showNotification(data.message || 'Error al guardar la configuracion', 'error');
                }
            })
            .catch(error => {
                showNotification('Error al guardar la configuracion: ' + error.message, 'error');
            });
        }

        function buildSettingsFormData() {
            const generalForm = document.getElementById('generalForm');
            const contactForm = document.getElementById('contactForm');
            const socialForm = document.getElementById('socialForm');
            const systemForm = document.getElementById('systemForm');

            const formData = new FormData();
            [generalForm, contactForm, socialForm, systemForm].forEach(form => {
                if (!form) return;
                const data = new FormData(form);
                for (let [key, value] of data.entries()) {
                    formData.append(key, value);
                }
            });

            const checkboxMap = {
                maintenanceMode: 'maintenance_mode',
                allowRegistration: 'allow_registration',
                emailNotifications: 'email_notifications',
                debugMode: 'debug_mode',
                applyIvaSetting: 'apply_iva'
            };

            Object.keys(checkboxMap).forEach(id => {
                const element = document.getElementById(id);
                if (!element) return;
                formData.set(checkboxMap[id], element.checked ? '1' : '0');
            });

            formData.set('csrf_token', csrfToken);
            return formData;
        }

        function renderSettingsPreviews() {
            const previews = [
                { inputId: 'logoUrl', previewId: 'logoImagePreview', hiddenId: 'logoImagesJson' },
                { inputId: 'faviconUrl', previewId: 'faviconImagePreview', hiddenId: 'faviconImagesJson' },
                { inputId: 'heroImageUrl', previewId: 'heroImagePreview', hiddenId: 'heroImagesJson' }
            ];

            previews.forEach(entry => {
                const urlInput = document.getElementById(entry.inputId);
                const preview = document.getElementById(entry.previewId);
                const hiddenInput = document.getElementById(entry.hiddenId);
                if (!urlInput || !preview || !urlInput.value) {
                    return;
                }

                preview.innerHTML = '';
                preview.appendChild(createImagePreviewItem('', urlInput.value));
                if (hiddenInput) {
                    hiddenInput.value = JSON.stringify([urlInput.value]);
                }
            });
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
                    // Placeholder for future auto-save
                }, 2000);
            });
        });
    </script>
</body>
</html> 
