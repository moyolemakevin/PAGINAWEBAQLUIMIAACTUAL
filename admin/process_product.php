<?php
require_once __DIR__ . '/../config/bootstrap.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit();
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['error' => 'Token CSRF invalido']);
    exit();
}

$product = new Product();
$category = new Category();
$response = ['success' => false, 'message' => ''];

function decodeAndSanitizeProductImages($imagesJson) {
    $decoded = json_decode((string) $imagesJson, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }

    return sanitizeStoredImages($decoded);
}

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            $name = cleanInput($_POST['name'] ?? '');
            $description = cleanInput($_POST['description'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $stock = (int) ($_POST['stock'] ?? 0);
            $featured = isset($_POST['featured']) ? (int) $_POST['featured'] : 0;
            $discountPercentage = (float) ($_POST['discount_percentage'] ?? 0);
            $images = decodeAndSanitizeProductImages($_POST['images_json'] ?? '[]');

            if ($name === '' || $description === '') {
                throw new Exception('Nombre y descripcion son obligatorios');
            }

            if ($price <= 0) {
                throw new Exception('El precio debe ser mayor a 0');
            }

            if ($categoryId <= 0 || !$category->getCategoryById($categoryId)) {
                throw new Exception('Categoria invalida');
            }

            if ($stock < 0) {
                throw new Exception('El stock no puede ser negativo');
            }

            if ($discountPercentage < 0 || $discountPercentage > 100) {
                throw new Exception('El descuento debe estar entre 0 y 100%');
            }

            $productData = [
                'name' => $name,
                'description' => $description,
                'short_description' => substr($description, 0, 150),
                'price' => $price,
                'category_id' => $categoryId,
                'stock_quantity' => $stock,
                'is_featured' => $featured,
                'discount_price' => $discountPercentage > 0 ? $price * (1 - $discountPercentage / 100) : null,
                'images' => json_encode($images),
                'slug' => generateSlug($name),
                'sku' => 'SKU-' . uniqid()
            ];

            $productId = $product->createProduct($productData);
            if (!$productId) {
                throw new Exception('Error al crear el producto');
            }

            $response = [
                'success' => true,
                'message' => 'Producto creado correctamente',
                'product_id' => $productId
            ];
            break;

        case 'update':
            $productId = (int) ($_POST['id'] ?? 0);
            $name = cleanInput($_POST['name'] ?? '');
            $description = cleanInput($_POST['description'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $stock = (int) ($_POST['stock'] ?? 0);
            $featured = isset($_POST['featured']) ? (int) $_POST['featured'] : 0;
            $discountPercentage = (float) ($_POST['discount_percentage'] ?? 0);
            $images = decodeAndSanitizeProductImages($_POST['images_json'] ?? '[]');

            if ($productId <= 0) {
                throw new Exception('ID de producto invalido');
            }

            $existing = $product->getProductById($productId);
            if (!$existing) {
                throw new Exception('Producto no encontrado');
            }

            if ($name === '' || $description === '') {
                throw new Exception('Nombre y descripcion son obligatorios');
            }

            if ($price <= 0) {
                throw new Exception('El precio debe ser mayor a 0');
            }

            if ($categoryId <= 0 || !$category->getCategoryById($categoryId)) {
                throw new Exception('Categoria invalida');
            }

            if ($stock < 0) {
                throw new Exception('El stock no puede ser negativo');
            }

            if ($discountPercentage < 0 || $discountPercentage > 100) {
                throw new Exception('El descuento debe estar entre 0 y 100%');
            }

            $productData = [
                'name' => $name,
                'description' => $description,
                'short_description' => substr($description, 0, 150),
                'price' => $price,
                'category_id' => $categoryId,
                'stock_quantity' => $stock,
                'is_featured' => $featured,
                'discount_price' => $discountPercentage > 0 ? $price * (1 - $discountPercentage / 100) : null,
                'images' => json_encode($images),
                'slug' => generateSlug($name),
                'sku' => $existing['sku']
            ];

            if (!$product->updateProduct($productId, $productData)) {
                throw new Exception('Error al actualizar el producto');
            }

            $response = [
                'success' => true,
                'message' => 'Producto actualizado correctamente'
            ];
            break;

        case 'delete':
            $productId = (int) ($_POST['id'] ?? 0);
            if ($productId <= 0) {
                throw new Exception('ID de producto invalido');
            }

            if (!$product->deleteProduct($productId)) {
                throw new Exception('Error al eliminar el producto');
            }

            $response = [
                'success' => true,
                'message' => 'Producto eliminado correctamente'
            ];
            break;

        case 'get_product':
            $productId = (int) ($_POST['id'] ?? 0);
            if ($productId <= 0) {
                throw new Exception('ID de producto invalido');
            }

            $productData = $product->getProductById($productId);
            if (!$productData) {
                throw new Exception('Producto no encontrado');
            }

            $productData['images'] = json_encode(sanitizeStoredImages($productData['images'] ?? '[]'));
            $response = [
                'success' => true,
                'product' => $productData
            ];
            break;

        case 'toggle_featured':
            $productId = (int) ($_POST['id'] ?? 0);
            if ($productId <= 0) {
                throw new Exception('ID de producto invalido');
            }

            $result = $product->toggleFeatured($productId);
            if ($result === false) {
                throw new Exception('Error al actualizar el estado');
            }

            $response = [
                'success' => true,
                'message' => 'Estado de destacado actualizado',
                'featured' => $result
            ];
            break;

        case 'update_stock':
            $productId = (int) ($_POST['id'] ?? 0);
            $stock = (int) ($_POST['stock'] ?? 0);

            if ($productId <= 0) {
                throw new Exception('ID de producto invalido');
            }

            if ($stock < 0) {
                throw new Exception('El stock no puede ser negativo');
            }

            if (!$product->updateStock($productId, $stock)) {
                throw new Exception('Error al actualizar el stock');
            }

            $response = [
                'success' => true,
                'message' => 'Stock actualizado correctamente'
            ];
            break;

        default:
            throw new Exception('Accion no valida');
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
