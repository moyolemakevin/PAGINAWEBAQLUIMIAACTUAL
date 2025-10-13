<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Order.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

$user = new User();
$product = new Product();
$category = new Category();
$order = new Order();

// Obtener estadísticas básicas
$totalUsers = count($user->getAllUsers());
$totalProducts = count($product->getAllProducts());
$totalCategories = count($category->getAllCategories());
$totalOrders = count($order->getAllOrders());

// Obtener productos recientes
$recentProducts = $product->getAllProducts(5);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin <?php echo SITE_NAME; ?></title>
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
                <li class="active">
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
                            <h1 class="h3 mb-0">Dashboard</h1>
                            <div class="text-muted">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <?php echo date('d/m/Y H:i'); ?>
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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Productos</div>
                                        <div class="h5 mb-0"><?php echo $totalProducts; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-box fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="products.php">Ver Detalles</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Categorías</div>
                                        <div class="h5 mb-0"><?php echo $totalCategories; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tags fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="categories.php">Ver Detalles</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Usuarios</div>
                                        <div class="h5 mb-0"><?php echo $totalUsers; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="users.php">Ver Detalles</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-danger text-white mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Pedidos</div>
                                        <div class="h5 mb-0"><?php echo $totalOrders; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-white-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="orders.php">Ver Detalles</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-xl-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-box me-1"></i>
                                Productos Recientes
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Categoría</th>
                                                <th>Precio</th>
                                                <th>Stock</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentProducts as $prod): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php 
                                                        $imageUrl = '../assets/images/placeholder.jpg';
                                                        if (!empty($prod['images'])) {
                                                            $images = json_decode($prod['images'], true);
                                                            if ($images && is_array($images) && !empty($images[0])) {
                                                                $imageUrl = $images[0];
                                                            }
                                                        }
                                                        ?>
                                                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                                                             alt="<?php echo $prod['name']; ?>" 
                                                             class="rounded me-2"
                                                             style="width: 40px; height: 40px; object-fit: cover;"
                                                             onerror="this.src='../assets/images/placeholder.jpg'">
                                                        <div>
                                                            <div class="fw-bold"><?php echo $prod['name']; ?></div>
                                                            <div class="text-muted small"><?php echo $prod['sku']; ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo $prod['category_name'] ?? 'Sin categoría'; ?></td>
                                                <td><?php echo formatPrice($prod['price']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $prod['stock_quantity'] > 0 ? 'success' : 'danger'; ?>">
                                                        <?php echo $prod['stock_quantity']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $prod['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst($prod['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                <a href="products.php?edit=<?php echo $prod['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-pie me-1"></i>
                                Acciones Rápidas
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="products.php?action=add" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Agregar Producto
                                    </a>
                                    <a href="categories.php?action=add" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Agregar Categoría
                                    </a>
                                    <a href="messages.php" class="btn btn-info">
                                        <i class="fas fa-envelope me-2"></i>Ver Mensajes
                                    </a>
                                    <a href="settings.php" class="btn btn-warning">
                                        <i class="fas fa-cog me-2"></i>Configuración
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-info-circle me-1"></i>
                                Información del Sistema
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Versión PHP</div>
                                        <div class="h6 mb-3"><?php echo phpversion(); ?></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Servidor</div>
                                        <div class="h6 mb-3"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Última Actualización</div>
                                        <div class="h6 mb-0"><?php echo date('d/m/Y H:i'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html> 