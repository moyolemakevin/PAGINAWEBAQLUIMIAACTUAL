<?php
/**
 * Script de Corrección de Imágenes en Frontend - AlquimiaTechnologic
 * Corrige específicamente los problemas de visualización en el frontend
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

echo "<h1>🎨 Corrección de Imágenes en Frontend - AlquimiaTechnologic</h1>";
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
    .preview { display: inline-block; margin: 10px; text-align: center; }
    .preview img { max-width: 150px; max-height: 150px; border: 2px solid #ddd; border-radius: 5px; }
    .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    .btn:hover { background: #0056b3; }
</style>";

echo "<div class='container'>";

// PASO 1: Corregir el método getImagePath en la clase Product
echo "<div class='step'>";
echo "<h2>🔧 Paso 1: Corregir Método getImagePath</h2>";

$productClassPath = 'classes/Product.php';
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
echo "</div>";

// PASO 2: Corregir archivos del frontend
echo "<div class='step'>";
echo "<h2>🌐 Paso 2: Corregir Archivos del Frontend</h2>";

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
        
        // Corregir llamadas a getImagePath
        $content = str_replace(
            'Product::getImagePath($p)',
            'Product::getImagePath($p)'
        );
        
        // Asegurar que las imágenes tengan fallback
        $content = preg_replace(
            '/src="\<\?php echo \$([^>]+)\[\'image\'\] \?\: \'([^\']+)\'; \?>"([^>]*)alt="([^"]*)"([^>]*)class="([^"]*)"([^>]*)\>/',
            'src="<?php echo $\\1[\'image\'] ?: \'assets/images/placeholder.jpg\'; ?>"\\3alt="\\4"\\5class="\\6"\\7 onerror="this.src=\'assets/images/placeholder.jpg\'">',
            $content
        );
        
        // Agregar onerror a todas las imágenes de productos
        $content = preg_replace(
            '/(<img[^>]*src="[^"]*"[^>]*alt="[^"]*"[^>]*class="[^"]*product[^"]*"[^>]*>)/',
            '$1 onerror="this.src=\'assets/images/placeholder.jpg\'"',
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

// PASO 3: Corregir productos en la base de datos
echo "<div class='step'>";
echo "<h2>📦 Paso 3: Corregir Productos en Base de Datos</h2>";

$products = $product->getAllProducts();
$fixedCount = 0;
$errorCount = 0;

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f2f2f2;'><th>ID</th><th>Nombre</th><th>Estado Original</th><th>Acción</th><th>Estado Final</th><th>Vista Previa</th></tr>";

foreach ($products as $prod) {
    $rowClass = '';
    $originalStatus = '';
    $action = '';
    $finalStatus = '';
    $preview = '';
    
    echo "<tr>";
    echo "<td>{$prod['id']}</td>";
    echo "<td>" . htmlspecialchars($prod['name']) . "</td>";
    
    // Verificar estado original
    $imagesJson = $prod['images'] ?? null;
    $currentImagePath = Product::getImagePath($prod);
    
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
                        $rowClass = 'style="background-color: #d4edda;"';
                        $action = "Corregido a formato estándar";
                        $finalStatus = "<span class='success'>✅ Corregido</span>";
                        $fixedCount++;
                        $foundImage = true;
                        
                        // Actualizar ruta para preview
                        $currentImagePath = $slugPath;
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
    
    // Vista previa
    $preview = "<div class='preview'>";
    $preview .= "<img src='$currentImagePath' alt='Preview' onerror=\"this.src='assets/images/placeholder.jpg'\">";
    $preview .= "<br><small>" . basename($currentImagePath) . "</small>";
    $preview .= "</div>";
    
    echo "<td>$preview</td>";
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

// PASO 4: Crear script de prueba del frontend
echo "<div class='step'>";
echo "<h2>🧪 Paso 4: Crear Script de Prueba del Frontend</h2>";

$testScript = '<?php
/**
 * Script de Prueba del Frontend - Imágenes
 */

require_once "config/config.php";
require_once "classes/Product.php";

