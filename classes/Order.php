<?php
require_once __DIR__ . '/../config/config.php';

class Order {
    private $pdo;
    private $lastError = '';
    
    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }
    
    /**
     * Obtener todos los pedidos
     */
    public function getAllOrders() {
        try {
            $stmt = $this->pdo->query("
                SELECT o.*, u.first_name, u.last_name, u.email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.id DESC
            ");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->normalizeOrders($orders);
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            // Si la tabla no existe, devolver datos simulados
            return $this->getMockOrders();
        }
    }

    /**
     * Obtener pedidos con filtros opcionales (status, rango de fechas, usuario)
     */
    public function getOrdersFiltered(array $filters = []) {
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = 'o.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = 'DATE(o.created_at) >= ?';
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = 'DATE(o.created_at) <= ?';
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['user_id'])) {
            $conditions[] = 'o.user_id = ?';
            $params[] = (int)$filters['user_id'];
        }

        $where = '';
        if ($conditions) {
            $where = 'WHERE ' . implode(' AND ', $conditions);
        }

        try {
            $sql = "
                SELECT o.*, u.first_name, u.last_name, u.email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                {$where}
                ORDER BY o.id DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->normalizeOrders($orders);
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            return [];
        }
    }

    public function getOrderCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM orders");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtener pedido por ID
     */
    public function getOrderById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT o.*, u.first_name, u.last_name, u.email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ?
            ");
            $stmt->execute([$id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$order) {
                return null;
            }
            return $this->normalizeOrder($order);
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener pedidos de un usuario
     */
    public function getOrdersByUserId($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM orders 
                WHERE user_id = ? 
                ORDER BY id DESC
            ");
            $stmt->execute([$userId]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->normalizeOrders($orders);
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Crear nuevo pedido con items y totales detallados.
     * $items: array de ['product_id' => int, 'quantity' => int, 'price' => float]
     * $meta:  array opcional con claves: status, subtotal, tax_amount, shipping_amount,
     *         discount_amount, total, payment_status, payment_method, shipping_address.
     */
    public function createOrder($userId, array $items, array $meta = []) {
        if (empty($items)) {
            return false;
        }

        try {
            $this->pdo->beginTransaction();

            // Validar stock y obtener precios actuales
            $productModel = new Product();
            foreach ($items as &$item) {
                $productId = (int)($item['product_id'] ?? 0);
                $qty       = max(1, (int)($item['quantity'] ?? 0));
                $product   = $productModel->getProductById($productId);
                if (!$product) {
                    throw new Exception('Producto no encontrado');
                }
                $stock = (int)($product['stock_quantity'] ?? 0);
                if ($stock < $qty) {
                    throw new Exception("Stock insuficiente para {$product['name']} ({$stock} disponibles)");
                }
                $price = (float)($product['price'] ?? 0);
                $item['price'] = $price;
                $item['quantity'] = $qty;
            }
            unset($item);

            // Normalizar montos
            $status          = $meta['status'] ?? 'pending';
            $paymentStatus   = $meta['payment_status'] ?? 'pending';
            $paymentMethod   = $meta['payment_method'] ?? null;
            $shippingAddress = $meta['shipping_address'] ?? null;

            $subtotal = 0.0;
            foreach ($items as &$item) {
                $qty   = max(1, (int)($item['quantity'] ?? 0));
                $price = (float)($item['price'] ?? 0);
                $item['quantity'] = $qty;
                $item['price']    = $price;
                $item['total']    = $price * $qty;
                $subtotal        += $item['total'];
            }
            unset($item);

            $tax      = isset($meta['tax_amount']) ? (float)$meta['tax_amount'] : 0.0;
            $shipping = isset($meta['shipping_amount']) ? (float)$meta['shipping_amount'] : 0.0;
            $discount = isset($meta['discount_amount']) ? (float)$meta['discount_amount'] : 0.0;
            $total    = isset($meta['total']) ? (float)$meta['total'] : ($subtotal + $tax + $shipping - $discount);

            $columns = $this->getOrderColumns();
            $fields  = [];
            $values  = [];

            if (in_array('user_id', $columns, true)) {
                $fields[] = 'user_id';
                $values[] = $userId;
            }

            if (in_array('status', $columns, true)) {
                $fields[] = 'status';
                $values[] = $status;
            }

            if (in_array('order_number', $columns, true)) {
                $fields[] = 'order_number';
                $values[] = $this->generateOrderNumber();
            }

            if (in_array('total_amount', $columns, true)) {
                $fields[] = 'total_amount';
                $values[] = $total;
            } elseif (in_array('total', $columns, true)) {
                $fields[] = 'total';
                $values[] = $total;
            }

            if (in_array('subtotal', $columns, true)) {
                $fields[] = 'subtotal';
                $values[] = $subtotal;
            }

            if (in_array('tax_amount', $columns, true)) {
                $fields[] = 'tax_amount';
                $values[] = $tax;
            }

            if (in_array('shipping_amount', $columns, true)) {
                $fields[] = 'shipping_amount';
                $values[] = $shipping;
            }

            if (in_array('discount_amount', $columns, true)) {
                $fields[] = 'discount_amount';
                $values[] = $discount;
            }

            if (in_array('payment_status', $columns, true)) {
                $fields[] = 'payment_status';
                $values[] = $paymentStatus;
            }

            if (in_array('payment_method', $columns, true)) {
                $fields[] = 'payment_method';
                $values[] = $paymentMethod;
            }

            if (in_array('shipping_address', $columns, true)) {
                $fields[] = 'shipping_address';
                $values[] = $shippingAddress;
            }

            if (in_array('created_at', $columns, true)) {
                $fields[] = 'created_at';
                $values[] = date('Y-m-d H:i:s');
            }

            $placeholders = implode(',', array_fill(0, count($fields), '?'));
            $stmt = $this->pdo->prepare(
                "INSERT INTO orders (" . implode(',', $fields) . ") VALUES (" . $placeholders . ")"
            );
            $stmt->execute($values);

            $orderId      = $this->pdo->lastInsertId();
            $itemColumns  = $this->getOrderItemColumns();
            $itemDateTime = date('Y-m-d H:i:s');

            foreach ($items as $item) {
                $itemFields = ['order_id', 'product_id', 'quantity', 'price'];
                $itemValues = [$orderId, $item['product_id'], $item['quantity'], $item['price']];

                if (in_array('total', $itemColumns, true)) {
                    $itemFields[] = 'total';
                    $itemValues[] = $item['total'];
                }

                if (in_array('created_at', $itemColumns, true)) {
                    $itemFields[] = 'created_at';
                    $itemValues[] = $itemDateTime;
                }

                $itemPlaceholders = implode(',', array_fill(0, count($itemFields), '?'));
                $itemSql = "INSERT INTO order_items (" . implode(',', $itemFields) . ") VALUES (" . $itemPlaceholders . ")";
                $itemStmt = $this->pdo->prepare($itemSql);
                $itemStmt->execute($itemValues);

                // Descontar stock
                $stockStmt = $this->pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?");
                $stockOk = $stockStmt->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
                if (!$stockOk || $stockStmt->rowCount() === 0) {
                    throw new Exception('No se pudo actualizar el stock');
                }
            }

            $this->pdo->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->lastError = $e->getMessage();
            return false;
        }
    }
    
    /**
     * Actualizar estado del pedido
     */
    public function updateOrderStatus($orderId, $status) {
        try {
            $columns = $this->getOrderColumns();
            $fields = ['status = ?'];
            $values = [$status];

            if (in_array('updated_at', $columns, true)) {
                $fields[] = 'updated_at = ?';
                $values[] = date('Y-m-d H:i:s');
            }
            if (in_array('status_changed_at', $columns, true)) {
                $fields[] = 'status_changed_at = ?';
                $values[] = date('Y-m-d H:i:s');
            }

            $setClause = implode(', ', $fields);
            $stmt = $this->pdo->prepare("UPDATE orders SET {$setClause} WHERE id = ?");
            $values[] = $orderId;
            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener items de un pedido
     */
    public function getOrderItems($orderId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT
                    oi.*,
                    p.name as product_name,
                    p.images as product_images,
                    p.slug as product_slug
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($items as &$item) {
                if (!isset($item['total'])) {
                    $qty = (int)($item['quantity'] ?? 0);
                    $price = (float)($item['price'] ?? 0);
                    $item['total'] = $qty * $price;
                }
            }
            unset($item);
            return $items;
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de pedidos
     */
    public function getOrderStats() {
        try {
            $orders = $this->getAllOrders();
            $stats = [
                'total' => count($orders),
                'total_sales' => 0,
                'by_status' => []
            ];

            $statusMap = [];
            foreach ($orders as $order) {
                $status = $order['status'] ?? 'pending';
                $statusMap[$status] = ($statusMap[$status] ?? 0) + 1;

                if ($status !== 'cancelled') {
                    $stats['total_sales'] += (float)($order['total'] ?? 0);
                }
            }

            foreach ($statusMap as $status => $count) {
                $stats['by_status'][] = ['status' => $status, 'count' => $count];
            }

            return $stats;
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            return $this->getMockOrderStats();
        }
    }

    public function getMonthlySales($months = 6) {
        $months = max(1, (int)$months);
        $start = new DateTime('first day of this month');
        $start->modify('-' . ($months - 1) . ' months');

        $monthTotals = [];
        for ($i = 0; $i < $months; $i++) {
            $monthTotals[$start->format('Y-m')] = 0.0;
            $start->modify('+1 month');
        }

        try {
            $orders = $this->getAllOrders();
            foreach ($orders as $order) {
                $status = $order['status'] ?? 'pending';
                if ($status === 'cancelled') {
                    continue;
                }

                $createdAt = $order['created_at'] ?? null;
                if (!$createdAt) {
                    continue;
                }

                $timestamp = strtotime($createdAt);
                if ($timestamp === false) {
                    continue;
                }

                $key = date('Y-m', $timestamp);
                if (!isset($monthTotals[$key])) {
                    continue;
                }

                $monthTotals[$key] += (float)($order['total'] ?? 0);
            }
        } catch (Exception $e) {
            // mantener ceros si falla
        }

        return [
            'months' => array_keys($monthTotals),
            'totals' => array_values($monthTotals)
        ];
    }

    public function getTopProducts($limit = 5) {
        $limit = max(1, (int)$limit);
        try {
            $stmt = $this->pdo->prepare("
                SELECT
                    p.id as product_id,
                    p.name as product_name,
                    SUM(oi.quantity) as total_quantity,
                    SUM(oi.quantity * oi.price) as total_revenue
                FROM order_items oi
                LEFT JOIN products p ON p.id = oi.product_id
                LEFT JOIN orders o ON o.id = oi.order_id
                WHERE o.id IS NOT NULL AND (o.status IS NULL OR o.status != 'cancelled')
                GROUP BY p.id, p.name
                ORDER BY total_quantity DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$row) {
                $row['total_quantity'] = (int)($row['total_quantity'] ?? 0);
                $row['total_revenue'] = (float)($row['total_revenue'] ?? 0);
            }
            unset($row);
            return $rows;
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Datos simulados para pedidos
     */
    private function getMockOrders() {
        return [
            [
                'id' => 1,
                'user_id' => 1,
                'user_name' => 'Juan Pérez',
                'user_email' => 'juan@ejemplo.com',
                'total' => 250000,
                'status' => 'pending',
                'created_at' => '2025-01-15 10:30:00'
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'user_name' => 'María González',
                'user_email' => 'maria@ejemplo.com',
                'total' => 180000,
                'status' => 'processing',
                'created_at' => '2025-01-14 15:45:00'
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'user_name' => 'Carlos Rodríguez',
                'user_email' => 'carlos@ejemplo.com',
                'total' => 99000,
                'status' => 'delivered',
                'created_at' => '2025-01-13 09:20:00'
            ],
            [
                'id' => 4,
                'user_id' => 4,
                'user_name' => 'Ana Martínez',
                'user_email' => 'ana@ejemplo.com',
                'total' => 45000,
                'status' => 'shipped',
                'created_at' => '2025-01-12 14:15:00'
            ],
            [
                'id' => 5,
                'user_id' => 5,
                'user_name' => 'Luis Fernández',
                'user_email' => 'luis@ejemplo.com',
                'total' => 120000,
                'status' => 'pending',
                'created_at' => '2025-01-11 11:30:00'
            ]
        ];
    }
    
    /**
     * Estadísticas simuladas
     */
    private function getMockOrderStats() {
        return [
            'total' => 5,
            'total_sales' => 644000,
            'by_status' => [
                ['status' => 'pending', 'count' => 2],
                ['status' => 'processing', 'count' => 1],
                ['status' => 'shipped', 'count' => 1],
                ['status' => 'delivered', 'count' => 1]
            ]
        ];
    }
    
    /**
     * Eliminar pedido
     */
    public function deleteOrder($orderId) {
        try {
            $this->pdo->beginTransaction();
            
            // Eliminar items del pedido
            $stmt = $this->pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->execute([$orderId]);
            
            // Eliminar pedido
            $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);
            
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            $this->pdo->rollBack();
            return false;
        }
    }
    
    /**
     * Obtener pedidos recientes
     */
    public function getRecentOrders($limit = 5) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT o.*, u.first_name, u.last_name, u.email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.id DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->normalizeOrders($orders);
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            return array_slice($this->getMockOrders(), 0, $limit);
        }
    }

    public function getOrderItemCounts($orderIds) {
        try {
            if (empty($orderIds)) {
                return [];
            }
            $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
            $stmt = $this->pdo->prepare("
                SELECT order_id, SUM(quantity) as item_count
                FROM order_items
                WHERE order_id IN ($placeholders)
                GROUP BY order_id
            ");
            $stmt->execute(array_values($orderIds));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $map = [];
            foreach ($rows as $row) {
                $map[$row['order_id']] = (int)$row['item_count'];
            }
            return $map;
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            return [];
        }
    }

    private function normalizeOrders($orders) {
        $normalized = [];
        foreach ($orders as $order) {
            $normalized[] = $this->normalizeOrder($order);
        }
        return $normalized;
    }

    private function normalizeOrder($order) {
        $subtotal = $order['subtotal'] ?? null;
        $tax = $order['tax_amount'] ?? 0;
        $shipping = $order['shipping_amount'] ?? 0;
        $discount = $order['discount_amount'] ?? 0;

        if ($subtotal === null && isset($order['total'])) {
            $subtotal = (float)$order['total'];
        }

        if ($subtotal === null && isset($order['total_amount'])) {
            $subtotal = (float)$order['total_amount'];
        }

        $total = $order['total'] ?? $order['total_amount'] ?? null;
        if ($total === null) {
            $subtotalCalc = $subtotal ?? 0;
            $total = (float)$subtotalCalc + (float)$tax + (float)$shipping - (float)$discount;
        }

        $firstName = $order['first_name'] ?? '';
        $lastName = $order['last_name'] ?? '';
        $userName = trim($firstName . ' ' . $lastName);
        if ($userName === '') {
            $userName = $order['user_name'] ?? '';
        }
        if ($userName === '' && !empty($order['shipping_address'])) {
            $userName = $order['shipping_address'];
        }

        $order['total'] = $total;
        $order['subtotal'] = $subtotal ?? 0;
        $order['tax_amount'] = (float)$tax;
        $order['shipping_amount'] = (float)$shipping;
        $order['discount_amount'] = (float)$discount;
        $order['order_number'] = $order['order_number'] ?? $order['id'];
        $order['created_at'] = $order['created_at'] ?? $order['updated_at'] ?? date('Y-m-d H:i:s');
        $order['user_name'] = $userName;
        $order['user_email'] = $order['email'] ?? ($order['user_email'] ?? null);

        return $order;
    }

    private function getOrderColumns() {
        static $columns = null;
        if ($columns !== null) {
            return $columns;
        }
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM orders");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $columns = array_map(function ($row) {
                return $row['Field'];
            }, $rows);
            return $columns;
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            $columns = [
                'id','user_id','order_number','status','total_amount','subtotal','tax_amount',
                'shipping_amount','discount_amount','payment_status','payment_method',
                'shipping_address','created_at','updated_at','total'
            ];
            return $columns;
        }
    }

    private function getOrderItemColumns() {
        static $columns = null;
        if ($columns !== null) {
            return $columns;
        }
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM order_items");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $columns = array_map(function ($row) {
                return $row['Field'];
            }, $rows);
            return $columns;
        } catch (PDOException $e) {
            error_log('Order: ' . $e->getMessage());
            $columns = ['order_id', 'product_id', 'quantity', 'price', 'total', 'created_at'];
            return $columns;
        }
    }

    private function generateOrderNumber() {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    public function getLastError() {
        return $this->lastError;
    }
}
?> 
