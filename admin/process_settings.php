<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
    exit();
}

$allowedKeys = [
    'site_name',
    'site_description',
    'hero_image_url',
    'contact_email',
    'contact_phone',
    'whatsapp_number',
    'address',
    'google_maps_embed',
    'facebook_url',
    'instagram_url',
    'tiktok_url',
    'whatsapp_url',
    'logo_url',
    'favicon_url',
    'maintenance_mode',
    'allow_registration',
    'email_notifications',
    'debug_mode',
    'timezone',
    'default_language',
    'apply_iva',
    'iva_rate'
];

$booleanKeys = [
    'maintenance_mode',
    'allow_registration',
    'email_notifications',
    'debug_mode',
    'apply_iva'
];

$settings = [];
foreach ($allowedKeys as $key) {
    if (!array_key_exists($key, $_POST)) {
        continue;
    }

    if (in_array($key, $booleanKeys, true)) {
        $settings[$key] = $_POST[$key] === '1' || $_POST[$key] === 'true';
        continue;
    }

    $settings[$key] = trim((string)$_POST[$key]);
}

if (!empty($settings['contact_email']) && !filter_var($settings['contact_email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email de contacto inválido']);
    exit();
}

$urlKeys = [
    'google_maps_embed',
    'facebook_url',
    'instagram_url',
    'tiktok_url',
    'whatsapp_url',
    'logo_url',
    'favicon_url',
    'hero_image_url'
];

$imageKeys = ['logo_url', 'favicon_url', 'hero_image_url'];
$numericKeys = ['iva_rate'];

function normalizeAssetPath($value) {
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }

    return sanitizeLocalImagePath($value, true);

    $clean = str_replace('\\', '/', $value);

    // Bloquear esquemas peligrosos
    if (stripos($clean, 'javascript:') === 0) {
        return false;
    }

    // Permitir data URI de imágenes
    if (preg_match('#^data:image/(png|jpe?g|gif|webp|svg\\+xml);base64,#i', $clean)) {
        return $clean;
    }

    // Permitir URL protocolo-relativa
    if (strpos($clean, '//') === 0) {
        return $clean;
    }

    // Permitir URL absoluta
    if (filter_var($clean, FILTER_VALIDATE_URL)) {
        return $clean;
    }

    // Remover prefijo SITE_URL si viene incluido
    $siteUrl = rtrim(SITE_URL, '/');
    if ($siteUrl !== '' && strpos($clean, $siteUrl) === 0) {
        $clean = ltrim(substr($clean, strlen($siteUrl)), '/');
    }

    // Limpiar prefijos relativos y barras iniciales
    while (strpos($clean, '../') === 0) {
        $clean = substr($clean, 3);
    }
    while (strpos($clean, './') === 0) {
        $clean = substr($clean, 2);
    }
    $clean = ltrim($clean, '/');

    // Rechazar traversal restante
    if (strpos($clean, '..') !== false) {
        return false;
    }

    // Validar ruta relativa segura (con posible query)
    if (!preg_match('#^[a-zA-Z0-9._/-]+(?:\\?[A-Za-z0-9_\\-\\.=,&%/+]*)?$#', $clean)) {
        return false;
    }

    return $clean;
}

function isSafeUrl($value) {
    $value = trim((string)$value);
    if ($value === '') return true;

    if (stripos($value, 'javascript:') === 0) return false;
    if (strpos($value, '//') === 0) return true;
    if (filter_var($value, FILTER_VALIDATE_URL)) return true;

    return false;
}

foreach ($urlKeys as $key) {
    if (!isset($settings[$key])) {
        continue;
    }

    $value = $settings[$key];

    if (in_array($key, $imageKeys, true)) {
        $normalized = normalizeAssetPath($value);
        if ($normalized === false) {
            echo json_encode(['success' => false, 'message' => 'URL inválida en ' . $key]);
            exit();
        }
        $settings[$key] = $normalized;
        continue;
    }

    if (!isSafeUrl($value)) {
        echo json_encode(['success' => false, 'message' => 'URL inválida en ' . $key]);
        exit();
    }
}

// Validar numéricos
foreach ($numericKeys as $key) {
    if (!isset($settings[$key]) || $settings[$key] === '') {
        continue;
    }
    if (!is_numeric($settings[$key])) {
        echo json_encode(['success' => false, 'message' => 'Valor inválido en ' . $key]);
        exit();
    }
    $num = (float)$settings[$key];
    if ($num < 0 || $num > 100) {
        echo json_encode(['success' => false, 'message' => 'Valor fuera de rango en ' . $key]);
        exit();
    }
    $settings[$key] = $num;
}

$allowedTimezones = ['America/Guayaquil', 'UTC', 'America/New_York', 'Europe/Madrid'];
if (!empty($settings['timezone']) && !in_array($settings['timezone'], $allowedTimezones, true)) {
    echo json_encode(['success' => false, 'message' => 'Zona horaria inválida']);
    exit();
}

$allowedLanguages = ['es', 'en', 'fr'];
if (!empty($settings['default_language']) && !in_array($settings['default_language'], $allowedLanguages, true)) {
    echo json_encode(['success' => false, 'message' => 'Idioma inválido']);
    exit();
}

$settingsFile = __DIR__ . '/../data/settings.json';
$existing = [];
if (file_exists($settingsFile)) {
    $current = json_decode(file_get_contents($settingsFile), true);
    if (is_array($current)) {
        $existing = $current;
    }
}

$newSettings = array_merge($existing, $settings);
$encoded = json_encode($newSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if ($encoded === false) {
    echo json_encode(['success' => false, 'message' => 'Error al serializar configuración']);
    exit();
}

if (file_put_contents($settingsFile, $encoded) === false) {
    echo json_encode(['success' => false, 'message' => 'No se pudo guardar el archivo de configuración']);
    exit();
}

echo json_encode(['success' => true, 'message' => 'Configuración guardada']);
