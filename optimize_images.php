<?php
/**
 * Script de Optimización de Imágenes - AlquimiaTechnologic
 * Optimiza el rendimiento del sistema de imágenes
 */

require_once 'config/config.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';

$product = new Product();

echo "<h1>⚡ Optimización de Imágenes - AlquimiaTechnologic</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #e9ecef; border-radius: 5px; }
    .progress { background: #e9ecef; border-radius: 10px; height: 20px; margin: 10px 0; }
    .progress-bar { background: #007bff; height: 100%; border-radius: 10px; transition: width 0.3s; }
</style>";

echo "<div class='container'>";

// 1. Crear directorios optimizados
echo "<div class='section'>";
echo "<h2>📁 Creación de Estructura Optimizada</h2>";

$directories = [
    'assets/images/products/thumbnails',
    'assets/images/products/originals',
    'assets/images/categories/thumbnails',
    'assets/images/categories/originals',
    'assets/images/settings/thumbnails',
    'assets/images/settings/originals'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "<p class='success'>✅ Directorio creado: $dir</p>";
    } else {
        echo "<p class='info'>ℹ️ Directorio existe: $dir</p>";
    }
}
echo "</div>";

// 2. Crear archivo .htaccess para optimización
echo "<div class='section'>";
echo "<h2>🔧 Configuración de Servidor</h2>";

$htaccessContent = "
# Optimización de imágenes - AlquimiaTechnologic
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg \"access plus 1 year\"
    ExpiresByType image/jpeg \"access plus 1 year\"
    ExpiresByType image/png \"access plus 1 year\"
    ExpiresByType image/webp \"access plus 1 year\"
    ExpiresByType image/gif \"access plus 1 year\"
</IfModule>

<IfModule mod_headers.c>
    <FilesMatch \"\\.(jpg|jpeg|png|webp|gif)$\">
        Header set Cache-Control \"max-age=31536000, public\"
    </FilesMatch>
</IfModule>

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE image/png image/jpg image/jpeg image/webp
</IfModule>
";

$htaccessPath = 'assets/images/.htaccess';
if (!file_exists($htaccessPath)) {
    file_put_contents($htaccessPath, $htaccessContent);
    echo "<p class='success'>✅ Archivo .htaccess creado para optimización de caché</p>";
} else {
    echo "<p class='info'>ℹ️ Archivo .htaccess ya existe</p>";
}
echo "</div>";

// 3. Crear función de optimización de imágenes
echo "<div class='section'>";
echo "<h2>🖼️ Función de Optimización</h2>";

$optimizeFunction = "
<?php
/**
 * Función de optimización de imágenes
 */
function optimizeImage(\$sourcePath, \$destinationPath, \$quality = 85, \$maxWidth = 800, \$maxHeight = 600) {
    if (!extension_loaded('gd')) {
        return copy(\$sourcePath, \$destinationPath);
    }
    
    \$imageInfo = getimagesize(\$sourcePath);
    if (!\$imageInfo) return false;
    
    \$width = \$imageInfo[0];
    \$height = \$imageInfo[1];
    \$type = \$imageInfo[2];
    
    // Calcular nuevas dimensiones
    \$ratio = min(\$maxWidth / \$width, \$maxHeight / \$height);
    \$newWidth = round(\$width * \$ratio);
    \$newHeight = round(\$height * \$ratio);
    
    // Crear imagen
    switch (\$type) {
        case IMAGETYPE_JPEG:
            \$source = imagecreatefromjpeg(\$sourcePath);
            break;
        case IMAGETYPE_PNG:
            \$source = imagecreatefrompng(\$sourcePath);
            break;
        case IMAGETYPE_GIF:
            \$source = imagecreatefromgif(\$sourcePath);
            break;
        case IMAGETYPE_WEBP:
            \$source = imagecreatefromwebp(\$sourcePath);
            break;
        default:
            return false;
    }
    
    if (!\$source) return false;
    
    // Crear nueva imagen
    \$destination = imagecreatetruecolor(\$newWidth, \$newHeight);
    
    // Preservar transparencia para PNG y GIF
    if (\$type == IMAGETYPE_PNG || \$type == IMAGETYPE_GIF) {
        imagealphablending(\$destination, false);
        imagesavealpha(\$destination, true);
        \$transparent = imagecolorallocatealpha(\$destination, 255, 255, 255, 127);
        imagefill(\$destination, 0, 0, \$transparent);
    }
    
    // Redimensionar
    imagecopyresampled(\$destination, \$source, 0, 0, 0, 0, \$newWidth, \$newHeight, \$width, \$height);
    
    // Guardar imagen optimizada
    \$success = false;
    switch (\$type) {
        case IMAGETYPE_JPEG:
            \$success = imagejpeg(\$destination, \$destinationPath, \$quality);
            break;
        case IMAGETYPE_PNG:
            \$success = imagepng(\$destination, \$destinationPath, round((100 - \$quality) / 10));
            break;
        case IMAGETYPE_GIF:
            \$success = imagegif(\$destination, \$destinationPath);
            break;
        case IMAGETYPE_WEBP:
            \$success = imagewebp(\$destination, \$destinationPath, \$quality);
            break;
    }
    
    // Limpiar memoria
    imagedestroy(\$source);
    imagedestroy(\$destination);
    
    return \$success;
}
?>
";

$optimizePath = 'includes/image_optimizer.php';
if (!file_exists('includes')) {
    mkdir('includes', 0755, true);
}
if (!file_exists($optimizePath)) {
    file_put_contents($optimizePath, $optimizeFunction);
    echo "<p class='success'>✅ Función de optimización creada</p>";
} else {
    echo "<p class='info'>ℹ️ Función de optimización ya existe</p>";
}
echo "</div>";

// 4. Crear archivo de configuración de imágenes
echo "<div class='section'>";
echo "<h2>⚙️ Configuración de Imágenes</h2>";

$imageConfig = "<?php
// Configuración de imágenes - AlquimiaTechnologic
define('IMAGE_QUALITY', 85);
define('IMAGE_MAX_WIDTH', 800);
define('IMAGE_MAX_HEIGHT', 600);
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 300);
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'webp', 'gif']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('IMAGE_CACHE_TIME', 31536000); // 1 año
?>";

