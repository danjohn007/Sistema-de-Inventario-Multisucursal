<?php
/**
 * Sistema de Inventario Multisucursal
 * Punto de entrada principal
 */

// Iniciar sesión
session_start();

// Cargar configuración
require_once 'config/config.php';

// Router simple para URLs amigables
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Remover base path y query string
$route = str_replace($base_path, '', parse_url($request_uri, PHP_URL_PATH));
$route = trim($route, '/');

// Si está vacío, mostrar login o dashboard
if (empty($route)) {
    if (isset($_SESSION['user_id'])) {
        $route = 'dashboard';
    } else {
        $route = 'auth/login';
    }
}

// Parsear ruta
$parts = explode('/', $route);
$controller_name = !empty($parts[0]) ? ucfirst($parts[0]) . 'Controller' : 'AuthController';
$action = isset($parts[1]) ? $parts[1] : 'index';

// Parámetros adicionales
$params = array_slice($parts, 2);

// Cargar controlador
$controller_file = BASE_PATH . '/app/controllers/' . $controller_name . '.php';

if (file_exists($controller_file)) {
    require_once $controller_file;
    
    if (class_exists($controller_name)) {
        $controller = new $controller_name();
        
        if (method_exists($controller, $action)) {
            call_user_func_array([$controller, $action], $params);
        } else {
            // Acción no encontrada
            header("HTTP/1.0 404 Not Found");
            echo "Error 404: Acción no encontrada";
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Error 404: Controlador no válido";
    }
} else {
    // Controlador no encontrado
    header("HTTP/1.0 404 Not Found");
    echo "Error 404: Página no encontrada";
}
