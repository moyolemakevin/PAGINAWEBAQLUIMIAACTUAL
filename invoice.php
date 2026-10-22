<?php
require_once __DIR__ . '/config/bootstrap.php';

$orderId     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$shouldPrint = isset($_GET['print']);

if ($orderId <= 0) { http_response_code(400); exit('ID de pedido inválido'); }

$orderModel = new Order();
$order      = $orderModel->getOrderById($orderId);
if (!$order) { http_response_code(404); exit('Pedido no encontrado'); }

$isOwner = isLoggedIn() && ($_SESSION['user_id'] ?? null) == ($order['user_id'] ?? null);
if (!isAdmin() && !$isOwner) { http_response_code(403); exit('Sin permiso'); }

$items = $orderModel->getOrderItems($orderId);
$subtotal = 0;
foreach ($items as $it) { $subtotal += ($it['price'] ?? 0) * ($it['quantity'] ?? 0); }
$shipping  = (float)($order['shipping_amount'] ?? 0);
$tax       = (float)($order['tax_amount'] ?? 0);
$discount  = (float)($order['discount_amount'] ?? 0);
$total     = (float)($order['total'] ?? ($subtotal + $shipping + $tax - $discount));
$effectiveIvaRate = ($subtotal > 0 && $tax > 0) ? round(($tax / $subtotal) * 100, 2) : (float)getSiteSetting('iva_rate', 15);

$customerName    = $order['user_name'] ?? 'Cliente';
$customerEmail   = $order['user_email'] ?? '';
$customerPhone   = $order['user_phone'] ?? '';
$shippingAddress = $order['shipping_address'] ?? '';
$customerId = '';
if ($shippingAddress && preg_match('/ID:\s*([^\n]+)/i', $shippingAddress, $m)) { $customerId = trim($m[1]); }

$orderNumber = $order['order_number'] ?? $orderId;
$createdAt   = $order['created_at'] ?? date('Y-m-d H:i:s');
$authNumber  = 'AUT-' . str_pad($orderId, 12, '0', STR_PAD_LEFT);
$barcodeText = $authNumber;
$statusLabelMap = [
    'pending' => 'Pendiente',
    'processing' => 'Cotización enviada',
    'shipped' => 'En espera de pago/envío',
    'delivered' => 'Completado',
    'cancelled' => 'Cancelado'
];
$statusLabel = $statusLabelMap[$order['status'] ?? 'pending'] ?? 'Pendiente';

$emitterName    = getSiteSetting('emitter_name', 'Alquimia Esencial');
$emitterRuc     = getSiteSetting('emitter_ruc', '0000000000');
$emitterAddress = getSiteSetting('emitter_address', getSiteSetting('address',''));
$emitterPhone   = getSiteSetting('emitter_phone', getSiteSetting('contact_phone',''));
$emitterEmail   = getSiteSetting('contact_email', ADMIN_EMAIL);
$logoSetting    = getSafeSiteImageSetting('logo_url', '');
$logoSrc = $logoSetting !== '' ? assetUrl($logoSetting) : '';

