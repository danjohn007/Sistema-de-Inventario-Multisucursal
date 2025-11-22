<?php
/**
 * Sistema de Inventario Multisucursal
 * Archivo de configuración principal
 */

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de errores (cambiar en producción)
// En producción, cambiar display_errors a 0
$is_production = ($_SERVER['HTTP_HOST'] ?? '') !== 'localhost';
if ($is_production) {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/php-error.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Configuración de Base de Datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'janetzy_inventarios');
define('DB_USER', 'janetzy_inventarios');
define('DB_PASS', 'Danjohn007!');
define('DB_CHARSET', 'utf8mb4');

// Detección automática de URL Base
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = str_replace('\\', '/', dirname($script));
    
    // Si está en la raíz, no agregar barra adicional
    if ($path === '/') {
        $path = '';
    }
    
    return $protocol . '://' . $host . $path;
}

define('BASE_URL', getBaseUrl());
define('BASE_PATH', dirname(__DIR__));

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

// Configuración del sitio (se puede modificar desde el panel)
define('SITE_NAME', 'Sistema de Inventario Multisucursal');
define('SITE_DESCRIPTION', 'Sistema de gestión de inventarios para productos artesanales');

// Configuración de Email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu-email@gmail.com');
define('SMTP_PASS', 'tu-password');
define('SMTP_FROM', 'noreply@inventario.com');

// Configuración de PayPal
define('PAYPAL_MODE', 'sandbox'); // sandbox o live
define('PAYPAL_CLIENT_ID', '');
define('PAYPAL_SECRET', '');

// Configuración de subida de archivos
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);
define('UPLOAD_PATH', BASE_PATH . '/public/uploads/');

// Configuración de paginación
define('ITEMS_PER_PAGE', 20);

// Configuración de seguridad
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_TIMEOUT', 3600); // 1 hora

// Configuración de programa de fidelidad
define('LOYALTY_POINTS_PER_CURRENCY', 1); // 1 punto por cada 100 de moneda
define('LOYALTY_CURRENCY_UNIT', 100);

// Cargar autoloader
require_once BASE_PATH . '/app/helpers/Autoloader.php';
