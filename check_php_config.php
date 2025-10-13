<?php
echo "<h2>Verificación de Configuración PHP</h2>";

echo "<h3>Extensiones de Imagen</h3>";
echo "GD: " . (extension_loaded('gd') ? "✅ Habilitado" : "❌ Deshabilitado") . "<br>";
if (extension_loaded('gd')) {
    $gd_info = gd_info();
    echo "Versión GD: " . $gd_info['GD Version'] . "<br>";
    echo "JPEG Support: " . ($gd_info['JPEG Support'] ? "✅" : "❌") . "<br>";
    echo "PNG Support: " . ($gd_info['PNG Support'] ? "✅" : "❌") . "<br>";
    echo "GIF Support: " . ($gd_info['GIF Support'] ? "✅" : "❌") . "<br>";
    echo "WebP Support: " . ($gd_info['WebP Support'] ? "✅" : "❌") . "<br>";
}

echo "<h3>Configuración de Subida</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";

echo "<h3>Configuración Recomendada</h3>";
echo "Para tu caso, deberías tener:<br>";
echo "upload_max_filesize = 40M<br>";
echo "post_max_size = 40M<br>";
echo "max_file_uploads = 20<br>";
echo "max_execution_time = 300<br>";
echo "memory_limit = 256M<br>";

echo "<h3>Verificación de Carpetas</h3>";
$folders = [
    'assets/images/',
    'assets/images/products/',
    'assets/images/categories/',
    'assets/images/settings/'
];

foreach ($folders as $folder) {
    if (file_exists($folder)) {
        echo "✅ $folder existe";
        if (is_writable($folder)) {
            echo " y es escribible";
        } else {
            echo " pero NO es escribible";
        }
        echo "<br>";
    } else {
        echo "❌ $folder NO existe<br>";
    }
}

echo "<h3>Test de Subida Simple</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    $file = $_FILES['test_file'];
    echo "Archivo recibido: " . $file['name'] . "<br>";
    echo "Tamaño: " . $file['size'] . " bytes<br>";
    echo "Tipo: " . $file['type'] . "<br>";
    echo "Error: " . $file['error'] . "<br>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'assets/images/test/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = 'test_' . uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            echo "✅ Archivo subido exitosamente a: $filePath<br>";
            echo "<img src='$filePath' style='max-width: 200px; border: 1px solid #ccc;'><br>";
        } else {
            echo "❌ Error al mover el archivo<br>";
        }
    }
}
?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="test_file" accept="image/*" required>
    <button type="submit">Probar Subida</button>
</form> 