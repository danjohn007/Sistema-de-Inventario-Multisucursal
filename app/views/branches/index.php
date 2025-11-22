<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>

<div class="flex">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>
    
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Sucursales</h1>
                <p class="text-gray-600 mt-1">Gestión de sucursales del sistema</p>
            </div>
            <?php if ($_SESSION['user_role'] === 'administrador'): ?>
            <a href="<?php echo BASE_URL; ?>/branches/create" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg transition">
                <i class="fas fa-plus mr-2"></i>Nueva Sucursal
            </a>
            <?php endif; ?>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php foreach ($branches as $branch): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-white">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xl font-bold"><?php echo htmlspecialchars($branch['nombre']); ?></h3>
                            <?php if ($branch['activo']): ?>
                                <span class="bg-green-500 px-2 py-1 rounded text-xs">Activa</span>
                            <?php else: ?>
                                <span class="bg-red-500 px-2 py-1 rounded text-xs">Inactiva</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm opacity-90">
                            <i class="fas fa-code mr-1"></i>
                            Código: <?php echo htmlspecialchars($branch['codigo']); ?>
                        </p>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-3 mb-6">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-gray-400 mt-1 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-800"><?php echo htmlspecialchars($branch['direccion']); ?></p>
                                    <p class="text-xs text-gray-500">
                                        <?php echo htmlspecialchars($branch['ciudad']); ?>, 
                                        <?php echo htmlspecialchars($branch['estado']); ?>
                                    </p>
                                </div>
                            </div>
                            
                            <?php if ($branch['telefono']): ?>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 mr-3"></i>
                                <p class="text-sm text-gray-800"><?php echo htmlspecialchars($branch['telefono']); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($branch['responsable_nombre']): ?>
                            <div class="flex items-center">
                                <i class="fas fa-user-tie text-gray-400 mr-3"></i>
                                <p class="text-sm text-gray-800"><?php echo htmlspecialchars($branch['responsable_nombre'] . ' ' . $branch['responsable_apellidos']); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($branch['horario_apertura'] && $branch['horario_cierre']): ?>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-gray-400 mr-3"></i>
                                <p class="text-sm text-gray-800">
                                    <?php echo date('H:i', strtotime($branch['horario_apertura'])); ?> - 
                                    <?php echo date('H:i', strtotime($branch['horario_cierre'])); ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Estadísticas de inventario -->
                        <?php if (isset($branch['stats'])): ?>
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Inventario</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500">Productos</p>
                                    <p class="text-lg font-bold text-blue-600"><?php echo number_format($branch['stats']['total_productos']); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Items</p>
                                    <p class="text-lg font-bold text-green-600"><?php echo number_format($branch['stats']['total_items']); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Valor Total</p>
                                    <p class="text-sm font-bold text-purple-600">$<?php echo number_format($branch['stats']['valor_inventario'], 2); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Stock Bajo</p>
                                    <p class="text-lg font-bold text-red-600"><?php echo number_format($branch['stats']['productos_stock_bajo']); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Acciones -->
                        <div class="mt-6 flex space-x-2">
                            <a href="<?php echo BASE_URL; ?>/branches/view/<?php echo $branch['id']; ?>" 
                               class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-600 py-2 px-4 rounded transition">
                                <i class="fas fa-eye mr-1"></i>Ver
                            </a>
                            <?php if ($_SESSION['user_role'] === 'administrador'): ?>
                            <a href="<?php echo BASE_URL; ?>/branches/edit/<?php echo $branch['id']; ?>" 
                               class="flex-1 text-center bg-yellow-50 hover:bg-yellow-100 text-yellow-600 py-2 px-4 rounded transition">
                                <i class="fas fa-edit mr-1"></i>Editar
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($branches)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-store text-6xl text-gray-300 mb-4"></i>
                <p class="text-xl text-gray-600 mb-4">No hay sucursales registradas</p>
                <?php if ($_SESSION['user_role'] === 'administrador'): ?>
                    <a href="<?php echo BASE_URL; ?>/branches/create" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                        <i class="fas fa-plus mr-2"></i>Crear Primera Sucursal
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
