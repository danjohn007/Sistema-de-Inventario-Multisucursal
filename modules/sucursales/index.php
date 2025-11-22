<?php
/**
 * Módulo de gestión de sucursales
 */

require_once __DIR__ . '/../../config/config.php';
require_once CONFIG_PATH . '/database.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: ../usuarios/login.php');
    exit;
}

// Obtener lista de sucursales
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("
        SELECT s.*, u.nombre as encargado_nombre, u.apellido as encargado_apellido
        FROM sucursales s
        LEFT JOIN usuarios u ON s.encargado_id = u.id
        ORDER BY s.fecha_creacion DESC
    ");
    $stmt->execute();
    $sucursales = $stmt->fetchAll();
} catch (Exception $e) {
    $error = 'Error al obtener las sucursales.';
    $sucursales = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursales - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Gestión de Sucursales</h1>
        </header>
        
        <nav>
            <ul>
                <li><a href="../../index.php">Dashboard</a></li>
                <li><a href="../productos/index.php">Productos</a></li>
                <li><a href="index.php">Sucursales</a></li>
                <li><a href="../usuarios/index.php">Usuarios</a></li>
                <li><a href="../reportes/index.php">Reportes</a></li>
                <li><a href="../usuarios/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
        
        <main>
            <h2>Sucursales Registradas</h2>
            
            <?php if (isset($error)): ?>
                <div class="message message-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <p>
                <a href="agregar.php" class="btn btn-success">+ Agregar Sucursal</a>
            </p>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Encargado</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sucursales)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No hay sucursales registradas</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sucursales as $sucursal): ?>
                            <tr>
                                <td><?php echo $sucursal['id']; ?></td>
                                <td><?php echo htmlspecialchars($sucursal['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($sucursal['direccion'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($sucursal['telefono'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($sucursal['email'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php 
                                    if ($sucursal['encargado_nombre']) {
                                        echo htmlspecialchars($sucursal['encargado_nombre'] . ' ' . $sucursal['encargado_apellido']);
                                    } else {
                                        echo 'Sin asignar';
                                    }
                                    ?>
                                </td>
                                <td><?php echo $sucursal['activa'] ? 'Activa' : 'Inactiva'; ?></td>
                                <td>
                                    <a href="ver.php?id=<?php echo $sucursal['id']; ?>" class="btn btn-info">Ver</a>
                                    <a href="editar.php?id=<?php echo $sucursal['id']; ?>" class="btn btn-info">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> - <?php echo APP_NAME; ?></p>
        </footer>
    </div>
</body>
</html>
