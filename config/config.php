<?php
/**
 * Archivo de configuración principal del Sistema de Inventario Multisucursal
 * 
 * Este archivo contiene las constantes y configuraciones necesarias para
 * el funcionamiento del sistema.
 */

// Definir la constante BASE_PATH si no está definida
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Definir otras constantes del sistema
define('CONFIG_PATH', BASE_PATH . '/config');
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('MODULES_PATH', BASE_PATH . '/modules');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('ASSETS_PATH', BASE_PATH . '/assets');

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'inventario_multisucursal');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la aplicación
define('APP_NAME', 'Sistema de Inventario Multisucursal');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // production, development, testing

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de errores según el entorno
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 si se usa HTTPS

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
