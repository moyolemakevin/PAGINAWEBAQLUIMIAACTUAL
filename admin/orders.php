<?php
require_once __DIR__ . '/../config/bootstrap.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

$orderModel = new Order();
$userModel = new User();
$productModel = new Product();

// Obtener pedidos
$filters = [
    'status' => $_GET['status'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
    'user_id' => isset($_GET['user_id']) ? (int)$_GET['user_id'] : null
];
$filters = array_filter($filters, function($v) { return $v !== '' && $v !== null; });

$orders = $filters ? $orderModel->getOrdersFiltered($filters) : $orderModel->getAllOrders();
$orderStats = $orderModel->getOrderStats();
$orderIds = array_map(function ($order) {
    return $order['id'];
}, $orders);
$itemCounts = $orderModel->getOrderItemCounts($orderIds);
$users = $userModel->getAllUsers();
$products = $productModel->getAllProducts();
$statusCounts = [
    'pending' => 0,
    'processing' => 0,
    'shipped' => 0,
    'delivered' => 0,
    'cancelled' => 0
];
foreach ($orders as $o) {
    $st = $o['status'] ?? '';
    if (array_key_exists($st, $statusCounts)) {
        $statusCounts[$st]++;
    }
}
$totalOrdersCount = count($orders);
$hasFilters = !empty($filters);
$pageTitle = 'Gestionar Pedidos';
$activePage = 'orders';
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
                            <h1 class="h3 mb-0">Gestionar Pedidos</h1>
                            <div>
                                <button class="btn btn-primary me-2" onclick="openCreateOrderModal()">
                                    <i class="fas fa-plus me-2"></i>Nuevo Pedido
                                </button>
                                <a class="btn btn-outline-success me-2" href="export_orders.php?<?php echo http_build_query($_GET); ?>">
                                    <i class="fas fa-download me-2"></i>Exportar CSV
                                </a>
                                <button class="btn btn-outline-primary" onclick="printOrders()">
                                    <i class="fas fa-print me-2"></i>Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form class="row g-3" method="get" id="filterForm">
                            <div class="col-md-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="status">
                                    <option value="">Todos</option>
                                    <option value="pending" <?php echo (($_GET['status'] ?? '') === 'pending') ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="processing" <?php echo (($_GET['status'] ?? '') === 'processing') ? 'selected' : ''; ?>>Procesando</option>
                                    <option value="shipped" <?php echo (($_GET['status'] ?? '') === 'shipped') ? 'selected' : ''; ?>>Enviado</option>
                                    <option value="delivered" <?php echo (($_GET['status'] ?? '') === 'delivered') ? 'selected' : ''; ?>>Completado</option>
                                    <option value="cancelled" <?php echo (($_GET['status'] ?? '') === 'cancelled') ? 'selected' : ''; ?>>Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Desde</label>
                                <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Hasta</label>
                                <input type="date" class="form-control" name="date_to" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cliente</label>
                                <select class="form-select" name="user_id">
                                    <option value="">Todos</option>
                                    <?php foreach ($users as $user): ?>
                                        <?php $uid = (int)$user['id']; ?>
                                        <option value="<?php echo $uid; ?>" <?php echo (($_GET['user_id'] ?? '') == $uid) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '') . ' - ' . ($user['email'] ?? '')); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filtrar
                                </button>
                                <a href="orders.php" class="btn btn-outline-secondary" title="Limpiar filtros">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>
                        </form>
                        <?php if ($hasFilters): ?>
                            <div class="small text-muted mt-2">Mostrando resultados filtrados. <a href="orders.php">Quitar filtros</a></div>
                        <?php endif; ?>
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
                                        <div class="h5 mb-0"><?php echo $totalOrdersCount; ?></div>
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
                                        <div class="h5 mb-0"><?php echo $statusCounts['delivered']; ?></div>
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
                                        <div class="h5 mb-0"><?php echo $statusCounts['pending']; ?></div>
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
                                        <div class="h5 mb-0"><?php echo $statusCounts['processing']; ?></div>
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
                                        <tr data-order-id="<?php echo (int)$order['id']; ?>">
                                            <td>
                                                <?php $orderNumber = $order['order_number'] ?? $order['id']; ?>
                                                <strong>#<?php echo htmlspecialchars($orderNumber); ?></strong>
                                            </td>
                                            <td>
                                                <?php 
                                                $userName = $order['user_name'] ?? 'Usuario no encontrado';
                                                $userEmail = $order['user_email'] ?? '';
                                                echo htmlspecialchars($userName);
                                                ?>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($userEmail); ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $itemCount = $itemCounts[$order['id']] ?? 0;
                                                echo $itemCount . ' producto' . ($itemCount != 1 ? 's' : '');
                                                ?>
                                            </td>
                                            <td>
                                                <strong><?php echo formatPrice((float)($order['total'] ?? 0)); ?></strong>
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
                                                <span class="badge <?php echo $statusClass; ?> order-status" data-status="<?php echo htmlspecialchars($order['status'] ?? '', ENT_QUOTES); ?>"><?php echo $statusText; ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $createdAt = $order['created_at'] ?? '';
                                                echo $createdAt ? date('d/m/Y H:i', strtotime($createdAt)) : '-';
                                                ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="viewOrder(<?php echo $order['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success btn-sm" onclick="updateStatus(<?php echo $order['id']; ?>, '<?php echo htmlspecialchars($order['status'] ?? 'pending', ENT_QUOTES); ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-info btn-sm" onclick="printInvoice(<?php echo $order['id']; ?>)">
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteOrder(<?php echo $order['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success btn-sm" title="Marcar como completado" onclick="markCompleted(<?php echo $order['id']; ?>)">
                                                        <i class="fas fa-check-double"></i>
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
                                <option value="pending">Datos pendientes</option>
                                <option value="processing">Cotización enviada</option>
                                <option value="shipped">En espera de pago/envío</option>
                                <option value="delivered">Completado</option>
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

    <!-- Create Order Modal -->
    <div class="modal fade" id="createOrderModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear nuevo pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <select class="form-select" id="createOrderUser">
                            <option value="">Selecciona un cliente</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo (int)$user['id']; ?>">
                                    <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>
                                    (<?php echo htmlspecialchars($user['email'] ?? ''); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="createOrderGuest">
                        <label class="form-check-label" for="createOrderGuest">
                            Cliente no registrado (ingresar datos manuales)
                        </label>
                    </div>

                    <div id="guestFields" class="border rounded p-3 mb-3 d-none">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" id="guestName" placeholder="Nombre y apellido">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Correo</label>
                                <input type="email" class="form-control" id="guestEmail" placeholder="cliente@correo.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="guestPhone" placeholder="+593 ...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">RUC / Cédula</label>
                                <input type="text" class="form-control" id="guestIdNumber" placeholder="ID fiscal">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="guestAddress" placeholder="Dirección de envío">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm" id="createOrderItems">
                            <thead>
                                <tr>
                                    <th style="width:45%">Producto</th>
                                    <th style="width:15%">Cantidad</th>
                                    <th style="width:20%">Precio</th>
                                    <th style="width:20%">Subtotal</th>
                                    <th style="width:5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="addOrderItemRow()">
                            <i class="fas fa-plus me-1"></i>Agregar producto
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0">IVA (<?php echo (float)getSiteSetting('iva_rate', 15); ?>%)</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="createOrderIvaCheck" <?php echo getSiteSetting('apply_iva', true) ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <input type="number" class="form-control" id="createOrderTax" value="0" min="0" step="0.01" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Envío</label>
                            <input type="number" class="form-control" id="createOrderShipping" value="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Descuento (valor)</label>
                            <input type="number" class="form-control" id="createOrderDiscount" value="0" min="0" step="0.01">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notas / Dirección de envío</label>
                        <textarea class="form-control" id="createOrderShippingAddress" rows="2" placeholder="Dirección, referencias, etc."></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Totales se calculan automaticamente.</small>
                        </div>
                        <div class="text-end">
                            <div>Subtotal: <strong id="createOrderSubtotal">$0.00</strong></div>
                            <div>Total: <strong id="createOrderTotal">$0.00</strong></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="submitCreateOrder()">Crear Pedido</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <?php include __DIR__ . '/partials/scripts.php'; ?>
    
    <script>
        const csrfToken = '<?php echo generateCSRFToken(); ?>';
        const productCatalog = <?php
            $productOptions = array_map(function ($p) {
                return [
                    'id' => (int)$p['id'],
                    'name' => $p['name'],
                    'price' => (float)($p['price'] ?? 0)
                ];
            }, $products ?? []);
            echo json_encode($productOptions);
        ?>;
        let currentOrderId = null;
        const applyIvaSetting = <?php echo getSiteSetting('apply_iva', true) ? 'true' : 'false'; ?>;
        const ivaRate = <?php echo (float)getSiteSetting('iva_rate', 15); ?>;
        const statusMap = {
            pending: { label: 'Datos pendientes', className: 'bg-warning' },
            processing: { label: 'Cotización enviada', className: 'bg-info' },
            shipped: { label: 'En espera de pago/envío', className: 'bg-primary' },
            delivered: { label: 'Completado', className: 'bg-success' },
            cancelled: { label: 'Cancelado', className: 'bg-danger' }
        };

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value == null ? '' : String(value);
            return div.innerHTML;
        }

        function formatMoney(value) {
            const amount = Number(value || 0);
            return '$' + amount.toLocaleString('es-EC', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function getStatusInfo(status) {
            return statusMap[status] || { label: 'Desconocido', className: 'bg-secondary' };
        }

        function openCreateOrderModal() {
            resetCreateOrderForm();
            addOrderItemRow();
            const modal = new bootstrap.Modal(document.getElementById('createOrderModal'));
            modal.show();
        }

        function resetCreateOrderForm() {
            document.getElementById('createOrderUser').value = '';
            document.getElementById('createOrderUser').disabled = false;
            const guestToggle = document.getElementById('createOrderGuest');
            const guestFields = document.getElementById('guestFields');
            if (guestToggle) guestToggle.checked = false;
            if (guestFields) guestFields.classList.add('d-none');
            ['guestName','guestEmail','guestPhone','guestIdNumber','guestAddress','createOrderShippingAddress'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            document.getElementById('createOrderTax').value = 0;
            document.getElementById('createOrderShipping').value = 0;
            document.getElementById('createOrderDiscount').value = 0;
            const ivaCheck = document.getElementById('createOrderIvaCheck');
            if (ivaCheck) ivaCheck.checked = applyIvaSetting;
            const tbody = document.querySelector('#createOrderItems tbody');
            if (tbody) tbody.innerHTML = '';
            updateCreateOrderTotals();
        }

        function addOrderItemRow() {
            const tbody = document.querySelector('#createOrderItems tbody');
            if (!tbody) return;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select class="form-select form-select-sm order-product">
                        <option value="">Selecciona producto</option>
                        ${productCatalog.map(p => `<option value=\"${p.id}\" data-price=\"${p.price}\">${escapeHtml(p.name)} - $${p.price.toFixed(2)}</option>`).join('')}
                    </select>
                </td>
                <td><input type="number" class="form-control form-control-sm order-qty" min="1" value="1"></td>
                <td><input type="number" class="form-control form-control-sm order-price" min="0" step="0.01" value="0"></td>
                <td class="order-line-total">$0.00</td>
                <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeOrderItemRow(this)"><i class="fas fa-trash"></i></button></td>
            `;

            tbody.appendChild(row);

            const productSelect = row.querySelector('.order-product');
            const priceInput = row.querySelector('.order-price');
            const qtyInput = row.querySelector('.order-qty');

            productSelect.addEventListener('change', () => {
                const selected = productSelect.options[productSelect.selectedIndex];
                const price = parseFloat(selected.dataset.price || '0');
                priceInput.value = price.toFixed(2);
                updateCreateOrderTotals();
            });

            [priceInput, qtyInput].forEach(el => {
                el.addEventListener('input', updateCreateOrderTotals);
            });

            updateCreateOrderTotals();
        }

        function removeOrderItemRow(btn) {
            const row = btn.closest('tr');
            if (row) {
                row.remove();
                updateCreateOrderTotals();
            }
        }

        function updateCreateOrderTotals() {
            const rows = document.querySelectorAll('#createOrderItems tbody tr');
            let subtotal = 0;

            rows.forEach(row => {
                const qty = Number(row.querySelector('.order-qty')?.value || 0);
                const price = Number(row.querySelector('.order-price')?.value || 0);
                const lineTotal = qty * price;
                subtotal += lineTotal;
                const lineCell = row.querySelector('.order-line-total');
                if (lineCell) {
                    lineCell.textContent = formatMoney(lineTotal);
                }
            });

            const taxInput = document.getElementById('createOrderTax');
            const ivaCheck = document.getElementById('createOrderIvaCheck');
              let tax = Number(taxInput?.value || 0);
              if (ivaCheck && ivaCheck.checked) {
                  tax = Math.max(0, subtotal * (ivaRate / 100));
                  if (taxInput) {
                      taxInput.value = tax.toFixed(2);
                      taxInput.readOnly = true;
                  }
              } else if (taxInput) {
                  taxInput.readOnly = false;
              }

            const shipping = Number(document.getElementById('createOrderShipping').value || 0);
            let discount = Number(document.getElementById('createOrderDiscount').value || 0);
            discount = Math.max(0, Math.min(discount, subtotal));

            const total = subtotal + tax + shipping - discount;

            document.getElementById('createOrderSubtotal').textContent = formatMoney(subtotal);
            document.getElementById('createOrderTotal').textContent = formatMoney(total);
        }

        function submitCreateOrder() {
            const userId = document.getElementById('createOrderUser').value;
            const guestToggle = document.getElementById('createOrderGuest');
            const guest = guestToggle ? guestToggle.checked : false;
            const guestName = document.getElementById('guestName')?.value.trim() || '';
            const guestEmail = document.getElementById('guestEmail')?.value.trim() || '';
            const guestPhone = document.getElementById('guestPhone')?.value.trim() || '';
            const guestId = document.getElementById('guestIdNumber')?.value.trim() || '';
            const guestAddress = document.getElementById('guestAddress')?.value.trim() || '';
            const shippingAddress = document.getElementById('createOrderShippingAddress')?.value.trim() || '';

            if (!userId && !guest) {
                alert('Selecciona un cliente o marca cliente no registrado');
                return;
            }
            if (guest && guestName === '') {
                alert('Ingresa el nombre del cliente');
                return;
            }

            const rows = document.querySelectorAll('#createOrderItems tbody tr');
            const items = [];
            rows.forEach(row => {
                const productId = Number(row.querySelector('.order-product')?.value || 0);
                const qty = Number(row.querySelector('.order-qty')?.value || 0);
                const price = Number(row.querySelector('.order-price')?.value || 0);
                if (productId > 0 && qty > 0) {
                    items.push({ product_id: productId, quantity: qty, price });
                }
            });

            if (items.length === 0) {
                alert('Agrega al menos un producto');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'create_order');
            formData.append('user_id', guest ? '' : userId);
            formData.append('items', JSON.stringify(items));
            formData.append('tax_amount', document.getElementById('createOrderTax').value || 0);
            formData.append('shipping_amount', document.getElementById('createOrderShipping').value || 0);
            formData.append('discount_amount', document.getElementById('createOrderDiscount').value || 0);
            formData.append('apply_iva', document.getElementById('createOrderIvaCheck').checked ? '1' : '0');
            formData.append('customer_name', guest ? guestName : '');
            formData.append('customer_email', guest ? guestEmail : '');
            formData.append('customer_phone', guest ? guestPhone : '');
            formData.append('customer_id_number', guest ? guestId : '');
            formData.append('customer_address', guest ? guestAddress : '');
            formData.append('shipping_address', shippingAddress);
            formData.append('csrf_token', csrfToken);

            fetch('process_order.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'No se pudo crear el pedido');
                        return;
                    }
                    // recargar para ver el nuevo pedido
                    window.location.reload();
                })
                .catch(err => {
                    alert('Error al crear el pedido: ' + err.message);
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            ['createOrderTax','createOrderShipping','createOrderDiscount'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', updateCreateOrderTotals);
                }
            });

            const ivaCheck = document.getElementById('createOrderIvaCheck');
            if (ivaCheck) {
                ivaCheck.addEventListener('change', updateCreateOrderTotals);
            }

            const guestToggle = document.getElementById('createOrderGuest');
            if (guestToggle) {
                guestToggle.addEventListener('change', () => {
                    const guestFields = document.getElementById('guestFields');
                    const userSelect = document.getElementById('createOrderUser');
                    if (guestToggle.checked) {
                        guestFields.classList.remove('d-none');
                        userSelect.value = '';
                        userSelect.disabled = true;
                    } else {
                        guestFields.classList.add('d-none');
                        userSelect.disabled = false;
                    }
                });
            }
        });

        function fetchOrder(orderId) {
            const formData = new FormData();
            formData.append('action', 'get_order');
            formData.append('id', orderId);
            formData.append('csrf_token', csrfToken);

            return fetch('process_order.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json());
        }

        function viewOrder(orderId) {
            fetchOrder(orderId)
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'No se pudo cargar el pedido');
                        return;
                    }

                    currentOrderId = orderId;

                    const order = data.order || {};
                    const items = data.items || [];
                    const statusInfo = getStatusInfo(order.status);
                    const createdAt = order.created_at ? new Date(order.created_at) : null;
                    const dateLabel = createdAt ? createdAt.toLocaleString('es-EC') : '-';
                    const customerName = order.user_name || order.shipping_address || 'Cliente';
                    const customerEmail = order.user_email || '';
                    const shippingAddress = order.shipping_address || 'No especificada';

                    let itemsHtml = '';
                    let subtotal = 0;
                    if (items.length === 0) {
                        itemsHtml = '<tr><td colspan="4" class="text-muted">Sin items</td></tr>';
                    } else {
                        itemsHtml = items.map(item => {
                            const qty = Number(item.quantity || 0);
                            const price = Number(item.price || 0);
                            const total = Number(item.total || (qty * price));
                            subtotal += total;
                            return `
                                <tr>
                                    <td>${escapeHtml(item.product_name || 'Producto')}</td>
                                    <td>${qty}</td>
                                    <td>${formatMoney(price)}</td>
                                    <td>${formatMoney(total)}</td>
                                </tr>
                            `;
                        }).join('');
                    }

                    const tax = Number(order.tax_amount || 0);
                    const shipping = Number(order.shipping_amount || 0);
                    const discount = Number(order.discount_amount || 0);
                    const total = Number(order.total || (subtotal + tax + shipping - discount));

                    const logs = data.logs || [];
                    let logsHtml = '<p class="text-muted mb-0">Sin movimientos registrados</p>';
                    if (logs.length) {
                        logsHtml = logs.map(log => {
                            const ts = log.timestamp ? new Date(log.timestamp).toLocaleString('es-EC') : '';
                            return `
                                <div class="d-flex align-items-start mb-2">
                                    <div class="badge bg-light text-dark me-2">${escapeHtml(log.from || '-')} ➜ ${escapeHtml(log.to || '-')}</div>
                                    <div>
                                        <div class="fw-bold">${escapeHtml(log.user || '')}</div>
                                        <div class="small text-muted">${ts}</div>
                                        <div class="small">${escapeHtml(log.comment || '')}</div>
                                    </div>
                                </div>
                            `;
                        }).join('');
                    }

                    document.getElementById('orderDetails').innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Informacion del Pedido</h6>
                                <p><strong>Pedido #:</strong> ${escapeHtml(order.order_number || order.id || orderId)}</p>
                                <p><strong>Fecha:</strong> ${dateLabel}</p>
                                <p><strong>Estado:</strong> <span class="badge ${statusInfo.className}">${statusInfo.label}</span></p>
                            </div>
                            <div class="col-md-6">
                                <h6>Informacion del Cliente</h6>
                                <p><strong>Nombre:</strong> ${escapeHtml(customerName)}</p>
                                <p><strong>Email:</strong> ${escapeHtml(customerEmail)}</p>
                                <p><strong>Dirección / Datos:</strong><br>${escapeHtml(shippingAddress).replace(/\n/g,'<br>')}</p>
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
                                        ${itemsHtml}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Subtotal:</strong></td>
                                        <td>${formatMoney(subtotal)}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Envio:</strong></td>
                                        <td>${formatMoney(shipping)}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Impuestos:</strong></td>
                                        <td>${formatMoney(tax)}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Descuento:</strong></td>
                                        <td>${formatMoney(discount)}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total:</strong></td>
                                        <td><strong>${formatMoney(total)}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h6>Bitácora</h6>
                                ${logsHtml}
                            </div>
                        </div>
                    `;

                    const modal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
                    modal.show();
                })
                .catch(error => {
                    alert('Error al cargar el pedido: ' + error.message);
                });
        }

        function updateStatus(orderId, currentStatus) {
            document.getElementById('orderId').value = orderId;
            const statusSelect = document.getElementById('orderStatus');
            if (statusSelect && currentStatus) {
                statusSelect.value = currentStatus;
            }
            const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
            modal.show();
        }

        function saveStatus() {
            const orderId = document.getElementById('orderId').value;
            const status = document.getElementById('orderStatus').value;
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('id', orderId);
            formData.append('status', status);
            formData.append('comments', document.querySelector('#updateStatusForm textarea[name=\"comments\"]')?.value || '');
            formData.append('csrf_token', csrfToken);

            fetch('process_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message || 'Error al actualizar el estado');
                    return;
                }

                const statusInfo = getStatusInfo(status);
                const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                if (row) {
                    const badge = row.querySelector('.order-status');
                    if (badge) {
                        badge.className = `badge ${statusInfo.className} order-status`;
                        badge.textContent = statusInfo.label;
                        badge.dataset.status = status;
                    }
                }

                const modal = bootstrap.Modal.getInstance(document.getElementById('updateStatusModal'));
                if (modal) {
                    modal.hide();
                }
            })
            .catch(error => {
                alert('Error al actualizar el estado: ' + error.message);
            });
        }

        function markCompleted(orderId) {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('id', orderId);
            formData.append('status', 'delivered');
            formData.append('csrf_token', csrfToken);

            fetch('process_order.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'No se pudo completar el pedido');
                        return;
                    }
                    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                    if (row) {
                        const badge = row.querySelector('.order-status');
                        const statusInfo = getStatusInfo('delivered');
                        if (badge) {
                            badge.className = `badge ${statusInfo.className} order-status`;
                            badge.textContent = statusInfo.label;
                            badge.dataset.status = 'delivered';
                        }
                    }
                })
                .catch(err => alert('Error al marcar completado: ' + err.message));
        }

        function deleteOrder(orderId) {
            if (!confirm('¿Eliminar permanentemente el pedido #' + orderId + '?')) return;

            const formData = new FormData();
            formData.append('action', 'delete_order');
            formData.append('id', orderId);
            formData.append('csrf_token', csrfToken);

            fetch('process_order.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'No se pudo eliminar');
                        return;
                    }
                    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                    if (row) row.remove();
                })
                .catch(err => alert('Error al eliminar: ' + err.message));
        }

        function printInvoice(orderId) {
            const targetId = orderId || currentOrderId;
            if (!targetId) {
                alert('Selecciona un pedido');
                return;
            }
            const url = '<?php echo rtrim(SITE_URL, '/'); ?>/invoice.php?id=' + targetId + '&print=1';
            window.open(url, '_blank');
        }

        function exportOrders() {
            if (typeof exportData === 'function') {
                exportData('csv');
            } else {
                alert('Exportacion no disponible');
            }
        }

        function printOrders() {
            window.print();
        }
    </script>
</body>
</html> 
