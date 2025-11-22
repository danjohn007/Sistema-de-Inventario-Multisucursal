<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>

<div class="flex">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>
    
    <main class="flex-1 p-8">
        <!-- Título y acciones -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Productos Artesanales</h1>
                <p class="text-gray-600 mt-1">Gestión del catálogo de productos</p>
            </div>
            <?php if (in_array($_SESSION['user_role'], ['administrador', 'gerente_sucursal'])): ?>
            <a href="<?php echo BASE_URL; ?>/products/create" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg transition">
                <i class="fas fa-plus mr-2"></i>Nuevo Producto
            </a>
            <?php endif; ?>
        </div>
        
        <!-- Filtros y búsqueda -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="<?php echo BASE_URL; ?>/products" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>"
                        placeholder="Nombre, descripción o código..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <select name="categoria" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                <?php echo ($filters['categoria_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg mr-2 transition">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                    <a href="<?php echo BASE_URL; ?>/products" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg transition">
                        <i class="fas fa-times mr-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Lista de productos -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if (empty($products)): ?>
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-box-open text-6xl mb-4 text-gray-300"></i>
                    <p class="text-xl">No se encontraron productos</p>
                    <?php if (in_array($_SESSION['user_role'], ['administrador', 'gerente_sucursal'])): ?>
                        <a href="<?php echo BASE_URL; ?>/products/create" class="text-blue-600 hover:text-blue-700 mt-2 inline-block">
                            <i class="fas fa-plus mr-1"></i>Crear el primer producto
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Producto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Categoría
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Artesano
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Precio Venta
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Código
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($products as $product): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                                <i class="fas fa-box text-gray-400 text-xl"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($product['nombre']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars(substr($product['descripcion'] ?? '', 0, 50)); ?>
                                                    <?php if (strlen($product['descripcion'] ?? '') > 50) echo '...'; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo htmlspecialchars($product['categoria_nombre']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($product['artesano_nombre'] ?? 'N/A'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                        $<?php echo number_format($product['precio_venta'], 2); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <code class="bg-gray-100 px-2 py-1 rounded">
                                            <?php echo htmlspecialchars($product['codigo_barras'] ?? 'N/A'); ?>
                                        </code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?php echo BASE_URL; ?>/products/view/<?php echo $product['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (in_array($_SESSION['user_role'], ['administrador', 'gerente_sucursal'])): ?>
                                            <a href="<?php echo BASE_URL; ?>/products/edit/<?php echo $product['id']; ?>" 
                                               class="text-yellow-600 hover:text-yellow-900 mr-3">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['user_role'] === 'administrador'): ?>
                                            <a href="<?php echo BASE_URL; ?>/products/delete/<?php echo $product['id']; ?>" 
                                               class="text-red-600 hover:text-red-900"
                                               onclick="return confirmDelete('¿Estás seguro de eliminar este producto?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <?php if ($total_pages > 1): ?>
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Página <span class="font-medium"><?php echo $current_page; ?></span> de 
                                <span class="font-medium"><?php echo $total_pages; ?></span>
                            </div>
                            <div class="flex space-x-2">
                                <?php if ($current_page > 1): ?>
                                    <a href="?page=<?php echo $current_page - 1; ?><?php echo !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?><?php echo !empty($filters['categoria_id']) ? '&categoria=' . $filters['categoria_id'] : ''; ?>" 
                                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                        Anterior
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($current_page < $total_pages): ?>
                                    <a href="?page=<?php echo $current_page + 1; ?><?php echo !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?><?php echo !empty($filters['categoria_id']) ? '&categoria=' . $filters['categoria_id'] : ''; ?>" 
                                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                        Siguiente
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
