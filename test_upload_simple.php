<?php
/**
 * Test Simple de Upload Handler
 * Para verificar si el problema está en la autenticación o en el código
 */

// Simular una petición POST con archivos
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['folder'] = 'settings';
$_POST['csrf_token'] = 'test_token';

// Crear un archivo de prueba temporal
$testImageData = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
$tempFile = tempnam(sys_get_temp_dir(), 'test_image');
file_put_contents($tempFile, base64_decode($testImageData));

// Simular $_FILES
$_FILES['images'] = [
    'name' => ['test.png'],
    'type' => ['image/png'],
    'tmp_name' => [$tempFile],
    'error' => [UPLOAD_ERR_OK],
    'size' => [filesize($tempFile)]
];

echo "<h2>🧪 Test Simple de Upload Handler</h2>";

// Verificar configuración básica
echo "<h3>1. Verificación Básica</h3>";
echo "Método: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "Archivos recibidos: " . (isset($_FILES['images']) ? 'Sí' : 'No') . "<br>";
echo "Carpeta: " . $_POST['folder'] . "<br>";

// Verificar directorio de destino
$uploadDir = __DIR__ . '/assets/images/' . $_POST['folder'] . '/';
echo "Directorio de destino: $uploadDir<br>";
if (file_exists($uploadDir)) {
    echo "✅ El directorio existe<br>";
    if (is_writable($uploadDir)) {
        echo "✅ El directorio es escribible<br>";
    } else {
        echo "❌ El directorio NO es escribible<br>";
    }
} else {
    echo "❌ El directorio NO existe<br>";
    if (mkdir($uploadDir, 0755, true)) {
        echo "✅ Directorio creado exitosamente<br>";
    } else {
        echo "❌ No se pudo crear el directorio<br>";
    }
}

// Procesar archivo de prueba
echo "<h3>2. Procesamiento de Archivo</h3>";
if (isset($_FILES['images']) && $_FILES['images']['error'][0] === UPLOAD_ERR_OK) {
    $file = $_FILES['images'];
    echo "✅ Archivo recibido correctamente<br>";
    echo "Nombre: " . $file['name'][0] . "<br>";
    echo "Tipo: " . $file['type'][0] . "<br>";
    echo "Tamaño: " . $file['size'][0] . " bytes<br>";
    
    // Intentar mover el archivo
    $fileName = 'test_' . uniqid() . '_' . basename($file['name'][0]);
    $filePath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'][0], $filePath)) {
        echo "✅ Archivo movido exitosamente a: $filePath<br>";
        echo "<img src='assets/images/" . $_POST['folder'] . "/$fileName' style='max-width: 200px; border: 1px solid #ccc;'><br>";
    } elseif (rename($file['tmp_name'][0], $filePath)) {
        echo "✅ Archivo movido con rename a: $filePath<br>";
        echo "<img src='assets/images/" . $_POST['folder'] . "/$fileName' style='max-width: 200px; border: 1px solid #ccc;'><br>";
    } else {
        echo "❌ Error al mover el archivo<br>";
    }
} else {
    echo "❌ Error en el archivo: " . ($_FILES['images']['error'][0] ?? 'Desconocido') . "<br>";
}

// Limpiar archivo temporal
if (file_exists($tempFile)) {
    unlink($tempFile);
}

echo "<h3>3. Próximos Pasos</h3>";
echo "Si el test básico funciona, el problema está en la autenticación o configuración.<br>";
echo "Si no funciona, el problema está en el código de subida.<br>";
?> 