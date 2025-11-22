<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>

<div class="flex">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>
    
    <main class="flex-1 p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Inventario</h1>
            <p class="text-gray-600 mt-1">Control de stock por sucursal</p>
        </div>
        
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="<?php echo BASE_URL; ?>/inventory" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sucursal</label>
                    <select name="sucursal" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <?php if ($_SESSION['user_role'] === 'administrador'): ?>
                            <option value="">Todas las sucursales</option>
                        <?php endif; ?>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?php echo $branch['id']; ?>" 
                                <?php echo ($filters['sucursal_id'] ?? '') == $branch['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($branch['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <select name="categoria" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                <?php echo ($filters['categoria_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>"
                        placeholder="Producto o código..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                    >
                </div>
                
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                    <a href="<?php echo BASE_URL; ?>/inventory" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
            
            <div class="mt-4 flex space-x-2">
                <a href="<?php echo BASE_URL; ?>/inventory/lowstock" class="text-sm text-red-600 hover:text-red-700">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Ver productos con stock bajo
                </a>
                <span class="text-gray-300">|</span>
                <a href="<?php echo BASE_URL; ?>/inventory/movements" class="text-sm text-blue-600 hover:text-blue-700">
                    <i class="fas fa-history mr-1"></i>Ver movimientos de inventario
                </a>
            </div>
        </div>
        
        <!-- Tabla de inventario -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if (empty($inventory)): ?>
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-box-open text-6xl mb-4 text-gray-300"></i>
                    <p class="text-xl">No se encontró inventario con los filtros seleccionados</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sucursal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stock Actual</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Mín/Máx</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($inventory as $item): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['producto_nombre']); ?></p>
                                            <?php if ($item['variante_nombre']): ?>
                                                <p class="text-xs text-gray-500">Variante: <?php echo htmlspecialchars($item['variante_nombre']); ?></p>
                                            <?php endif; ?>
                                            <p class="text-xs text-gray-400">
                                                <i class="fas fa-barcode mr-1"></i><?php echo htmlspecialchars($item['codigo_barras'] ?? 'N/A'); ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900"><?php echo htmlspecialchars($item['sucursal_nombre']); ?></span>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($item['sucursal_codigo']); ?></p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">
                                            <?php echo htmlspecialchars($item['ubicacion_fisica'] ?? 'No definida'); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-lg font-bold text-gray-900"><?php echo $item['cantidad_actual']; ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm text-gray-600">
                                            <?php echo $item['cantidad_minima']; ?> / <?php echo $item['cantidad_maxima']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-semibold text-green-600">
                                            $<?php echo number_format($item['cantidad_actual'] * $item['precio_venta'], 2); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <?php if ($item['cantidad_actual'] <= 0): ?>
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>Agotado
                                            </span>
                                        <?php elseif ($item['cantidad_actual'] <= $item['cantidad_minima']): ?>
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Bajo
                                            </span>
                                        <?php elseif ($item['cantidad_actual'] >= $item['cantidad_maxima']): ?>
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                <i class="fas fa-arrow-up mr-1"></i>Exceso
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Normal
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
