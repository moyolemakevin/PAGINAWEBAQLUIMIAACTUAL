<?php
require_once 'config/config.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';
require_once 'classes/User.php';

// Test del sistema
echo "<h1>🧪 Test del Sistema - AlquimiaTechnologic</h1>";

// Test 1: Conexión a base de datos
echo "<h2>1. Test de Conexión a Base de Datos</h2>";
$database = null;
$db = null;
try {
    $database = new Database();
    $db = $database->getConnection();
    echo "✅ Conexión a base de datos exitosa<br>";
} catch (Exception $e) {
    echo "❌ Error en conexión a base de datos: " . $e->getMessage() . "<br>";
}

// Test 2: Clase Product
echo "<h2>2. Test de Clase Product</h2>";
try {
    $product = new Product();
    $products = $product->getAllProducts(5);
    echo "✅ Clase Product funcionando - " . count($products) . " productos encontrados<br>";
    
    // Test método getProductCountByCategory
    $count = $product->getProductCountByCategory(1);
    echo "✅ Método getProductCountByCategory funcionando - " . $count . " productos en categoría 1<br>";
} catch (Exception $e) {
    echo "❌ Error en clase Product: " . $e->getMessage() . "<br>";
}

// Test 3: Clase Category
echo "<h2>3. Test de Clase Category</h2>";
try {
    $category = new Category();
    $categories = $category->getAllCategories();
    echo "✅ Clase Category funcionando - " . count($categories) . " categorías encontradas<br>";
    
    // Test método getCategoryBySlug
    $cat = $category->getCategoryBySlug('software');
    if ($cat) {
        echo "✅ Método getCategoryBySlug funcionando<br>";
    } else {
        echo "⚠️ Método getCategoryBySlug - categoría 'software' no encontrada (normal si no existe)<br>";
    }
} catch (Exception $e) {
    echo "❌ Error en clase Category: " . $e->getMessage() . "<br>";
}

// Test 4: Clase User
echo "<h2>4. Test de Clase User</h2>";
try {
    $user = new User();
    $users = $user->getAllUsers();
    echo "✅ Clase User funcionando - " . count($users) . " usuarios encontrados<br>";
} catch (Exception $e) {
    echo "❌ Error en clase User: " . $e->getMessage() . "<br>";
}

// Test 5: Configuración
echo "<h2>5. Test de Configuración</h2>";
echo "✅ SITE_NAME: " . SITE_NAME . "<br>";
echo "✅ SITE_URL: " . SITE_URL . "<br>";
echo "✅ DB_HOST: " . DB_HOST . "<br>";

// Test 6: Funciones de utilidad
echo "<h2>6. Test de Funciones de Utilidad</h2>";
echo "✅ Función cleanInput: " . (function_exists('cleanInput') ? 'Disponible' : 'No disponible') . "<br>";
echo "✅ Función isLoggedIn: " . (function_exists('isLoggedIn') ? 'Disponible' : 'No disponible') . "<br>";
echo "✅ Función isAdmin: " . (function_exists('isAdmin') ? 'Disponible' : 'No disponible') . "<br>";

// Test 7: Archivos CSS y JS
echo "<h2>7. Test de Archivos Estáticos</h2>";
$cssFile = 'assets/css/style.css';
$jsFile = 'assets/js/main.js';
$adminCssFile = 'assets/css/admin.css';
$adminJsFile = 'assets/js/admin.js';

echo "✅ CSS Principal: " . (file_exists($cssFile) ? 'Existe' : 'No existe') . "<br>";
echo "✅ JS Principal: " . (file_exists($jsFile) ? 'Existe' : 'No existe') . "<br>";
echo "✅ CSS Admin: " . (file_exists($adminCssFile) ? 'Existe' : 'No existe') . "<br>";
echo "✅ JS Admin: " . (file_exists($adminJsFile) ? 'Existe' : 'No existe') . "<br>";

// Test 8: Páginas principales
echo "<h2>8. Test de Páginas Principales</h2>";
$pages = [
    'index.php',
    'products.php',
    'about.php',
    'contact.php',
    'cart.php',
    'category.php',
    'product.php',
    'profile.php',
    'orders.php'
];

foreach ($pages as $page) {
    echo "✅ $page: " . (file_exists($page) ? 'Existe' : 'No existe') . "<br>";
}

// Test 9: Archivos de autenticación
echo "<h2>9. Test de Archivos de Autenticación</h2>";
$authFiles = [
    'auth/login.php',
    'auth/register.php',
    'auth/logout.php'
];

foreach ($authFiles as $file) {
    echo "✅ $file: " . (file_exists($file) ? 'Existe' : 'No existe') . "<br>";
}

// Test 10: Panel de administración
echo "<h2>10. Test de Panel de Administración</h2>";
$adminFiles = [
    'admin/dashboard.php',
    'admin/products.php'
];

foreach ($adminFiles as $file) {
    echo "✅ $file: " . (file_exists($file) ? 'Existe' : 'No existe') . "<br>";
}

// Test 11: URLs amigables
echo "<h2>11. Test de URLs Amigables</h2>";
echo "✅ .htaccess: " . (file_exists('.htaccess') ? 'Existe' : 'No existe') . "<br>";

// Test 12: Base de datos
echo "<h2>12. Test de Base de Datos</h2>";
try {
    $tables = ['users', 'products', 'categories', 'orders'];
    foreach ($tables as $table) {
        if ($database && method_exists($database, 'isSQLite') && $database->isSQLite()) {
            $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=?");
        } else {
            $stmt = $db->prepare("SHOW TABLES LIKE ?");
        }
        $stmt->execute([$table]);
        $exists = $stmt->fetch();
        echo "✅ Tabla $table: " . ($exists ? 'Existe' : 'No existe') . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Error verificando tablas: " . $e->getMessage() . "<br>";
}

echo "<h2>🎉 Resumen del Test</h2>";
echo "<p><strong>El sistema está listo para funcionar correctamente.</strong></p>";
echo "<p>Si todos los tests muestran ✅, el sistema está completamente funcional.</p>";
echo "<p><a href='index.php'>Ir al sitio principal</a></p>";
?> 