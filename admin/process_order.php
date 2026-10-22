<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Token CSRF invalido']);
    exit();
}

$orderModel = new Order();
$productModel = new Product();
$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Accion invalida'];

try {
    switch ($action) {
        case 'get_order':
            $orderId = (int)($_POST['id'] ?? 0);
            if ($orderId <= 0) {
                throw new Exception('ID de pedido invalido');
            }

            $order = $orderModel->getOrderById($orderId);
            if (!$order) {
                throw new Exception('Pedido no encontrado');
            }

            $items = $orderModel->getOrderItems($orderId);
            $logs = loadOrderLogs($orderId);
            $response = [
                'success' => true,
                'order' => $order,
                'items' => $items,
                'logs' => $logs
            ];
            break;

        case 'update_status':
            $orderId = (int)($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? '';
            $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            if ($orderId <= 0) {
                throw new Exception('ID de pedido invalido');
            }
            if (!in_array($status, $allowed, true)) {
                throw new Exception('Estado invalido');
            }

            $current = $orderModel->getOrderById($orderId);
            $prevStatus = $current['status'] ?? '';

            $result = $orderModel->updateOrderStatus($orderId, $status);
            if (!$result) {
                throw new Exception('No se pudo actualizar el estado');
            }

            $comment = trim((string)($_POST['comments'] ?? ''));
            $userEmail = $_SESSION['email'] ?? ($_SESSION['user_email'] ?? 'admin');
            appendOrderLog($orderId, $prevStatus, $status, $comment, $userEmail);

            $response = [
                'success' => true,
                'message' => 'Estado actualizado',
                'status' => $status
            ];
            break;

        case 'delete_order':
            $orderId = (int)($_POST['id'] ?? 0);
            if ($orderId <= 0) {
                throw new Exception('ID de pedido invalido');
            }
            if (!$orderModel->deleteOrder($orderId)) {
                throw new Exception('No se pudo eliminar el pedido');
            }
            $response = [
                'success' => true,
                'message' => 'Pedido eliminado'
            ];
            break;

        case 'create_order':
            $userId = (int)($_POST['user_id'] ?? 0);
            $rawItems = $_POST['items'] ?? '[]';
            $status = $_POST['status'] ?? 'pending';
            $taxAmount = (float)($_POST['tax_amount'] ?? 0);
            $shippingAmount = (float)($_POST['shipping_amount'] ?? 0);
            $discountAmount = (float)($_POST['discount_amount'] ?? 0);
            $applyIvaSetting = getSiteSetting('apply_iva', true);
            $ivaRate = (float)getSiteSetting('iva_rate', 15);
            $applyIva = $applyIvaSetting && isset($_POST['apply_iva']) && $_POST['apply_iva'] == '1';

            $customerName = trim((string)($_POST['customer_name'] ?? ''));
            $customerEmail = trim((string)($_POST['customer_email'] ?? ''));
            $customerPhone = trim((string)($_POST['customer_phone'] ?? ''));
            $customerAddress = trim((string)($_POST['customer_address'] ?? ''));
            $customerIdNumber = trim((string)($_POST['customer_id_number'] ?? ''));
            $shippingAddressForm = trim((string)($_POST['shipping_address'] ?? ''));

            if ($userId <= 0 && $customerName === '') {
                throw new Exception('Selecciona un cliente o ingresa datos del cliente');
            }

            $itemsInput = json_decode($rawItems, true);
            if (!is_array($itemsInput) || empty($itemsInput)) {
                throw new Exception('Agrega al menos un producto');
            }

            $items = [];
            $subtotal = 0;
            foreach ($itemsInput as $item) {
                $productId = (int)($item['product_id'] ?? 0);
                $quantity = max(1, (int)($item['quantity'] ?? 0));
                if ($productId <= 0 || $quantity <= 0) {
                    continue;
                }

                $product = $productModel->getProductById($productId);
                if (!$product) {
                    continue;
                }

                $price = (float)($product['price'] ?? 0);
                $lineTotal = $price * $quantity;
                $subtotal += $lineTotal;

                $items[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $price
                ];
            }

            if (empty($items)) {
                throw new Exception('Los productos seleccionados no son validos');
            }

            if ($applyIva) {
                $taxAmount = round($subtotal * ($ivaRate / 100), 2);
            }

            $discountAmount = max(0, min($discountAmount, $subtotal));

            $shippingAddress = $shippingAddressForm;
            if ($customerName !== '' || $customerEmail !== '' || $customerPhone !== '' || $customerAddress !== '' || $customerIdNumber !== '') {
                $extra = trim(
                    "Nombre: {$customerName}\n" .
                    ($customerEmail ? "Email: {$customerEmail}\n" : '') .
                    ($customerPhone ? "Tel: {$customerPhone}\n" : '') .
                    ($customerIdNumber ? "ID: {$customerIdNumber}\n" : '') .
                    ($customerAddress ? "Direccion: {$customerAddress}" : '')
                );
                $shippingAddress = trim($shippingAddress . "\n" . $extra);
            }

            $orderId = $orderModel->createOrder($userId ?: null, $items, [
                'status' => $status,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => $discountAmount,
                'payment_status' => 'paid',
                'payment_method' => 'manual-admin',
                'shipping_address' => $shippingAddress
            ]);

            if (!$orderId) {
                $err = $orderModel->getLastError();
                throw new Exception($err ?: 'No se pudo crear el pedido');
            }

            $response = [
                'success' => true,
                'message' => 'Pedido creado correctamente',
                'order_id' => $orderId
            ];
            break;
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);

function appendOrderLog($orderId, $fromStatus, $toStatus, $comment, $userEmail) {
    $file = __DIR__ . '/../data/order_logs.json';
    $logs = [];
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (is_array($data)) {
            $logs = $data;
        }
    }
    $logs[] = [
        'order_id' => $orderId,
        'from' => $fromStatus,
        'to' => $toStatus,
        'comment' => $comment,
        'user' => $userEmail,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    file_put_contents($file, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function loadOrderLogs($orderId) {
    $file = __DIR__ . '/../data/order_logs.json';
    if (!file_exists($file)) {
        return [];
    }
    $data = json_decode(file_get_contents($file), true);
    if (!is_array($data)) {
        return [];
    }
    return array_values(array_filter($data, function($log) use ($orderId) {
        return (int)($log['order_id'] ?? 0) === (int)$orderId;
    }));
}
