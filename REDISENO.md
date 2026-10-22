# Rediseño visual local — 22 de octubre de 2026

La portada conserva la imagen configurada en el administrador. Los textos presentan software a medida, aceites esenciales, maceteros de yeso y suscripciones digitales. Incluye un selector de categorías, productos destacados y acceso al carrito y a WhatsApp.

Orden actual del inicio: presentación, categorías, productos destacados, cómo comprar, contacto por WhatsApp y escaparate como última sección comercial, antes del pie «Hecho por Moyoalquimia». El carrusel conserva la rotación, las flechas, los puntos y el botón de pausa, sin textos técnicos sobre el intervalo de rotación.

Los estilos nuevos están en `assets/css/experience.css`. Las interacciones de la portada están en `assets/js/experience.js`. El estilo anterior se conserva en `assets/css/style.css`.

## Puntos de retorno

El modo oscuro es la opción inicial si no hay una preferencia guardada. El selector conserva la elección de modo claro u oscuro al recargar y navegar, y el tema inicial se aplica desde la cabecera. Si el almacenamiento no está disponible, se utiliza el modo oscuro. Bajo «Hecho por Moyoalquimia», una línea discreta de 12 px invita: «¿Quieres una página web? Contáctanos», enlazada a la página de contacto.

- Estado anterior: commit `9763551`, etiqueta `antes-rediseno-20261022`.
- Rediseño terminado: etiqueta `rediseno-visual-20261022`.
- Paleta clara y oscura en verde petróleo: etiqueta `paleta-clara-oscura-20261022`.
- Escaparate de productos y movimiento: etiqueta `escaparate-interactivo-20261022`.
- Modo claro en arena y crema: etiqueta `modo-claro-arena-20261022`.
- Rotación ampliada y publicidad inferior izquierda: etiqueta `publicidad-productos-20261022`.
- Paleta gris azulada y tipografía más legible: etiqueta `paleta-legible-20261022`.
- Correcciones de superposición, contraste y composición: etiqueta `correcciones-visuales-20261022`.
- Orden final del inicio y retirada del texto de rotación: etiqueta `inicio-ordenado-20261022`.
- Catálogo completo, rotación más rápida y ChatGPT primero en publicidad: etiqueta `escaparate-completo-20261022`.
- Copia completa anterior de la carpeta: `C:\laragon\backups\WEBMOYOALQUIMIA-antes-rediseno-20261022`.

Para deshacer únicamente la ampliación a todo el catálogo y la prioridad de ChatGPT, con los cambios posteriores guardados:

```powershell
& 'C:\laragon\bin\git\bin\git.exe' revert escaparate-completo-20261022
```

Después, para deshacer también el cambio de orden del inicio:

```powershell
& 'C:\laragon\bin\git\bin\git.exe' revert inicio-ordenado-20261022
```

Después, para deshacer también las correcciones visuales:

```powershell
& 'C:\laragon\bin\git\bin\git.exe' revert correcciones-visuales-20261022
```

Después, para deshacer también el ajuste de paleta y tamaños de texto:

```powershell
& 'C:\laragon\bin\git\bin\git.exe' revert paleta-legible-20261022
```

Después, para deshacer también la publicidad y la ampliación del carrusel:

```powershell
& 'C:\laragon\bin\git\bin\git.exe' revert publicidad-productos-20261022
```

Después, para deshacer también el tono arena del modo claro:

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

La paleta clara usa fondos gris azulado, superficies ligeramente más claras, títulos azul profundo y acentos azul petróleo, coordinados con la imagen principal. El modo oscuro conserva sus tonos azul verdoso y menta. El cambio de paleta afecta la portada, el menú, el pie y las superficies del catálogo. El botón superior conserva la preferencia al recargar y navegar. Se mantuvo la imagen original. Se verificaron ambos modos en escritorio y en 360, 390 y 768 px, el menú móvil y el contraste de las superficies y textos principales de la portada y las tarjetas del catálogo.

La tipografía de la portada se ajustó para la lectura: descripción principal de 18 px en escritorio y 16 px en celular; menú de 15 px y botones principales de 15 px en escritorio y 14 px en celular; descripciones del escaparate de 16 px; etiquetas y notas de al menos 12 px. Los títulos mantienen su jerarquía, con el título principal un poco más contenido. Los tamaños también se aplican al modo oscuro. Las etiquetas de categoría tienen mayor contraste y los controles admiten saltos de línea en pantallas pequeñas.

## Tarjetas compactas del catálogo

