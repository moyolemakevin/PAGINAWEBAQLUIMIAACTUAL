# 🔧 Correcciones del Sistema de Imágenes - AlquimiaTechnologic

## 📋 Resumen del Problema

El sistema de imágenes de productos presentaba los siguientes problemas:

1. **Formato JSON inconsistente**: El `upload_handler.php` guardaba imágenes como objetos `{original, thumbnail, name}`, pero `getImagePath()` esperaba strings directos.
2. **Validación insuficiente**: No se verificaba correctamente la existencia y validez de los archivos de imagen.
3. **Manejo incorrecto de rutas**: Las rutas de las imágenes no se procesaban correctamente en el frontend.

## ✅ Correcciones Implementadas

### 1. Método `getImagePath()` en `classes/Product.php`

**Problema**: Solo manejaba arrays de strings, no objetos.

**Solución**: 
```php
public static function getImagePath($product) {
    $imagesJson = is_array($product) ? ($product['images'] ?? '') : $product;

    if ($imagesJson) {
        $images = json_decode($imagesJson, true);
        if (json_last_error() === JSON_ERROR_NONE && !empty($images)) {
            // Manejar tanto arrays de strings como arrays de objetos
            $firstImage = null;
            
            if (is_array($images[0])) {
                // Formato: [{"original": "path", "thumbnail": "path", "name": "name"}]
                $firstImage = $images[0]['original'] ?? $images[0]['thumbnail'] ?? null;
            } else {
                // Formato: ["path1", "path2", "path3"]
                $firstImage = $images[0];
            }
            
            if ($firstImage) {
                // Verificar si es una URL externa
                if (strpos($firstImage, 'http') === 0) {
                    return $firstImage;
                }
                
                // Verificar si es una ruta local válida
                $localPath = __DIR__ . '/../' . ltrim($firstImage, '/');
                if (file_exists($localPath) && filesize($localPath) > 100) {
                    return $firstImage;
                }
            }
        }
    }

    // Buscar imagen por slug si no hay imágenes en JSON
    $slug = is_array($product) ? ($product['slug'] ?? '') : '';
    if ($slug) {
        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        foreach ($extensions as $ext) {
            $path = "assets/images/products/{$slug}.{$ext}";
            $fullPath = __DIR__ . '/../' . $path;
            if (file_exists($fullPath) && filesize($fullPath) > 100) {
                return $path;
            }
        }
    }

    return 'assets/images/placeholder.jpg';
}
```

### 2. JavaScript en `admin/products.php`

**Problema**: No manejaba correctamente el formato de objetos de imágenes.

**Solución**: 
```javascript
// Cargar imágenes si existen
if (product.images) {
    try {
        const images = JSON.parse(product.images);
        const preview = document.getElementById('editImagePreview');
        preview.innerHTML = '';
        
        images.forEach(imageData => {
            const item = document.createElement('div');
            item.className = 'image-preview-item';

            const img = document.createElement('img');
            // Manejar tanto objetos como strings
            let imageUrl = imageData;
            if (typeof imageData === 'object' && imageData.original) {
                imageUrl = imageData.original;
            }
            
            img.src = imageUrl.startsWith('http') ? imageUrl : '../' + imageUrl.replace(/^\/+/, '');
            img.dataset.original = imageUrl;
            img.alt = 'Product image';
            
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-btn';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.onclick = () => {
                item.remove();
                updateImagesJson();
            };
            
            item.appendChild(img);
            item.appendChild(removeBtn);
            preview.appendChild(item);
        });
        
        document.getElementById('editImagesJson').value = product.images;
    } catch (e) {
        console.error('Error parsing images:', e);
    }
}
```

### 3. Función `updateImagesJson()` en `assets/js/admin.js`

**Problema**: No extraía correctamente las rutas de las imágenes.

