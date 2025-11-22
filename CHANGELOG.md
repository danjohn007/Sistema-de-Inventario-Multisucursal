# Changelog

Todas las modificaciones importantes de este proyecto serán documentadas en este archivo.

## [1.0.0] - 2025-11-22

### 🐛 Arreglado
- **CRÍTICO**: Error "Undefined constant BASE_PATH" en config/config.php línea 17
  - El error ocurría porque BASE_PATH se usaba antes de ser definido
  - Solución: Definir BASE_PATH en líneas 10-12 antes de su uso
  - La constante ahora se define usando `dirname(__DIR__)` que obtiene el directorio raíz del proyecto

### ✨ Agregado
- Sistema completo de inventario multisucursal
- Estructura de directorios organizada (config/, modules/, assets/)
- Archivo de configuración principal (config/config.php) con:
  - Constantes de ruta del sistema (BASE_PATH, CONFIG_PATH, etc.)
  - Configuración de base de datos
  - Configuración de zona horaria (America/Mexico_City)
  - Manejo de errores según entorno
  - Configuración de sesiones seguras

- Clase de conexión a base de datos (config/database.php):
  - Implementa patrón Singleton
  - Usa PDO para conexiones seguras
  - Protección contra inyección SQL
  - Manejo de errores robusto

- Esquema de base de datos (config/schema.sql):
  - Tabla de usuarios con roles (admin, gerente, empleado)
  - Tabla de sucursales
  - Tabla de categorías de productos
  - Tabla de productos
  - Tabla de inventario por sucursal
  - Tabla de movimientos de inventario
  - Datos iniciales de ejemplo

- Módulos del sistema:
  - **Usuarios**: Login, logout, gestión de usuarios
  - **Productos**: Catálogo de productos artesanales
  - **Sucursales**: Gestión de múltiples sucursales
  - **Reportes**: Sistema de reportes

- Herramientas adicionales:
  - `install.php`: Instalador web de base de datos
  - `test_connection.php`: Verificador de conexión a BD
  - Interfaz de usuario responsive con CSS moderno

- Documentación:
  - README.md completo con instrucciones de instalación
  - CHANGELOG.md para seguimiento de cambios

### 🔒 Seguridad
- Contraseñas encriptadas con `password_hash()`
- Prepared statements para prevenir inyección SQL
- Sesiones seguras con HttpOnly cookies
- Validación y sanitización de datos de entrada
- Control de acceso basado en roles
- Credenciales de prueba solo visibles en modo desarrollo

### 🔧 Mejoras
- Protección del patrón Singleton contra serialización/deserialización
- Verificación de estado de sesión antes de destruir
- Mejora en el parsing de SQL del instalador
- Todos los archivos PHP validados sin errores de sintaxis

### 📝 Notas
- Usuario administrador por defecto:
  - Email: admin@sistema.com
  - Contraseña: admin123
  - **Importante**: Cambiar estas credenciales en producción

## Estructura del Error Original

```
[22-Nov-2025 13:57:46 America/Mexico_City] PHP Fatal error: 
Uncaught Error: Undefined constant "BASE_PATH" in config/config.php:17
Stack trace:
#0 /test_connection.php(5): require_once()
#1 {main}
thrown in config/config.php on line 17
```

## Solución Implementada

```php
// Líneas 10-12 de config/config.php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
```

Esta solución:
1. Verifica si BASE_PATH ya está definido
2. Define BASE_PATH como el directorio padre del directorio config/
3. Se ejecuta ANTES de que se use BASE_PATH en las líneas siguientes
4. Es seguro para múltiples inclusiones del archivo
