<?php
require_once __DIR__ . '/config/config.php';

echo "<h1>Comprobación de Conexión a Base de Datos</h1>";

try {
    $database = new Database();
    $pdo = $database->getConnection();
    if ($database->isSQLite()) {
        $version = $pdo->query('SELECT sqlite_version()')->fetchColumn();
        echo "<p style='color:green'>✅ Conexión SQLite exitosa. Versión: $version</p>";
    } else {
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        echo "<p style='color:green'>✅ Conexión MySQL exitosa. Versión del servidor: $version</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error de conexión: " . $e->getMessage() . "</p>";
    if (!$database || !$database->isSQLite()) {
        echo "<p>Verifique que el servicio MySQL esté activo y que las credenciales en <code>config/config.php</code> sean correctas.</p>";
        echo "<p>Host: " . DB_HOST . "</p>";
        echo "<p>Base de datos: " . DB_NAME . "</p>";
        echo "<p>Usuario: " . DB_USER . "</p>";
    }
}
