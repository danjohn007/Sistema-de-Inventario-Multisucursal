<?php
/**
 * Módulo de gestión de usuarios
 */

require_once __DIR__ . '/../../config/config.php';
require_once CONFIG_PATH . '/database.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Solo administradores y gerentes pueden ver esta página
if ($_SESSION['user_rol'] !== 'admin' && $_SESSION['user_rol'] !== 'gerente') {
    header('Location: ../../index.php');
    exit;
}

// Obtener lista de usuarios
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("
        SELECT u.*, s.nombre as sucursal_nombre 
        FROM usuarios u
        LEFT JOIN sucursales s ON u.sucursal_id = s.id
        ORDER BY u.fecha_creacion DESC
    ");
    $stmt->execute();
    $usuarios = $stmt->fetchAll();
} catch (Exception $e) {
    $error = 'Error al obtener los usuarios.';
    $usuarios = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Gestión de Usuarios</h1>
        </header>
        
        <nav>
            <ul>
                <li><a href="../../index.php">Dashboard</a></li>
                <li><a href="../productos/index.php">Productos</a></li>
                <li><a href="../sucursales/index.php">Sucursales</a></li>
                <li><a href="index.php">Usuarios</a></li>
                <li><a href="../reportes/index.php">Reportes</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
        
        <main>
            <h2>Lista de Usuarios</h2>
            
            <?php if (isset($error)): ?>
                <div class="message message-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <p>
                <a href="agregar.php" class="btn btn-success">+ Agregar Usuario</a>
            </p>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Sucursal</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No hay usuarios registrados</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo ucfirst($usuario['rol']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['sucursal_nombre'] ?? 'Sin asignar'); ?></td>
                                <td><?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?></td>
                                <td>
                                    <a href="editar.php?id=<?php echo $usuario['id']; ?>" class="btn btn-info">Editar</a>
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
