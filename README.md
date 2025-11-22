# Sistema de Inventario Multisucursal

Sistema Online de Inventarios de Productos Artesanales para gestión eficiente de múltiples sucursales.

## 🚀 Características

- **Gestión de Productos**: Catálogo completo de productos artesanales con categorías
- **Múltiples Sucursales**: Control de inventario independiente por sucursal
- **Control de Usuarios**: Sistema de autenticación con roles (Admin, Gerente, Empleado)
- **Movimientos de Inventario**: Registro de entradas, salidas, ajustes y transferencias
- **Reportes**: Generación de reportes de inventario, valoración y productos con stock bajo
- **Interfaz Responsive**: Diseño adaptable a diferentes dispositivos

## 📋 Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior / MariaDB 10.3 o superior
- Servidor web (Apache/Nginx)
- Extensiones PHP requeridas:
  - PDO
  - pdo_mysql
  - session

## 🔧 Instalación

1. **Clonar o descargar el repositorio**
   ```bash
   git clone https://github.com/danjohn007/Sistema-de-Inventario-Multisucursal.git
   cd Sistema-de-Inventario-Multisucursal
   ```

2. **Configurar la base de datos**
   
   Editar el archivo `config/config.php` con tus credenciales:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'inventario_multisucursal');
   define('DB_USER', 'tu_usuario');
   define('DB_PASS', 'tu_contraseña');
   ```

3. **Instalar la base de datos**
   
   Opción 1 - Usando el instalador web:
   - Navegar a `http://tu-dominio/install.php`
   - Hacer clic en "Iniciar Instalación"
   
   Opción 2 - Manualmente con MySQL:
   ```bash
   mysql -u usuario -p < config/schema.sql
   ```

4. **Probar la conexión**
   
   Navegar a `http://tu-dominio/test_connection.php` para verificar que la configuración es correcta.

5. **Acceder al sistema**
   
   Navegar a `http://tu-dominio/` e iniciar sesión con:
   - **Usuario**: admin@sistema.com
   - **Contraseña**: admin123

## 🗂️ Estructura del Proyecto

```
Sistema-de-Inventario-Multisucursal/
├── config/
│   ├── config.php          # Configuración principal
│   ├── database.php        # Clase de conexión a BD
│   └── schema.sql          # Esquema de la base de datos
├── modules/
│   ├── productos/          # Módulo de productos
│   ├── sucursales/         # Módulo de sucursales
│   ├── usuarios/           # Módulo de usuarios y autenticación
│   └── reportes/           # Módulo de reportes
├── assets/
│   ├── css/               # Hojas de estilo
│   └── js/                # Scripts JavaScript
├── index.php              # Página principal
├── install.php            # Instalador de BD
├── test_connection.php    # Verificador de conexión
└── README.md
```

## 🔐 Seguridad

- Contraseñas encriptadas con `password_hash()`
- Protección contra inyección SQL con prepared statements (PDO)
- Sesiones seguras con cookies HttpOnly
- Validación y sanitización de datos de entrada
- Control de acceso basado en roles

## 🐛 Solución de Problemas

### Error: "Undefined constant BASE_PATH"

Este error ha sido resuelto en la versión actual. La constante BASE_PATH se define correctamente en `config/config.php` líneas 10-12:

```php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
```

### Error de conexión a la base de datos

1. Verificar credenciales en `config/config.php`
2. Asegurar que el servidor MySQL está ejecutándose
3. Verificar que el usuario tiene permisos adecuados
4. Ejecutar `test_connection.php` para diagnóstico

## 📝 Uso

1. **Gestión de Productos**: Agregar, editar y visualizar productos artesanales
2. **Control de Sucursales**: Administrar información de cada sucursal
3. **Gestión de Usuarios**: Crear usuarios con diferentes roles y permisos
4. **Inventario**: Registrar entradas, salidas y transferencias entre sucursales
5. **Reportes**: Generar informes de inventario y valoración

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crear una rama para tu característica (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📄 Licencia

Este proyecto es de código abierto y está disponible bajo la Licencia MIT.

## 👥 Autores

- **Sistema de Inventario Multisucursal** - Versión 1.0.0

## 🆘 Soporte

Para reportar problemas o solicitar características, por favor crear un issue en el repositorio de GitHub.
