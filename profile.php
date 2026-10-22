<?php
require_once __DIR__ . '/config/bootstrap.php';

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
    <link href="<?php echo assetUrl('assets/css/style.css'); ?>" rel="stylesheet">
    <link rel="icon" href="<?php echo assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')); ?>">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>
    <?php
$currentPage = 'profile';
include __DIR__ . '/partials/navbar.php';
?>
<main id="main-content">

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
                            <h5 class="fw-bold"><?php echo htmlspecialchars(trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''))); ?></h5>
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
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars(trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''))); ?></p>
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
                                                    <td>$<?php echo number_format($order['total'], 2, ',', '.'); ?></td>
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
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars(trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''))); ?>" required>
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

    </main>
<?php include __DIR__ . '/partials/footer.php'; ?>

    <!-- Scripts -->
    <?php include __DIR__ . '/partials/scripts.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.AOS) {
                AOS.init();
            }
        });
        
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
