# Rediseño visual local — 22 de octubre de 2026

La portada conserva la imagen configurada en el administrador. Los textos presentan software a medida, aceites esenciales, maceteros de yeso y suscripciones digitales. Incluye un selector de categorías, productos destacados y acceso al carrito y a WhatsApp.

Los estilos nuevos están en `assets/css/experience.css`. Las interacciones de la portada están en `assets/js/experience.js`. El estilo anterior se conserva en `assets/css/style.css`.

## Puntos de retorno

- Estado anterior: commit `9763551`, etiqueta `antes-rediseno-20261022`.
- Rediseño terminado: etiqueta `rediseno-visual-20261022`.
- Paleta clara y oscura en verde petróleo: etiqueta `paleta-clara-oscura-20261022`.
- Escaparate de productos y movimiento: etiqueta `escaparate-interactivo-20261022`.
- Modo claro en arena y crema: etiqueta `modo-claro-arena-20261022`.
- Copia completa anterior de la carpeta: `C:\laragon\backups\WEBMOYOALQUIMIA-antes-rediseno-20261022`.

Para deshacer solo el tono arena del modo claro, con los cambios posteriores guardados:

```powershell
& 'C:\laragon\bin\git\bin\git.exe' revert modo-claro-arena-20261022
```

Después, para deshacer el escaparate y los efectos:

```powershell
& 'C:\laragon\bin\git\bin\git.exe' revert escaparate-interactivo-20261022
```

Después, para deshacer también el ajuste de colores:

```powershell
& 'C:\laragon\bin\git\bin\git.exe' revert paleta-clara-oscura-20261022
```

Después, si también se desea deshacer el rediseño inicial:

```powershell
& 'C:\laragon\bin\git\bin\git.exe' revert rediseno-visual-20261022
```

El repositorio guarda los archivos de presentación seleccionados; la copia externa guarda la carpeta completa anterior. Configuración privada, imágenes y exportaciones SQL no se agregaron a Git. No se publicó el rediseño ni se migró la base de datos.

## Verificación

Comprobados: sintaxis PHP y JavaScript; portada en 360, 390, 768 y 1440 px; imagen principal original; selector de categorías; carrito; menú y desplegable móvil; catálogo, categoría, ficha de producto, carrito, nosotros y contacto; preferencia de movimiento reducido y contenido de portada sin JavaScript. Sin errores JavaScript durante estas pruebas.

Capturas y scripts de comprobación locales: `C:\laragon\backups\moyo-visual-checks-20261022`.

La paleta usa fondos arena, tarjetas crema, detalles salvia y acentos verde petróleo como presentación inicial. El modo oscuro conserva sus tonos azul verdoso y menta. El cambio de paleta afecta la portada, el menú, el pie y las superficies del catálogo. El botón superior conserva la preferencia al recargar y navegar. Se mantuvo la imagen original. Se verificaron ambos modos en escritorio y en 360, 390 y 768 px, el menú móvil y el contraste de las superficies y textos principales de la portada y las tarjetas del catálogo.

## Escaparate y movimiento

El escaparate combina hasta cuatro productos: primero los de mayor cantidad de unidades en pedidos pagados y no cancelados, siempre que sigan disponibles públicamente; después, los productos destacados del catálogo. Solo los primeros reciben la etiqueta de ventas. Los descuentos y precios salen del producto actual. No se generan compradores, ventas recientes ni cifras simuladas.

El carrusel tiene anterior, siguiente, selección directa y pausa. Cambia cada siete segundos mientras está visible; se detiene al pasar el ratón, interactuar con el teclado, ocultar la pestaña o activar movimiento reducido. La navegación manual sigue funcionando con movimiento reducido. Sin JavaScript se muestra el primer producto con sus enlaces.

La portada incorpora movimiento suave de la imagen original, una cuadrícula animada, iluminación en las categorías, entradas al desplazarse y una barra de progreso. Se respetan las preferencias de movimiento reducido. No se modifican los archivos de imagen ni los datos de productos o pedidos.
