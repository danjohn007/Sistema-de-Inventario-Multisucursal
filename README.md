# Sistema de Inventario Multisucursal para Productos Artesanales

Sistema completo de gestión de inventarios diseñado específicamente para negocios de productos artesanales con múltiples sucursales en Querétaro, México.

## 🎨 Características Principales

### Gestión de Productos Artesanales
- ✅ Catálogo completo con atributos específicos para artesanías
- ✅ Información detallada: materiales, técnicas, artesano, región de origen
- ✅ Gestión de variantes (colores, tamaños, diseños)
- ✅ Soporte para múltiples fotos por producto
- ✅ Control de productos de edición limitada
- ✅ Certificados de autenticidad

### Sistema Multisucursal
- ✅ Gestión completa de sucursales
- ✅ Transferencias entre sucursales
- ✅ Inventario independiente por sucursal
- ✅ Seguimiento en tiempo real

### Control de Inventario
- ✅ Stock por sucursal y producto
- ✅ Alertas de stock bajo
- ✅ Movimientos de inventario (entradas/salidas/ajustes)
- ✅ Historial completo de movimientos
- ✅ Ubicación física dentro de la sucursal

### Ventas y Punto de Venta
- ✅ Módulo POS (Punto de Venta)
- ✅ Múltiples métodos de pago
- ✅ Gestión de clientes y programa de fidelización
- ✅ Historial de ventas

### Compras y Proveedores
- ✅ Gestión de artesanos y proveedores
- ✅ Órdenes de compra/producción
- ✅ Seguimiento de pedidos

### Reportes y Analytics
- ✅ Reportes de inventario y ventas
- ✅ Gráficas interactivas con Chart.js
- ✅ Análisis por sucursal
- ✅ Productos más vendidos

### Gestión de Usuarios
- ✅ Sistema de roles y permisos
- ✅ Roles: Administrador, Gerente, Vendedor, Almacenista, Artesano
- ✅ Autenticación segura con password_hash()

### Módulo de Configuración
- ✅ Configuración del sitio (nombre, logo)
- ✅ Configuración de email
- ✅ Personalización de colores
- ✅ Integración con PayPal
- ✅ API para códigos QR

## 🛠️ Tecnologías

- **Backend:** PHP 7.4+ (sin framework, MVC puro)
- **Base de Datos:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript
- **Diseño:** Tailwind CSS (CDN)
- **Gráficas:** Chart.js
- **Iconos:** Font Awesome 6
- **Servidor:** Apache con mod_rewrite

## 📋 Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache 2.4 con mod_rewrite habilitado
- Extensiones PHP requeridas:
  - PDO
  - pdo_mysql
  - mbstring
  - openssl

## 🚀 Instalación

### 1. Clonar o descargar el repositorio

```bash
git clone https://github.com/danjohn007/Sistema-de-Inventario-Multisucursal.git
cd Sistema-de-Inventario-Multisucursal
```

### 2. Configurar Apache

Asegúrate de que mod_rewrite esté habilitado:

```bash
# En Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2

# En CentOS/RHEL
# mod_rewrite generalmente está habilitado por defecto
```

Configura el VirtualHost o copia el proyecto a tu DocumentRoot:

```apache
<VirtualHost *:80>
    ServerName inventario.local
    DocumentRoot /ruta/al/proyecto/Sistema-de-Inventario-Multisucursal
    
    <Directory /ruta/al/proyecto/Sistema-de-Inventario-Multisucursal>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 3. Crear la Base de Datos

```bash
# Acceder a MySQL
mysql -u root -p

# En el prompt de MySQL:
CREATE DATABASE inventario_multisucursal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 4. Importar el Schema

```bash
mysql -u root -p inventario_multisucursal < database/schema.sql
```

El schema incluye:
- Estructura completa de todas las tablas
- Datos de ejemplo del estado de Querétaro
- Usuario administrador de prueba

### 5. Configurar Credenciales

Edita el archivo `config/config.php` con tus credenciales de base de datos:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'inventario_multisucursal');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 6. Configurar Permisos

```bash
# Dar permisos de escritura a la carpeta de uploads
chmod -R 755 public/uploads
chown -R www-data:www-data public/uploads

# En CentOS/RHEL
chown -R apache:apache public/uploads
```

### 7. Probar la Instalación

Abre tu navegador y accede a:

```
http://localhost/test_connection.php
```

Este archivo verificará:
- ✅ Conexión a la base de datos
- ✅ Configuración de URL base automática
- ✅ Extensiones PHP requeridas
- ✅ Versión de MySQL

