<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    exit('Acceso denegado');
}

$orderModel = new Order();
$userModel  = new User();

$filters = [
    'status' => $_GET['status'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
    'user_id' => isset($_GET['user_id']) ? (int)$_GET['user_id'] : null
];
$filters = array_filter($filters, fn($v) => $v !== '' && $v !== null);

$orders = $filters ? $orderModel->getOrdersFiltered($filters) : $orderModel->getAllOrders();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="pedidos.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Pedido', 'Cliente', 'Email', 'Estado', 'Fecha', 'Subtotal', 'IVA', 'Envío', 'Descuento', 'Total']);

foreach ($orders as $order) {
    $client = trim(($order['user_name'] ?? '') ?: (($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')));
    fputcsv($out, [
        $order['order_number'] ?? $order['id'],
        $client,
        $order['user_email'] ?? '',
        $order['status'],
        $order['created_at'],
        $order['subtotal'],
        $order['tax_amount'],
        $order['shipping_amount'],
        $order['discount_amount'],
        $order['total'] ?? $order['total_amount'] ?? ''
    ]);
}

fclose($out);
exit;
