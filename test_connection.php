<?php
/**
 * Test de Conexión a Base de Datos y Configuración de URL Base
 */
require_once 'config/config.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión - Sistema de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">
                Test de Conexión y Configuración
            </h1>
            
            <!-- Test de URL Base -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-blue-600">
                    ✓ Configuración de URL Base
                </h2>
                <div class="space-y-2">
                    <p><strong>URL Base detectada:</strong> <code class="bg-gray-100 px-2 py-1 rounded"><?php echo BASE_URL; ?></code></p>
                    <p><strong>Ruta del sistema:</strong> <code class="bg-gray-100 px-2 py-1 rounded"><?php echo BASE_PATH; ?></code></p>
                    <p class="text-green-600">✓ La URL base se ha configurado automáticamente</p>
                </div>
            </div>
            
            <!-- Test de Conexión a Base de Datos -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-blue-600">
                    Test de Conexión a Base de Datos
                </h2>
                <?php
                try {
                    $db = Database::getInstance();
                    $conn = $db->getConnection();
                    
                    // Verificar si la base de datos existe
                    $stmt = $conn->query("SELECT DATABASE() as db_name");
                    $result = $stmt->fetch();
                    
                    echo '<div class="space-y-2">';
                    echo '<p class="text-green-600 font-semibold">✓ Conexión exitosa a la base de datos</p>';
                    echo '<p><strong>Base de datos:</strong> ' . htmlspecialchars($result['db_name']) . '</p>';
                    echo '<p><strong>Host:</strong> ' . DB_HOST . '</p>';
                    echo '<p><strong>Usuario:</strong> ' . DB_USER . '</p>';
                    echo '<p><strong>Charset:</strong> ' . DB_CHARSET . '</p>';
                    
                    // Verificar versión de MySQL
                    $stmt = $conn->query("SELECT VERSION() as version");
                    $version = $stmt->fetch();
                    echo '<p><strong>Versión MySQL:</strong> ' . htmlspecialchars($version['version']) . '</p>';
                    echo '</div>';
                    
                } catch (Exception $e) {
                    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">';
                    echo '<p class="font-semibold">✗ Error de conexión</p>';
                    echo '<p class="text-sm mt-2">' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<div class="mt-4 text-sm">';
                    echo '<p><strong>Verificar:</strong></p>';
                    echo '<ul class="list-disc list-inside ml-4">';
                    echo '<li>Que MySQL esté instalado y en ejecución</li>';
                    echo '<li>Que exista la base de datos "' . DB_NAME . '"</li>';
                    echo '<li>Que las credenciales sean correctas</li>';
                    echo '<li>Ejecutar el script de instalación: database/schema.sql</li>';
                    echo '</ul>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
            
            <!-- Test de PHP -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-blue-600">
                    ✓ Configuración de PHP
                </h2>
                <div class="space-y-2">
                    <p><strong>Versión PHP:</strong> <?php echo PHP_VERSION; ?></p>
                    <p><strong>Extensión PDO:</strong> 
                        <?php echo extension_loaded('pdo') ? '<span class="text-green-600">✓ Instalada</span>' : '<span class="text-red-600">✗ No instalada</span>'; ?>
                    </p>
                    <p><strong>Extensión PDO MySQL:</strong> 
                        <?php echo extension_loaded('pdo_mysql') ? '<span class="text-green-600">✓ Instalada</span>' : '<span class="text-red-600">✗ No instalada</span>'; ?>
                    </p>
                    <p><strong>Zona horaria:</strong> <?php echo date_default_timezone_get(); ?></p>
                </div>
            </div>
            
            <!-- Información de Instalación -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="font-semibold text-blue-800 mb-2">📋 Pasos de instalación</h3>
                <ol class="list-decimal list-inside space-y-1 text-sm text-blue-900">
                    <li>Crear la base de datos "<?php echo DB_NAME; ?>"</li>
                    <li>Importar el archivo database/schema.sql</li>
                    <li>Verificar las credenciales en config/config.php</li>
                    <li>Acceder a <a href="<?php echo BASE_URL; ?>/index.php" class="text-blue-600 underline">la aplicación</a></li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>
