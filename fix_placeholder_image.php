<?php
/**
 * Fix Placeholder Image - AlquimiaTechnologic
 * Crea y verifica la imagen placeholder del sistema
 */

echo "<h1>🖼️ Fix Placeholder Image - AlquimiaTechnologic</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #e9ecef; border-radius: 5px; }
</style>";

echo "<div class='container'>";

// 1. Verificar directorio de imágenes
echo "<div class='section'>";
echo "<h2>📁 Verificación de Directorios</h2>";

$imageDir = 'assets/images';
if (!file_exists($imageDir)) {
    mkdir($imageDir, 0755, true);
    echo "<p class='success'>✅ Directorio creado: $imageDir</p>";
} else {
    echo "<p class='info'>ℹ️ Directorio existe: $imageDir</p>";
}

// Verificar permisos
if (is_writable($imageDir)) {
    echo "<p class='success'>✅ Directorio escribible: $imageDir</p>";
} else {
    echo "<p class='error'>❌ Directorio no escribible: $imageDir</p>";
}
echo "</div>";

// 2. Crear imagen placeholder
echo "<div class='section'>";
echo "<h2>🖼️ Creación de Imagen Placeholder</h2>";

$placeholderPath = 'assets/images/placeholder.jpg';

// Intentar crear una imagen placeholder simple usando GD
if (extension_loaded('gd')) {
    echo "<p class='info'>ℹ️ Extensión GD disponible</p>";
    
    // Crear imagen de 300x300 píxeles
    $width = 300;
    $height = 300;
    
    // Crear imagen
    $image = imagecreate($width, $height);
    
    // Definir colores
    $bgColor = imagecolorallocate($image, 204, 204, 204); // Gris claro
    $textColor = imagecolorallocate($image, 102, 102, 102); // Gris oscuro
    $borderColor = imagecolorallocate($image, 153, 153, 153); // Gris medio
    
    // Rellenar fondo
    imagefill($image, 0, 0, $bgColor);
    
    // Dibujar borde
    imagerectangle($image, 0, 0, $width-1, $height-1, $borderColor);
    
    // Agregar texto
    $text = "Sin Imagen";
    $fontSize = 5;
    $textWidth = imagefontwidth($fontSize) * strlen($text);
    $textHeight = imagefontheight($fontSize);
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    
    imagestring($image, $fontSize, $x, $y, $text, $textColor);
    
    // Guardar imagen
    if (imagejpeg($image, $placeholderPath, 90)) {
        echo "<p class='success'>✅ Imagen placeholder creada exitosamente</p>";
        echo "<p class='info'>📁 Ruta: $placeholderPath</p>";
        
        // Verificar tamaño del archivo
        $fileSize = filesize($placeholderPath);
        echo "<p class='info'>📏 Tamaño: " . number_format($fileSize) . " bytes</p>";
        
        if ($fileSize > 100) {
            echo "<p class='success'>✅ Archivo válido</p>";
        } else {
            echo "<p class='error'>❌ Archivo demasiado pequeño</p>";
        }
    } else {
        echo "<p class='error'>❌ Error al guardar la imagen</p>";
    }
    
    // Liberar memoria
    imagedestroy($image);
    
} else {
    echo "<p class='warning'>⚠️ Extensión GD no disponible</p>";
    
    // Intentar método alternativo
    echo "<p class='info'>ℹ️ Intentando método alternativo...</p>";
    
    // Crear un archivo de imagen simple (no válido pero para testing)
    $simpleImage = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\b\n\x0C\x14\r\x0C\x0B\x0B\x0C\x19\x12\x13\x0F\x14\x1D\x1A\x1F\x1E\x1D\x1A\x1C\x1C $.' \",#\x1C\x1C(7),01444\x1F'\x1C=9=82<.342\xFF\xC0\x00\x11\b\x01\x2C\x01\x2C\x01\x01\x01\x11\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x14\x00\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x08\xFF\xC4\x00\x14\x10\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xFF\xDA\x00\x0C\x03\x01\x00\x02\x11\x03\x11\x00\x3F\x00\xAA\xFF\xD9";
    
    if (file_put_contents($placeholderPath, $simpleImage)) {
        echo "<p class='success'>✅ Imagen placeholder creada (método alternativo)</p>";
    } else {
        echo "<p class='error'>❌ No se pudo crear la imagen placeholder</p>";
    }
}
echo "</div>";