**Solución**:
```javascript
function updateImagesJson() {
    const previews = document.querySelectorAll('.image-preview');
    previews.forEach(preview => {
        const images = Array.from(preview.querySelectorAll('img')).map(img => {
            // Usar dataset.original si está disponible, sino extraer de src
            let imagePath = img.dataset.original;
            if (!imagePath) {
                imagePath = img.src;
                // Remover la parte '../' si existe
                if (imagePath.includes('../')) {
                    imagePath = imagePath.split('../')[1];
                }
            }
            return imagePath;
        });
        
        let hiddenInput = preview.parentElement.querySelector('[id$="ImagesJson"], [id$="image_json"], [id$="images_json"]');
        if (!hiddenInput) {
            const container = preview.closest('.mb-3, form');
            if (container) {
                hiddenInput = container.querySelector('[id$="ImagesJson"], [id$="image_json"], [id$="images_json"]');
            }
        }
        if (hiddenInput) {
            hiddenInput.value = JSON.stringify(images);
        }
    });
}
```

### 4. Manejo de subida de imágenes en `admin/products.php`

**Problema**: No actualizaba correctamente el campo JSON con las rutas de las imágenes.

**Solución**:
```javascript
// Mostrar imágenes subidas
data.files.forEach(file => {
    const item = createImagePreviewItem(file.thumbnail, file.original);
    preview.appendChild(item);
});

// Actualizar campo oculto con solo las rutas originales
const originalPaths = data.files.map(file => file.original);
const currentImages = JSON.parse(document.getElementById('imagesJson').value || '[]');
const updatedImages = [...currentImages, ...originalPaths];
document.getElementById('imagesJson').value = JSON.stringify(updatedImages);
```

## 🛠️ Scripts de Depuración Creados

### 1. `debug_images.php`
Script completo de depuración que:
- Verifica la estructura de directorios
- Analiza productos en la base de datos
- Corrige automáticamente problemas de imágenes
- Genera un reporte detallado

### 2. `test_image_system.php`
Script de pruebas que:
- Verifica el método `getImagePath()`
- Analiza el formato JSON de imágenes
- Verifica archivos físicos
- Simula creación de productos
- Verifica permisos de directorios

## 📁 Estructura de Directorios Requerida

```
assets/
├── images/
│   ├── placeholder.jpg          # Imagen por defecto
│   ├── products/               # Imágenes de productos
│   ├── categories/             # Imágenes de categorías
│   └── settings/               # Imágenes de configuración
```

## 🔍 Verificación de Funcionamiento

### Pasos para verificar:

1. **Ejecutar script de depuración**:
   ```bash
   http://localhost/codex/debug_images.php
   ```

2. **Ejecutar script de pruebas**:
   ```bash
   http://localhost/codex/test_image_system.php
   ```

3. **Crear un producto de prueba** desde el panel de administración

4. **Verificar visualización** en el frontend

## 🚨 Consideraciones Importantes

### Tamaño mínimo de archivos
- Las imágenes deben tener al menos 100 bytes para ser consideradas válidas
- Se verifica `filesize() > 100` antes de mostrar la imagen

### Formatos soportados
- JPG/JPEG
- PNG
- WebP
- GIF

### Validación de rutas
- Se verifica la existencia del archivo físico
- Se manejan tanto URLs externas como rutas locales
- Fallback a imagen placeholder si no se encuentra la imagen

### Permisos de directorios
- Los directorios deben tener permisos 755
- El servidor web debe tener permisos de escritura

## 📝 Logs y Debugging

### Archivo de log de subidas
- Ubicación: `assets/images/upload_debug.log`
- Registra errores durante la subida de archivos

### Verificación de errores
- Revisar logs de PHP/Apache
- Verificar permisos de archivos
- Comprobar espacio en disco

## 🎯 Resultados Esperados

Después de aplicar estas correcciones:

1. ✅ Las imágenes se suben correctamente
2. ✅ Las imágenes se muestran en el panel de administración
3. ✅ Las imágenes se muestran en el frontend
4. ✅ El sistema maneja tanto formatos de objeto como de string
5. ✅ Fallback a imagen placeholder cuando no hay imagen
6. ✅ Validación robusta de archivos

## 🔄 Mantenimiento

### Recomendaciones:
- Ejecutar `debug_images.php` periódicamente
- Monitorear el tamaño del directorio de imágenes
- Implementar limpieza de archivos huérfanos
- Considerar implementar CDN para mejor rendimiento

---

**Fecha de corrección**: $(date)
**Versión del sistema**: 1.0
**Estado**: ✅ Completado 