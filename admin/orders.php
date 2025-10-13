<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Order.php';
require_once __DIR__ . '/../classes/User.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

$orderModel = new Order();
$user = new User();

// Obtener pedidos
$orders = $orderModel->getAllOrders();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Pedidos - Admin <?php echo SITE_NAME; ?></title>
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
                <li class="active">
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
                <li>
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
                            <h1 class="h3 mb-0">Gestionar Pedidos</h1>
                            <div>
                                <button class="btn btn-outline-success me-2" onclick="exportOrders()">
                                    <i class="fas fa-download me-2"></i>Exportar
                                </button>
                                <button class="btn btn-outline-primary" onclick="printOrders()">
                                    <i class="fas fa-print me-2"></i>Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total Pedidos</div>
                                        <div class="h5 mb-0"><?php echo count($orders); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Entregados</div>
                                        <div class="h5 mb-0">15</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Pendientes</div>
                                        <div class="h5 mb-0">8</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-info text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">En Proceso</div>
                                        <div class="h5 mb-0">12</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-cog fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Lista de Pedidos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Pedido #</th>
                                        <th>Cliente</th>
                                        <th>Productos</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?php echo $order['id']; ?></strong>
                                            </td>
                                            <td>
                                                <?php 
                                                $userData = $user->getUserById($order['user_id']);
                                                $userName = $userData ? ($userData['first_name'] . ' ' . $userData['last_name']) : 'Usuario no encontrado';
                                                echo htmlspecialchars($userName);
                                                ?>
                                                <br>
                                                <small class="text-muted"><?php echo $userData ? htmlspecialchars($userData['email']) : ''; ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $items = $orderModel->getOrderItems($order['id']);
                                                $itemCount = is_array($items) ? count($items) : 0;
                                                echo $itemCount . ' producto' . ($itemCount != 1 ? 's' : '');
                                                ?>
                                            </td>
                                            <td>
                                                <strong>$<?php echo number_format($order['total'], 0, ',', '.'); ?></strong>
                                            </td>
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
                                                <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="viewOrder(<?php echo $order['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success btn-sm" onclick="updateStatus(<?php echo $order['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-info btn-sm" onclick="printInvoice(<?php echo $order['id']; ?>)">
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Order Modal -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetails">
                    <!-- Order details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="printInvoice()">
                        <i class="fas fa-print me-2"></i>Imprimir Factura
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Actualizar Estado del Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStatusForm">
                        <input type="hidden" id="orderId" name="order_id">
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="status" id="orderStatus">
                                <option value="pending">Pendiente</option>
                                <option value="processing">Procesando</option>
                                <option value="shipped">Enviado</option>
                                <option value="delivered">Entregado</option>
                                <option value="cancelled">Cancelado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Comentarios</label>
                            <textarea class="form-control" name="comments" rows="3" placeholder="Agregar comentarios sobre el pedido..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveStatus()">Actualizar Estado</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    
    <script>
        // Order management functions
        function viewOrder(orderId) {
            const modal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
            document.getElementById('orderDetails').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información del Pedido</h6>
                        <p><strong>Pedido #:</strong> ${orderId}</p>
                        <p><strong>Fecha:</strong> ${new Date().toLocaleDateString()}</p>
                        <p><strong>Estado:</strong> <span class="badge bg-warning">Pendiente</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Información del Cliente</h6>
                        <p><strong>Nombre:</strong> Cliente Ejemplo</p>
                        <p><strong>Email:</strong> cliente@ejemplo.com</p>
                        <p><strong>Teléfono:</strong> +593 983015307</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6>Productos</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Producto Ejemplo</td>
                                    <td>2</td>
                                    <td>$99.000</td>
                                    <td>$198.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td>$198.000</td>
                            </tr>
                            <tr>
                                <td><strong>Envío:</strong></td>
                                <td>Gratis</td>
                            </tr>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td><strong>$198.000</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            `;
            modal.show();
        }
        
        function updateStatus(orderId) {
            document.getElementById('orderId').value = orderId;
            const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
            modal.show();
        }
        
        function saveStatus() {
            // Simulate updating status
            alert('Estado del pedido actualizado correctamente');
            const modal = bootstrap.Modal.getInstance(document.getElementById('updateStatusModal'));
            modal.hide();
            location.reload();
        }
        
        function printInvoice(orderId) {
            // Simulate printing invoice
            alert('Imprimiendo factura...');
        }
        
        function exportOrders() {
            // Simulate exporting orders
            alert('Exportando pedidos...');
        }
        
        function printOrders() {
            // Simulate printing orders
            window.print();
        }
    </script>
</body>
</html> 