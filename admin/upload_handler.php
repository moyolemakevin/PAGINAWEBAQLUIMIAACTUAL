<?php
/**
 * Secure image upload handler for the admin panel.
 */

require_once __DIR__ . '/../config/bootstrap.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token CSRF invalido']);
    exit();
}

$folder = sanitizeUploadFolder($_POST['folder'] ?? 'products');
$uploadDir = __DIR__ . '/../assets/images/' . $folder . '/';
$maxFileSize = 5 * 1024 * 1024; // 5 MB
$maxFiles = 5;

if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo preparar la carpeta de carga']);
    exit();
}

function resizeImage($sourcePath, $destinationPath, $maxWidth = 800, $maxHeight = 600) {
    if (!extension_loaded('gd')) {
        return copy($sourcePath, $destinationPath);
    }

    $imageInfo = @getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }

    $width = $imageInfo[0];
    $height = $imageInfo[1];
    $type = $imageInfo[2];

    if ($width <= 0 || $height <= 0) {
        return false;
    }

    $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
    $newWidth = (int) round($width * $ratio);
    $newHeight = (int) round($height * $ratio);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = @imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $source = @imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $source = @imagecreatefromgif($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $source = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false;
            break;
        default:
            return false;
    }

    if (!$source) {
        return false;
    }

    $destination = imagecreatetruecolor($newWidth, $newHeight);
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
        $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
        imagefill($destination, 0, 0, $transparent);
    }

    imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

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
            $success = function_exists('imagewebp') ? imagewebp($destination, $destinationPath, 85) : false;
            break;
    }

    imagedestroy($source);
    imagedestroy($destination);

    return $success;
}

function logUploadError($message) {
    error_log('[upload_handler] ' . $message);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['images']) || !is_array($_FILES['images']['name'] ?? null)) {
    echo json_encode(['success' => false, 'message' => 'No se recibieron archivos']);
    exit();
}

$files = $_FILES['images'];
$fileCount = count($files['name']);

if ($fileCount === 0) {
    echo json_encode(['success' => false, 'message' => 'No se recibieron archivos validos']);
    exit();
}

if ($fileCount > $maxFiles) {
    $message = "Maximo $maxFiles archivos permitidos";
    logUploadError($message);
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

$uploadedFiles = [];
$errors = [];

for ($i = 0; $i < $fileCount; $i++) {
    $originalName = (string) ($files['name'][$i] ?? 'imagen');
    $tmpName = (string) ($files['tmp_name'][$i] ?? '');
    $fileSize = (int) ($files['size'][$i] ?? 0);
    $fileError = (int) ($files['error'][$i] ?? UPLOAD_ERR_NO_FILE);

    if ($fileError !== UPLOAD_ERR_OK) {
        $message = "Error en el archivo '$originalName': $fileError";
        logUploadError($message);
        $errors[] = $message;
        continue;
    }

    if ($tmpName === '' || !is_file($tmpName)) {
        $message = "El archivo '$originalName' no se pudo leer correctamente";
        logUploadError($message);
        $errors[] = $message;
        continue;
    }

    if ($fileSize <= 0 || $fileSize > $maxFileSize) {
        $message = "El archivo '$originalName' excede el tamano permitido";
        logUploadError($message);
        $errors[] = $message;
        continue;
    }

    $imageInfo = detectImageFileInfo($tmpName);
    if (!$imageInfo) {
        $message = "El archivo '$originalName' no es una imagen valida";
        logUploadError($message);
        $errors[] = $message;
        continue;
    }

    $fileName = generateSafeImageFileName($originalName, $imageInfo['extension'], $uploadDir);
    $filePath = $uploadDir . $fileName;

    $moveSuccess = move_uploaded_file($tmpName, $filePath);
    if (!$moveSuccess && !is_uploaded_file($tmpName)) {
        $moveSuccess = @rename($tmpName, $filePath);
    }

    if (!$moveSuccess) {
        $message = "Error al mover el archivo '$originalName'";
        logUploadError($message);
        $errors[] = $message;
        continue;
    }

    @chmod($filePath, 0644);

    $relativeOriginal = 'assets/images/' . $folder . '/' . $fileName;
    $relativeThumbnail = $relativeOriginal;
    $thumbnailPath = $uploadDir . 'thumb_' . $fileName;

    if (resizeImage($filePath, $thumbnailPath, 300, 300)) {
        @chmod($thumbnailPath, 0644);
        $relativeThumbnail = 'assets/images/' . $folder . '/thumb_' . $fileName;
    }

    $uploadedFiles[] = [
        'original' => $relativeOriginal,
        'thumbnail' => $relativeThumbnail,
        'name' => $originalName
    ];
}

if (!empty($uploadedFiles)) {
    echo json_encode([
        'success' => true,
        'files' => $uploadedFiles,
        'errors' => $errors
    ]);
    exit();
}

$message = !empty($errors) ? implode('; ', $errors) : 'No se subio ningun archivo';
logUploadError($message);

echo json_encode([
    'success' => false,
    'message' => $message,
    'errors' => $errors
]);
