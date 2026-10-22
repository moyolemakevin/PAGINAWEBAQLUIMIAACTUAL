<?php
require_once __DIR__ . '/config/bootstrap.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID invalido']);
    exit;
}

$productModel = new Product();
$product = $productModel->getProductById($id, true);
if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    exit;
}

$image = Product::getImagePath($product);
$price = (float) ($product['price'] ?? 0);
$discountPrice = isset($product['discount_price']) && $product['discount_price'] !== null
    ? (float) $product['discount_price']
    : null;
$finalPrice = $discountPrice !== null ? $discountPrice : $price;
$discountPercentage = $discountPrice !== null && $price > 0
    ? round((1 - ($discountPrice / $price)) * 100)
    : 0;

$response = [
    'success' => true,
    'id' => $product['id'],
    'name' => $product['name'],
    'description' => $product['short_description'] ?: $product['description'],
    'price' => $price,
    'discount_price' => $discountPrice,
    'final_price' => $finalPrice,
    'discount_percentage' => $discountPercentage,
    'image' => $image,
];

echo json_encode($response);