## 🔐 Acceso al Sistema

### Credenciales por Defecto

```
Email: admin@inventario.com
Contraseña: admin123
```

⚠️ **IMPORTANTE:** Cambia estas credenciales después del primer acceso.

## 📁 Estructura del Proyecto

```
Sistema-de-Inventario-Multisucursal/
├── app/
│   ├── controllers/      # Controladores MVC
│   ├── models/          # Modelos de datos
│   ├── views/           # Vistas (HTML/PHP)
│   │   ├── layouts/     # Plantillas reutilizables
│   │   ├── auth/        # Vistas de autenticación
│   │   ├── dashboard/   # Dashboard
│   │   ├── products/    # Gestión de productos
│   │   ├── inventory/   # Gestión de inventario
│   │   ├── branches/    # Sucursales
│   │   ├── pos/         # Punto de venta
│   │   └── ...
│   └── helpers/         # Clases auxiliares
├── config/
│   └── config.php       # Configuración principal
├── database/
│   └── schema.sql       # Schema de la base de datos
├── public/
│   ├── css/            # Estilos personalizados
│   ├── js/             # JavaScript personalizado
│   ├── img/            # Imágenes del sistema
│   └── uploads/        # Archivos subidos
├── .htaccess           # Configuración Apache
├── index.php           # Punto de entrada
├── test_connection.php # Test de instalación
└── README.md           # Este archivo
```

## 🎯 Características Técnicas

### Arquitectura MVC
- Separación clara de responsabilidades
- Controladores para lógica de negocio
- Modelos para acceso a datos
- Vistas para presentación

### URLs Amigables
El sistema utiliza URLs limpias y amigables:
```
/products              # Lista de productos
/products/view/1       # Ver producto #1
/products/create       # Crear producto
/inventory             # Inventario
/branches              # Sucursales
/pos                   # Punto de venta
```

### URL Base Automática
El sistema detecta automáticamente la URL base, permitiendo instalación en cualquier directorio:
- http://localhost/inventario/
- http://midominio.com/
- http://midominio.com/sistema/

### Seguridad
- ✅ Contraseñas hasheadas con password_hash()
- ✅ Prevención de SQL Injection (PDO con prepared statements)
- ✅ Protección XSS (htmlspecialchars en vistas)
- ✅ Sesiones seguras
- ✅ Control de acceso basado en roles

### Base de Datos
- ✅ MySQL 5.7+ con soporte completo UTF-8 (utf8mb4)
- ✅ Relaciones de integridad referencial
- ✅ Índices optimizados
- ✅ Timestamps automáticos

## 📊 Datos de Ejemplo

El sistema incluye datos de ejemplo de Querétaro:

### Sucursales
1. Centro Histórico - Santiago de Querétaro
2. Plaza Constitución - El Retablo
3. San Juan del Río
4. Tequisquiapan

### Categorías
- Cerámica
- Textiles
- Madera
- Metalistería
- Joyería
- Cestería
- Vidrio Soplado
- Papel Maché

### Productos de Ejemplo
- Macetas de barro
- Rebozos bordados
- Tablas de madera
- Joyería en plata
- Y más...

## 🔧 Configuración Adicional

### Configurar Email (Opcional)
Edita `config/config.php`:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu-email@gmail.com');
define('SMTP_PASS', 'tu-password');
```

### Configurar PayPal (Opcional)
```php
define('PAYPAL_MODE', 'sandbox'); // o 'live'
define('PAYPAL_CLIENT_ID', 'tu-client-id');
define('PAYPAL_SECRET', 'tu-secret');
```

## 🐛 Solución de Problemas

### Error 404 en todas las rutas
- Verifica que mod_rewrite esté habilitado
- Revisa que AllowOverride esté configurado en All
- Verifica que el archivo .htaccess esté presente

### Error de conexión a base de datos
- Verifica credenciales en config/config.php
- Asegúrate de que MySQL esté corriendo
- Verifica que la base de datos exista

### Páginas sin estilos
- Verifica la configuración de BASE_URL
- Revisa permisos de la carpeta public/

## 📝 Licencia

Este proyecto es de código abierto y está disponible bajo licencia MIT.

## 👥 Contribuciones

Las contribuciones son bienvenidas. Por favor:
1. Fork el proyecto
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📞 Soporte

Para reportar problemas o solicitar nuevas características, por favor abre un issue en GitHub.

---

**Desarrollado con ❤️ para los artesanos de Querétaro, México**
