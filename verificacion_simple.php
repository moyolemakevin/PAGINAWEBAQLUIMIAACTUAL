<?php
/**
 * Verificación Simple del Sistema - AlquimiaTechnologic
 * Verificación básica de funcionalidades críticas
 */

echo "<h1>🔍 Verificación Simple del Sistema - AlquimiaTechnologic</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #e9ecef; border-radius: 5px; }
    .test-result { padding: 10px; margin: 5px 0; border-radius: 3px; }
    .test-success { background: #d4edda; border: 1px solid #c3e6cb; }
    .test-error { background: #f8d7da; border: 1px solid #f5c6cb; }
    .test-warning { background: #fff3cd; border: 1px solid #ffeaa7; }
    .test-info { background: #d1ecf1; border: 1px solid #bee5eb; }
</style>";

echo "<div class='container'>";

// Contadores
$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

function runTest($testName, $testFunction) {
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    
    echo "<div class='test-result test-info'>";
    echo "<strong>🧪 Test:</strong> $testName<br>";
    
    try {
        $result = $testFunction();
        if ($result === true) {
            echo "<span class='success'>✅ PASÓ</span>";
            $passedTests++;
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

// 1. Verificación de Archivos Críticos
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
    'config/database.php',
    'auth/login.php',
    'auth/register.php',
    'cart.php',
    'orders.php'
];

foreach ($criticalFiles as $file) {
    runTest("Archivo: $file", function() use ($file) {
        return file_exists($file) ? true : false;
    });
}
echo "</div>";

// 2. Verificación de Directorios
echo "<div class='section'>";
echo "<h2>📂 Directorios</h2>";

$directories = [
    'assets/images',
    'assets/images/products',
    'assets/images/categories',
    'assets/css',
    'assets/js',
    'admin',
    'auth',
    'classes',
    'config'
];

foreach ($directories as $dir) {
    runTest("Directorio: $dir", function() use ($dir) {
        return is_dir($dir) ? true : false;
    });
}
echo "</div>";

// 3. Verificación de Base de Datos
echo "<div class='section'>";
echo "<h2>🗄️ Base de Datos</h2>";

runTest("Archivo de configuración de BD", function() {
    return file_exists('config/database.php') ? true : false;
});

runTest("Archivo SQL de BD", function() {
    return file_exists('database/alquimia_db.sql') ? true : false;
});

// Intentar conexión simple
runTest("Conexión a base de datos", function() {
    if (!file_exists('config/database.php')) {
        return false;
    }
    
    try {
        require_once 'config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        return $db ? true : false;
    } catch (Exception $e) {
        return false;
    }
});
echo "</div>";

// 4. Verificación de Funciones de Seguridad
echo "<div class='section'>";
echo "<h2>🔒 Seguridad</h2>";

// Verificar si las funciones existen en archivos principales
runTest("Función cleanInput", function() {
    $files = ['index.php', 'admin/products.php', 'admin/process_product.php'];
    foreach ($files as $file) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            if (strpos($content, 'function cleanInput') !== false) {
                return true;
            }
        }
    }
    return false;
});

runTest("Función generateCSRFToken", function() {
    $files = ['index.php', 'admin/products.php', 'admin/process_product.php'];
    foreach ($files as $file) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            if (strpos($content, 'function generateCSRFToken') !== false) {
                return true;
            }
        }
    }
    return false;
});

runTest("Función isLoggedIn", function() {
    $files = ['index.php', 'admin/dashboard.php', 'profile.php'];
    foreach ($files as $file) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            if (strpos($content, 'function isLoggedIn') !== false) {
                return true;
            }
        }
    }
    return false;
});

runTest("Función isAdmin", function() {
    $files = ['admin/dashboard.php', 'admin/products.php'];
    foreach ($files as $file) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            if (strpos($content, 'function isAdmin') !== false) {
                return true;
            }
        }
    }
    return false;
});
echo "</div>";

// 5. Verificación de Assets
echo "<div class='section'>";
echo "<h2>🎨 Assets</h2>";

$assets = [
    'assets/css/style.css',
    'assets/css/admin.css',
    'assets/js/main.js',
    'assets/js/admin.js',
    'assets/images/placeholder.jpg'
];

foreach ($assets as $asset) {
    runTest("Asset: $asset", function() use ($asset) {
        return file_exists($asset) ? true : false;
    });
}
echo "</div>";

// 6. Verificación de Permisos
echo "<div class='section'>";
echo "<h2>🔐 Permisos</h2>";

runTest("Directorio de imágenes escribible", function() {
    return is_writable('assets/images') ? true : false;
});

runTest("Directorio de productos escribible", function() {
    return is_writable('assets/images/products') ? true : false;
});

runTest("Directorio de categorías escribible", function() {
    return is_writable('assets/images/categories') ? true : false;
});
echo "</div>";

// 7. Resumen
echo "<div class='section'>";
echo "<h2>📊 Resumen</h2>";

$successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0;

echo "<div class='test-result test-success'>";
echo "<h3>📈 Estadísticas</h3>";
echo "<p><strong>Total de pruebas:</strong> $totalTests</p>";
echo "<p class='success'><strong>Pruebas exitosas:</strong> $passedTests</p>";
echo "<p class='error'><strong>Pruebas fallidas:</strong> $failedTests</p>";
echo "<p><strong>Tasa de éxito:</strong> $successRate%</p>";

if ($successRate >= 90) {
    echo "<p class='success'><strong>🎉 ¡Excelente! El sistema está funcionando correctamente.</strong></p>";
} elseif ($successRate >= 75) {
    echo "<p class='warning'><strong>⚠️ Bueno. Algunas mejoras menores son recomendadas.</strong></p>";
} else {
    echo "<p class='error'><strong>❌ Se requieren correcciones importantes.</strong></p>";
}
echo "</div>";

// Enlaces útiles
echo "<div class='test-result test-info'>";
echo "<h3>🔗 Enlaces Útiles</h3>";
echo "<ul>";
echo "<li><a href='debug_images_public.php' target='_blank'>🔍 Depuración de Imágenes</a></li>";
echo "<li><a href='test_image_system.php' target='_blank'>🧪 Prueba del Sistema de Imágenes</a></li>";
echo "<li><a href='admin/dashboard.php' target='_blank'>📊 Panel de Administración</a></li>";
echo "<li><a href='index.php' target='_blank'>🏠 Página Principal</a></li>";
echo "</ul>";
echo "</div>";
echo "</div>";

echo "<h2>✅ Verificación Simple Completada</h2>";
echo "<p>Se han verificado los componentes críticos del sistema. Revisa los resultados y sigue las recomendaciones.</p>";

echo "</div>";
?> 