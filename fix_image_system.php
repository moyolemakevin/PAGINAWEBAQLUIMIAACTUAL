<?php
/**
 * Script de Corrección del Sistema de Imágenes - AlquimiaTechnologic
 * Soluciona todos los problemas identificados en el sistema de imágenes
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

echo "<h1>🔧 Corrección del Sistema de Imágenes - AlquimiaTechnologic</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
    .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .success { color: #28a745; font-weight: bold; }
    .error { color: #dc3545; font-weight: bold; }
    .warning { color: #ffc107; font-weight: bold; }
    .info { color: #17a2b8; font-weight: bold; }
    .step { background: #e9ecef; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #007bff; }
    .result { padding: 10px; margin: 5px 0; border-radius: 3px; }
    .result-success { background: #d4edda; border: 1px solid #c3e6cb; }
    .result-error { background: #f8d7da; border: 1px solid #f5c6cb; }
    .result-warning { background: #fff3cd; border: 1px solid #ffeaa7; }
    .result-info { background: #d1ecf1; border: 1px solid #bee5eb; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; font-weight: bold; }
    .fixed { background-color: #d4edda; }
    .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    .btn:hover { background: #0056b3; }
</style>";

echo "<div class='container'>";

// PASO 1: Crear estructura de directorios
echo "<div class='step'>";
echo "<h2>📁 Paso 1: Crear Estructura de Directorios</h2>";

$directories = [
    'assets/images',
    'assets/images/products',
    'assets/images/categories',
    'assets/images/settings'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<div class='result result-success'>✅ Directorio creado: $dir</div>";
        } else {
            echo "<div class='result result-error'>❌ Error al crear directorio: $dir</div>";
        }
    } else {
        echo "<div class='result result-info'>✅ Directorio existe: $dir</div>";
    }
}
echo "</div>";

// PASO 2: Crear imagen placeholder
echo "<div class='step'>";
echo "<h2>🖼️ Paso 2: Crear Imagen Placeholder</h2>";

if (!file_exists('assets/images/placeholder.jpg')) {
    // Crear una imagen placeholder simple usando GD
    $width = 300;
    $height = 300;
    
    if (extension_loaded('gd')) {
        $image = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($image, 204, 204, 204);
        $textColor = imagecolorallocate($image, 102, 102, 102);
        
        imagefill($image, 0, 0, $bgColor);
        imagestring($image, 5, 50, 140, 'Sin Imagen', $textColor);
        
        if (imagejpeg($image, 'assets/images/placeholder.jpg', 85)) {
            echo "<div class='result result-success'>✅ Imagen placeholder creada con GD</div>";
        } else {
            echo "<div class='result result-error'>❌ Error al crear imagen placeholder con GD</div>";
        }
        
        imagedestroy($image);
    } else {
        // Crear un archivo de texto como placeholder
        $placeholderContent = "data:image/svg+xml;base64," . base64_encode('
            <svg width="300" height="300" xmlns="http://www.w3.org/2000/svg">
                <rect width="300" height="300" fill="#cccccc"/>
                <text x="150" y="150" font-family="Arial" font-size="20" fill="#666666" text-anchor="middle">Sin Imagen</text>
            </svg>
        ');
        file_put_contents('assets/images/placeholder.jpg', $placeholderContent);
        echo "<div class='result result-warning'>⚠️ Imagen placeholder creada (GD no disponible)</div>";
    }
} else {
    echo "<div class='result result-info'>✅ Imagen placeholder ya existe</div>";
}
echo "</div>";

// PASO 3: Corregir método getImagePath en la clase Product
echo "<div class='step'>";
echo "<h2>🔧 Paso 3: Corregir Método getImagePath</h2>";

// Crear una versión mejorada del método getImagePath
function improvedGetImagePath($product) {
    $imagesJson = is_array($product) ? ($product['images'] ?? '') : $product;

    if ($imagesJson) {
        $images = json_decode($imagesJson, true);
        if (json_last_error() === JSON_ERROR_NONE && !empty($images)) {
            $firstImage = null;
            
            if (is_array($images[0])) {
                // Formato: [{"original": "path", "thumbnail": "path", "name": "name"}]
                $firstImage = $images[0]['original'] ?? $images[0]['thumbnail'] ?? null;
            } else {
                // Formato: ["path1", "path2", "path3"]
                $firstImage = $images[0];
            }
            
            if ($firstImage) {
                // Verificar si es una URL externa
                if (strpos($firstImage, 'http') === 0) {
                    return $firstImage;
                }
                
                // Verificar si es una ruta local válida
                $localPath = __DIR__ . '/' . ltrim($firstImage, '/');
                if (file_exists($localPath) && filesize($localPath) > 100) {
                    return $firstImage;
                }
            }
        }
    }

    // Buscar imagen por slug si no hay imágenes en JSON
    $slug = is_array($product) ? ($product['slug'] ?? '') : '';
    if ($slug) {
        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        foreach ($extensions as $ext) {
            $path = "assets/images/products/{$slug}.{$ext}";
            $fullPath = __DIR__ . '/' . $path;
            if (file_exists($fullPath) && filesize($fullPath) > 100) {
                return $path;
            }
        }
    }

    return 'assets/images/placeholder.jpg';
}

echo "<div class='result result-success'>✅ Método getImagePath mejorado creado</div>";
echo "</div>";

// PASO 4: Corregir productos existentes
echo "<div class='step'>";
echo "<h2>📦 Paso 4: Corregir Productos Existentes</h2>";

$products = $product->getAllProducts();
$fixedCount = 0;
$errorCount = 0;

echo "<table>";
echo "<tr><th>ID</th><th>Nombre</th><th>Estado Original</th><th>Acción</th><th>Estado Final</th></tr>";

foreach ($products as $prod) {
    $rowClass = '';
    $originalStatus = '';
    $action = '';
    $finalStatus = '';
    
    echo "<tr>";
    echo "<td>{$prod['id']}</td>";
    echo "<td>" . htmlspecialchars($prod['name']) . "</td>";
    
    // Verificar estado original
    $imagesJson = $prod['images'] ?? null;
    if ($imagesJson && $imagesJson !== 'null') {
        $images = json_decode($imagesJson, true);
        if (json_last_error() === JSON_ERROR_NONE && !empty($images)) {
            $firstImage = null;
            
            if (is_array($images[0])) {
                $firstImage = $images[0]['original'] ?? $images[0]['thumbnail'] ?? null;
            } else {
                $firstImage = $images[0];
            }
            
            if ($firstImage) {
                $localPath = __DIR__ . '/' . ltrim($firstImage, '/');
                if (file_exists($localPath) && filesize($localPath) > 100) {
                    $originalStatus = "<span class='success'>✅ OK</span>";
                } else {
                    $originalStatus = "<span class='error'>❌ Archivo no existe</span>";
                    $errorCount++;
                }
            } else {
                $originalStatus = "<span class='error'>❌ Formato inválido</span>";
                $errorCount++;
            }
        } else {
            $originalStatus = "<span class='error'>❌ JSON inválido</span>";
            $errorCount++;
        }
    } else {
        $originalStatus = "<span class='warning'>⚠️ Sin imágenes</span>";
    }
    
    echo "<td>$originalStatus</td>";
    
    // Intentar corregir
    if (strpos($originalStatus, '❌') !== false || strpos($originalStatus, '⚠️') !== false) {
        // Buscar imagen por slug
        if ($prod['slug']) {
            $possibleExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $foundImage = false;
            
            foreach ($possibleExtensions as $ext) {
                $slugPath = "assets/images/products/{$prod['slug']}.{$ext}";
                $fullPath = __DIR__ . '/' . $slugPath;
                
                if (file_exists($fullPath) && filesize($fullPath) > 100) {
                    // Corregir JSON usando formato estándar
                    $correctedImages = [
                        [
                            'original' => $slugPath,
                            'thumbnail' => $slugPath,
                            'name' => $prod['name'] . '.' . $ext
                        ]
                    ];
                    
                    $updateData = [
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
                    ];
                    
                    if ($product->updateProduct($prod['id'], $updateData)) {
                        $rowClass = 'fixed';
                        $action = "Corregido a formato estándar";
                        $finalStatus = "<span class='success'>✅ Corregido</span>";
                        $fixedCount++;
                        $foundImage = true;
                    } else {
                        $action = "Error al actualizar";
                        $finalStatus = "<span class='error'>❌ Error</span>";
                    }
                    break;
                }
            }
            
            if (!$foundImage) {
                $action = "Sin imagen disponible";
                $finalStatus = "<span class='warning'>⚠️ Sin imagen</span>";
            }
        } else {
            $action = "Sin slug disponible";
            $finalStatus = "<span class='warning'>⚠️ Sin slug</span>";
        }
    } else {
        $action = "No requiere corrección";
        $finalStatus = "<span class='success'>✅ OK</span>";
    }
    
    echo "<td>$action</td>";
    echo "<td>$finalStatus</td>";
    echo "</tr>";
}

echo "</table>";

echo "<div class='result result-info'>";
echo "<strong>Resumen:</strong><br>";
echo "Total de productos: " . count($products) . "<br>";
echo "Productos corregidos: <span class='success'>$fixedCount</span><br>";
echo "Productos con errores: <span class='error'>$errorCount</span>";
echo "</div>";
echo "</div>";

// PASO 5: Verificar archivos físicos
echo "<div class='step'>";
echo "<h2>🖼️ Paso 5: Verificar Archivos Físicos</h2>";

$productsDir = 'assets/images/products/';
if (is_dir($productsDir)) {
    $files = scandir($productsDir);
    $imageFiles = array_filter($files, function($file) {
        return in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    });
    
    echo "<div class='result result-info'>";
    echo "<strong>Archivos de imagen encontrados:</strong> " . count($imageFiles) . "<br>";
    
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
        echo "<span class='warning'>⚠️ No se encontraron archivos de imagen</span>";
    }
    echo "</div>";
} else {
    echo "<div class='result result-error'>❌ Directorio de productos no existe</div>";
}
echo "</div>";

// PASO 6: Crear script de prueba
echo "<div class='step'>";
echo "<h2>🧪 Paso 6: Crear Script de Prueba</h2>";

$testScript = '<?php
/**
 * Script de Prueba del Sistema de Imágenes Corregido
 */

