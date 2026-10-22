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

$category = new Category();
$product = new Product();
$response = ['success' => false, 'message' => ''];

function decodeAndSanitizeCategoryImage($imageJson) {
    $decoded = json_decode((string) $imageJson, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        return null;
    }

    $images = sanitizeStoredImages($decoded, 1);
    if (empty($images)) {
        return null;
    }

    $first = $images[0];
    return is_array($first) ? ($first['original'] ?? ($first['thumbnail'] ?? null)) : $first;
}

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            $name = cleanInput($_POST['name'] ?? '');
            $description = cleanInput($_POST['description'] ?? '');
            $slug = generateSlug($name);
            $image = decodeAndSanitizeCategoryImage($_POST['image_json'] ?? '[]');

            if ($name === '') {
                throw new Exception('El nombre de la categoria es obligatorio');
            }

            if (strlen($name) < 2) {
                throw new Exception('El nombre debe tener al menos 2 caracteres');
            }

            if ($category->getCategoryByName($name)) {
                throw new Exception('Ya existe una categoria con ese nombre');
            }

            if ($category->slugExists($slug)) {
                throw new Exception('El slug ya esta en uso');
            }

            $categoryId = $category->createCategory([
                'name' => $name,
                'description' => $description,
                'image' => $image,
                'slug' => $slug
            ]);

            if (!$categoryId) {
                throw new Exception('Error al crear la categoria');
            }

            $response = [
                'success' => true,
                'message' => 'Categoria creada correctamente',
                'category_id' => $categoryId
            ];
            break;

        case 'update':
            $categoryId = (int) ($_POST['id'] ?? 0);
            $name = cleanInput($_POST['name'] ?? '');
            $description = cleanInput($_POST['description'] ?? '');
            $slug = cleanInput($_POST['slug'] ?? generateSlug($name));
            $image = decodeAndSanitizeCategoryImage($_POST['image_json'] ?? '[]');

            if ($categoryId <= 0) {
                throw new Exception('ID de categoria invalido');
            }

            if ($name === '') {
                throw new Exception('El nombre de la categoria es obligatorio');
            }

            if (strlen($name) < 2) {
                throw new Exception('El nombre debe tener al menos 2 caracteres');
            }

            $existing = $category->getCategoryById($categoryId);
            if (!$existing) {
                throw new Exception('Categoria no encontrada');
            }

            if ($image === null) {
                $image = sanitizeLocalImagePath($existing['image'] ?? '', true);
            }

            $existingCategory = $category->getCategoryByName($name);
            if ($existingCategory && $existingCategory['id'] != $categoryId) {
                throw new Exception('Ya existe otra categoria con ese nombre');
            }

            if ($category->slugExists($slug, $categoryId)) {
                throw new Exception('El slug ya esta en uso');
            }

            if (!$category->updateCategory($categoryId, [
                'name' => $name,
                'description' => $description,
                'image' => $image,
                'slug' => $slug
            ])) {
                throw new Exception('Error al actualizar la categoria');
            }

            $response = [
                'success' => true,
                'message' => 'Categoria actualizada correctamente'
            ];
            break;

        case 'delete':
            $categoryId = (int) ($_POST['id'] ?? 0);

            if ($categoryId <= 0) {
                throw new Exception('ID de categoria invalido');
            }

            $productCount = $product->getProductCountByCategory($categoryId);
            if ($productCount > 0) {
                throw new Exception("No se puede eliminar la categoria porque tiene $productCount productos asociados");
            }

            if (!$category->deleteCategory($categoryId)) {
                throw new Exception('Error al eliminar la categoria');
            }

            $response = [
                'success' => true,
                'message' => 'Categoria eliminada correctamente'
            ];
            break;

        case 'get_category':
            $categoryId = (int) ($_POST['id'] ?? 0);

            if ($categoryId <= 0) {
                throw new Exception('ID de categoria invalido');
            }

            $categoryData = $category->getCategoryById($categoryId);
            if (!$categoryData) {
                throw new Exception('Categoria no encontrada');
            }

            $categoryData['image'] = sanitizeLocalImagePath($categoryData['image'] ?? '', true) ?: null;
            $response = [
                'success' => true,
                'category' => $categoryData
            ];
            break;

        case 'toggle_active':
            $categoryId = (int) ($_POST['id'] ?? 0);

            if ($categoryId <= 0) {
                throw new Exception('ID de categoria invalido');
            }

            $result = $category->toggleActive($categoryId);
            if ($result === false) {
                throw new Exception('Error al actualizar el estado');
            }

            $response = [
                'success' => true,
                'message' => 'Estado de la categoria actualizado',
                'active' => $result
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
