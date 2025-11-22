<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>

<div class="flex">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>
    
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Artesanos y Proveedores</h1>
                <p class="text-gray-600 mt-1">Gestión de artesanos colaboradores</p>
            </div>
            <?php if (in_array($_SESSION['user_role'], ['administrador', 'gerente_sucursal'])): ?>
            <a href="<?php echo BASE_URL; ?>/suppliers/create" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg transition">
                <i class="fas fa-plus mr-2"></i>Nuevo Artesano
            </a>
            <?php endif; ?>
        </div>
        
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="<?php echo BASE_URL; ?>/suppliers" class="flex flex-wrap gap-4">
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>"
                    placeholder="Buscar por nombre o especialidad..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg"
                >
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </form>
        </div>
        
        <!-- Lista de artesanos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($suppliers as $supplier): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-2xl font-bold">
                                <?php echo strtoupper(substr($supplier['nombre'], 0, 1)); ?>
                            </div>
                            <div class="flex space-x-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $supplier['calificacion'] ? 'text-yellow-300' : 'text-gray-400'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">
                            <?php echo htmlspecialchars($supplier['nombre_comercial'] ?: ($supplier['nombre'] . ' ' . $supplier['apellidos'])); ?>
                        </h3>
                        
                        <?php if ($supplier['especialidad']): ?>
                        <p class="text-sm text-purple-600 font-medium mb-4">
                            <i class="fas fa-hands mr-1"></i><?php echo htmlspecialchars($supplier['especialidad']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <div class="space-y-2 mb-4">
                            <?php if ($supplier['region_origen']): ?>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt w-5 text-gray-400"></i>
                                <?php echo htmlspecialchars($supplier['region_origen']); ?>, 
                                <?php echo htmlspecialchars($supplier['estado']); ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($supplier['telefono']): ?>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-phone w-5 text-gray-400"></i>
                                <?php echo htmlspecialchars($supplier['telefono']); ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($supplier['email']): ?>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-envelope w-5 text-gray-400"></i>
                                <?php echo htmlspecialchars($supplier['email']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($supplier['tecnicas']): ?>
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 mb-1">Técnicas:</p>
                            <p class="text-sm text-gray-700"><?php echo htmlspecialchars(substr($supplier['tecnicas'], 0, 100)); ?><?php echo strlen($supplier['tecnicas']) > 100 ? '...' : ''; ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="border-t border-gray-200 pt-4 mb-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-gray-500">Productos</p>
                                    <p class="text-2xl font-bold text-blue-600"><?php echo $supplier['product_count']; ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Calificación</p>
                                    <p class="text-2xl font-bold text-yellow-500"><?php echo number_format($supplier['calificacion'], 1); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex space-x-2">
                            <a href="<?php echo BASE_URL; ?>/suppliers/view/<?php echo $supplier['id']; ?>" 
                               class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-600 py-2 px-4 rounded transition">
                                <i class="fas fa-eye mr-1"></i>Ver
                            </a>
                            <?php if (in_array($_SESSION['user_role'], ['administrador', 'gerente_sucursal'])): ?>
                            <a href="<?php echo BASE_URL; ?>/suppliers/edit/<?php echo $supplier['id']; ?>" 
                               class="flex-1 text-center bg-yellow-50 hover:bg-yellow-100 text-yellow-600 py-2 px-4 rounded transition">
                                <i class="fas fa-edit mr-1"></i>Editar
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($suppliers)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-user-tie text-6xl text-gray-300 mb-4"></i>
                <p class="text-xl text-gray-600 mb-4">No se encontraron artesanos</p>
                <?php if (in_array($_SESSION['user_role'], ['administrador', 'gerente_sucursal'])): ?>
                    <a href="<?php echo BASE_URL; ?>/suppliers/create" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                        <i class="fas fa-plus mr-2"></i>Agregar Primer Artesano
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