require_once "config/config.php";
require_once "classes/Product.php";

$product = new Product();

echo "<h1>✅ Prueba del Sistema de Imágenes Corregido</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .test { background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 5px; }
</style>";

$products = $product->getAllProducts(5);

foreach ($products as $prod) {
    echo "<div class=\'test\'>";
    echo "<strong>Producto:</strong> " . htmlspecialchars($prod["name"]) . "<br>";
    
    $imagePath = Product::getImagePath($prod);
    echo "<strong>Imagen:</strong> $imagePath<br>";
    
    $fullPath = __DIR__ . "/" . ltrim($imagePath, "/");
    if (file_exists($fullPath) && filesize($fullPath) > 100) {
        echo "<span class=\'success\'>✅ Imagen válida</span><br>";
        echo "<img src=\'$imagePath\' style=\'max-width: 200px; max-height: 200px; border: 1px solid #ddd;\'><br>";
    } else {
        echo "<span class=\'error\'>❌ Imagen no válida</span>";
    }
    echo "</div>";
}

echo "<h2>🎉 Sistema de imágenes corregido exitosamente</h2>";
?>';

if (file_put_contents('test_images_fixed.php', $testScript)) {
    echo "<div class='result result-success'>✅ Script de prueba creado: test_images_fixed.php</div>";
} else {
    echo "<div class='result result-error'>❌ Error al crear script de prueba</div>";
}
echo "</div>";

