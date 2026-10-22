<?php
require_once __DIR__ . '/config/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
    exit;
}

$rawItems = $_POST['items'] ?? '[]';
$itemsInput = json_decode($rawItems, true);
if (!is_array($itemsInput) || empty($itemsInput)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Carrito vacío']);
    exit;
}

$productModel = new Product();
$orderModel = new Order();
$userId = isLoggedIn() ? ($_SESSION['user_id'] ?? null) : null;

$customerName = trim((string)($_POST['customer_name'] ?? ''));
$customerEmail = trim((string)($_POST['customer_email'] ?? ''));
$customerPhone = trim((string)($_POST['customer_phone'] ?? ''));
$customerIdNumber = trim((string)($_POST['customer_id_number'] ?? ''));
$customerAddress = trim((string)($_POST['customer_address'] ?? ''));
$applyIvaSetting = getSiteSetting('apply_iva', true);
$applyIva = $applyIvaSetting && (isset($_POST['apply_iva']) ? $_POST['apply_iva'] == '1' : $applyIvaSetting);
$ivaRate = (float)getSiteSetting('iva_rate', 15);
$discountInput = (float)($_POST['discount_amount'] ?? 0);

$items = [];
$subtotal = 0;

foreach ($itemsInput as $item) {
    $productId = (int)($item['product_id'] ?? $item['id'] ?? 0);
    $quantity = max(1, (int)($item['quantity'] ?? 0));
    if ($productId <= 0 || $quantity <= 0) {
        continue;
    }

    $product = $productModel->getProductById($productId, true);
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
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Los productos del carrito no son válidos']);
    exit;
}

$taxAmount = $applyIva ? round($subtotal * ($ivaRate / 100), 2) : 0;
$discountAmount = 0; // cliente no aplica descuento directo
$shippingAmount = (float)($_POST['shipping_amount'] ?? 0);
$totalAmount = round($subtotal + $taxAmount + $shippingAmount - $discountAmount, 2);

if ($userId === null && ($customerName === '' || $customerEmail === '' || $customerPhone === '')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ingresa nombre, correo y teléfono para continuar'] );
    exit;
}

$shippingAddress = trim(
    ($customerName ? "Nombre: {$customerName}\n" : '') .
    ($customerEmail ? "Email: {$customerEmail}\n" : '') .
    ($customerPhone ? "Tel: {$customerPhone}\n" : '') .
    ($customerIdNumber ? "ID: {$customerIdNumber}\n" : '') .
    ($customerAddress ? "Dirección: {$customerAddress}" : '')
);

$orderId = $orderModel->createOrder($userId, $items, [
    'status' => 'pending',
    'subtotal' => $subtotal,
    'tax_amount' => $taxAmount,
    'shipping_amount' => $shippingAmount,
    'discount_amount' => $discountAmount,
    'payment_status' => 'pending',
    'payment_method' => 'checkout',
    'shipping_address' => $shippingAddress,
    'total' => $totalAmount
]);

if (!$orderId) {
    http_response_code(500);
    $err = method_exists($orderModel, 'getLastError') ? $orderModel->getLastError() : '';
    echo json_encode(['success' => false, 'message' => $err ?: 'No se pudo crear el pedido']);
    exit;
}

// Obtener datos finales para responder y notificar
$orderData = $orderModel->getOrderById($orderId) ?: [];
$orderNumber = $orderData['order_number'] ?? $orderId;
$finalTotal = $orderData['total'] ?? $orderData['total_amount'] ?? $totalAmount;

echo json_encode([
    'success' => true,
    'order_id' => $orderId,
    'redirect' => SITE_URL . '/orders.php?id=' . $orderId,
    'order_number' => $orderNumber,
    'total' => $finalTotal
]);

// Notificar por correo al cliente (si proporcionó email)
if (!empty($customerEmail)) {
    $subject = 'Proforma de tu pedido #' . $orderNumber;
    $body = "Hola {$customerName},\n\n".
            "Hemos recibido tu pedido en Alquimia Esencial.\n".
            "Monto estimado (incluye IVA): $" . number_format($finalTotal,2) . "\n".
            "Teléfono de contacto: {$customerPhone}\n".
            "Puedes ver tu proforma en: " . SITE_URL . "/invoice.php?id={$orderId}\n\n".
            "Te enviaremos los datos de pago para completar la compra.\n\n".
            "Gracias.";
    @sendEmail($customerEmail, $subject, $body);
}

if ($userId === null) {
    $adminSubject = "Nuevo pedido (invitado) #{$orderNumber}";
    $adminBody = "Se registró un nuevo pedido de invitado.\n\n".
                 "Nombre: {$customerName}\n".
                 "Email: {$customerEmail}\n".
                 "Tel: {$customerPhone}\n".
                 "Total estimado: $" . number_format($finalTotal,2) . "\n".
                 "Ver pedido: " . SITE_URL . "/admin/orders.php?id={$orderId}";
    @sendEmail(ADMIN_EMAIL, $adminSubject, $adminBody);
}
exit;
