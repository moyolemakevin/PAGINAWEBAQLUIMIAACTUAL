<?php
require_once 'config/config.php';
require_once 'classes/User.php';
require_once 'classes/Order.php';

// Verificar si el usuario está logueado
if (!isLoggedIn()) {
    header('Location: auth/login.php');
    exit;
}

$user = new User();
$order = new Order();

// Obtener datos del usuario
$userData = $user->getUserById($_SESSION['user_id']);
$userOrders = $order->getOrdersByUserId($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - <?php echo SITE_NAME; ?></title>
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu shadow-lg border-0">
                            <li><a class="dropdown-item active" href="profile.php">
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
                </ul>
            </div>
        </div>
    </nav>

    <!-- Profile Section -->
    <section class="section" style="padding-top: 120px;">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-4">
                    <h1 class="section-title text-gradient" data-aos="fade-up">
                        <i class="fas fa-user-circle me-3"></i>
                        Mi Perfil
                    </h1>
                </div>
            </div>

            <div class="row">
                <!-- Profile Sidebar -->
                <div class="col-lg-3 mb-4" data-aos="fade-right">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <img src="assets/images/placeholder.jpg"
                                     alt="Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <h5 class="fw-bold"><?php echo htmlspecialchars($userData['name']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($userData['email']); ?></p>
                            <div class="d-grid">
                                <button class="btn btn-outline-primary btn-sm" onclick="editProfile()">
                                    <i class="fas fa-edit me-2"></i>Editar Perfil
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card border-0 shadow-lg mt-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Estadísticas</h6>
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-primary fw-bold"><?php echo count($userOrders); ?></h4>
                                    <small class="text-muted">Pedidos</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success fw-bold">5</h4>
                                    <small class="text-muted">Favoritos</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="col-lg-9" data-aos="fade-left">
                    <!-- Personal Information -->
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-user me-2"></i>
                                Información Personal
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nombre Completo</label>
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($userData['name']); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($userData['email']); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Teléfono</label>
                                    <p class="form-control-plaintext"><?php echo $userData['phone'] ? htmlspecialchars($userData['phone']) : 'No especificado'; ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Fecha de Registro</label>
                                    <p class="form-control-plaintext"><?php echo date('d/m/Y', strtotime($userData['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-shopping-bag me-2"></i>
                                Pedidos Recientes
                            </h5>
                            <a href="orders.php" class="btn btn-outline-primary btn-sm">
                                Ver Todos
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($userOrders)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-bag text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="text-muted mt-3">No tienes pedidos aún</h6>
                                    <p class="text-muted">Realiza tu primera compra para ver tus pedidos aquí</p>
                                    <a href="products.php" class="btn btn-primary">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        Explorar Productos
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Pedido #</th>
                                                <th>Fecha</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($userOrders, 0, 5) as $order): ?>
                                                <tr>
                                                    <td>
                                                        <strong>#<?php echo $order['id']; ?></strong>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                                    <td>$<?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                                                    <td>
                                                        <?php
                                                        $statusClass = '';
                                                        $statusText = '';
                                                        switch ($order['status']) {
                                                            case 'pending':
                                                                $statusClass = 'bg-warning';
                                                                $statusText = 'Pendiente';
                                                                break;
                                                            case 'processing':
                                                                $statusClass = 'bg-info';
                                                                $statusText = 'Procesando';
                                                                break;
                                                            case 'shipped':
                                                                $statusClass = 'bg-primary';
                                                                $statusText = 'Enviado';
                                                                break;
                                                            case 'delivered':
                                                                $statusClass = 'bg-success';
                                                                $statusText = 'Entregado';
                                                                break;
                                                            case 'cancelled':
                                                                $statusClass = 'bg-danger';
                                                                $statusText = 'Cancelado';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                                    </td>
                                                    <td>
                                                        <a href="orders.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Account Settings -->
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-cog me-2"></i>
                                Configuración de Cuenta
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <button class="btn btn-outline-primary w-100" onclick="changePassword()">
                                        <i class="fas fa-key me-2"></i>
                                        Cambiar Contraseña
                                    </button>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <button class="btn btn-outline-info w-100" onclick="updateProfile()">
                                        <i class="fas fa-user-edit me-2"></i>
                                        Actualizar Información
                                    </button>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <button class="btn btn-outline-warning w-100" onclick="notificationSettings()">
                                        <i class="fas fa-bell me-2"></i>
                                        Configurar Notificaciones
                                    </button>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <button class="btn btn-outline-danger w-100" onclick="deleteAccount()">
                                        <i class="fas fa-trash me-2"></i>
                                        Eliminar Cuenta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($userData['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveProfile()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <div class="mb-3">
                            <label class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="savePassword()">Cambiar Contraseña</button>
                </div>
            </div>
        </div>
    </div>

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
                        <li><i class="fas fa-map-marker-alt me-2"></i>Anbato, Ecuador</li>
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
        
        // Profile functions
        function editProfile() {
            const modal = new bootstrap.Modal(document.getElementById('editProfileModal'));
            modal.show();
        }
        
        function changePassword() {
            const modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
            modal.show();
        }
        
        function saveProfile() {
            // Simulate saving profile
            showNotification('Perfil actualizado correctamente', 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
            modal.hide();
        }
        
        function savePassword() {
            const form = document.getElementById('changePasswordForm');
            const formData = new FormData(form);
            
            if (formData.get('new_password') !== formData.get('confirm_password')) {
                showNotification('Las contraseñas no coinciden', 'error');
                return;
            }
            
            // Simulate password change
            showNotification('Contraseña cambiada correctamente', 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
            modal.hide();
            form.reset();
        }
        
        function updateProfile() {
            editProfile();
        }
        
        function notificationSettings() {
            showNotification('Configuración de notificaciones en desarrollo', 'info');
        }
        
        function deleteAccount() {
            if (confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer.')) {
                showNotification('Función de eliminación de cuenta en desarrollo', 'info');
            }
        }
    </script>
</body>
</html> 