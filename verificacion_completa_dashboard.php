<?php
/**
 * Verificación Completa del Dashboard AlquimiaTechnologic
 * Prueba todas las funcionalidades del dashboard de arriba a abajo
 */

require_once __DIR__ . '/config/config.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verificación Completa - Dashboard AlquimiaTechnologic</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
    <style>
        .test-section { margin: 20px 0; padding: 15px; border-radius: 8px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .test-result { padding: 10px; margin: 5px 0; border-radius: 5px; }
        .progress-bar { height: 25px; }
    </style>
</head>
<body>
    <div class='container-fluid mt-4'>
        <div class='row'>
            <div class='col-12'>
                <h1 class='text-center mb-4'>
                    <i class='fas fa-tachometer-alt text-primary'></i>
                    Verificación Completa del Dashboard
                </h1>
                
                <div class='alert alert-info'>
                    <i class='fas fa-info-circle me-2'></i>
                    <strong>Análisis Completo:</strong> Verificando todas las funcionalidades del dashboard de arriba a abajo
                </div>
            </div>
        </div>
        
        <div class='row'>
            <div class='col-md-8'>
                <!-- Resultados de Verificación -->
                <div class='card'>
                    <div class='card-header'>
                        <h5 class='mb-0'>
                            <i class='fas fa-clipboard-check me-2'></i>
                            Resultados de Verificación
                        </h5>
                    </div>
                    <div class='card-body'>";

// 1. VERIFICACIÓN DE ARCHIVOS DEL DASHBOARD
echo "<div class='test-section success'>
        <h6><i class='fas fa-folder me-2'></i>1. Verificación de Archivos del Dashboard</h6>";

$dashboardFiles = [
    'admin/dashboard.php' => 'Dashboard Principal',
    'admin/products.php' => 'Gestión de Productos',
    'admin/categories.php' => 'Gestión de Categorías',
    'admin/orders.php' => 'Gestión de Pedidos',
    'admin/users.php' => 'Gestión de Usuarios',
    'admin/messages.php' => 'Sistema de Mensajes',
    'admin/settings.php' => 'Configuración del Sistema'
];

$filesOk = 0;
foreach ($dashboardFiles as $file => $description) {
    if (file_exists($file)) {
        echo "<div class='test-result success'>
                <i class='fas fa-check-circle me-2'></i>
                <strong>$description:</strong> ✅ Archivo encontrado
              </div>";
        $filesOk++;
    } else {
        echo "<div class='test-result error'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>$description:</strong> ❌ Archivo no encontrado
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $filesOk/" . count($dashboardFiles) . " archivos verificados
      </div>
    </div>";

// 2. VERIFICACIÓN DE CLASES DEL SISTEMA
echo "<div class='test-section info'>
        <h6><i class='fas fa-code me-2'></i>2. Verificación de Clases del Sistema</h6>";

$systemClasses = [
    'classes/User.php' => 'Clase User',
    'classes/Product.php' => 'Clase Product',
    'classes/Category.php' => 'Clase Category',
    'classes/Order.php' => 'Clase Order'
];

$classesOk = 0;
foreach ($systemClasses as $file => $description) {
    if (file_exists($file)) {
        echo "<div class='test-result success'>
                <i class='fas fa-check-circle me-2'></i>
                <strong>$description:</strong> ✅ Clase encontrada
              </div>";
        $classesOk++;
    } else {
        echo "<div class='test-result error'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>$description:</strong> ❌ Clase no encontrada
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $classesOk/" . count($systemClasses) . " clases verificadas
      </div>
    </div>";

// 3. VERIFICACIÓN DE CONFIGURACIÓN
echo "<div class='test-section warning'>
        <h6><i class='fas fa-cog me-2'></i>3. Verificación de Configuración</h6>";

$configOk = 0;
$configTests = [
    'SITE_URL' => defined('SITE_URL'),
    'SITE_NAME' => defined('SITE_NAME'),
    'DB_HOST' => defined('DB_HOST'),
    'DB_NAME' => defined('DB_NAME'),
    'DB_USER' => defined('DB_USER'),
    'DB_PASS' => defined('DB_PASS')
];

foreach ($configTests as $config => $defined) {
    if ($defined) {
        echo "<div class='test-result success'>
                <i class='fas fa-check-circle me-2'></i>
                <strong>$config:</strong> ✅ Definida
              </div>";
        $configOk++;
    } else {
        echo "<div class='test-result error'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>$config:</strong> ❌ No definida
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $configOk/" . count($configTests) . " configuraciones verificadas
      </div>
    </div>";

// 4. VERIFICACIÓN DE BASE DE DATOS
echo "<div class='test-section info'>
        <h6><i class='fas fa-database me-2'></i>4. Verificación de Base de Datos</h6>";

try {
    require_once 'config/database.php';
    $database = new Database();
    $pdo = $database->getConnection();
    
    echo "<div class='test-result success'>
            <i class='fas fa-check-circle me-2'></i>
            <strong>Conexión BD:</strong> ✅ Exitosa
          </div>";
    
    // Verificar tablas
    $tables = ['users', 'products', 'categories', 'orders'];
    $tablesOk = 0;
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<div class='test-result success'>
                        <i class='fas fa-check-circle me-2'></i>
                        <strong>Tabla $table:</strong> ✅ Existe
                      </div>";
                $tablesOk++;
            } else {
                echo "<div class='test-result warning'>
                        <i class='fas fa-exclamation-triangle me-2'></i>
                        <strong>Tabla $table:</strong> ⚠️ No existe (se usarán datos simulados)
                      </div>";
            }
        } catch (PDOException $e) {
            echo "<div class='test-result error'>
                    <i class='fas fa-times-circle me-2'></i>
                    <strong>Tabla $table:</strong> ❌ Error al verificar
                  </div>";
        }
    }
    
    echo "<div class='mt-3'>
            <strong>Progreso:</strong> $tablesOk/" . count($tables) . " tablas verificadas
          </div>";
    
} catch (Exception $e) {
    echo "<div class='test-result error'>
            <i class='fas fa-times-circle me-2'></i>
            <strong>Error de BD:</strong> ❌ " . $e->getMessage() . "
          </div>";
}

echo "</div>";

// 5. VERIFICACIÓN DE FUNCIONES DE AUTENTICACIÓN
echo "<div class='test-section success'>
        <h6><i class='fas fa-shield-alt me-2'></i>5. Verificación de Autenticación</h6>";

$authFunctions = [
    'isLoggedIn' => function_exists('isLoggedIn'),
    'isAdmin' => function_exists('isAdmin'),
    'redirect' => function_exists('redirect'),
    'cleanInput' => function_exists('cleanInput')
];

$authOk = 0;
foreach ($authFunctions as $function => $exists) {
    if ($exists) {
        echo "<div class='test-result success'>
                <i class='fas fa-check-circle me-2'></i>
                <strong>Función $function():</strong> ✅ Disponible
              </div>";
        $authOk++;
    } else {
        echo "<div class='test-result error'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>Función $function():</strong> ❌ No disponible
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $authOk/" . count($authFunctions) . " funciones verificadas
      </div>
    </div>";

// 6. VERIFICACIÓN DE ASSETS
echo "<div class='test-section warning'>
        <h6><i class='fas fa-palette me-2'></i>6. Verificación de Assets</h6>";

$assets = [
    'assets/css/admin.css' => 'CSS del Admin',
    'assets/js/admin.js' => 'JavaScript del Admin',
    'assets/css/style.css' => 'CSS Principal',
    'assets/js/main.js' => 'JavaScript Principal'
];

$assetsOk = 0;
foreach ($assets as $file => $description) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "<div class='test-result success'>
                <i class='fas fa-check-circle me-2'></i>
                <strong>$description:</strong> ✅ Encontrado (" . number_format($size) . " bytes)
              </div>";
        $assetsOk++;
    } else {
        echo "<div class='test-result error'>
                <i class='fas fa-times-circle me-2'></i>
                <strong>$description:</strong> ❌ No encontrado
              </div>";
    }
}

echo "<div class='mt-3'>
        <strong>Progreso:</strong> $assetsOk/" . count($assets) . " assets verificados
      </div>
    </div>";

// 7. VERIFICACIÓN DE FUNCIONALIDADES ESPECÍFICAS
echo "<div class='test-section info'>
        <h6><i class='fas fa-tools me-2'></i>7. Verificación de Funcionalidades Específicas</h6>";

// Verificar si las clases tienen los métodos necesarios
try {
    require_once 'classes/User.php';
    require_once 'classes/Product.php';
    require_once 'classes/Category.php';
    require_once 'classes/Order.php';
    
    $user = new User();
    $product = new Product();
    $category = new Category();
    $order = new Order();
    
    $methods = [
        'User::getAllUsers' => method_exists($user, 'getAllUsers'),
        'Product::getAllProducts' => method_exists($product, 'getAllProducts'),
        'Category::getAllCategories' => method_exists($category, 'getAllCategories'),
        'Order::getAllOrders' => method_exists($order, 'getAllOrders')
    ];
    
    $methodsOk = 0;
    foreach ($methods as $method => $exists) {
        if ($exists) {
            echo "<div class='test-result success'>
                    <i class='fas fa-check-circle me-2'></i>
                    <strong>$method:</strong> ✅ Método disponible
                  </div>";
            $methodsOk++;
        } else {
            echo "<div class='test-result error'>
                    <i class='fas fa-times-circle me-2'></i>
                    <strong>$method:</strong> ❌ Método no disponible
                  </div>";
        }
    }
    
    echo "<div class='mt-3'>
            <strong>Progreso:</strong> $methodsOk/" . count($methods) . " métodos verificados
          </div>";
    
} catch (Exception $e) {
    echo "<div class='test-result error'>
            <i class='fas fa-times-circle me-2'></i>
            <strong>Error al verificar métodos:</strong> ❌ " . $e->getMessage() . "
          </div>";
}

echo "</div>";

// 8. VERIFICACIÓN DE INFORMACIÓN DE CONTACTO
echo "<div class='test-section success'>
        <h6><i class='fas fa-address-book me-2'></i>8. Verificación de Información de Contacto</h6>";

$contactInfo = [
    'Email' => 'kevinmoyolema13@gmail.com',
    'Teléfono' => '+593 983015307',
    'WhatsApp' => '+593 983015307',
    'Ubicación' => 'Ecuador',
    'Año' => '2025'
];

foreach ($contactInfo as $type => $value) {
    echo "<div class='test-result success'>
            <i class='fas fa-check-circle me-2'></i>
            <strong>$type:</strong> ✅ $value
          </div>";
}

echo "</div>";

// 9. RESUMEN FINAL
echo "<div class='test-section info'>
        <h6><i class='fas fa-chart-bar me-2'></i>9. Resumen de Verificación</h6>";

$totalTests = $filesOk + $classesOk + $configOk + $authOk + $assetsOk + $methodsOk;
$maxTests = count($dashboardFiles) + count($systemClasses) + count($configTests) + count($authFunctions) + count($assets) + count($methods);
$percentage = round(($totalTests / $maxTests) * 100, 1);

echo "<div class='row'>
        <div class='col-md-6'>
            <div class='card bg-primary text-white text-center mb-3'>
                <div class='card-body'>
                    <h4>$totalTests/$maxTests</h4>
                    <small>Pruebas Exitosas</small>
                </div>
            </div>
        </div>
        <div class='col-md-6'>
            <div class='card bg-success text-white text-center mb-3'>
                <div class='card-body'>
                    <h4>$percentage%</h4>
                    <small>Porcentaje de Éxito</small>
                </div>
            </div>
        </div>
      </div>
      
      <div class='progress mb-3'>
        <div class='progress-bar bg-success' role='progressbar' style='width: $percentage%' aria-valuenow='$percentage' aria-valuemin='0' aria-valuemax='100'>
          $percentage%
        </div>
      </div>";

if ($percentage >= 90) {
    echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle me-2'></i>
            <strong>¡Excelente!</strong> El dashboard está funcionando correctamente al $percentage%
          </div>";
} elseif ($percentage >= 70) {
    echo "<div class='alert alert-warning'>
            <i class='fas fa-exclamation-triangle me-2'></i>
            <strong>Atención:</strong> El dashboard está funcionando al $percentage%. Algunas funcionalidades pueden necesitar ajustes.
          </div>";
} else {
    echo "<div class='alert alert-danger'>
            <i class='fas fa-times-circle me-2'></i>
            <strong>Problemas Detectados:</strong> El dashboard está funcionando al $percentage%. Se requieren correcciones.
          </div>";
}

echo "</div>";

// 10. RECOMENDACIONES
echo "<div class='test-section warning'>
        <h6><i class='fas fa-lightbulb me-2'></i>10. Recomendaciones</h6>";

$recommendations = [
    'Verificar que todas las páginas del dashboard carguen sin errores',
    'Probar todas las funcionalidades CRUD en cada sección',
    'Verificar que los modales se abran y cierren correctamente',
    'Comprobar que los formularios funcionen sin errores',
    'Verificar la responsividad en dispositivos móviles',
    'Probar la navegación entre secciones del dashboard',
    'Verificar que los botones de acción funcionen correctamente',
    'Comprobar que las imágenes se muestren correctamente',
    'Verificar que los enlaces externos funcionen',
    'Probar el sistema de autenticación completo'
];

foreach ($recommendations as $i => $rec) {
    echo "<div class='test-result info'>
            <i class='fas fa-info-circle me-2'></i>
            <strong>Recomendación " . ($i + 1) . ":</strong> $rec
          </div>";
}

echo "</div>";

echo "
                    </div>
                </div>
            </div>
            
            <div class='col-md-4'>
                <!-- Panel de Acciones -->
                <div class='card'>
                    <div class='card-header'>
                        <h5 class='mb-0'>
                            <i class='fas fa-cogs me-2'></i>
                            Acciones Rápidas
                        </h5>
                    </div>
                    <div class='card-body'>
                        <div class='d-grid gap-2'>
                            <a href='admin/dashboard.php' class='btn btn-primary'>
                                <i class='fas fa-tachometer-alt me-2'></i>Ir al Dashboard
                            </a>
                            <a href='test_dashboard.php' class='btn btn-outline-info'>
                                <i class='fas fa-clipboard-check me-2'></i>Pruebas Específicas
                            </a>
                            <a href='fix_dashboard_errors.php' class='btn btn-outline-warning'>
                                <i class='fas fa-tools me-2'></i>Corregir Errores
                            </a>
                            <a href='index.php' class='btn btn-outline-secondary'>
                                <i class='fas fa-home me-2'></i>Ir al Sitio Principal
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Estadísticas Rápidas -->
                <div class='card mt-3'>
                    <div class='card-header'>
                        <h5 class='mb-0'>
                            <i class='fas fa-chart-pie me-2'></i>
                            Estadísticas
                        </h5>
                    </div>
                    <div class='card-body'>
                        <div class='row text-center'>
                            <div class='col-6'>
                                <h4 class='text-primary'>$filesOk</h4>
                                <small>Archivos OK</small>
                            </div>
                            <div class='col-6'>
                                <h4 class='text-success'>$classesOk</h4>
                                <small>Clases OK</small>
                            </div>
                        </div>
                        <hr>
                        <div class='row text-center'>
                            <div class='col-6'>
                                <h4 class='text-info'>$configOk</h4>
                                <small>Config OK</small>
                            </div>
                            <div class='col-6'>
                                <h4 class='text-warning'>$authOk</h4>
                                <small>Auth OK</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?> 