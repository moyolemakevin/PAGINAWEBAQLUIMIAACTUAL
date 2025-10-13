<?php
// Crear una imagen placeholder simple usando datos base64
$placeholderData = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

// Decodificar y guardar
$imageData = base64_decode($placeholderData);
file_put_contents('assets/images/placeholder.jpg', $imageData);

echo "Placeholder image created successfully!\n";
?> 