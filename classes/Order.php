<?php
require_once __DIR__ . '/../config/config.php';

class Order {
    private $pdo;
    
    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }
    
    /**
     * Obtener todos los pedidos almacenados.
     */
    public function getAllOrders() {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    o.*,
                    o.total_amount AS total,
                    TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS user_name,
                    u.email AS user_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY COALESCE(o.created_at, o.id) DESC
            ");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($orders as &$order) {
                if (empty(trim($order['user_name'] ?? ''))) {
                    $order['user_name'] = $order['user_email'] ?? 'Cliente';
                }
            }
            return $orders;
        } catch (PDOException $e) {
            return $this->getMockOrders();
        }
    }
    
    /**
     * Obtener un pedido por ID.
     */
    public function getOrderById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    o.*,
                    o.total_amount AS total,
                    TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS user_name,
                    u.email AS user_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ?
            ");
            $stmt->execute([$id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($order && empty(trim($order['user_name'] ?? ''))) {
                $order['user_name'] = $order['user_email'] ?? 'Cliente';
            }
            return $order;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Obtener pedidos de un usuario concreto.
     */
    public function getOrdersByUserId($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    o.*,
                    o.total_amount AS total
                FROM orders o
                WHERE o.user_id = ? 
                ORDER BY COALESCE(o.created_at, o.id) DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Crear un nuevo pedido con sus ítems asociados.
     */
    public function createOrder($userId, $total, $items, $status = 'pending') {
        try {
            $this->pdo->beginTransaction();

            $subtotal = 0;
            foreach ($items as $item) {
                $price = isset($item['price']) ? (float)$item['price'] : 0;
                $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
                $subtotal += $price * $quantity;
            }

            $orderNumber = $this->generateOrderNumber();
            $taxAmount = 0;
            $shippingAmount = 0;
            $discountAmount = max(0, $subtotal - $total);

            $stmt = $this->pdo->prepare("
                INSERT INTO orders (
                    user_id,
                    order_number,
                    status,
                    total_amount,
                    subtotal,
                    tax_amount,
                    shipping_amount,
                    discount_amount,
                    payment_status,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                $orderNumber,
                $status,
                $total,
                $subtotal,
                $taxAmount,
                $shippingAmount,
                $discountAmount,
                'pending'
            ]);

            $orderId = (int)$this->pdo->lastInsertId();

            foreach ($items as $item) {
                $price = isset($item['price']) ? (float)$item['price'] : 0;
                $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;

                $stmt = $this->pdo->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, price, total) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $quantity,
                    $price,
                    $price * $quantity
                ]);
            }

            $this->pdo->commit();
            return $orderId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    
    /**
     * Actualizar el estado del pedido.
     */
    public function updateOrderStatus($orderId, $status) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?
            ");
            return $stmt->execute([$status, $orderId]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtener los ítems de un pedido.
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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Obtener estadísticas básicas de pedidos.
     */
    public function getOrderStats() {
        try {
            $stats = [];

            $stmt = $this->pdo->query("SELECT COUNT(*) AS total_orders FROM orders");
            $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'] ?: 0;

            $stmt = $this->pdo->query("
                SELECT status, COUNT(*) AS count 
                FROM orders 
                GROUP BY status
            ");
            $stats['by_status'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->pdo->query("
                SELECT SUM(total_amount) AS total_sales 
                FROM orders 
                WHERE status != 'cancelled'
            ");
            $stats['total_sales'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?: 0;

            return $stats;
        } catch (PDOException $e) {
            return $this->getMockOrderStats();
        }
    }
    
    /**
     * Datos simulados para pedidos (fallback).
     */
    private function getMockOrders() {
        return [
            [
                'id' => 1,
                'user_id' => 1,
                'user_name' => 'Juan Perez',
                'user_email' => 'juan@ejemplo.com',
                'total' => 250.00,
                'status' => 'pending',
                'created_at' => '2025-01-15 10:30:00'
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'user_name' => 'Maria Gonzalez',
                'user_email' => 'maria@ejemplo.com',
                'total' => 180.00,
                'status' => 'processing',
                'created_at' => '2025-01-14 15:45:00'
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'user_name' => 'Carlos Rodriguez',
                'user_email' => 'carlos@ejemplo.com',
                'total' => 99.00,
                'status' => 'delivered',
                'created_at' => '2025-01-13 09:20:00'
            ],
            [
                'id' => 4,
                'user_id' => 4,
                'user_name' => 'Ana Martinez',
                'user_email' => 'ana@ejemplo.com',
                'total' => 45.00,
                'status' => 'shipped',
                'created_at' => '2025-01-12 14:15:00'
            ],
            [
                'id' => 5,
                'user_id' => 5,
                'user_name' => 'Luis Fernandez',
                'user_email' => 'luis@ejemplo.com',
                'total' => 120.00,
                'status' => 'pending',
                'created_at' => '2025-01-11 11:30:00'
            ]
        ];
    }
    
    /**
     * Estadísticas simuladas (fallback).
     */
    private function getMockOrderStats() {
        return [
            'total' => 5,
            'total_sales' => 644.00,
            'by_status' => [
                ['status' => 'pending', 'count' => 2],
                ['status' => 'processing', 'count' => 1],
                ['status' => 'shipped', 'count' => 1],
                ['status' => 'delivered', 'count' => 1]
            ]
        ];
    }
    
    /**
     * Eliminar un pedido y sus ítems asociados.
     */
    public function deleteOrder($orderId) {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->execute([$orderId]);

            $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    
    /**
     * Obtener los pedidos más recientes.
     */
    public function getRecentOrders($limit = 5) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    o.*,
                    o.total_amount AS total,
                    TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS user_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY COALESCE(o.created_at, o.id) DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($orders as &$order) {
                if (empty(trim($order['user_name'] ?? ''))) {
                    $order['user_name'] = 'Cliente';
                }
            }
            return $orders;
        } catch (PDOException $e) {
            return array_slice($this->getMockOrders(), 0, $limit);
        }
    }

    /**
     * Generar un código de pedido único.
     */
    private function generateOrderNumber() {
        return 'ALQ-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}
?> 
