<?php
/**
 * Script de prueba de conexión a la base de datos
 */

require_once __DIR__ . '/config/config.php';
require_once CONFIG_PATH . '/database.php';

echo "<h2>Prueba de Conexión - " . APP_NAME . "</h2>";
echo "<p><strong>Versión:</strong> " . APP_VERSION . "</p>";
echo "<p><strong>Entorno:</strong> " . APP_ENV . "</p>";
echo "<hr>";

try {
    echo "<p>Intentando conectar a la base de datos...</p>";
    
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "<p style='color: green;'><strong>✓ Conexión exitosa a la base de datos!</strong></p>";
    echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
    echo "<p><strong>Base de datos:</strong> " . DB_NAME . "</p>";
    echo "<p><strong>Charset:</strong> " . DB_CHARSET . "</p>";
    
    // Verificar la versión de MySQL
    $version = $conn->query('SELECT VERSION()')->fetchColumn();
    echo "<p><strong>Versión MySQL:</strong> $version</p>";
    
    // Verificar constantes del sistema
    echo "<hr>";
    echo "<h3>Constantes del Sistema:</h3>";
    echo "<ul>";
    echo "<li><strong>BASE_PATH:</strong> " . BASE_PATH . "</li>";
    echo "<li><strong>CONFIG_PATH:</strong> " . CONFIG_PATH . "</li>";
    echo "<li><strong>INCLUDES_PATH:</strong> " . INCLUDES_PATH . "</li>";
    echo "<li><strong>MODULES_PATH:</strong> " . MODULES_PATH . "</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>✗ Error de conexión:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    
    if (APP_ENV === 'development') {
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}