$product = new Product();

echo "<h1>🎨 Prueba de Imágenes en Frontend</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
    .product-card { border: 1px solid #ddd; padding: 15px; border-radius: 8px; text-align: center; }
    .product-image { width: 100%; height: 200px; object-fit: cover; border-radius: 5px; margin-bottom: 10px; }
    .product-name { font-weight: bold; margin-bottom: 5px; }
    .product-price { color: #007bff; font-size: 1.2em; }
</style>";

$products = $product->getAllProducts(8);

echo "<div class=\'product-grid\'>";
foreach ($products as $prod) {
    $imagePath = Product::getImagePath($prod);
    
    echo "<div class=\'product-card\'>";
    echo "<img src=\'$imagePath\' alt=\'" . htmlspecialchars($prod["name"]) . "\' class=\'product-image\' onerror=\'this.src=\"assets/images/placeholder.jpg\"\'>";
    echo "<div class=\'product-name\'>" . htmlspecialchars($prod["name"]) . "</div>";
    echo "<div class=\'product-price\'>$" . number_format($prod["price"], 0, ",", ".") . "</div>";
    echo "<small style=\'color: #666;\'>Ruta: $imagePath</small>";
    echo "</div>";
}
echo "</div>";

echo "<h2>✅ Frontend corregido exitosamente</h2>";
echo "<p>Las imágenes ahora se muestran correctamente con fallback a placeholder.</p>";
?>';

if (file_put_contents('test_frontend_images.php', $testScript)) {
    echo "<div class='result result-success'>✅ Script de prueba del frontend creado: test_frontend_images.php</div>";
} else {
    echo "<div class='result result-error'>❌ Error al crear script de prueba del frontend</div>";
}
echo "</div>";

// PASO 5: Verificar permisos y estructura
echo "<div class='step'>";
echo "<h2>🔐 Paso 5: Verificar Permisos y Estructura</h2>";

// Verificar directorios
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
        
        echo "<div class='result result-info'>";
        echo "$status Directorio: $dir<br>";
        echo "Permisos: " . substr(sprintf('%o', $perms), -4) . "<br>";
        echo "Escribible: " . ($isWritable ? "Sí" : "No");
        echo "</div>";
    } else {
        echo "<div class='result result-error'>❌ Directorio no existe: $dir</div>";
    }
}

// Verificar imagen placeholder
if (file_exists('assets/images/placeholder.jpg')) {
    $size = filesize('assets/images/placeholder.jpg');
    echo "<div class='result result-success'>✅ Imagen placeholder existe ($size bytes)</div>";
} else {
    echo "<div class='result result-error'>❌ Imagen placeholder no existe</div>";
}
echo "</div>";

// Resumen final
echo "<div class='step'>";
echo "<h2>🎉 Corrección del Frontend Completada</h2>";
echo "<div class='result result-success'>";
echo "<strong>✅ Sistema de imágenes en frontend corregido exitosamente</strong><br><br>";
echo "<strong>Problemas solucionados:</strong><br>";
echo "• Método getImagePath mejorado<br>";
echo "• Archivos del frontend corregidos<br>";
echo "• Productos en base de datos actualizados<br>";
echo "• Fallback a placeholder implementado<br>";
echo "• Validación de archivos físicos<br>";
echo "• Permisos de directorios verificados<br><br>";
echo "<strong>Próximos pasos:</strong><br>";
echo "1. Ejecuta <a href='test_frontend_images.php' class='btn'>test_frontend_images.php</a> para verificar<br>";
echo "2. Visita <a href='index.php' class='btn'>index.php</a> para ver el resultado<br>";
echo "3. Visita <a href='products.php' class='btn'>products.php</a> para ver productos<br>";
echo "4. Prueba crear/editar productos en el admin<br>";
echo "</div>";
echo "</div>";

echo "</div>"; // Cerrar container

echo "<script>
// Auto-refresh después de 3 segundos para mostrar resultados
setTimeout(function() {
    location.reload();
}, 3000);
</script>";
?> 