Las tarjetas de `products.php` y `category.php` presentan foto, categoría, nombre enlazado a la ficha, precio y acciones. No incluyen descripción ni la valoración fija de estrellas. La descripción completa sigue disponible en la ficha y en la vista rápida. La misma presentación se aplica al cargar más productos. Las fotos conservan su contenido completo en un marco de 176 px en escritorio y 136 px en celular; se reducen los espacios del cuerpo y se mantienen botones de al menos 44 px en celular. WhatsApp utiliza un icono con nombre accesible en pantallas pequeñas. Estos ajustes no cambian las descripciones guardadas ni el escaparate del inicio.

## Escaparate y movimiento

El escaparate incluye todos los productos públicos activos del catálogo, sin duplicados: primero los productos con más unidades en pedidos pagados y no cancelados, después una mezcla de categorías y destacados, y a continuación el resto del catálogo. Solo los primeros reciben la etiqueta de ventas. Los descuentos y precios salen del producto actual. No se generan compradores, ventas recientes ni cifras simuladas. Las imágenes del resto de productos se cargan de forma diferida y se precarga la siguiente. Los indicadores muestran una ventana de siete puntos para evitar una fila enorme de botones.

El carrusel tiene anterior, siguiente, selección directa y pausa. Cambia cada tres segundos mientras está visible, incluso con el puntero encima; se detiene con el botón de pausa, al navegar con el teclado dentro del carrusel, ocultar la pestaña o activar movimiento reducido. No muestra mensajes sobre el intervalo de rotación. Las flechas y los puntos usados con el ratón no desactivan la rotación automática. La navegación manual sigue funcionando con movimiento reducido. Sin JavaScript se muestra el primer producto con sus enlaces.

La portada incorpora movimiento suave de la imagen original, una cuadrícula animada, iluminación en las categorías, entradas al desplazarse y una barra de progreso. Se respetan las preferencias de movimiento reducido. No se modifican los archivos de imagen ni los datos de productos o pedidos.

## Publicidad de productos

La tarjeta inferior izquierda empieza después de diez segundos. Muestra imagen, nombre, precio y enlace del producto. Usa «Producto vendido» solo para productos con pedidos pagados registrados; las demás tarjetas dicen «Producto destacado». No representa ventas en tiempo real ni muestra datos de compradores.

La primera tarjeta publicitaria prioriza ChatGPT cuando está disponible públicamente. La prioridad no altera el estado de venta: si no consta un pedido pagado, ChatGPT se presenta como producto destacado.

La publicidad se muestra nueve segundos y deja una pausa de veintitrés segundos antes de la siguiente, con un máximo de cuatro tarjetas por carga. Se mantiene visible al interactuar con ella. Se puede cerrar y queda descartada durante la sesión de la pestaña, incluso al recargar. Con movimiento reducido solo aparece una tarjeta estática, cerrable. En celular su posición evita el botón de WhatsApp. Se comprobaron rotación, pausa, cierre, persistencia, etiquetas, cambio de producto, móvil y movimiento reducido en navegador.

## Correcciones de la auditoría visual

- `assets/js/floating-ui.js` protege los controles de la página: la publicidad y WhatsApp se ocultan temporalmente si su posición tapa botones o enlaces de acción y vuelven al quedar libres. Se reprodujeron los dos fallos originales y se verificó el elemento que recibe el clic, incluyendo el carrito.
- Las etiquetas de categoría del catálogo tienen fondo y texto acordes al tema, sin opacidad que reduzca el contraste. El selector de lista/cuadrícula usa el acento de la marca y mantiene ambos botones alineados.
- La portada tiene menos altura y elementos repetidos. En móvil se conserva la imagen completa, en un tamaño más compacto. La organización posterior acordada presenta primero las categorías, después los destacados y deja el escaparate al final del inicio.
- Los resúmenes del escaparate parten de la descripción completa y se acortan por palabras. Las tarjetas del catálogo ya no muestran descripciones; la ficha y la vista rápida conservan el contenido completo. La base de datos permanece intacta. Se retiraron los mínimos de altura individuales que dejaban huecos entre nombre, descripción y precio del escaparate.
- Las imágenes mantienen su contenido completo dentro de marcos con espaciado uniforme. No se modificaron los archivos originales.

Verificados 360, 390, 768, 1024, 1366 y 1440 px sin desbordamiento horizontal; cabecera alineada; carrito; ambas vistas del catálogo y carga adicional de productos; contraste de etiquetas superior a 4,5:1 en ambos modos. Sin errores JavaScript durante las pruebas. Capturas y mediciones: `C:\laragon\backups\moyo-visual-checks-20261022\correcciones`.
