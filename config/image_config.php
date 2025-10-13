<?php
// Configuración de imágenes - AlquimiaTechnologic
define('IMAGE_QUALITY', 85);
define('IMAGE_MAX_WIDTH', 800);
define('IMAGE_MAX_HEIGHT', 600);
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 300);
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'webp', 'gif']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('IMAGE_CACHE_TIME', 31536000); // 1 año

// Función para optimizar imagen
function optimizeImage($sourcePath, $destinationPath, $quality = 85, $maxWidth = 800, $maxHeight = 600) {
    if (!extension_loaded('gd')) {
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
    
    // Guardar imagen optimizada
    $success = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($destination, $destinationPath, $quality);
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($destination, $destinationPath, round((100 - $quality) / 10));
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($destination, $destinationPath);
            break;
        case IMAGETYPE_WEBP:
            $success = imagewebp($destination, $destinationPath, $quality);
            break;
    }
    
    // Limpiar memoria
    imagedestroy($source);
    imagedestroy($destination);
    
    return $success;
}
?>