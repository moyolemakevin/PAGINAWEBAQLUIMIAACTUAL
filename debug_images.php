<?php
/**
 * Script de Depuración de Imágenes - AlquimiaTechnologic
 * Verifica y corrige problemas con las imágenes de productos
 */

require_once 'config/config.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    die('Acceso denegado. Solo administradores pueden ejecutar este script.');
}

$product = new Product();
$category = new Category();

echo "<h1>🔍 Depuración de Imágenes - AlquimiaTechnologic</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .debug { background: #f5f5f5; padding: 10px; margin: 10px 0; border-left: 4px solid #007bff; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .fixed { background-color: #d4edda; }
</style>";

// 1. Verificar estructura de directorios
echo "<h2>📁 Verificación de Directorios</h2>";
$directories = [
    'assets/images/products',
    'assets/images/categories',
    'assets/images/settings'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "<p class='warning'>📁 Directorio creado: $dir</p>";
    } else {
        echo "<p class='success'>✅ Directorio existe: $dir</p>";
    }
}

// 2. Verificar imagen placeholder
if (!file_exists('assets/images/placeholder.jpg')) {
    echo "<p class='error'>❌ Imagen placeholder no encontrada</p>";
    // Crear una imagen placeholder simple
    $placeholderContent = file_get_contents('https://via.placeholder.com/300x300/cccccc/666666?text=Sin+Imagen');
    if ($placeholderContent) {
        file_put_contents('assets/images/placeholder.jpg', $placeholderContent);
        echo "<p class='success'>✅ Imagen placeholder creada</p>";
    }
} else {
    echo "<p class='success'>✅ Imagen placeholder existe</p>";
}

// 3. Analizar productos en la base de datos
echo "<h2>📦 Análisis de Productos</h2>";
$products = $product->getAllProducts();

echo "<table>";
echo "<tr><th>ID</th><th>Nombre</th><th>Slug</th><th>Imágenes JSON</th><th>Ruta Actual</th><th>Estado</th><th>Acción</th></tr>";

$fixedCount = 0;
$errorCount = 0;

foreach ($products as $prod) {
    $rowClass = '';
    $status = '';
    $action = '';
    
    echo "<tr>";
    echo "<td>{$prod['id']}</td>";
    echo "<td>" . htmlspecialchars($prod['name']) . "</td>";
    echo "<td>" . htmlspecialchars($prod['slug']) . "</td>";
    
    // Mostrar JSON de imágenes
    $imagesJson = $prod['images'] ?? 'null';
    echo "<td><div class='debug'>" . htmlspecialchars(substr($imagesJson, 0, 100)) . "</div></td>";
    
    // Obtener ruta actual
    $currentPath = Product::getImagePath($prod);
    echo "<td>" . htmlspecialchars($currentPath) . "</td>";
    
    // Verificar estado de la imagen
    if ($imagesJson && $imagesJson !== 'null') {
        $images = json_decode($imagesJson, true);
        if (json_last_error() === JSON_ERROR_NONE && !empty($images)) {
            $firstImage = null;
            
            if (is_array($images[0])) {
                // Formato de objeto
                $firstImage = $images[0]['original'] ?? $images[0]['thumbnail'] ?? null;
            } else {
                // Formato de string
                $firstImage = $images[0];
            }
            
            if ($firstImage) {
                $localPath = __DIR__ . '/' . ltrim($firstImage, '/');
                if (file_exists($localPath) && filesize($localPath) > 100) {
                    $status = "<span class='success'>✅ OK</span>";
                } else {
                    $status = "<span class='error'>❌ Archivo no existe o está vacío</span>";
                    $errorCount++;
                    
                    // Intentar corregir
                    if ($prod['slug']) {
                        $possibleExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                        $foundImage = false;
                        
                        foreach ($possibleExtensions as $ext) {
                            $slugPath = "assets/images/products/{$prod['slug']}.{$ext}";
                            $fullPath = __DIR__ . '/' . $slugPath;
                            
                            if (file_exists($fullPath) && filesize($fullPath) > 100) {
                                // Corregir JSON
                                $correctedImages = [$slugPath];
                                $product->updateProduct($prod['id'], [
                                    'name' => $prod['name'],
                                    'description' => $prod['description'],
                                    'short_description' => $prod['short_description'],
                                    'price' => $prod['price'],
                                    'category_id' => $prod['category_id'],
                                    'stock_quantity' => $prod['stock_quantity'],
                                    'is_featured' => $prod['is_featured'],
                                    'images' => json_encode($correctedImages),
                                    'slug' => $prod['slug'],
                                    'sku' => $prod['sku']
                                ]);
                                
                                $rowClass = 'fixed';
                                $status = "<span class='success'>✅ Corregido</span>";
                                $action = "Corregido a: $slugPath";
                                $fixedCount++;
                                $foundImage = true;
                                break;
                            }
                        }
                        
                        if (!$foundImage) {
                            $action = "Sin imagen disponible";
                        }
                    }
                }
            } else {
                $status = "<span class='error'>❌ Formato inválido</span>";
                $errorCount++;
            }
        } else {
            $status = "<span class='error'>❌ JSON inválido</span>";
            $errorCount++;
        }
    } else {
        $status = "<span class='warning'>⚠️ Sin imágenes</span>";
    }
    
    echo "<td>$status</td>";
    echo "<td>$action</td>";
    echo "</tr>";
}

echo "</table>";

// 4. Resumen
echo "<h2>📊 Resumen</h2>";
echo "<p><strong>Total de productos:</strong> " . count($products) . "</p>";
echo "<p class='success'><strong>Productos corregidos:</strong> $fixedCount</p>";
echo "<p class='error'><strong>Productos con errores:</strong> $errorCount</p>";

// 5. Verificar imágenes físicas
echo "<h2>🖼️ Verificación de Archivos Físicos</h2>";
$productsDir = 'assets/images/products/';
if (is_dir($productsDir)) {
    $files = scandir($productsDir);
    $imageFiles = array_filter($files, function($file) {
        return in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    });
    
    echo "<p><strong>Archivos de imagen encontrados:</strong> " . count($imageFiles) . "</p>";
    
    if (!empty($imageFiles)) {
        echo "<ul>";
        foreach ($imageFiles as $file) {
            $filePath = $productsDir . $file;
            $fileSize = filesize($filePath);
            $status = $fileSize > 100 ? "✅" : "❌";
            echo "<li>$status $file ($fileSize bytes)</li>";
        }
        echo "</ul>";
    }
}

// 6. Recomendaciones
echo "<h2>💡 Recomendaciones</h2>";
echo "<ul>";
echo "<li>Verifica que las imágenes subidas tengan un tamaño mínimo de 100 bytes</li>";
echo "<li>Asegúrate de que los permisos de escritura estén configurados correctamente</li>";
echo "<li>Considera usar nombres de archivo basados en el slug del producto</li>";
echo "<li>Implementa validación de tipos de archivo en el frontend</li>";
echo "</ul>";

echo "<h2>✅ Depuración Completada</h2>";
echo "<p>El script ha verificado y corregido los problemas encontrados con las imágenes de productos.</p>";
?> 