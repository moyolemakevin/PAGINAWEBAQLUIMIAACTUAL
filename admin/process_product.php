<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit();
}

// Verificar token CSRF
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['error' => 'Token CSRF inválido']);
    exit();
}

$product = new Product();
$category = new Category();
$response = ['success' => false, 'message' => ''];

try {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $name = cleanInput($_POST['name'] ?? '');
            $description = cleanInput($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $category_id = (int)($_POST['category_id'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $featured = isset($_POST['featured']) ? (int)$_POST['featured'] : 0;
            $discount_percentage = (float)($_POST['discount_percentage'] ?? 0);
            $images_json = $_POST['images_json'] ?? '[]';

            // Validar JSON de imágenes
            $images = json_decode($images_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $images = [];
            }
            
            // Validaciones
            if (empty($name) || empty($description)) {
                throw new Exception('Nombre y descripción son obligatorios');
            }
            
            if ($price <= 0) {
                throw new Exception('El precio debe ser mayor a 0');
            }
            
            if ($category_id <= 0) {
                throw new Exception('Debe seleccionar una categoría');
            }
            
            if ($stock < 0) {
                throw new Exception('El stock no puede ser negativo');
            }
            
            if ($discount_percentage < 0 || $discount_percentage > 100) {
                throw new Exception('El descuento debe estar entre 0 y 100%');
            }
            
            // Verificar si la categoría existe
            if (!$category->getCategoryById($category_id)) {
                throw new Exception('Categoría no válida');
            }

            $productData = [
                'name' => $name,
                'description' => $description,
                'short_description' => substr($description, 0, 150),
                'price' => $price,
                'category_id' => $category_id,
                'stock_quantity' => $stock,
                'is_featured' => $featured,
                'discount_price' => $discount_percentage > 0 ? $price * (1 - $discount_percentage / 100) : null,
                'images' => json_encode($images),
                'slug' => generateSlug($name),
                'sku' => 'SKU-' . uniqid()
            ];
            
            $productId = $product->createProduct($productData);
            
            if ($productId) {
                $response = [
                    'success' => true,
                    'message' => 'Producto creado correctamente',
                    'product_id' => $productId
                ];
            } else {
                throw new Exception('Error al crear el producto');
            }
            break;
            
        case 'update':
            $productId = (int)($_POST['id'] ?? 0);
            $name = cleanInput($_POST['name'] ?? '');
            $description = cleanInput($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $category_id = (int)($_POST['category_id'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $featured = isset($_POST['featured']) ? (int)$_POST['featured'] : 0;
            $discount_percentage = (float)($_POST['discount_percentage'] ?? 0);
            $images_json = $_POST['images_json'] ?? '[]';
            
            // Validar JSON de imágenes
            $images = json_decode($images_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $images = [];
            }
            
            if ($productId <= 0) {
                throw new Exception('ID de producto inválido');
            }

            $existing = $product->getProductById($productId);
            if (!$existing) {
                throw new Exception('Producto no encontrado');
            }
            
            if (empty($name) || empty($description)) {
                throw new Exception('Nombre y descripción son obligatorios');
            }
            
            if ($price <= 0) {
                throw new Exception('El precio debe ser mayor a 0');
            }
            
            if ($category_id <= 0) {
                throw new Exception('Debe seleccionar una categoría');
            }
            
            if ($stock < 0) {
                throw new Exception('El stock no puede ser negativo');
            }
            
            if ($discount_percentage < 0 || $discount_percentage > 100) {
                throw new Exception('El descuento debe estar entre 0 y 100%');
            }
            
            // Verificar si la categoría existe
            if (!$category->getCategoryById($category_id)) {
                throw new Exception('Categoría no válida');
            }
            
            $productData = [
                'name' => $name,
                'description' => $description,
                'short_description' => substr($description, 0, 150),
                'price' => $price,
                'category_id' => $category_id,
                'stock_quantity' => $stock,
                'is_featured' => $featured,
                'discount_price' => $discount_percentage > 0 ? $price * (1 - $discount_percentage / 100) : null,
                'images' => json_encode($images),
                'slug' => generateSlug($name),
                // Conservar el SKU actual
                'sku' => $existing['sku']
            ];
            
            $result = $product->updateProduct($productId, $productData);
            
            if ($result) {
                $response = [
                    'success' => true,
                    'message' => 'Producto actualizado correctamente'
                ];
            } else {
                throw new Exception('Error al actualizar el producto');
            }
            break;
            
        case 'delete':
            $productId = (int)($_POST['id'] ?? 0);
            
            if ($productId <= 0) {
                throw new Exception('ID de producto inválido');
            }
            
            $result = $product->deleteProduct($productId);
            
            if ($result) {
                $response = [
                    'success' => true,
                    'message' => 'Producto eliminado correctamente'
                ];
            } else {
                throw new Exception('Error al eliminar el producto');
            }
            break;
            
        case 'get_product':
            $productId = (int)($_POST['id'] ?? 0);
            
            if ($productId <= 0) {
                throw new Exception('ID de producto inválido');
            }
            
            $productData = $product->getProductById($productId);
            
            if ($productData) {
                $response = [
                    'success' => true,
                    'product' => $productData
                ];
            } else {
                throw new Exception('Producto no encontrado');
            }
            break;
            
        case 'toggle_featured':
            $productId = (int)($_POST['id'] ?? 0);
            
            if ($productId <= 0) {
                throw new Exception('ID de producto inválido');
            }
            
            $result = $product->toggleFeatured($productId);
            
            if ($result !== false) {
                $response = [
                    'success' => true,
                    'message' => 'Estado de destacado actualizado',
                    'featured' => $result
                ];
            } else {
                throw new Exception('Error al actualizar el estado');
            }
            break;
            
        case 'update_stock':
            $productId = (int)($_POST['id'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            
            if ($productId <= 0) {
                throw new Exception('ID de producto inválido');
            }
            
            if ($stock < 0) {
                throw new Exception('El stock no puede ser negativo');
            }
            
            $result = $product->updateStock($productId, $stock);
            
            if ($result) {
                $response = [
                    'success' => true,
                    'message' => 'Stock actualizado correctamente'
                ];
            } else {
                throw new Exception('Error al actualizar el stock');
            }
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?> 