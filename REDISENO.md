# Rediseño visual local — 22 de octubre de 2026

La portada conserva la imagen configurada en el administrador. Los textos presentan software a medida, aceites esenciales, maceteros de yeso y suscripciones digitales. Incluye un selector de categorías, productos destacados y acceso al carrito y a WhatsApp.

Los estilos nuevos están en `assets/css/experience.css`. Las interacciones de la portada están en `assets/js/experience.js`. El estilo anterior se conserva en `assets/css/style.css`.

## Puntos de retorno

- Estado anterior: commit `9763551`, etiqueta `antes-rediseno-20261022`.
- Rediseño terminado: etiqueta `rediseno-visual-20261022`.
- Copia completa anterior de la carpeta: `C:\laragon\backups\WEBMOYOALQUIMIA-antes-rediseno-20261022`.

Para deshacer este rediseño conservando el historial, con los cambios posteriores guardados, ejecutar en PowerShell desde la carpeta del proyecto:

```powershell
& 'C:\laragon\bin\git\bin\git.exe' revert rediseno-visual-20261022
```

El repositorio guarda los archivos de presentación seleccionados; la copia externa guarda la carpeta completa anterior. Configuración privada, imágenes y exportaciones SQL no se agregaron a Git. No se publicó el rediseño ni se migró la base de datos.

## Verificación

Comprobados: sintaxis PHP y JavaScript; portada en 360, 390, 768 y 1440 px; imagen principal original; selector de categorías; carrito; menú y desplegable móvil; catálogo, categoría, ficha de producto, carrito, nosotros y contacto; preferencia de movimiento reducido y contenido de portada sin JavaScript. Sin errores JavaScript durante estas pruebas.

Capturas y scripts de comprobación locales: `C:\laragon\backups\moyo-visual-checks-20261022`.
