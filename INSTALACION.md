# 🚀 Instalación Rápida - AlquimiaTechnologic

## Pasos para instalar en XAMPP

### 1. Preparar XAMPP
```bash
# Descargar XAMPP desde https://www.apachefriends.org/
# Instalar XAMPP en tu sistema
# Iniciar Apache y MySQL desde el panel de control
```

### 2. Instalar el proyecto
```bash
# Navegar a la carpeta htdocs de XAMPP
cd C:\xampp\htdocs

# Copiar el proyecto a la carpeta
# El proyecto debe quedar en: C:\xampp\htdocs\TiendawebAlquimia
```

### 3. Configurar la base de datos
```sql
-- Abrir phpMyAdmin: http://localhost/phpmyadmin
-- Crear nueva base de datos: alquimia_technologic
-- Importar el archivo: database/alquimia_db.sql
```

### 4. Verificar configuración
```php
// Revisar config/database.php
private $host = 'localhost';
private $dbname = 'alquimia_technologic';
private $username = 'root';
private $password = '';
```

### 5. Acceder al sistema
- **Sitio web**: http://localhost/TiendawebAlquimia
- **Admin**: http://localhost/TiendawebAlquimia/admin/dashboard.php

### 6. Credenciales de administrador
- **Usuario**: admin
- **Contraseña**: password

## ✅ Verificación rápida
1. ¿Apache está ejecutándose? ✓
2. ¿MySQL está ejecutándose? ✓
3. ¿Base de datos creada? ✓
4. ¿Proyecto en htdocs? ✓
5. ¿Puedes acceder al sitio? ✓

## 🔧 Solución de problemas comunes

### Error de conexión a BD
```
Verificar que MySQL esté ejecutándose en XAMPP
Verificar credenciales en config/database.php
```

### Error 404
```
Verificar que el proyecto esté en htdocs
Verificar que Apache esté ejecutándose
```

### Problemas de permisos
```
Verificar permisos de la carpeta del proyecto
Ejecutar XAMPP como administrador si es necesario
```

## 📞 ¿Necesitas ayuda?
Si tienes problemas con la instalación, revisa el archivo README.md completo para más detalles. 