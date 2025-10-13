<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Product.php';

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

$category = new Category();
$product = new Product();
$response = ['success' => false, 'message' => ''];

try {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $name = cleanInput($_POST['name'] ?? '');
            $description = cleanInput($_POST['description'] ?? '');
            $slug = generateSlug($name);
            $image_json = $_POST['image_json'] ?? '[]';
            $images = json_decode($image_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) { $images = []; }
            $image = isset($images[0]) ? $images[0] : null;
            
            // Validaciones
            if (empty($name)) {
                throw new Exception('El nombre de la categoría es obligatorio');
            }
            
            if (strlen($name) < 2) {
                throw new Exception('El nombre debe tener al menos 2 caracteres');
            }
            
            // Verificar si la categoría ya existe
            if ($category->getCategoryByName($name)) {
                throw new Exception('Ya existe una categoría con ese nombre');
            }
            if ($category->slugExists($slug)) {
                throw new Exception('El slug ya está en uso');
            }

            $catData = [
                'name' => $name,
                'description' => $description,
                'image' => $image,
                'slug' => $slug
            ];

            $categoryId = $category->createCategory($catData);
            
            if ($categoryId) {
                $response = [
                    'success' => true,
                    'message' => 'Categoría creada correctamente',
                    'category_id' => $categoryId
                ];
            } else {
                throw new Exception('Error al crear la categoría');
            }
            break;
            
        case 'update':
            $categoryId = (int)($_POST['id'] ?? 0);
            $name = cleanInput($_POST['name'] ?? '');
            $description = cleanInput($_POST['description'] ?? '');
            $slug = cleanInput($_POST['slug'] ?? generateSlug($name));
            $image_json = $_POST['image_json'] ?? '[]';
            $images = json_decode($image_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) { $images = []; }
            $image = isset($images[0]) ? $images[0] : null;
            if ($image === null) {
                $existing = $category->getCategoryById($categoryId);
                if ($existing && $existing['image']) {
                    $image = $existing['image'];
                }
            }
            
            if ($categoryId <= 0) {
                throw new Exception('ID de categoría inválido');
            }
            
            if (empty($name)) {
                throw new Exception('El nombre de la categoría es obligatorio');
            }
            
            if (strlen($name) < 2) {
                throw new Exception('El nombre debe tener al menos 2 caracteres');
            }
            
            // Verificar si el nombre ya existe en otra categoría
            $existingCategory = $category->getCategoryByName($name);
            if ($existingCategory && $existingCategory['id'] != $categoryId) {
                throw new Exception('Ya existe otra categoría con ese nombre');
            }
            if ($category->slugExists($slug, $categoryId)) {
                throw new Exception('El slug ya está en uso');
            }

            $catData = [
                'name' => $name,
                'description' => $description,
                'image' => $image,
                'slug' => $slug
            ];

            $result = $category->updateCategory($categoryId, $catData);
            
            if ($result) {
                $response = [
                    'success' => true,
                    'message' => 'Categoría actualizada correctamente'
                ];
            } else {
                throw new Exception('Error al actualizar la categoría');
            }
            break;
            
        case 'delete':
            $categoryId = (int)($_POST['id'] ?? 0);
            
            if ($categoryId <= 0) {
                throw new Exception('ID de categoría inválido');
            }
            
            // Verificar si hay productos en esta categoría
            $productCount = $product->getProductCountByCategory($categoryId);
            if ($productCount > 0) {
                throw new Exception("No se puede eliminar la categoría porque tiene $productCount productos asociados");
            }
            
            $result = $category->deleteCategory($categoryId);
            
            if ($result) {
                $response = [
                    'success' => true,
                    'message' => 'Categoría eliminada correctamente'
                ];
            } else {
                throw new Exception('Error al eliminar la categoría');
            }
            break;
            
        case 'get_category':
            $categoryId = (int)($_POST['id'] ?? 0);
            
            if ($categoryId <= 0) {
                throw new Exception('ID de categoría inválido');
            }
            
            $categoryData = $category->getCategoryById($categoryId);
            
            if ($categoryData) {
                $response = [
                    'success' => true,
                    'category' => $categoryData
                ];
            } else {
                throw new Exception('Categoría no encontrada');
            }
            break;
            
        case 'toggle_active':
            $categoryId = (int)($_POST['id'] ?? 0);
            
            if ($categoryId <= 0) {
                throw new Exception('ID de categoría inválido');
            }
            
            $result = $category->toggleActive($categoryId);
            
            if ($result !== false) {
                $response = [
                    'success' => true,
                    'message' => 'Estado de la categoría actualizado',
                    'active' => $result
                ];
            } else {
                throw new Exception('Error al actualizar el estado');
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