// Limpiar dirección de envío para mostrar solo la dirección (sin duplicar contacto)
function cleanShippingAddress($text) {
    $lines = preg_split('/\r?\n/', (string)$text);
    $filtered = array_filter($lines, function($line) {
        $t = trim($line);
        return $t !== '' &&
            stripos($t, 'nombre:') !== 0 &&
            stripos($t, 'email:')  !== 0 &&
            stripos($t, 'tel:')    !== 0 &&
            stripos($t, 'id:')     !== 0;
    });
    return implode("\n", $filtered);
}
$cleanAddress = cleanShippingAddress($shippingAddress);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #<?php echo htmlspecialchars($orderNumber); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Libre+Barcode+128&display=swap" rel="stylesheet">
    <style>
        :root { --primary:#0e9f6e; --accent:#0ea5e9; --muted:#6b7280; --border:#d8dee6; --bg:#f5f7fb; }
        * { box-sizing:border-box; }
        body { margin: 16px; font-family:'Inter', Arial, sans-serif; color:#0f172a; background:#eef1f5; }
        .sheet { max-width: 980px; margin:0 auto; background:#fff; border:1px solid var(--border); border-radius:12px; padding:24px 26px; box-shadow:0 10px 30px rgba(0,0,0,0.08); }
        .top { display:flex; justify-content:space-between; gap:18px; }
        .brand { display:flex; gap:12px; align-items:center; }
        .logo { width:70px; height:70px; border-radius:14px; background:linear-gradient(135deg,var(--accent),var(--primary)); color:#fff; font-weight:800; font-size:24px; letter-spacing:1px; display:flex; align-items:center; justify-content:center; text-transform:uppercase; overflow:hidden; }
        .logo img { width:100%; height:100%; object-fit:cover; display:block; }
        .company h2 { margin:0; font-size:22px; letter-spacing:0.5px; }
        .muted { color:var(--muted); font-size:13px; }
        .invoice-meta { text-align:right; min-width:260px; }
        .pill { display:inline-block; padding:5px 12px; border-radius:999px; background:rgba(14,159,110,0.12); color:var(--primary); font-weight:700; font-size:12px; }
        table.meta { width:100%; border-collapse:collapse; margin-top:8px; }
        table.meta td { padding:4px 6px; font-size:13px; }
        table.meta td.label { color:var(--muted); width:45%; }
        .info-grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap:12px; margin-top:16px; }
        .card { border:1px solid var(--border); border-radius:10px; padding:12px 14px; background:#f9fbff; }
        .title { font-size:13px; text-transform:uppercase; letter-spacing:0.4px; font-weight:700; margin:0 0 6px; color:#334155; }
        .barcode { font-family:'Libre Barcode 128', cursive; font-size:40px; letter-spacing:2px; }
        table.items { width:100%; border-collapse:collapse; margin-top:18px; font-size:13px; }
        table.items th, table.items td { border:1px solid var(--border); padding:9px; }
        table.items th { background:#eef2f7; text-transform:uppercase; font-size:12px; letter-spacing:0.3px; }
        .totals { width:340px; margin-left:auto; border:1px solid var(--border); border-radius:10px; overflow:hidden; margin-top:16px; }
        .totals td { padding:8px 10px; border-bottom:1px solid var(--border); }
        .totals tr:last-child td { border-bottom:none; font-weight:800; background:#f7fafc; }
        @media print { body { margin:0; background:#fff; } .sheet { border:none; box-shadow:none; } }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="top">
            <div class="brand">
                <div class="logo">
                    <?php if ($logoSrc): ?>
                        <img src="<?php echo htmlspecialchars($logoSrc); ?>" alt="<?php echo htmlspecialchars(SITE_NAME); ?>">
                    <?php else: ?>
                        AE
                    <?php endif; ?>
                </div>
                <div class="company">
                    <h2><?php echo htmlspecialchars($emitterName); ?></h2>
                    <div class="muted"><?php echo htmlspecialchars(rtrim(SITE_URL,'/')); ?></div>
                    <div class="muted">Email: <?php echo htmlspecialchars($emitterEmail); ?></div>
                </div>
            </div>
            <div class="invoice-meta">
                <div class="pill">FACTURA</div>
                <table class="meta">
                    <tr><td class="label">Factura No.</td><td><strong><?php echo htmlspecialchars($orderNumber); ?></strong></td></tr>
                    <tr><td class="label">Fecha</td><td><?php echo date('d/m/Y H:i', strtotime($createdAt)); ?></td></tr>
                    <tr><td class="label">Estado</td><td><strong><?php echo htmlspecialchars($statusLabel); ?></strong></td></tr>
                    <tr><td class="label">Autorización</td><td><?php echo htmlspecialchars($authNumber); ?></td></tr>
                </table>
            </div>
        </div>

        <div class="card" style="margin-top:12px;">
            <table class="meta">
                <tr>
                    <td class="label">Número de Autorización</td><td><?php echo htmlspecialchars($authNumber); ?></td>
                    <td class="label">Fecha/hora Autorización</td><td><?php echo date('d/m/Y H:i', strtotime($createdAt)); ?></td>
                </tr>
                <tr>
                    <td class="label">Ambiente</td><td>PRODUCCIÓN</td>
                    <td class="label">Clave de Acceso</td><td class="barcode"><?php echo htmlspecialchars($barcodeText); ?></td>
                </tr>
            </table>
        </div>

        <div class="info-grid">
            <div class="card">
                <div class="title">Emisor</div>
                <div><strong><?php echo htmlspecialchars($emitterName); ?></strong></div>
                <div class="muted">RUC: <?php echo htmlspecialchars($emitterRuc); ?></div>
                <div class="muted">Dirección: <?php echo htmlspecialchars($emitterAddress); ?></div>
                <div class="muted">Tel: <?php echo htmlspecialchars($emitterPhone); ?></div>
            </div>
            <div class="card">
                <div class="title">Comprador</div>
                <div><strong><?php echo htmlspecialchars($customerName); ?></strong></div>
                <div class="muted">RUC/Cédula: <?php echo $customerId ? htmlspecialchars($customerId) : 'No indicado'; ?></div>
                <div class="muted">Tel: <?php echo $customerPhone ? htmlspecialchars($customerPhone) : 'No indicado'; ?></div>
                <div class="muted">Correo: <?php echo $customerEmail ? htmlspecialchars($customerEmail) : 'No indicado'; ?></div>
                <div class="muted" style="white-space:pre-line; margin-top:4px;"><?php echo $cleanAddress ? htmlspecialchars($cleanAddress) : 'Dirección no especificada'; ?></div>
            </div>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:15%;">Código</th>
                    <th style="width:8%;">Cant.</th>
                    <th>Descripción</th>
                    <th style="width:12%;">Precio Unit.</th>
                    <th style="width:10%;">Desc.</th>
                    <th style="width:12%;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="6" style="text-align:center" class="muted">Sin items</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <?php
                            $code  = $item['product_id'] ?? '';
                            $qty   = (float)($item['quantity'] ?? 0);
                            $price = (float)($item['price'] ?? 0);
                            $line  = $price * $qty;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($code); ?></td>
                            <td><?php echo number_format($qty,2); ?></td>
                            <td><?php echo htmlspecialchars($item['product_name'] ?? 'Producto'); ?></td>
                            <td>$<?php echo number_format($price,2,'.',','); ?></td>
                            <td>$0.00</td>
                            <td>$<?php echo number_format($line,2,'.',','); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="totals">
            <table style="width:100%; border-collapse:collapse;">
                <tr><td>Subtotal</td><td style="text-align:right">$<?php echo number_format($subtotal,2,'.',','); ?></td></tr>
                <tr><td>Envío</td><td style="text-align:right">$<?php echo number_format($shipping,2,'.',','); ?></td></tr>
                <tr><td>IVA <?php echo htmlspecialchars($effectiveIvaRate); ?>%</td><td style="text-align:right">$<?php echo number_format($tax,2,'.',','); ?></td></tr>
                <tr><td>Descuento</td><td style="text-align:right">-$<?php echo number_format($discount,2,'.',','); ?></td></tr>
                <tr><td>Total</td><td style="text-align:right">$<?php echo number_format($total,2,'.',','); ?></td></tr>
            </table>
        </div>
    </div>
    <?php if ($shouldPrint): ?><script>window.print();</script><?php endif; ?>
</body>
</html>

