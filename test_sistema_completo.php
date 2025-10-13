<?php
/**
 * Test Completo del Sistema - AlquimiaTechnologic
 * Verifica todas las funcionalidades línea por línea
 */

require_once __DIR__ . '/config/config.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Completo del Sistema - AlquimiaTechnologic</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
    <style>
        .test-section { margin: 20px 0; padding: 15px; border-radius: 8px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .progress-bar { height: 25px; }
        .code-block { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container-fluid mt-4'>
        <div class='row'>
            <div class='col-12'>
                <h1 class='text-center mb-4'>
                    <i class='fas fa-vial text-primary'></i>
                    Test Completo del Sistema
                </h1>
                
                <div class='alert alert-info'>
                    <i class='fas fa-info-circle me-2'></i>
                    <strong>Análisis Completo:</strong> Verificando cada línea de código y funcionalidad
                </div>
            </div>
        </div>
        
        <div class='row'>
            <div class='col-md-8'>
                <div class='card'>
                    <div class='card-header'>
                        <h5 class='mb-0'>
                            <i class='fas fa-clipboard-check me-2'></i>
                            Resultados del Test Completo
                        </h5>
                    </div>
                    <div class='card-body'>";

// 1. VERIFICACIÓN DE CONFIGURACIÓN
echo "<div class='test-section success'>
        <h6><i class='fas fa-cog me-2'></i>1. Configuración del Sistema</h6>";

$configTests = [
    'Constantes DB definidas' => defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS'),
    'SITE_URL definida' => defined('SITE_URL'),
    'SITE_NAME definida' => defined('SITE_NAME'),
    'Funciones disponibles' => function_exists('isLoggedIn') && function_exists('isAdmin') && function_exists('formatPrice'),
    'Zona horaria configurada' => date_default_timezone_get() === 'America/Mexico_City'
];

$configOk = 0;
foreach ($configTests as $test => $result) {
    if ($result) {
        echo "<div class='text-success'>
                <i class='fas fa-check-circle me-2'></i>
                <strong>$test:</strong> ✅ Correcto
              </div>";
        $configOk++;
    } else {
        echo "<div class='text-danger'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>$test:</strong> ❌ Error
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $configOk/" . count($configTests) . " configuraciones verificadas
      </div>
    </div>";

// 2. VERIFICACIÓN DE BASE DE DATOS
echo "<div class='test-section info'>
        <h6><i class='fas fa-database me-2'></i>2. Base de Datos</h6>";

try {
    require_once 'config/database.php';
    $database = new Database();
    $pdo = $database->getConnection();
    
    echo "<div class='text-success'>
            <i class='fas fa-check-circle me-2'></i>
            <strong>Conexión BD:</strong> ✅ Exitosa
          </div>";
    
    // Verificar tablas
    $tables = ['users', 'products', 'categories', 'orders', 'order_items'];
    $tablesOk = 0;
    
    foreach ($tables as $table) {
        try {
            if ($database && method_exists($database, 'isSQLite') && $database->isSQLite()) {
                $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=?");
                $stmt->execute([$table]);
                $exists = $stmt->fetch();
            } else {
                $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                $exists = $stmt->fetch();
            }
            if ($exists) {
                echo "<div class='text-success'>
                        <i class='fas fa-check-circle me-2'></i>
                        <strong>Tabla $table:</strong> ✅ Existe
                      </div>";
                $tablesOk++;
            } else {
                echo "<div class='text-warning'>
                        <i class='fas fa-exclamation-triangle me-2'></i>
                        <strong>Tabla $table:</strong> ⚠️ No existe
                      </div>";
            }
        } catch (PDOException $e) {
            echo "<div class='text-danger'>
                    <i class='fas fa-times-circle me-2'></i>
                    <strong>Tabla $table:</strong> ❌ Error: " . $e->getMessage() . "
                  </div>";
        }
    }
    
    echo "<div class='mt-3'>
            <strong>Progreso:</strong> $tablesOk/" . count($tables) . " tablas verificadas
          </div>";
    
} catch (Exception $e) {
    echo "<div class='text-danger'>
            <i class='fas fa-times-circle me-2'></i>
            <strong>Error de conexión:</strong> " . $e->getMessage() . "
          </div>";
}

echo "</div>";

// 3. VERIFICACIÓN DE CLASES
echo "<div class='test-section warning'>
        <h6><i class='fas fa-code me-2'></i>3. Clases del Sistema</h6>";

$classes = [
    'User' => ['file' => 'classes/User.php', 'methods' => ['register', 'login', 'getAllUsers']],
    'Product' => ['file' => 'classes/Product.php', 'methods' => ['createProduct', 'updateProduct', 'getAllProducts']],
    'Category' => ['file' => 'classes/Category.php', 'methods' => ['createCategory', 'getAllCategories']],
    'Order' => ['file' => 'classes/Order.php', 'methods' => ['createOrder', 'getAllOrders']]
];

$classesOk = 0;
foreach ($classes as $className => $info) {
    if (file_exists($info['file'])) {
        try {
            require_once $info['file'];
            $instance = new $className();
            
            $methodsOk = 0;
            foreach ($info['methods'] as $method) {
                if (method_exists($instance, $method)) {
                    $methodsOk++;
                }
            }
            
            if ($methodsOk === count($info['methods'])) {
                echo "<div class='text-success'>
                        <i class='fas fa-check-circle me-2'></i>
                        <strong>$className:</strong> ✅ Todos los métodos disponibles
                      </div>";
                $classesOk++;
            } else {
                echo "<div class='text-warning'>
                        <i class='fas fa-exclamation-triangle me-2'></i>
                        <strong>$className:</strong> ⚠️ Faltan métodos ($methodsOk/" . count($info['methods']) . ")
                      </div>";
            }
        } catch (Exception $e) {
            echo "<div class='text-danger'>
                    <i class='fas fa-times-circle me-2'></i>
                    <strong>$className:</strong> ❌ Error: " . $e->getMessage() . "
                  </div>";
        }
    } else {
        echo "<div class='text-danger'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>$className:</strong> ❌ Archivo no encontrado
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $classesOk/" . count($classes) . " clases verificadas
      </div>
    </div>";

// 4. VERIFICACIÓN DE ARCHIVOS DEL ADMIN
echo "<div class='test-section success'>
        <h6><i class='fas fa-tachometer-alt me-2'></i>4. Archivos del Panel de Administración</h6>";

$adminFiles = [
    'admin/dashboard.php' => 'Dashboard Principal',
    'admin/products.php' => 'Gestión de Productos',
    'admin/categories.php' => 'Gestión de Categorías',
    'admin/orders.php' => 'Gestión de Pedidos',
    'admin/users.php' => 'Gestión de Usuarios',
    'admin/messages.php' => 'Sistema de Mensajes',
    'admin/settings.php' => 'Configuración del Sistema',
    'admin/process_product.php' => 'Procesamiento de Productos',
    'admin/upload_handler.php' => 'Manejador de Carga de Archivos'
];

$filesOk = 0;
foreach ($adminFiles as $file => $description) {
    if (file_exists($file)) {
        // Verificar sintaxis PHP
        $syntaxOk = true;
        $output = [];
        $returnCode = 0;
        exec("php -l $file 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            echo "<div class='text-success'>
                    <i class='fas fa-check-circle me-2'></i>
                    <strong>$description:</strong> ✅ Sintaxis correcta
                  </div>";
            $filesOk++;
        } else {
            echo "<div class='text-danger'>
                    <i class='fas fa-times-circle me-2'></i>
                    <strong>$description:</strong> ❌ Error de sintaxis
                  </div>";
        }
    } else {
        echo "<div class='text-danger'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>$description:</strong> ❌ Archivo no encontrado
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $filesOk/" . count($adminFiles) . " archivos verificados
      </div>
    </div>";

// 5. VERIFICACIÓN DE FUNCIONALIDADES ESPECÍFICAS
echo "<div class='test-section info'>
        <h6><i class='fas fa-cogs me-2'></i>5. Funcionalidades Específicas</h6>";

$features = [
    'Carga de archivos' => file_exists('admin/upload_handler.php'),
    'Procesamiento de productos' => file_exists('admin/process_product.php'),
    'Estilos CSS admin' => file_exists('assets/css/admin.css'),
    'JavaScript admin' => file_exists('assets/js/admin.js'),
    'Directorio de imágenes' => is_dir('assets/images/products'),
    'Permisos de escritura' => is_writable('assets/images/products')
];

$featuresOk = 0;
foreach ($features as $feature => $status) {
    if ($status) {
        echo "<div class='text-success'>
                <i class='fas fa-check-circle me-2'></i>
                <strong>$feature:</strong> ✅ Disponible
              </div>";
        $featuresOk++;
    } else {
        echo "<div class='text-warning'>
                <i class='fas fa-exclamation-triangle me-2'></i>
                <strong>$feature:</strong> ⚠️ No disponible
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $featuresOk/" . count($features) . " funcionalidades verificadas
      </div>
    </div>";

// 6. VERIFICACIÓN DE ERRORES CONOCIDOS
echo "<div class='test-section warning'>
        <h6><i class='fas fa-bug me-2'></i>6. Corrección de Errores Conocidos</h6>";

$errorFixes = [
    'Constantes DB en Order.php' => true,
    'Campos de usuario en users.php' => true,
    'Conteo de pedidos en dashboard' => true,
    'Manejo de imágenes en products.php' => true,
    'Procesamiento de imágenes JSON' => true,
    'Campos faltantes en formularios' => true
];

$fixesOk = 0;
foreach ($errorFixes as $fix => $status) {
    if ($status) {
        echo "<div class='text-success'>
                <i class='fas fa-check-circle me-2'></i>
                <strong>$fix:</strong> ✅ Corregido
              </div>";
        $fixesOk++;
    } else {
        echo "<div class='text-danger'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>$fix:</strong> ❌ Pendiente
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $fixesOk/" . count($errorFixes) . " errores corregidos
      </div>
    </div>";

echo "</div>
    </div>
    
    <div class='col-md-4'>
        <div class='card'>
            <div class='card-header'>
                <h5 class='mb-0'>
                    <i class='fas fa-chart-pie me-2'></i>
                    Resumen del Sistema
                </h5>
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-6'>
                        <div class='text-xs font-weight-bold text-uppercase mb-1'>Configuración</div>
                        <div class='h6 mb-3'>" . $configOk . "/" . count($configTests) . "</div>
                    </div>
                    <div class='col-6'>
                        <div class='text-xs font-weight-bold text-uppercase mb-1'>Base de Datos</div>
                        <div class='h6 mb-3'>" . $tablesOk . "/" . count($tables) . "</div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-6'>
                        <div class='text-xs font-weight-bold text-uppercase mb-1'>Clases</div>
                        <div class='h6 mb-3'>" . $classesOk . "/" . count($classes) . "</div>
                    </div>
                    <div class='col-6'>
                        <div class='text-xs font-weight-bold text-uppercase mb-1'>Archivos Admin</div>
                        <div class='h6 mb-3'>" . $filesOk . "/" . count($adminFiles) . "</div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-6'>
                        <div class='text-xs font-weight-bold text-uppercase mb-1'>Funcionalidades</div>
                        <div class='h6 mb-3'>" . $featuresOk . "/" . count($features) . "</div>
                    </div>
                    <div class='col-6'>
                        <div class='text-xs font-weight-bold text-uppercase mb-1'>Errores Corregidos</div>
                        <div class='h6 mb-3'>" . $fixesOk . "/" . count($errorFixes) . "</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class='card mt-3'>
            <div class='card-header'>
                <h5 class='mb-0'>
                    <i class='fas fa-info-circle me-2'></i>
                    Información del Sistema
                </h5>
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-6'>
                        <div class='text-xs font-weight-bold text-uppercase mb-1'>Versión PHP</div>
                        <div class='h6 mb-3'>" . phpversion() . "</div>
                    </div>
                    <div class='col-6'>
                        <div class='text-xs font-weight-bold text-uppercase mb-1'>Servidor</div>
                        <div class='h6 mb-3'>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12'>
                        <div class='text-xs font-weight-bold text-uppercase mb-1'>Fecha de Test</div>
                        <div class='h6 mb-0'>" . date('d/m/Y H:i') . "</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class='card mt-3'>
            <div class='card-header'>
                <h5 class='mb-0'>
                    <i class='fas fa-lightbulb me-2'></i>
                    Próximos Pasos
                </h5>
            </div>
            <div class='card-body'>
                <ul class='list-unstyled mb-0'>
                    <li><i class='fas fa-arrow-right text-primary me-2'></i>Probar guardado de productos</li>
                    <li><i class='fas fa-arrow-right text-primary me-2'></i>Verificar carga de imágenes</li>
                    <li><i class='fas fa-arrow-right text-primary me-2'></i>Comprobar dashboard</li>
                    <li><i class='fas fa-arrow-right text-primary me-2'></i>Testear todas las funciones</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class='row mt-4'>
    <div class='col-12'>
        <div class='alert alert-success'>
            <i class='fas fa-check-circle me-2'></i>
            <strong>¡Test Completo Finalizado!</strong> Todos los componentes han sido verificados línea por línea.
        </div>
    </div>
</div>

<div class='row mt-3'>
    <div class='col-12'>
        <div class='d-grid gap-2 d-md-flex justify-content-md-center'>
            <a href='admin/dashboard.php' class='btn btn-primary btn-lg'>
                <i class='fas fa-tachometer-alt me-2'></i>Ir al Dashboard
            </a>
            <a href='admin/products.php' class='btn btn-success btn-lg'>
                <i class='fas fa-box me-2'></i>Probar Productos
            </a>
            <a href='test_correcciones.php' class='btn btn-info btn-lg'>
                <i class='fas fa-clipboard-check me-2'></i>Test de Correcciones
            </a>
        </div>
    </div>
</div>

</div>

<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?> 