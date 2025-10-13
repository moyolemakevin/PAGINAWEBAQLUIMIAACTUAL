<?php
// Iniciar sesión
session_start();

// Configuración general
// Intentar detectar automáticamente la URL de la aplicación para evitar
// problemas cuando el proyecto no se aloja exactamente en "TiendawebAlquimia".
// Si necesitas forzar una URL distinta, define SITE_URL antes de incluir
// este archivo.
if (!defined('SITE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
    $rootDir = str_replace('\\', '/', dirname(__DIR__));
    $basePath = rtrim(str_replace($docRoot, '', $rootDir), '/');
    define('SITE_URL', $protocol . $host . ($basePath ? '/' . ltrim($basePath, '/') : ''));
}
define('SITE_NAME', 'AlquimiaTechnologic');
define('ADMIN_EMAIL', 'admin@alquimiatechnologic.com');

// Configuración de la base de datos
// Datos de conexión para InfinityFree
define('DB_HOST', 'sql306.infinityfree.com');
define('DB_NAME', 'if0_39489683_alquimia_technologic');
define('DB_USER', 'if0_39489683');
define('DB_PASS', 'cfaXshkzbj');

require_once __DIR__ . '/database.php';

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Función para incluir archivos de forma segura
function includeFile($file) {
    if (file_exists($file)) {
        include $file;
    } else {
        die("Archivo no encontrado: $file");
    }
}

// Función para redireccionar
function redirect($url) {
    header("Location: $url");
    exit();
}

// Función para limpiar datos de entrada
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Función para verificar si el usuario es administrador
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Función para generar token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Función para verificar token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Función para formatear precio
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Función para generar slug
function generateSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

// Obtener un valor de la configuración del sitio almacenado en data/settings.json
function getSiteSetting($key, $default = null) {
    static $siteSettings = null;
    if ($siteSettings === null) {
        $settingsFile = __DIR__ . '/../data/settings.json';
        if (file_exists($settingsFile)) {
            $json = file_get_contents($settingsFile);
            $data = json_decode($json, true);
            if (is_array($data)) {
                $siteSettings = $data;
            } else {
                $siteSettings = [];
            }
        } else {
            $siteSettings = [];
        }
    }
    return $siteSettings[$key] ?? $default;
}

// Configuración de errores (solo para desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?> 