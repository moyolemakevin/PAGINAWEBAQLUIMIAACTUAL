<?php
/**
 * Script de Depuración de Imágenes Público - AlquimiaTechnologic
 * Verifica y corrige problemas con las imágenes de productos (versión pública)
 */

require_once 'config/config.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';

$product = new Product();
$category = new Category();

echo "<h1>🔍 Depuración de Imágenes - AlquimiaTechnologic (Versión Pública)</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .debug { background: #f5f5f5; padding: 10px; margin: 10px 0; border-left: 4px solid #007bff; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; background: white; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .fixed { background-color: #d4edda; }
    .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #e9ecef; border-radius: 5px; }
</style>";

echo "<div class='container'>";

// 1. Verificar estructura de directorios
echo "<div class='section'>";
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
echo "</div>";

// 2. Verificar imagen placeholder
echo "<div class='section'>";
echo "<h2>🖼️ Verificación de Imagen Placeholder</h2>";
if (!file_exists('assets/images/placeholder.jpg')) {
    echo "<p class='error'>❌ Imagen placeholder no encontrada</p>";
    // Crear una imagen placeholder simple
    $placeholderContent = file_get_contents('https://via.placeholder.com/300x300/cccccc/666666?text=Sin+Imagen');
    if ($placeholderContent) {
        file_put_contents('assets/images/placeholder.jpg', $placeholderContent);
        echo "<p class='success'>✅ Imagen placeholder creada</p>";
    } else {
        echo "<p class='warning'>⚠️ No se pudo crear la imagen placeholder automáticamente</p>";
    }
} else {
    echo "<p class='success'>✅ Imagen placeholder existe</p>";
}
echo "</div>";

// 3. Analizar productos en la base de datos
echo "<div class='section'>";
echo "<h2>📦 Análisis de Productos</h2>";
$products = $product->getAllProducts();

echo "<table>";
echo "<tr><th>ID</th><th>Nombre</th><th>Slug</th><th>Imágenes JSON</th><th>Ruta Actual</th><th>Estado</th></tr>";

$fixedCount = 0;
$errorCount = 0;
$okCount = 0;

foreach ($products as $prod) {
    $rowClass = '';
    $status = '';
    
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
                    $okCount++;
                } else {
                    $status = "<span class='error'>❌ Archivo no existe o está vacío</span>";
                    $errorCount++;
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
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// 4. Resumen
echo "<div class='section'>";
echo "<h2>📊 Resumen</h2>";
echo "<p><strong>Total de productos:</strong> " . count($products) . "</p>";
echo "<p class='success'><strong>Productos con imágenes OK:</strong> $okCount</p>";
echo "<p class='error'><strong>Productos con errores:</strong> $errorCount</p>";
echo "</div>";

// 5. Verificar imágenes físicas
echo "<div class='section'>";
echo "<h2>🖼️ Verificación de Archivos Físicos</h2>";
$productsDir = 'assets/images/products/';
if (is_dir($productsDir)) {
    $files = scandir($productsDir);
    $imageFiles = array_filter($files, function($file) {
        return in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    });
    
    echo "<p><strong>Total de archivos de imagen:</strong> " . count($imageFiles) . "</p>";
    
    if (!empty($imageFiles)) {
        echo "<ul>";
        foreach ($imageFiles as $file) {
            $filePath = $productsDir . $file;
            $fileSize = filesize($filePath);
            $status = $fileSize > 100 ? "✅" : "❌";
            echo "<li>$status $file ($fileSize bytes)</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='warning'>⚠️ No se encontraron archivos de imagen</p>";
    }
} else {
    echo "<p class='error'>❌ Directorio de productos no existe</p>";
}
echo "</div>";

// 6. Verificar permisos de directorios
echo "<div class='section'>";
echo "<h2>🔐 Verificación de Permisos</h2>";
$directories = [
    'assets/images',
    'assets/images/products',
    'assets/images/categories',
    'assets/images/settings'
];

foreach ($directories as $dir) {
    if (file_exists($dir)) {
        $perms = fileperms($dir);
        $isWritable = is_writable($dir);
        $status = $isWritable ? "✅" : "❌";
        
        echo "<p>$status <strong>$dir:</strong> " . ($isWritable ? "Escribible" : "NO escribible") . " (Permisos: " . substr(sprintf('%o', $perms), -4) . ")</p>";
    } else {
        echo "<p class='error'>❌ <strong>$dir:</strong> No existe</p>";
    }
}
echo "</div>";

// 7. Recomendaciones
echo "<div class='section'>";
echo "<h2>💡 Recomendaciones</h2>";
echo "<ul>";
echo "<li>Verifica que las imágenes subidas tengan un tamaño mínimo de 100 bytes</li>";
echo "<li>Asegúrate de que los permisos de escritura estén configurados correctamente</li>";
echo "<li>Considera usar nombres de archivo basados en el slug del producto</li>";
echo "<li>Implementa validación de tipos de archivo en el frontend</li>";
echo "<li>Para corregir productos con errores, usa el panel de administración</li>";
echo "</ul>";
echo "</div>";

echo "<h2>✅ Depuración Completada</h2>";
echo "<p>El script ha verificado el estado actual de las imágenes de productos.</p>";
echo "<p><strong>Nota:</strong> Esta es la versión pública del script. Para correcciones automáticas, ejecuta <code>debug_images.php</code> desde el panel de administración.</p>";

echo "</div>";
?> 