<?php
/**
 * Script de instalación de la base de datos
 * 
 * Este script crea la base de datos y las tablas necesarias
 * para el funcionamiento del sistema
 */

require_once __DIR__ . '/config/config.php';

$message = '';
$error = '';

// Procesar la instalación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Conectar a MySQL sin seleccionar base de datos
        $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        
        $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        // Leer el archivo schema.sql
        $schema = file_get_contents(CONFIG_PATH . '/schema.sql');
        
        // Remover comentarios de línea completa (-- al inicio)
        $lines = explode("\n", $schema);
        $cleanedLines = array_filter($lines, function($line) {
            $trimmed = trim($line);
            return !empty($trimmed) && strpos($trimmed, '--') !== 0;
        });
        $cleanedSchema = implode("\n", $cleanedLines);
        
        // Dividir en sentencias individuales
        $statements = array_filter(
            array_map('trim', explode(';', $cleanedSchema)),
            function($stmt) { return !empty($stmt); }
        );
        
        // Ejecutar cada sentencia
        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                $conn->exec($statement);
            }
        }
        
        $message = '¡Instalación completada exitosamente! La base de datos y las tablas han sido creadas.';
        $message .= '<br><br><strong>Credenciales de acceso:</strong>';
        $message .= '<br>Usuario: admin@sistema.com';
        $message .= '<br>Contraseña: admin123';
        
    } catch (PDOException $e) {
        $error = 'Error durante la instalación: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .install-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .install-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .install-header h2 {
            color: #667eea;
        }
        .info-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h2>Instalación de <?php echo APP_NAME; ?></h2>
            <p>Configuración inicial de la base de datos</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message message-success">
                <?php echo $message; ?>
                <br><br>
                <a href="index.php" class="btn btn-primary">Ir al sistema</a>
                <a href="test_connection.php" class="btn btn-info">Probar conexión</a>
            </div>
        <?php elseif ($error): ?>
            <div class="message message-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!$message): ?>
            <div class="info-box">
                <h3>Configuración actual:</h3>
                <ul>
                    <li><strong>Host:</strong> <?php echo DB_HOST; ?></li>
                    <li><strong>Base de datos:</strong> <?php echo DB_NAME; ?></li>
                    <li><strong>Usuario:</strong> <?php echo DB_USER; ?></li>
                    <li><strong>Charset:</strong> <?php echo DB_CHARSET; ?></li>
                </ul>
            </div>
            
            <div class="warning-box">
                <strong>⚠️ Advertencia:</strong> Este proceso creará la base de datos y todas las tablas necesarias. 
                Si la base de datos ya existe, asegúrese de tener una copia de seguridad antes de continuar.
            </div>
            
            <div class="info-box">
                <h3>El proceso de instalación:</h3>
                <ol>
                    <li>Creará la base de datos <strong><?php echo DB_NAME; ?></strong></li>
                    <li>Creará todas las tablas necesarias (usuarios, productos, sucursales, etc.)</li>
                    <li>Insertará datos iniciales de ejemplo</li>
                    <li>Creará un usuario administrador por defecto</li>
                </ol>
            </div>
            
            <form method="POST" action="" onsubmit="return confirm('¿Está seguro de que desea continuar con la instalación?');">
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">Iniciar Instalación</button>
                    <a href="index.php" class="btn btn-info">Cancelar</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
