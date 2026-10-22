<?php
require_once __DIR__ . '/config/bootstrap.php';

header('Content-Type: application/json');

try {
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $limit = (int) ($_GET['limit'] ?? 12);
    if ($limit <= 0) {
        $limit = 12;
    }
    if ($limit > 48) {
        $limit = 48;
    }

    $offset = ($page - 1) * $limit;
    $search = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
    $categoryFilter = isset($_GET['category']) ? (int) $_GET['category'] : 0;

    $productModel = new Product();
    $categoryModel = new Category();
    $products = [];
    $hasMore = false;

    if ($search !== '') {
        $rawResults = $productModel->searchProducts($search, $limit * $page + 1, true);
        $products = array_slice($rawResults, $offset, $limit);
        $hasMore = count($rawResults) > ($offset + count($products));
    } elseif ($categoryFilter > 0) {
        if (!$categoryModel->getCategoryById($categoryFilter, true)) {
            echo json_encode([
                'success' => true,
                'products' => [],
                'has_more' => false,
            ]);
            exit;
        }

        $rawResults = $productModel->getProductsByCategory($categoryFilter, $limit + 1, $offset, true);
        $hasMore = count($rawResults) > $limit;
        $products = array_slice($rawResults, 0, $limit);
    } else {
        $rawResults = $productModel->getAllProducts($limit + 1, $offset, true);
        $hasMore = count($rawResults) > $limit;
        $products = array_slice($rawResults, 0, $limit);
    }

    if (!is_array($products)) {
        $products = [];
    }

    foreach ($products as &$prod) {
        $prod['image'] = Product::getImagePath($prod);

        $price = isset($prod['price']) ? (float) $prod['price'] : 0.0;
        $discountPrice = null;
        if (isset($prod['discount_price']) && $prod['discount_price'] !== null) {
            $discountPrice = (float) $prod['discount_price'];
        }

        $discountPercentage = 0;
        if ($discountPrice !== null && $price > 0) {
            $discountPercentage = round((1 - ($discountPrice / $price)) * 100);
        } elseif (isset($prod['discount_percentage']) && is_numeric($prod['discount_percentage'])) {
            $discountPercentage = (float) $prod['discount_percentage'];
        }

        $prod['price'] = $price;
        $prod['discount_price'] = $discountPrice;
        $prod['discount_percentage'] = $discountPercentage;
        $prod['final_price'] = $discountPrice !== null ? $discountPrice : $price;
        $prod['category_name'] = $prod['category_name'] ?? '';
    }
    unset($prod);

    echo json_encode([
        'success' => true,
        'products' => $products,
        'has_more' => $hasMore,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener productos',
    ]);
}
