<?php
require_once 'config/config.php';
require_once 'classes/User.php';
require_once 'classes/Order.php';
require_once 'classes/Product.php';

// Verificar si el usuario está logueado
if (!isLoggedIn()) {
    header('Location: auth/login.php');
    exit;
}

$user = new User();
$order = new Order();

// Obtener datos del usuario
$userData = $user->getUserById($_SESSION['user_id']);

// Obtener pedido específico o todos los pedidos
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : null;
if ($orderId) {
    $orderData = $order->getOrderById($orderId);
    if (!$orderData || $orderData['user_id'] != $_SESSION['user_id']) {
        header('Location: orders.php');
        exit;
    }
    $orderItems = $order->getOrderItems($orderId);
    foreach ($orderItems as &$item) {
        $item['image'] = Product::getImagePath([
            'images' => $item['product_images'] ?? '',
            'slug'   => $item['product_slug'] ?? ''
        ]);
    }
    unset($item);
} else {
    $userOrders = $order->getOrdersByUserId($_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $orderId ? 'Detalle del Pedido' : 'Mis Pedidos'; ?> - <?php echo SITE_NAME; ?></title>
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
                            <li><a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user me-2"></i>Mi Perfil
                            </a></li>
                            <li><a class="dropdown-item active" href="orders.php">
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

    <!-- Orders Section -->
    <section class="section" style="padding-top: 120px;">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="section-title text-gradient" data-aos="fade-right">
                            <i class="fas fa-shopping-bag me-3"></i>
                            <?php echo $orderId ? 'Detalle del Pedido' : 'Mis Pedidos'; ?>
                        </h1>
                        <?php if ($orderId): ?>
                            <a href="orders.php" class="btn btn-outline-primary hover-lift">
                                <i class="fas fa-arrow-left me-2"></i>
                                Volver a Pedidos
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($orderId && $orderData): ?>
                <!-- Order Detail -->
                <div class="row">
                    <div class="col-lg-8" data-aos="fade-right">
                        <!-- Order Items -->
                        <div class="card border-0 shadow-lg mb-4">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-boxes me-2"></i>
                                    Productos del Pedido
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($orderItems as $item): ?>
                                    <div class="row align-items-center mb-3 p-3 border rounded">
                                        <div class="col-md-2">
                                            <img src="<?php echo $item['image'] ?: 'assets/images/placeholder.jpg'; ?>"
                                                   alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                   class="img-fluid rounded">
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <small class="text-muted">Cantidad: <?php echo $item['quantity']; ?></small>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <span class="fw-bold">$<?php echo number_format($item['price'], 0, ',', '.'); ?></span>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <span class="fw-bold text-primary">$<?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Order Timeline -->
                        <div class="card border-0 shadow-lg">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-clock me-2"></i>
                                    Seguimiento del Pedido
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item completed">
                                        <div class="timeline-marker bg-success">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="fw-bold">Pedido Confirmado</h6>
                                            <p class="text-muted mb-0">Tu pedido ha sido recibido y confirmado</p>
                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($orderData['created_at'])); ?></small>
                                        </div>
                                    </div>
                                    <div class="timeline-item <?php echo in_array($orderData['status'], ['processing', 'shipped', 'delivered']) ? 'completed' : ''; ?>">
                                        <div class="timeline-marker <?php echo in_array($orderData['status'], ['processing', 'shipped', 'delivered']) ? 'bg-success' : 'bg-secondary'; ?>">
                                            <i class="fas fa-cog"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="fw-bold">En Procesamiento</h6>
                                            <p class="text-muted mb-0">Preparando tu pedido para el envío</p>
                                            <small class="text-muted"><?php echo in_array($orderData['status'], ['processing', 'shipped', 'delivered']) ? date('d/m/Y H:i', strtotime($orderData['updated_at'])) : 'Pendiente'; ?></small>
                                        </div>
                                    </div>
                                    <div class="timeline-item <?php echo in_array($orderData['status'], ['shipped', 'delivered']) ? 'completed' : ''; ?>">
                                        <div class="timeline-marker <?php echo in_array($orderData['status'], ['shipped', 'delivered']) ? 'bg-success' : 'bg-secondary'; ?>">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="fw-bold">Enviado</h6>
                                            <p class="text-muted mb-0">Tu pedido está en camino</p>
                                            <small class="text-muted"><?php echo in_array($orderData['status'], ['shipped', 'delivered']) ? date('d/m/Y H:i', strtotime($orderData['updated_at'])) : 'Pendiente'; ?></small>
                                        </div>
                                    </div>
                                    <div class="timeline-item <?php echo $orderData['status'] == 'delivered' ? 'completed' : ''; ?>">
                                        <div class="timeline-marker <?php echo $orderData['status'] == 'delivered' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <i class="fas fa-home"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="fw-bold">Entregado</h6>
                                            <p class="text-muted mb-0">Tu pedido ha sido entregado</p>
                                            <small class="text-muted"><?php echo $orderData['status'] == 'delivered' ? date('d/m/Y H:i', strtotime($orderData['updated_at'])) : 'Pendiente'; ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4" data-aos="fade-left">
                        <!-- Order Summary -->
                        <div class="card border-0 shadow-lg mb-4">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-receipt me-2"></i>
                                    Resumen del Pedido
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Pedido #<?php echo $orderData['id']; ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($orderData['created_at'])); ?></small>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span>$<?php echo number_format($orderData['subtotal'], 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Envío:</span>
                                        <span class="text-success">Gratis</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Impuestos:</span>
                                        <span>$<?php echo number_format($orderData['tax'], 0, ',', '.'); ?></span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total:</strong>
                                        <strong class="text-primary">$<?php echo number_format($orderData['total'], 0, ',', '.'); ?></strong>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold">Estado del Pedido</h6>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($orderData['status']) {
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
                                    <span class="badge <?php echo $statusClass; ?> fs-6"><?php echo $statusText; ?></span>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="https://wa.me/593983015307?text=Hola%2C%20tengo%20una%20consulta%20sobre%20mi%20pedido%20%23<?php echo $orderData['id']; ?>%20de%20AlquimiaTechnologic" 
                                       class="btn btn-success hover-lift" target="_blank">
                                        <i class="fab fa-whatsapp me-2"></i>
                                        Consultar por WhatsApp
                                    </a>
                                    <button class="btn btn-outline-primary hover-lift" onclick="downloadInvoice(<?php echo $orderData['id']; ?>)">
                                        <i class="fas fa-download me-2"></i>
                                        Descargar Factura
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Information -->
                        <div class="card border-0 shadow-lg">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Información de Envío
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2">
                                    <strong><?php echo htmlspecialchars($userData['name']); ?></strong>
                                </p>
                                <p class="text-muted mb-2">
                                    <?php echo htmlspecialchars($userData['address'] ?? 'Dirección no especificada'); ?>
                                </p>
                                <p class="text-muted mb-0">
                                    <?php echo htmlspecialchars($userData['phone'] ?? 'Teléfono no especificado'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- Orders List -->
                <div class="row">
                    <div class="col-12" data-aos="fade-up">
                        <?php if (empty($userOrders)): ?>
                            <div class="card border-0 shadow-lg">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-shopping-bag text-muted" style="font-size: 4rem;"></i>
                                    <h4 class="text-muted mt-3">No tienes pedidos aún</h4>
                                    <p class="text-muted mb-4">Realiza tu primera compra para ver tus pedidos aquí</p>
                                    <a href="products.php" class="btn btn-primary hover-lift">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        Explorar Productos
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card border-0 shadow-lg">
                                <div class="card-header bg-transparent border-0">
                                    <h5 class="fw-bold mb-0">
                                        <i class="fas fa-list me-2"></i>
                                        Historial de Pedidos
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Pedido #</th>
                                                    <th>Fecha</th>
                                                    <th>Productos</th>
                                                    <th>Total</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($userOrders as $order): ?>
                                                    <tr>
                                                        <td>
                                                            <strong>#<?php echo $order['id']; ?></strong>
                                                        </td>
                                                        <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                                        <td>
                                                            <?php 
                                                            $items = $order->getOrderItems($order['id']);
                                                            echo count($items) . ' producto' . (count($items) != 1 ? 's' : '');
                                                            ?>
                                                        </td>
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
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
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
        
        function downloadInvoice(orderId) {
            showNotification('Descarga de factura en desarrollo', 'info');
        }
    </script>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -35px;
            top: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: -20px;
            top: 30px;
            width: 2px;
            height: calc(100% + 30px);
            background-color: #e9ecef;
        }
        
        .timeline-item.completed:not(:last-child)::after {
            background-color: #28a745;
        }
        
        .timeline-content {
            padding-left: 20px;
        }
    </style>
</body>
</html> 