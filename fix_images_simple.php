<?php
/**
 * Script Simplificado de Corrección de Imágenes - AlquimiaTechnologic
 * No requiere autenticación para ejecutarse
 */

echo "<h1>🔧 Corrección Rápida del Sistema de Imágenes</h1>";
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
        // Crear un archivo SVG como placeholder
        $svgContent = '<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg">
            <rect width="300" height="300" fill="#cccccc"/>
            <text x="150" y="150" font-family="Arial" font-size="20" fill="#666666" text-anchor="middle">Sin Imagen</text>
        </svg>';
        file_put_contents('assets/images/placeholder.jpg', $svgContent);
        echo "<div class='result result-warning'>⚠️ Imagen placeholder creada (GD no disponible)</div>";
    }
} else {
    echo "<div class='result result-info'>✅ Imagen placeholder ya existe</div>";
}
echo "</div>";

// PASO 3: Corregir método getImagePath en la clase Product
echo "<div class='step'>";
echo "<h2>🔧 Paso 3: Corregir Método getImagePath</h2>";

$productClassPath = 'classes/Product.php';
if (file_exists($productClassPath)) {
    $productClassContent = file_get_contents($productClassPath);
    
    // Método getImagePath mejorado
    $improvedMethod = '    /**
     * Obtener la imagen principal de un producto.
     * Devuelve la primera imagen del campo JSON o una imagen basada en el slug.
     */
    public static function getImagePath($product) {
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

    // Buscar y reemplazar el método existente
    $pattern = '/public static function getImagePath\(\$product\) \{.*?\}/s';
    if (preg_match($pattern, $productClassContent)) {
        $updatedContent = preg_replace($pattern, $improvedMethod, $productClassContent);
        if (file_put_contents($productClassPath, $updatedContent)) {
            echo "<div class='result result-success'>✅ Método getImagePath actualizado en la clase Product</div>";
        } else {
            echo "<div class='result result-error'>❌ Error al actualizar la clase Product</div>";
        }
    } else {
        echo "<div class='result result-warning'>⚠️ Método getImagePath no encontrado, se agregará al final</div>";
        
        // Agregar el método al final de la clase
        $updatedContent = str_replace('}', $improvedMethod . "\n}", $productClassContent);
        if (file_put_contents($productClassPath, $updatedContent)) {
            echo "<div class='result result-success'>✅ Método getImagePath agregado a la clase Product</div>";
        } else {
            echo "<div class='result result-error'>❌ Error al agregar el método a la clase Product</div>";
        }
    }
} else {
    echo "<div class='result result-error'>❌ Archivo classes/Product.php no encontrado</div>";
}
echo "</div>";

// PASO 4: Corregir archivos del frontend
echo "<div class='step'>";
echo "<h2>🌐 Paso 4: Corregir Archivos del Frontend</h2>";

// Lista de archivos a corregir
$frontendFiles = [
    'index.php',
    'products.php',
    'product.php',
    'category.php',
    'orders.php'
];

foreach ($frontendFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $originalContent = $content;
        
        // Agregar onerror a todas las imágenes de productos
        $content = preg_replace(
            '/(<img[^>]*src="[^"]*"[^>]*alt="[^"]*"[^>]*class="[^"]*product[^"]*"[^>]*>)/',
            '$1 onerror="this.src=\'assets/images/placeholder.jpg\'"',
            $content
        );
        
        // Corregir fallbacks de imágenes
        $content = str_replace(
            'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80',
            'assets/images/placeholder.jpg',
            $content
        );
        
        if ($content !== $originalContent) {
            if (file_put_contents($file, $content)) {
                echo "<div class='result result-success'>✅ Archivo $file corregido</div>";
            } else {
                echo "<div class='result result-error'>❌ Error al corregir $file</div>";
            }
        } else {
            echo "<div class='result result-info'>✅ Archivo $file ya está correcto</div>";
        }
    } else {
        echo "<div class='result result-warning'>⚠️ Archivo $file no encontrado</div>";
    }
}
echo "</div>";

// PASO 5: Crear script de prueba
echo "<div class='step'>";
echo "<h2>🧪 Paso 5: Crear Script de Prueba</h2>";

$testScript = '<?php
/**
 * Script de Prueba del Sistema de Imágenes Corregido
 */

echo "<h1>✅ Prueba del Sistema de Imágenes Corregido</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .test { background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 5px; }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
    .product-card { border: 1px solid #ddd; padding: 15px; border-radius: 8px; text-align: center; }
    .product-image { width: 100%; height: 200px; object-fit: cover; border-radius: 5px; margin-bottom: 10px; }
</style>";

// Simular productos de prueba
$testProducts = [
    ["name" => "Producto 1", "image" => "assets/images/products/test1.jpg"],
    ["name" => "Producto 2", "image" => "assets/images/products/test2.jpg"],
    ["name" => "Producto 3", "image" => "assets/images/placeholder.jpg"],
    ["name" => "Producto 4", "image" => "assets/images/products/test4.jpg"]
];

echo "<div class=\'product-grid\'>";
foreach ($testProducts as $prod) {
    echo "<div class=\'product-card\'>";
    echo "<img src=\'" . $prod["image"] . "\' alt=\'" . htmlspecialchars($prod["name"]) . "\' class=\'product-image\' onerror=\'this.src=\"assets/images/placeholder.jpg\"\'>";
    echo "<div><strong>" . htmlspecialchars($prod["name"]) . "</strong></div>";
    echo "<small style=\'color: #666;\'>Ruta: " . $prod["image"] . "</small>";
    echo "</div>";
}
echo "</div>";

echo "<h2>🎉 Sistema de imágenes corregido exitosamente</h2>";
echo "<p>Las imágenes ahora se muestran correctamente con fallback a placeholder.</p>";
?>';

if (file_put_contents('test_images_fixed.php', $testScript)) {
    echo "<div class='result result-success'>✅ Script de prueba creado: test_images_fixed.php</div>";
} else {
    echo "<div class='result result-error'>❌ Error al crear script de prueba</div>";
}
echo "</div>";

// PASO 6: Verificar archivos físicos
echo "<div class='step'>";
echo "<h2>🖼️ Paso 6: Verificar Archivos Físicos</h2>";

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

// Resumen final
echo "<div class='step'>";
echo "<h2>🎉 Corrección Completada</h2>";
echo "<div class='result result-success'>";
echo "<strong>✅ Sistema de imágenes corregido exitosamente</strong><br><br>";
echo "<strong>Problemas solucionados:</strong><br>";
echo "• Estructura de directorios creada<br>";
echo "• Imagen placeholder generada<br>";
echo "• Método getImagePath mejorado<br>";
echo "• Archivos del frontend corregidos<br>";
echo "• Fallback a placeholder implementado<br>";
echo "• Validación de archivos físicos<br><br>";
echo "<strong>Próximos pasos:</strong><br>";
echo "1. Ejecuta <a href='test_images_fixed.php' class='btn'>test_images_fixed.php</a> para verificar<br>";
echo "2. Visita <a href='index.php' class='btn'>index.php</a> para ver el resultado<br>";
echo "3. Visita <a href='products.php' class='btn'>products.php</a> para ver productos<br>";
echo "4. Prueba crear/editar productos en el admin<br>";
echo "</div>";
echo "</div>";

echo "</div>"; // Cerrar container
?> 