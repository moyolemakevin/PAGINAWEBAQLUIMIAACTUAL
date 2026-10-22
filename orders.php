<?php
require_once __DIR__ . '/config/bootstrap.php';

// Verificar si el usuario está logueado
if (!isLoggedIn()) {
    header('Location: auth/login.php');
    exit;
}

$user = new User();
$orderModel = new Order();
$applyIva = getSiteSetting('apply_iva', true);
$ivaRate = (float)getSiteSetting('iva_rate', 15);

// Obtener datos del usuario
$userData = $user->getUserById($_SESSION['user_id']);

// Obtener pedido específico o todos los pedidos
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : null;
if ($orderId) {
    $orderData = $orderModel->getOrderById($orderId);
    if (!$orderData || $orderData['user_id'] != $_SESSION['user_id']) {
        header('Location: orders.php');
        exit;
    }
    $orderItems = $orderModel->getOrderItems($orderId);
    foreach ($orderItems as &$item) {
        $item['image'] = Product::getImagePath([
            'images' => $item['product_images'] ?? '',
            'slug'   => $item['product_slug'] ?? ''
        ]);
    }
    unset($item);

    // Calcular totales seguros
    $computedSubtotal = 0;
    foreach ($orderItems as $it) {
        $qty = (int)($it['quantity'] ?? 0);
        $price = (float)($it['price'] ?? 0);
        $computedSubtotal += $qty * $price;
    }
    $orderData['subtotal'] = $orderData['subtotal'] ?? $computedSubtotal;
    $orderData['tax_amount'] = $orderData['tax_amount'] ?? ($orderData['tax'] ?? 0);
    $orderData['shipping_amount'] = $orderData['shipping_amount'] ?? 0;
    $orderData['discount_amount'] = $orderData['discount_amount'] ?? 0;
    $orderData['total'] = $orderData['total'] ?? $orderData['total_amount'] ?? ($orderData['subtotal'] + $orderData['tax_amount'] + $orderData['shipping_amount'] - $orderData['discount_amount']);
} else {
    $userOrders = $orderModel->getOrdersByUserId($_SESSION['user_id']);
    $orderIds = array_map(function ($order) { return $order['id']; }, $userOrders);
    $userOrderItemCounts = $orderModel->getOrderItemCounts($orderIds);
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
    <link href="<?php echo assetUrl('assets/css/style.css'); ?>" rel="stylesheet">
    <link rel="icon" href="<?php echo assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')); ?>">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>
    <?php
$currentPage = 'orders';
include __DIR__ . '/partials/navbar.php';
?>
<main id="main-content">

    <!-- Orders Section -->
    <section class="section">
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
                                                   alt="<?php echo htmlspecialchars($item['product_name'] ?? 'Producto'); ?>"
                                                   class="img-fluid rounded">
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($item['product_name'] ?? 'Producto'); ?></h6>
                                            <small class="text-muted">Cantidad: <?php echo $item['quantity']; ?></small>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <span class="fw-bold">$<?php echo number_format($item['price'], 2, ',', '.'); ?></span>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <span class="fw-bold text-primary">$<?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></span>
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
                                <?php $timelineDate = $orderData['updated_at'] ?? $orderData['created_at']; ?>
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
                                            <small class="text-muted"><?php echo in_array($orderData['status'], ['processing', 'shipped', 'delivered']) ? date('d/m/Y H:i', strtotime($timelineDate)) : 'Pendiente'; ?></small>
                                        </div>
                                    </div>
                                    <div class="timeline-item <?php echo in_array($orderData['status'], ['shipped', 'delivered']) ? 'completed' : ''; ?>">
                                        <div class="timeline-marker <?php echo in_array($orderData['status'], ['shipped', 'delivered']) ? 'bg-success' : 'bg-secondary'; ?>">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="fw-bold">Enviado</h6>
                                            <p class="text-muted mb-0">Tu pedido está en camino</p>
                                            <small class="text-muted"><?php echo in_array($orderData['status'], ['shipped', 'delivered']) ? date('d/m/Y H:i', strtotime($timelineDate)) : 'Pendiente'; ?></small>
                                        </div>
                                    </div>
                                    <div class="timeline-item <?php echo $orderData['status'] == 'delivered' ? 'completed' : ''; ?>">
                                        <div class="timeline-marker <?php echo $orderData['status'] == 'delivered' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <i class="fas fa-home"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="fw-bold">Entregado</h6>
                                            <p class="text-muted mb-0">Tu pedido ha sido entregado</p>
                                            <small class="text-muted"><?php echo $orderData['status'] == 'delivered' ? date('d/m/Y H:i', strtotime($timelineDate)) : 'Pendiente'; ?></small>
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
                                        <span>$<?php echo number_format($orderData['subtotal'], 2, ',', '.'); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Envío:</span>
                                        <span>$<?php echo number_format($orderData['shipping_amount'], 2, ',', '.'); ?></span>
                                    </div>
                                    <?php if ($applyIva): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>IVA (<?php echo $ivaRate; ?>%):</span>
                                        <span>$<?php echo number_format($orderData['tax_amount'], 2, ',', '.'); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Descuento:</span>
                                        <span>$<?php echo number_format($orderData['discount_amount'], 2, ',', '.'); ?></span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total:</strong>
                                        <strong class="text-primary">$<?php echo number_format($orderData['total'], 2, ',', '.'); ?></strong>
                                    </div>
                                    <?php if (!$applyIva): ?>
                                        <div class="small text-muted mt-1">IVA no aplicado por configuración.</div>
                                    <?php endif; ?>
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
                                    <a href="https://wa.me/593983015307?text=Hola%2C%20tengo%20una%20consulta%20sobre%20mi%20pedido%20%23<?php echo $orderData['id']; ?>%20de%20Moyoalquimia"
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
                                <?php
                                    $shippingBlock = $orderData['shipping_address'] ?? '';
                                    $defaultName = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));
                                    $defaultAddress = $userData['address'] ?? 'Dirección no especificada';
                                    $defaultPhone = $userData['phone'] ?? 'Teléfono no especificado';
                                ?>
                                <p class="mb-2">
                                    <strong><?php echo $shippingBlock ? nl2br(htmlspecialchars($shippingBlock)) : htmlspecialchars($defaultName); ?></strong>
                                </p>
                                <?php if (!$shippingBlock): ?>
                                    <p class="text-muted mb-2">
                                        <?php echo htmlspecialchars($defaultAddress); ?>
                                    </p>
                                    <p class="text-muted mb-0">
                                        <?php echo htmlspecialchars($defaultPhone); ?>
                                    </p>
                                <?php endif; ?>
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
                                                <?php foreach ($userOrders as $orderRow): ?>
                                                    <tr>
                                                        <td>
                                                            <strong>#<?php echo $orderRow['id']; ?></strong>
                                                        </td>
                                                        <td><?php echo date('d/m/Y', strtotime($orderRow['created_at'])); ?></td>
                                                        <td>
                                                            <?php
                                                            $itemCount = $userOrderItemCounts[$orderRow['id']] ?? 0;
                                                            echo $itemCount . ' producto' . ($itemCount != 1 ? 's' : '');
                                                            ?>
                                                        </td>
                                                        <td>$<?php echo number_format($orderRow['total'], 2, ',', '.'); ?></td>
                                                        <td>
                                                            <?php
                                                            $statusClass = '';
                                                            $statusText = '';
                                                            switch ($orderRow['status']) {
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
                                                            <a href="orders.php?id=<?php echo $orderRow['id']; ?>" class="btn btn-outline-primary btn-sm">
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

        function downloadInvoice(orderId) {
            const targetId = orderId || <?php echo $orderId ? $orderId : 'null'; ?>;
            if (!targetId) {
                showNotification('Selecciona un pedido para descargar la factura', 'warning');
                return;
            }
            window.open('invoice.php?id=' + targetId + '&print=1', '_blank');
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
