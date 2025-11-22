<?php
/**
 * Módulo de reportes
 */

require_once __DIR__ . '/../../config/config.php';
require_once CONFIG_PATH . '/database.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: ../usuarios/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Reportes del Sistema</h1>
        </header>
        
        <nav>
            <ul>
                <li><a href="../../index.php">Dashboard</a></li>
                <li><a href="../productos/index.php">Productos</a></li>
                <li><a href="../sucursales/index.php">Sucursales</a></li>
                <li><a href="../usuarios/index.php">Usuarios</a></li>
                <li><a href="index.php">Reportes</a></li>
                <li><a href="../usuarios/logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
        
        <main>
            <h2>Tipos de Reportes Disponibles</h2>
            
            <div class="features">
                <div class="feature">
                    <h3>Inventario por Sucursal</h3>
                    <p>Visualiza el inventario actual de cada sucursal</p>
                    <a href="inventario.php" class="btn btn-info">Generar Reporte</a>
                </div>
                
                <div class="feature">
                    <h3>Movimientos de Inventario</h3>
                    <p>Historial de entradas, salidas y transferencias</p>
                    <a href="movimientos.php" class="btn btn-info">Generar Reporte</a>
                </div>
                
                <div class="feature">
                    <h3>Productos con Stock Bajo</h3>
                    <p>Productos que están por debajo del stock mínimo</p>
                    <a href="stock-bajo.php" class="btn btn-info">Generar Reporte</a>
                </div>
                
                <div class="feature">
                    <h3>Valoración de Inventario</h3>
                    <p>Valor total del inventario por sucursal</p>
                    <a href="valoracion.php" class="btn btn-info">Generar Reporte</a>
                </div>
            </div>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> - <?php echo APP_NAME; ?></p>
        </footer>
    </div>
</body>
</html>
