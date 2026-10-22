<?php
require_once __DIR__ . '/../config/bootstrap.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

if (!function_exists('formatMonthLabel')) {
    function formatMonthLabel($yearMonth) {
        $parts = explode('-', (string)$yearMonth);
        if (count($parts) < 2) {
            return $yearMonth;
        }

        $monthIndex = (int)$parts[1];
        $monthNames = [
            '', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
            'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
        ];
        $monthName = $monthNames[$monthIndex] ?? $yearMonth;
        $yearShort = substr($parts[0], -2);
        return trim($monthName . ' ' . $yearShort);
    }
}

$totalUsers = 0;
$totalProducts = 0;
$totalCategories = 0;
$totalOrders = 0;

// Obtener estadísticas básicas
$recentProducts = [];

// Obtener productos recientes

$metricsMonths = 6;
$dashboardLoadWarning = '';
$monthlySales = ['months' => [], 'totals' => []];
$topProducts = [];
$visitSummary = ['total' => 0, 'today' => 0, 'month' => 0];
$monthlyVisits = ['months' => [], 'totals' => []];

try {
    $user = new User();
    $product = new Product();
    $category = new Category();
    $order = new Order();

    $totalUsers = $user->getUserCount();
    $totalProducts = $product->getProductCount();
    $totalCategories = $category->getCategoryCount();
    $totalOrders = $order->getOrderCount();
    $recentProducts = $product->getAllProducts(5);
} catch (Throwable $e) {
    $dashboardLoadWarning = 'El panel no pudo cargarse completamente. Revisa la conexion y la estructura de la base de datos en el hosting.';
    error_log('Admin dashboard bootstrap failed for ' . getNormalizedRequestHost() . ': ' . $e->getMessage());
}

if ($dashboardLoadWarning === '') {
    try {
        $monthlySales = $order->getMonthlySales($metricsMonths);
        $topProducts = $order->getTopProducts(5);

        $visitTracker = new VisitTracker();
        $visitSummary = $visitTracker->getVisitSummary();
        $monthlyVisits = $visitTracker->getMonthlyVisits($metricsMonths);
    } catch (Throwable $e) {
        $dashboardLoadWarning = 'No se pudieron cargar todas las estadisticas del panel.';
        error_log('Admin dashboard analytics failed for ' . getNormalizedRequestHost() . ': ' . $e->getMessage());
    }
}

$salesLabels = array_map('formatMonthLabel', $monthlySales['months'] ?? []);
$salesData = $monthlySales['totals'] ?? [];

$visitLabels = array_map('formatMonthLabel', $monthlyVisits['months'] ?? []);
$visitData = $monthlyVisits['totals'] ?? [];

$topProductLabels = [];
$topProductData = [];
foreach ($topProducts as $row) {
    $label = $row['product_name'] ?? '';
    $label = $label !== '' ? $label : ('Producto #' . (int)($row['product_id'] ?? 0));
    $topProductLabels[] = $label;
    $topProductData[] = (int)($row['total_quantity'] ?? 0);
}
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
?>

<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navigation -->
            <?php include __DIR__ . '/partials/topbar.php'; ?>

            <!-- Main Content -->
            <div class="container-fluid">
                <?php if ($dashboardLoadWarning !== ''): ?>
                    <div class="alert alert-warning" role="alert">
                        <?php echo htmlspecialchars($dashboardLoadWarning); ?>
                    </div>
                <?php endif; ?>

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
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Categorias</div>
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

                <!-- Metrics Panel -->
                <div class="row mb-4">
                    <div class="col-xl-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <i class="fas fa-chart-line me-1"></i>
                                Ventas por Mes
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <i class="fas fa-chart-bar me-1"></i>
                                Visitas por Mes
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-4 mb-3">
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Total</div>
                                        <div class="h5 mb-0"><?php echo (int)($visitSummary['total'] ?? 0); ?></div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Hoy</div>
                                        <div class="h5 mb-0"><?php echo (int)($visitSummary['today'] ?? 0); ?></div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Este Mes</div>
                                        <div class="h5 mb-0"><?php echo (int)($visitSummary['month'] ?? 0); ?></div>
                                    </div>
                                </div>
                                <div class="chart-container">
                                    <canvas id="visitsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-star me-1"></i>
                                Top Productos
                            </div>
                            <div class="card-body">
                                <?php if (empty($topProducts)): ?>
                                    <div class="text-muted">No hay ventas registradas.</div>
                                <?php else: ?>
                                    <div class="row align-items-center">
                                        <div class="col-lg-7">
                                            <div class="chart-container">
                                                <canvas id="productChart"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="table-responsive">
                                                <table class="table table-sm align-middle mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Producto</th>
                                                            <th class="text-end">Unidades</th>
                                                            <th class="text-end">Ingresos</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($topProducts as $row): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($row['product_name'] ?? 'Producto #' . (int)($row['product_id'] ?? 0)); ?></td>
                                                                <td class="text-end"><?php echo (int)($row['total_quantity'] ?? 0); ?></td>
                                                                <td class="text-end"><?php echo formatPrice((float)($row['total_revenue'] ?? 0)); ?></td>
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
                                                            $imagePath = Product::getImagePath($prod);
                                                            $isAbsolute = strpos($imagePath, 'http') === 0 || strpos($imagePath, 'data:') === 0 || strpos($imagePath, '//') === 0;
                                                            $placeholder = assetUrl('assets/images/placeholder.jpg');
                                                            $imageSrc = $isAbsolute ? $imagePath : assetUrl($imagePath);
                                                        ?>
                                                        <img src="<?php echo htmlspecialchars($imageSrc); ?>"
                                                             alt="<?php echo htmlspecialchars($prod['name'] ?? ''); ?>" 
                                                             class="rounded me-2"
                                                             style="width: 40px; height: 40px; object-fit: cover;"
                                                             onerror="this.src='<?php echo $placeholder; ?>'">
                                                        <div>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($prod['name'] ?? ''); ?></div>
                                                            <div class="text-muted small"><?php echo htmlspecialchars($prod['sku'] ?? ''); ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($prod['category_name'] ?? 'Sin categoria'); ?></td>
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
                                Acciones Rapidas
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="products.php?action=add" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Agregar Producto
                                    </a>
                                    <a href="categories.php?action=add" class="btn btn-success">
                                        <i class="fas fa-plus me-2"></i>Agregar Categoria
                                    </a>
                                    <a href="messages.php" class="btn btn-info">
                                        <i class="fas fa-envelope me-2"></i>Ver Mensajes
                                    </a>
                                    <a href="settings.php" class="btn btn-warning">
                                        <i class="fas fa-cog me-2"></i>Configuracion
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-info-circle me-1"></i>
                                Informacion del Sistema
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Version PHP</div>
                                        <div class="h6 mb-3"><?php echo phpversion(); ?></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Servidor</div>
                                        <div class="h6 mb-3"><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'); ?></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">Ultima Actualizacion</div>
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

    <script>
        window.dashboardMetrics = {
            sales: {
                labels: <?php echo json_encode($salesLabels); ?>,
                data: <?php echo json_encode($salesData); ?>
            },
            visits: {
                labels: <?php echo json_encode($visitLabels); ?>,
                data: <?php echo json_encode($visitData); ?>
            },
            topProducts: {
                labels: <?php echo json_encode($topProductLabels); ?>,
                data: <?php echo json_encode($topProductData); ?>
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html> 
