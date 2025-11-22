<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>

<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>

<div class="flex">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>
    
    <main class="flex-1 p-8">
        <!-- Mensajes flash -->
        <?php if (Session::flash('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo Session::flash('success'); ?>
            </div>
        <?php endif; ?>
        
        <?php if (Session::flash('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo Session::flash('error'); ?>
            </div>
        <?php endif; ?>
        
        <!-- Título -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-gray-600 mt-1">Resumen general del sistema de inventario</p>
        </div>
        
        <!-- Tarjetas de estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Productos -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold uppercase">Total Productos</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo number_format($stats['total_productos']); ?></p>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-full">
                        <i class="fas fa-box text-2xl text-blue-500"></i>
                    </div>
                </div>
            </div>
            
            <!-- Total Sucursales -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold uppercase">Sucursales</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo number_format($stats['total_sucursales']); ?></p>
                    </div>
                    <div class="bg-green-100 p-4 rounded-full">
                        <i class="fas fa-store text-2xl text-green-500"></i>
                    </div>
                </div>
            </div>
            
            <!-- Total Inventario -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold uppercase">Total Inventario</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo number_format($stats['total_inventario']); ?></p>
                    </div>
                    <div class="bg-purple-100 p-4 rounded-full">
                        <i class="fas fa-warehouse text-2xl text-purple-500"></i>
                    </div>
                </div>
            </div>
            
            <!-- Stock Bajo -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-semibold uppercase">Stock Bajo</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo number_format($stats['productos_stock_bajo']); ?></p>
                    </div>
                    <div class="bg-red-100 p-4 rounded-full">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Segunda fila de estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Ventas de Hoy -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Ventas de Hoy</h3>
                    <i class="fas fa-calendar-day text-xl text-blue-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">$<?php echo number_format($stats['monto_ventas_hoy'], 2); ?></p>
                    <p class="text-sm text-gray-500 mt-1"><?php echo $stats['ventas_hoy']; ?> transacciones</p>
                </div>
            </div>
            
            <!-- Transferencias Pendientes -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Transferencias</h3>
                    <i class="fas fa-truck text-xl text-orange-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800"><?php echo number_format($stats['transferencias_pendientes']); ?></p>
                    <p class="text-sm text-gray-500 mt-1">Pendientes</p>
                </div>
            </div>
            
            <!-- Acciones Rápidas -->
            <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
                <h3 class="text-lg font-semibold mb-4">Acciones Rápidas</h3>
                <div class="space-y-2">
                    <a href="<?php echo BASE_URL; ?>/pos" class="block bg-white bg-opacity-20 hover:bg-opacity-30 rounded px-3 py-2 text-sm transition">
                        <i class="fas fa-cash-register mr-2"></i>Nueva Venta
                    </a>
                    <a href="<?php echo BASE_URL; ?>/transfers/create" class="block bg-white bg-opacity-20 hover:bg-opacity-30 rounded px-3 py-2 text-sm transition">
                        <i class="fas fa-truck mr-2"></i>Nueva Transferencia
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Gráficas y tablas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Productos Más Vendidos -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                    Productos Más Vendidos (Últimos 30 días)
                </h3>
                <?php if (!empty($stats['productos_mas_vendidos'])): ?>
                    <div class="space-y-3">
                        <?php foreach ($stats['productos_mas_vendidos'] as $producto): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($producto['nombre']); ?></p>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo min(100, ($producto['total_vendido'] / max(array_column($stats['productos_mas_vendidos'], 'total_vendido'))) * 100); ?>%"></div>
                                    </div>
                                </div>
                                <span class="ml-4 text-lg font-bold text-gray-800"><?php echo $producto['total_vendido']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-4">No hay datos de ventas en los últimos 30 días</p>
                <?php endif; ?>
            </div>
            
            <!-- Ventas por Sucursal -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-store text-green-500 mr-2"></i>
                    Ventas por Sucursal (Últimos 30 días)
                </h3>
                <?php if (!empty($stats['ventas_por_sucursal'])): ?>
                    <div class="space-y-3">
                        <?php foreach ($stats['ventas_por_sucursal'] as $sucursal): ?>
                            <div class="border-b border-gray-200 pb-3">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($sucursal['nombre']); ?></span>
                                    <span class="text-sm font-bold text-green-600">$<?php echo number_format($sucursal['monto_total'], 2); ?></span>
                                </div>
                                <p class="text-xs text-gray-500"><?php echo $sucursal['total_ventas']; ?> ventas realizadas</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-4">No hay datos de ventas</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Alertas Recientes -->
        <?php if (!empty($stats['alertas_recientes'])): ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-bell text-red-500 mr-2"></i>
                Alertas Recientes
            </h3>
            <div class="space-y-2">
                <?php foreach ($stats['alertas_recientes'] as $alerta): ?>
                    <div class="border-l-4 border-yellow-500 bg-yellow-50 p-3 rounded">
                        <p class="font-medium text-gray-800"><?php echo htmlspecialchars($alerta['titulo']); ?></p>
                        <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($alerta['mensaje']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
