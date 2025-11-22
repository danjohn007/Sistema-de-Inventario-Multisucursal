<?php
/**
 * Página principal del Sistema de Inventario Multisucursal
 */

require_once __DIR__ . '/config/config.php';
require_once CONFIG_PATH . '/database.php';

// Verificar si el usuario ha iniciado sesión
$isLoggedIn = isset($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo APP_NAME; ?></h1>
            <p class="version">Versión <?php echo APP_VERSION; ?></p>
        </header>
        
        <nav>
            <ul>
                <?php if ($isLoggedIn): ?>
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="modules/productos/index.php">Productos</a></li>
                    <li><a href="modules/sucursales/index.php">Sucursales</a></li>
                    <li><a href="modules/usuarios/index.php">Usuarios</a></li>
                    <li><a href="modules/reportes/index.php">Reportes</a></li>
                    <li><a href="modules/usuarios/logout.php">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="modules/usuarios/login.php">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <main>
            <?php if ($isLoggedIn): ?>
                <section class="dashboard">
                    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></h2>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3>Productos</h3>
                            <p class="stat-number">0</p>
                            <a href="modules/productos/index.php">Ver todos</a>
                        </div>
                        
                        <div class="stat-card">
                            <h3>Sucursales</h3>
                            <p class="stat-number">0</p>
                            <a href="modules/sucursales/index.php">Ver todas</a>
                        </div>
                        
                        <div class="stat-card">
                            <h3>Usuarios</h3>
                            <p class="stat-number">0</p>
                            <a href="modules/usuarios/index.php">Ver todos</a>
                        </div>
                    </div>
                </section>
            <?php else: ?>
                <section class="welcome">
                    <h2>Sistema de Inventario de Productos Artesanales</h2>
                    <p>Gestión eficiente de inventarios en múltiples sucursales</p>
                    
                    <div class="features">
                        <div class="feature">
                            <h3>Control de Productos</h3>
                            <p>Administra tu catálogo de productos artesanales</p>
                        </div>
                        
                        <div class="feature">
                            <h3>Múltiples Sucursales</h3>
                            <p>Gestiona inventarios en diferentes ubicaciones</p>
                        </div>
                        
                        <div class="feature">
                            <h3>Reportes Detallados</h3>
                            <p>Genera reportes de inventarios y movimientos</p>
                        </div>
                    </div>
                    
                    <p class="cta">
                        <a href="modules/usuarios/login.php" class="btn-primary">Iniciar Sesión</a>
                    </p>
                </section>
            <?php endif; ?>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> - <?php echo APP_NAME; ?></p>
        </footer>
    </div>
</body>
</html>
