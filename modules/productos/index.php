<?php
/**
 * Módulo de gestión de productos
 */

require_once __DIR__ . '/../../config/config.php';
require_once CONFIG_PATH . '/database.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: ../usuarios/login.php');
    exit;
}

// Obtener lista de productos
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("
        SELECT p.*, c.nombre as categoria_nombre
        FROM productos p
        INNER JOIN categorias c ON p.categoria_id = c.id
        ORDER BY p.fecha_creacion DESC
    ");
    $stmt->execute();
    $productos = $stmt->fetchAll();
} catch (Exception $e) {
    $error = 'Error al obtener los productos.';
    $productos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Gestión de Productos</h1>
        </header>
        
        <nav>
            <ul>
                <li><a href="../../index.php">Dashboard</a></li>
                <li><a href="index.php">Productos</a></li>
                <li><a href="../sucursales/index.php">Sucursales</a></li>
                <li><a href="../usuarios/index.php">Usuarios</a></li>
                <li><a href="../reportes/index.php">Reportes</a></li>
                <li><a href="../usuarios/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
        
        <main>
            <h2>Catálogo de Productos Artesanales</h2>
            
            <?php if (isset($error)): ?>
                <div class="message message-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <p>
                <a href="agregar.php" class="btn btn-success">+ Agregar Producto</a>
            </p>
            
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Stock Mínimo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No hay productos registrados</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($producto['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($producto['categoria_nombre']); ?></td>
                                <td>$<?php echo number_format($producto['precio_compra'], 2); ?></td>
                                <td>$<?php echo number_format($producto['precio_venta'], 2); ?></td>
                                <td><?php echo $producto['stock_minimo']; ?></td>
                                <td><?php echo $producto['activo'] ? 'Activo' : 'Inactivo'; ?></td>
                                <td>
                                    <a href="ver.php?id=<?php echo $producto['id']; ?>" class="btn btn-info">Ver</a>
                                    <a href="editar.php?id=<?php echo $producto['id']; ?>" class="btn btn-info">Editar</a>
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