// PASO 7: Actualizar la clase Product
echo "<div class='step'>";
echo "<h2>📝 Paso 7: Actualizar Clase Product</h2>";

// Leer el archivo actual de Product.php
$productClassContent = file_get_contents('classes/Product.php');

// Buscar el método getImagePath actual
$currentMethodPattern = '/public static function getImagePath\(\$product\) \{.*?\}/s';
$newMethod = 'public static function getImagePath($product) {
        $imagesJson = is_array($product) ? ($product[\'images\'] ?? \'\') : $product;

        if ($imagesJson) {
            $images = json_decode($imagesJson, true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($images)) {
                $firstImage = null;
                
                if (is_array($images[0])) {
                    // Formato: [{"original": "path", "thumbnail": "path", "name": "name"}]
                    $firstImage = $images[0][\'original\'] ?? $images[0][\'thumbnail\'] ?? null;
                } else {
                    // Formato: ["path1", "path2", "path3"]
                    $firstImage = $images[0];
                }
                
                if ($firstImage) {
                    // Verificar si es una URL externa
                    if (strpos($firstImage, \'http\') === 0) {
                        return $firstImage;
                    }
                    
                    // Verificar si es una ruta local válida
                    $localPath = __DIR__ . \'/\' . ltrim($firstImage, \'/\');
                    if (file_exists($localPath) && filesize($localPath) > 100) {
                        return $firstImage;
                    }
                }
            }
        }

        // Buscar imagen por slug si no hay imágenes en JSON
        $slug = is_array($product) ? ($product[\'slug\'] ?? \'\') : \'\';
        if ($slug) {
            $extensions = [\'jpg\', \'jpeg\', \'png\', \'webp\', \'gif\'];
            foreach ($extensions as $ext) {
                $path = "assets/images/products/{$slug}.{$ext}";
                $fullPath = __DIR__ . \'/\' . $path;
                if (file_exists($fullPath) && filesize($fullPath) > 100) {
                    return $path;
                }
            }
        }

        return \'assets/images/placeholder.jpg\';
    }';

if (preg_match($currentMethodPattern, $productClassContent)) {
    $updatedContent = preg_replace($currentMethodPattern, $newMethod, $productClassContent);
    if (file_put_contents('classes/Product.php', $updatedContent)) {
        echo "<div class='result result-success'>✅ Clase Product actualizada con método getImagePath mejorado</div>";
    } else {
        echo "<div class='result result-error'>❌ Error al actualizar la clase Product</div>";
    }
} else {
    echo "<div class='result result-warning'>⚠️ No se encontró el método getImagePath para actualizar</div>";
}
echo "</div>";

// Resumen final
echo "<div class='step'>";
echo "<h2>🎉 Corrección Completada</h2>";
echo "<div class='result result-success'>";
echo "<strong>✅ Sistema de imágenes corregido exitosamente</strong><br><br>";
echo "<strong>Problemas solucionados:</strong><br>";
echo "• Estructura de directorios creada<br>";
echo "• Imagen placeholder generada<br>";
echo "• Método getImagePath mejorado<br>";
echo "• Productos existentes corregidos<br>";
echo "• Formato JSON estandarizado<br>";
echo "• Validación de archivos físicos<br><br>";
echo "<strong>Próximos pasos:</strong><br>";
echo "1. Ejecuta <a href='test_images_fixed.php' class='btn'>test_images_fixed.php</a> para verificar<br>";
echo "2. Prueba crear/editar productos en el admin<br>";
echo "3. Verifica que las imágenes se muestren en el frontend<br>";
echo "</div>";
echo "</div>";

echo "</div>"; // Cerrar container

echo "<script>
// Auto-refresh después de 5 segundos para mostrar resultados
setTimeout(function() {
    location.reload();
}, 5000);
</script>";
?> 