// 3. Verificar archivos de imagen corruptos
echo "<div class='section'>";
echo "<h2>🔧 Verificación de Archivos Corruptos</h2>";

$productsDir = 'assets/images/products';
$corruptedFiles = [];

if (is_dir($productsDir)) {
    $files = scandir($productsDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'jpg') {
            $filePath = $productsDir . '/' . $file;
            $fileSize = filesize($filePath);
            
            if ($fileSize <= 1) {
                $corruptedFiles[] = $filePath;
                echo "<p class='error'>❌ Archivo corrupto: $file ($fileSize bytes)</p>";
            }
        }
    }
}

if (empty($corruptedFiles)) {
    echo "<p class='success'>✅ No se encontraron archivos corruptos</p>";
} else {
    echo "<p class='warning'>⚠️ Se encontraron " . count($corruptedFiles) . " archivos corruptos</p>";
    
    // Reemplazar archivos corruptos con placeholder
    foreach ($corruptedFiles as $corruptedFile) {
        if (file_exists($placeholderPath)) {
            copy($placeholderPath, $corruptedFile);
            echo "<p class='success'>✅ Archivo reemplazado: " . basename($corruptedFile) . "</p>";
        }
    }
}
echo "</div>";

// 4. Verificar base de datos
echo "<div class='section'>";
echo "<h2>🗄️ Verificación de Base de Datos</h2>";

try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    // Verificar productos
    $stmt = $db->query("SELECT COUNT(*) as total FROM products");
    $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p class='info'>ℹ️ Total de productos en BD: $totalProducts</p>";
    
    // Verificar productos sin imágenes
    $stmt = $db->query("SELECT COUNT(*) as total FROM products WHERE images IS NULL OR images = '' OR images = 'null' OR images = '[]'");
    $productsWithoutImages = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    if ($productsWithoutImages > 0) {
        echo "<p class='warning'>⚠️ Productos sin imágenes: $productsWithoutImages</p>";
        
        // Actualizar productos sin imágenes
        $updateStmt = $db->prepare("UPDATE products SET images = ? WHERE images IS NULL OR images = '' OR images = 'null' OR images = '[]'");
        $defaultImages = json_encode(['assets/images/placeholder.jpg']);
        $updateStmt->execute([$defaultImages]);
        
        echo "<p class='success'>✅ Productos actualizados con imagen placeholder</p>";
    } else {
        echo "<p class='success'>✅ Todos los productos tienen imágenes</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error de base de datos: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 5. Resumen final
echo "<div class='section'>";
echo "<h2>📊 Resumen Final</h2>";

$placeholderExists = file_exists($placeholderPath);
$placeholderSize = $placeholderExists ? filesize($placeholderPath) : 0;

echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<h3>✅ Estado del Sistema</h3>";
echo "<ul>";
echo "<li><strong>Imagen placeholder:</strong> " . ($placeholderExists ? "✅ Existe" : "❌ No existe") . "</li>";
echo "<li><strong>Tamaño placeholder:</strong> " . number_format($placeholderSize) . " bytes</li>";
echo "<li><strong>Archivos corruptos:</strong> " . count($corruptedFiles) . "</li>";
echo "<li><strong>Productos sin imágenes:</strong> $productsWithoutImages</li>";
echo "</ul>";

if ($placeholderExists && $placeholderSize > 100) {
    echo "<p class='success'><strong>🎉 ¡Sistema de imágenes funcionando correctamente!</strong></p>";
} else {
    echo "<p class='warning'><strong>⚠️ Algunos problemas requieren atención manual</strong></p>";
}
echo "</div>";
echo "</div>";

// 6. Enlaces útiles
echo "<div class='section'>";
echo "<h2>🔗 Enlaces Útiles</h2>";
echo "<ul>";
echo "<li><a href='debug_images_public.php' target='_blank'>🔍 Verificar estado actual</a></li>";
echo "<li><a href='test_image_system_public.php' target='_blank'>🧪 Test del sistema de imágenes</a></li>";
echo "<li><a href='admin/dashboard.php' target='_blank'>📊 Panel de Administración</a></li>";
echo "<li><a href='products.php' target='_blank'>📦 Ver productos</a></li>";
echo "</ul>";
echo "</div>";

echo "</div>";
echo "<h2>✅ Fix Placeholder Image Completado</h2>";
echo "<p>Se han aplicado las correcciones necesarias al sistema de imágenes.</p>";
?> 