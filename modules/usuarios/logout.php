<?php
/**
 * Cerrar sesión
 */

require_once __DIR__ . '/../../config/config.php';

// Verificar que hay una sesión activa
if (session_status() === PHP_SESSION_ACTIVE) {
    // Destruir todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la sesión
    session_destroy();
}

// Redirigir al inicio
header('Location: ../../index.php');
exit;
