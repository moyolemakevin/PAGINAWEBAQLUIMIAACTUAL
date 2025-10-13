<?php
/**
 * Manejador de Carga de Archivos Simplificado - Sin Autenticación
 * Para pruebas y debugging
 */

// Configuración de carga
$folder = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['folder'] ?? 'products');
$uploadDir = __DIR__ . '/../assets/images/' . $folder . '/';
$baseUrl = 'http://localhost/codex/'; // URL base hardcodeada para pruebas
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$maxFileSize = 5 * 1024 * 1024; // 5MB
$maxFiles = 5;

// Crear directorio si no existe
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Función para generar nombre único de archivo
function generateUniqueFileName($originalName, $uploadDir) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
    
    $fileName = $baseName . '_' . uniqid() . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    // Si el archivo ya existe, generar otro nombre
    $counter = 1;
    while (file_exists($filePath)) {
        $fileName = $baseName . '_' . uniqid() . '_' . $counter . '.' . $extension;
        $filePath = $uploadDir . $fileName;
        $counter++;
    }
    
    return $fileName;
}

// Función para redimensionar imagen (sin GD)
function resizeImage($sourcePath, $destinationPath, $maxWidth = 800, $maxHeight = 600) {
    // Verificar si GD está disponible
    if (!extension_loaded('gd')) {
        // Si no hay GD, simplemente copiar la imagen original
        return copy($sourcePath, $destinationPath);
    }
    
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) return false;
    
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    $type = $imageInfo[2];
    
    // Calcular nuevas dimensiones
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = round($width * $ratio);
    $newHeight = round($height * $ratio);
    
    // Crear imagen
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$source) return false;
    
    // Crear nueva imagen
    $destination = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preservar transparencia para PNG y GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
        $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
        imagefill($destination, 0, 0, $transparent);
    }
    
    // Redimensionar
    imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Guardar imagen
    $success = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($destination, $destinationPath, 85);
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($destination, $destinationPath, 8);
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($destination, $destinationPath);
            break;
        case IMAGETYPE_WEBP:
            $success = imagewebp($destination, $destinationPath, 85);
            break;
    }
    
    // Limpiar memoria
    imagedestroy($source);
    imagedestroy($destination);
    
    return $success;
}

// Función para loguear errores
function log_upload_error($msg) {
    $logFile = __DIR__ . '/../assets/images/upload_debug.log';
    $date = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$date] $msg\n", FILE_APPEND);
}

// Procesar carga de archivos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    $uploadedFiles = [];
    $errors = [];
    
    // Procesar múltiples archivos
    $files = $_FILES['images'];
    $fileCount = count($files['name']);
    
    if ($fileCount > $maxFiles) {
        $msg = "Máximo $maxFiles archivos permitidos";
        log_upload_error($msg);
        echo json_encode(['success' => false, 'message' => $msg]);
        exit();
    }
    
    for ($i = 0; $i < $fileCount; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $tmpName = $files['tmp_name'][$i];
            $originalName = $files['name'][$i];
            $fileSize = $files['size'][$i];
            $fileType = $files['type'][$i];
            
            // Validar tipo de archivo
            if (!in_array($fileType, $allowedTypes)) {
                $msg = "El archivo '$originalName' no es una imagen válida ($fileType)";
                log_upload_error($msg);
                $errors[] = $msg;
                continue;
            }
            
            // Validar tamaño
            if ($fileSize > $maxFileSize) {
                $msg = "El archivo '$originalName' es demasiado grande ($fileSize bytes)";
                log_upload_error($msg);
                $errors[] = $msg;
                continue;
            }
            
            // Generar nombre único
            $fileName = generateUniqueFileName($originalName, $uploadDir);
            $filePath = $uploadDir . $fileName;
            
            // Mover archivo
            $moveSuccess = move_uploaded_file($tmpName, $filePath);
            if (!$moveSuccess && !is_uploaded_file($tmpName)) {
                $moveSuccess = rename($tmpName, $filePath);
            }
            if ($moveSuccess) {
                // Crear versión redimensionada
                $resizedPath = $uploadDir . 'thumb_' . $fileName;
                $resizeResult = resizeImage($filePath, $resizedPath, 300, 300);
                if ($resizeResult) {
                    $uploadedFiles[] = [
                        'original' => $baseUrl . 'assets/images/' . $folder . '/' . $fileName,
                        'thumbnail' => $baseUrl . 'assets/images/' . $folder . '/thumb_' . $fileName,
                        'name' => $originalName
                    ];
                } else {
                    $msg = "No se pudo redimensionar la imagen '$originalName'. Puede que la extensión GD no esté habilitada.";
                    log_upload_error($msg);
                    // Si no se puede redimensionar, usar la original
                    $uploadedFiles[] = [
                        'original' => $baseUrl . 'assets/images/' . $folder . '/' . $fileName,
                        'thumbnail' => $baseUrl . 'assets/images/' . $folder . '/' . $fileName,
                        'name' => $originalName
                    ];
                }
            } else {
                $msg = "Error al mover el archivo '$originalName' a '$filePath'. Permisos?";
                log_upload_error($msg);
                $errors[] = $msg;
            }
        } else {
            $msg = "Error en el archivo '" . $files['name'][$i] . "': " . $files['error'][$i];
            log_upload_error($msg);
            $errors[] = $msg;
        }
    }
    
    // Respuesta
    if (!empty($uploadedFiles)) {
        echo json_encode([
            'success' => true,
            'files' => $uploadedFiles,
            'errors' => $errors
        ]);
    } else {
        $msg = !empty($errors) ? implode('; ', $errors) : 'No se subió ningún archivo';
        log_upload_error($msg);
        echo json_encode([
            'success' => false,
            'message' => $msg,
            'errors' => $errors
        ]);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'No se recibieron archivos']);
    exit();
}
?> 