<?php
/**
 * Script para aplicar configuración de PHP recomendada
 * Ejecutar desde la línea de comandos: php apply_php_config.php
 */

echo "=== Aplicando Configuración de PHP ===\n\n";

// Configuración recomendada
$recommended_config = [
    'upload_max_filesize' => '40M',
    'post_max_size' => '40M',
    'max_file_uploads' => '20',
    'max_execution_time' => '300',
    'memory_limit' => '256M'
];

echo "Configuración actual vs recomendada:\n";
echo str_repeat('-', 50) . "\n";

$php_ini_path = 'C:/xampp/php/php.ini';
$backup_path = 'C:/xampp/php/php.ini.backup.' . date('Y-m-d_H-i-s');

if (!file_exists($php_ini_path)) {
    echo "❌ No se encontró el archivo php.ini en: $php_ini_path\n";
    exit(1);
}

// Crear backup
if (copy($php_ini_path, $backup_path)) {
    echo "✅ Backup creado en: $backup_path\n";
} else {
    echo "❌ No se pudo crear el backup\n";
    exit(1);
}

// Leer archivo actual
$content = file_get_contents($php_ini_path);

// Aplicar configuraciones
$changes_made = 0;
foreach ($recommended_config as $setting => $value) {
    $pattern = "/^;?\s*$setting\s*=\s*.*$/m";
    $replacement = "$setting = $value";
    
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $replacement, $content);
        echo "✅ $setting = $value\n";
        $changes_made++;
    } else {
        echo "⚠️  $setting no encontrado en php.ini\n";
    }
}

// Habilitar GD
if (strpos($content, ';extension=gd') !== false) {
    $content = str_replace(';extension=gd', 'extension=gd', $content);
    echo "✅ GD habilitado\n";
    $changes_made++;
} elseif (strpos($content, 'extension=gd') !== false) {
    echo "✅ GD ya estaba habilitado\n";
} else {
    echo "⚠️  No se encontró la línea de GD en php.ini\n";
}

// Guardar cambios
if (file_put_contents($php_ini_path, $content)) {
    echo "\n✅ Configuración aplicada exitosamente!\n";
    echo "📝 Se realizaron $changes_made cambios\n";
    echo "\n🔄 Por favor reinicia Apache en XAMPP para aplicar los cambios\n";
    echo "\n📋 Pasos para reiniciar:\n";
    echo "1. Ve al Panel de Control de XAMPP\n";
    echo "2. Detén Apache\n";
    echo "3. Inicia Apache de nuevo\n";
    echo "4. Ve a: http://localhost/codex/check_php_config.php\n";
} else {
    echo "❌ No se pudo guardar la configuración\n";
    echo "🔧 Restaurando backup...\n";
    copy($backup_path, $php_ini_path);
    exit(1);
}

echo "\n=== Fin ===\n";
?> 