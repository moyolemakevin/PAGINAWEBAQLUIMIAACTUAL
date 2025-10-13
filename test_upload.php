<?php
// Script de prueba para verificar subida de archivos
echo "<h2>Test de Subida de Archivos</h2>";

// Verificar si GD está habilitado
echo "<h3>1. Verificación de GD</h3>";
if (extension_loaded('gd')) {
    echo "✅ GD está habilitado<br>";
    echo "Versión GD: " . gd_info()['GD Version'] . "<br>";
} else {
    echo "❌ GD NO está habilitado<br>";
}

// Verificar permisos de carpetas
echo "<h3>2. Verificación de Permisos</h3>";
$uploadDir = __DIR__ . '/assets/images/products/';
echo "Directorio: $uploadDir<br>";
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

// Verificar configuración de PHP
echo "<h3>3. Configuración de PHP</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";

// Formulario de prueba
echo "<h3>4. Formulario de Prueba</h3>";
?>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="test_image" accept="image/*" required>
    <button type="submit">Subir Imagen de Prueba</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    echo "<h3>5. Resultado de la Prueba</h3>";
    $file = $_FILES['test_image'];
    
    echo "Nombre: " . $file['name'] . "<br>";
    echo "Tipo: " . $file['type'] . "<br>";
    echo "Tamaño: " . $file['size'] . " bytes<br>";
    echo "Error: " . $file['error'] . "<br>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = 'test_' . uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            echo "✅ Archivo subido exitosamente a: $filePath<br>";
            echo "<img src='assets/images/products/$fileName' style='max-width: 200px;'><br>";
        } else {
            echo "❌ Error al mover el archivo<br>";
        }
    } else {
        echo "❌ Error en la subida: " . $file['error'] . "<br>";
    }
}
?> 