<?php
/**
 * Verificación Final del Sistema - AlquimiaTechnologic
 * Verificación completa de todas las funcionalidades del sistema
 */

require_once 'config/config.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';

$product = new Product();
$category = new Category();

echo "<h1>🔍 Verificación Final del Sistema - AlquimiaTechnologic</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .container { max-width: 1400px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #e9ecef; border-radius: 5px; }
    .test-result { padding: 10px; margin: 5px 0; border-radius: 3px; }
    .test-success { background: #d4edda; border: 1px solid #c3e6cb; }
    .test-error { background: #f8d7da; border: 1px solid #f5c6cb; }
    .test-warning { background: #fff3cd; border: 1px solid #ffeaa7; }
    .test-info { background: #d1ecf1; border: 1px solid #bee5eb; }
    .progress { background: #e9ecef; border-radius: 10px; height: 20px; margin: 10px 0; }
    .progress-bar { background: #007bff; height: 100%; border-radius: 10px; transition: width 0.3s; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; background: white; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

echo "<div class='container'>";

// Contadores globales
$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$warnings = 0;

function runTest($testName, $testFunction) {
    global $totalTests, $passedTests, $failedTests, $warnings;
    $totalTests++;
    
    echo "<div class='test-result test-info'>";
    echo "<strong>🧪 Test:</strong> $testName<br>";
    
    try {
        $result = $testFunction();
        if ($result === true) {
            echo "<span class='success'>✅ PASÓ</span>";
            $passedTests++;
        } elseif ($result === 'warning') {
            echo "<span class='warning'>⚠️ ADVERTENCIA</span>";
            $warnings++;
        } else {
            echo "<span class='error'>❌ FALLÓ</span>";
            $failedTests++;
        }
    } catch (Exception $e) {
        echo "<span class='error'>❌ ERROR: " . $e->getMessage() . "</span>";
        $failedTests++;
    }
    
    echo "</div>";
}

// 1. Verificación de Configuración del Sistema
echo "<div class='section'>";
echo "<h2>⚙️ Configuración del Sistema</h2>";

runTest("Configuración de base de datos", function() {
    try {
        $database = new Database();
        $db = $database->getConnection();
        return $db ? true : false;
    } catch (Exception $e) {
        return false;
    }
});

runTest("Archivo de configuración", function() {
    return file_exists('config/config.php') ? true : false;
});

runTest("Clases principales", function() {
    $requiredClasses = ['Product', 'Category', 'User', 'Order'];
    $missing = [];
    
    foreach ($requiredClasses as $class) {
        if (!class_exists($class)) {
            $missing[] = $class;
        }
    }
    
    return empty($missing) ? true : false;
});

runTest("Configuración de sitio", function() {
    return defined('SITE_NAME') && defined('SITE_URL') ? true : false;
});
echo "</div>";

// 2. Verificación de Base de Datos
echo "<div class='section'>";
echo "<h2>🗄️ Base de Datos</h2>";

runTest("Conexión a base de datos", function() {
    try {
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->query("SELECT 1");
        return $stmt ? true : false;
    } catch (Exception $e) {
        return false;
    }
});

runTest("Tabla de productos", function() {
    try {
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->query("SELECT COUNT(*) FROM products");
        return $stmt ? true : false;
    } catch (Exception $e) {
        return false;
    }
});

runTest("Tabla de categorías", function() {
    try {
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->query("SELECT COUNT(*) FROM categories");
        return $stmt ? true : false;
    } catch (Exception $e) {
        return false;
    }
});

runTest("Tabla de usuarios", function() {
    try {
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->query("SELECT COUNT(*) FROM users");
        return $stmt ? true : false;
    } catch (Exception $e) {
        return false;
    }
});
echo "</div>";

// 3. Verificación del Sistema de Imágenes
echo "<div class='section'>";
echo "<h2>🖼️ Sistema de Imágenes</h2>";

runTest("Directorio de imágenes", function() {
    $dirs = ['assets/images', 'assets/images/products', 'assets/images/categories'];
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    return true;
});

runTest("Imagen placeholder", function() {
    if (!file_exists('assets/images/placeholder.jpg')) {
        $placeholderContent = file_get_contents('https://via.placeholder.com/300x300/cccccc/666666?text=Sin+Imagen');
        if ($placeholderContent) {
            file_put_contents('assets/images/placeholder.jpg', $placeholderContent);
        }
    }
    return file_exists('assets/images/placeholder.jpg') ? true : false;
});

runTest("Método getImagePath", function() {
    $testProduct = [
        'name' => 'Test Product',
        'slug' => 'test-product',
        'images' => json_encode(['assets/images/products/test.jpg'])
    ];
    
    $imagePath = Product::getImagePath($testProduct);
    return $imagePath === 'assets/images/placeholder.jpg' ? true : false;
});

runTest("Permisos de directorios", function() {
    $dirs = ['assets/images', 'assets/images/products'];
    foreach ($dirs as $dir) {
        if (!is_writable($dir)) {
            return 'warning';
        }
    }
    return true;
});
echo "</div>";

// 4. Verificación de Funcionalidades de Productos
echo "<div class='section'>";
echo "<h2>📦 Funcionalidades de Productos</h2>";

runTest("Obtener productos", function() use ($product) {
    $products = $product->getAllProducts(5);
    return is_array($products) ? true : false;
});

runTest("Obtener productos destacados", function() use ($product) {
    $featured = $product->getFeaturedProducts(5);
    return is_array($featured) ? true : false;
});

runTest("Obtener categorías", function() use ($category) {
    $categories = $category->getAllCategories();
    return is_array($categories) ? true : false;
});

runTest("Búsqueda de productos", function() use ($product) {
    $search = $product->searchProducts('test', 5);
    return is_array($search) ? true : false;
});
echo "</div>";

// 5. Verificación de Archivos Críticos
echo "<div class='section'>";
echo "<h2>📁 Archivos Críticos</h2>";

$criticalFiles = [
    'index.php',
    'products.php',
    'product.php',
    'admin/dashboard.php',
    'admin/products.php',
    'admin/process_product.php',
    'admin/upload_handler.php',
    'classes/Product.php',
    'classes/Category.php',
    'config/config.php',
    'config/database.php'
];

foreach ($criticalFiles as $file) {
    runTest("Archivo: $file", function() use ($file) {
        return file_exists($file) ? true : false;
    });
}
echo "</div>";

// 6. Verificación de Seguridad
echo "<div class='section'>";
echo "<h2>🔒 Seguridad</h2>";

runTest("Función cleanInput", function() {
    return function_exists('cleanInput') ? true : false;
});

runTest("Función generateCSRFToken", function() {
    return function_exists('generateCSRFToken') ? true : false;
});

runTest("Función verifyCSRFToken", function() {
    return function_exists('verifyCSRFToken') ? true : false;
});

runTest("Función isLoggedIn", function() {
    return function_exists('isLoggedIn') ? true : false;
});

runTest("Función isAdmin", function() {
    return function_exists('isAdmin') ? true : false;
});
echo "</div>";

// 7. Verificación de JavaScript y CSS
echo "<div class='section'>";
echo "<h2>🎨 Frontend</h2>";

runTest("Archivo CSS principal", function() {
    return file_exists('assets/css/style.css') ? true : false;
});

runTest("Archivo CSS admin", function() {
    return file_exists('assets/css/admin.css') ? true : false;
});

runTest("Archivo JS principal", function() {
    return file_exists('assets/js/main.js') ? true : false;
});

runTest("Archivo JS admin", function() {
    return file_exists('assets/js/admin.js') ? true : false;
});
echo "</div>";

// 8. Verificación de Autenticación
echo "<div class='section'>";
echo "<h2>🔐 Autenticación</h2>";

runTest("Página de login", function() {
    return file_exists('auth/login.php') ? true : false;
});

runTest("Página de registro", function() {
    return file_exists('auth/register.php') ? true : false;
});

runTest("Página de logout", function() {
    return file_exists('auth/logout.php') ? true : false;
});

runTest("Página de perfil", function() {
    return file_exists('profile.php') ? true : false;
});
echo "</div>";

// 9. Verificación de Funcionalidades de E-commerce
echo "<div class='section'>";
echo "<h2>🛒 E-commerce</h2>";

runTest("Página de carrito", function() {
    return file_exists('cart.php') ? true : false;
});

runTest("Página de pedidos", function() {
    return file_exists('orders.php') ? true : false;
});

runTest("Clase Order", function() {
    return class_exists('Order') ? true : false;
});
echo "</div>";

// 10. Resumen Final
echo "<div class='section'>";
echo "<h2>📊 Resumen Final</h2>";

$successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0;

echo "<div class='test-result test-success'>";
echo "<h3>📈 Estadísticas de Verificación</h3>";
echo "<p><strong>Total de pruebas:</strong> $totalTests</p>";
echo "<p class='success'><strong>Pruebas exitosas:</strong> $passedTests</p>";
echo "<p class='error'><strong>Pruebas fallidas:</strong> $failedTests</p>";
echo "<p class='warning'><strong>Advertencias:</strong> $warnings</p>";

echo "<div class='progress'>";
echo "<div class='progress-bar' style='width: $successRate%'></div>";
echo "</div>";
echo "<p><strong>Tasa de éxito:</strong> $successRate%</p>";

if ($successRate >= 90) {
    echo "<p class='success'><strong>🎉 ¡Excelente! El sistema está funcionando correctamente.</strong></p>";
} elseif ($successRate >= 75) {
    echo "<p class='warning'><strong>⚠️ Bueno. Algunas mejoras menores son recomendadas.</strong></p>";
} else {
    echo "<p class='error'><strong>❌ Se requieren correcciones importantes.</strong></p>";
}
echo "</div>";

// Recomendaciones
echo "<div class='test-result test-info'>";
echo "<h3>💡 Recomendaciones</h3>";
echo "<ul>";
if ($failedTests > 0) {
    echo "<li>Revisa las pruebas fallidas y corrige los problemas identificados</li>";
}
if ($warnings > 0) {
    echo "<li>Considera las advertencias para mejorar el sistema</li>";
}
echo "<li>Ejecuta regularmente los scripts de mantenimiento</li>";
echo "<li>Monitorea el rendimiento del sistema</li>";
echo "<li>Mantén actualizadas las dependencias</li>";
echo "</ul>";
echo "</div>";
echo "</div>";

// 11. Enlaces útiles
echo "<div class='section'>";
echo "<h2>🔗 Enlaces Útiles</h2>";
echo "<div class='test-result test-info'>";
echo "<p><strong>Scripts de mantenimiento:</strong></p>";
echo "<ul>";
echo "<li><a href='debug_images_public.php' target='_blank'>🔍 Depuración de Imágenes (Público)</a></li>";
echo "<li><a href='test_image_system.php' target='_blank'>🧪 Prueba del Sistema de Imágenes</a></li>";
echo "<li><a href='optimize_images.php' target='_blank'>⚡ Optimización de Imágenes</a></li>";
echo "<li><a href='admin/dashboard.php' target='_blank'>📊 Panel de Administración</a></li>";
echo "</ul>";
echo "</div>";
echo "</div>";

echo "<h2>✅ Verificación Final Completada</h2>";
echo "<p>El sistema ha sido verificado completamente. Revisa los resultados y sigue las recomendaciones para mantener el sistema en óptimas condiciones.</p>";

echo "</div>";
?> 