$configPath = 'config/image_config.php';
if (!file_exists($configPath)) {
    file_put_contents($configPath, $imageConfig);
    echo "<p class='success'>✅ Configuración de imágenes creada</p>";
} else {
    echo "<p class='info'>ℹ️ Configuración de imágenes ya existe</p>";
}
echo "</div>";

// 5. Crear script de limpieza de archivos huérfanos
echo "<div class='section'>";
echo "<h2>🧹 Script de Limpieza</h2>";

$cleanupScript = "<?php
/**
 * Script de limpieza de archivos huérfanos
 */
require_once 'config/config.php';
require_once 'classes/Product.php';

\$product = new Product();
\$products = \$product->getAllProducts();

// Obtener todas las imágenes referenciadas en la base de datos
\$referencedImages = [];
foreach (\$products as \$prod) {
    if (\$prod['images']) {
        \$images = json_decode(\$prod['images'], true);
        if (is_array(\$images)) {
            foreach (\$images as \$image) {
                if (is_array(\$image)) {
                    \$referencedImages[] = \$image['original'] ?? \$image['thumbnail'] ?? '';
                } else {
                    \$referencedImages[] = \$image;
                }
            }
        }
    }
}

// Verificar archivos físicos
\$productsDir = 'assets/images/products/';
if (is_dir(\$productsDir)) {
    \$files = scandir(\$productsDir);
    \$orphanedFiles = [];
    
    foreach (\$files as \$file) {
        if (in_array(pathinfo(\$file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            \$filePath = 'assets/images/products/' . \$file;
            if (!in_array(\$filePath, \$referencedImages)) {
                \$orphanedFiles[] = \$filePath;
            }
        }
    }
    
    echo \"Archivos huérfanos encontrados: \" . count(\$orphanedFiles) . \"\\n\";
    foreach (\$orphanedFiles as \$file) {
        echo \"- \$file\\n\";
    }
}
?>
";

$cleanupPath = 'admin/cleanup_orphaned_images.php';
if (!file_exists($cleanupPath)) {
    file_put_contents($cleanupPath, $cleanupScript);
    echo "<p class='success'>✅ Script de limpieza creado</p>";
} else {
    echo "<p class='info'>ℹ️ Script de limpieza ya existe</p>";
}
echo "</div>";

// 6. Crear archivo de estadísticas de imágenes
echo "<div class='section'>";
echo "<h2>📊 Estadísticas de Imágenes</h2>";

$statsScript = "<?php
/**
 * Estadísticas de imágenes del sistema
 */
require_once 'config/config.php';
require_once 'classes/Product.php';

\$product = new Product();
\$products = \$product->getAllProducts();

\$totalProducts = count(\$products);
\$productsWithImages = 0;
\$totalImages = 0;
\$totalSize = 0;

foreach (\$products as \$prod) {
    if (\$prod['images']) {
        \$images = json_decode(\$prod['images'], true);
        if (is_array(\$images) && !empty(\$images)) {
            \$productsWithImages++;
            \$totalImages += count(\$images);
            
            foreach (\$images as \$image) {
                \$imagePath = is_array(\$image) ? (\$image['original'] ?? '') : \$image;
                if (\$imagePath && file_exists(\$imagePath)) {
                    \$totalSize += filesize(\$imagePath);
                }
            }
        }
    }
}

echo \"Estadísticas de imágenes:\\n\";
echo \"- Total de productos: \$totalProducts\\n\";
echo \"- Productos con imágenes: \$productsWithImages\\n\";
echo \"- Total de imágenes: \$totalImages\\n\";
echo \"- Tamaño total: \" . round(\$totalSize / 1024 / 1024, 2) . \" MB\\n\";
echo \"- Promedio por producto: \" . round(\$totalImages / max(\$totalProducts, 1), 2) . \" imágenes\\n\";
?>
";

$statsPath = 'admin/image_stats.php';
if (!file_exists($statsPath)) {
    file_put_contents($statsPath, $statsScript);
    echo "<p class='success'>✅ Script de estadísticas creado</p>";
} else {
    echo "<p class='info'>ℹ️ Script de estadísticas ya existe</p>";
}
echo "</div>";

// 7. Recomendaciones de optimización
echo "<div class='section'>";
echo "<h2>💡 Recomendaciones de Optimización</h2>";
echo "<ul>";
echo "<li><strong>Compresión:</strong> Usa formatos WebP para mejor compresión</li>";
echo "<li><strong>Dimensiones:</strong> Redimensiona imágenes antes de subirlas</li>";
echo "<li><strong>Caché:</strong> Configura headers de caché para imágenes</li>";
echo "<li><strong>CDN:</strong> Considera usar un CDN para imágenes</li>";
echo "<li><strong>Lazy Loading:</strong> Implementa carga diferida en el frontend</li>";
echo "<li><strong>Limpieza:</strong> Ejecuta el script de limpieza periódicamente</li>";
echo "</ul>";
echo "</div>";

echo "<h2>✅ Optimización Completada</h2>";
echo "<p>El sistema de imágenes ha sido optimizado para mejor rendimiento.</p>";
echo "<p><strong>Archivos creados:</strong></p>";
echo "<ul>";
echo "<li>Estructura de directorios optimizada</li>";
echo "<li>Archivo .htaccess para caché</li>";
echo "<li>Función de optimización de imágenes</li>";
echo "<li>Configuración de imágenes</li>";
echo "<li>Script de limpieza de archivos huérfanos</li>";
echo "<li>Script de estadísticas</li>";
echo "</ul>";

echo "</div>";
?> 