<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>

<div class="flex">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>
    
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Clientes</h1>
                <p class="text-gray-600 mt-1">Gestión de clientes y programa de fidelización</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/customers/create" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg transition">
                <i class="fas fa-plus mr-2"></i>Nuevo Cliente
            </a>
        </div>
        
        <!-- Búsqueda -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="<?php echo BASE_URL; ?>/customers" class="flex gap-4">
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>"
                    placeholder="Buscar por nombre, email o teléfono..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg"
                >
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </form>
        </div>
        
        <!-- Lista de clientes -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if (empty($customers)): ?>
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-users text-6xl mb-4 text-gray-300"></i>
                    <p class="text-xl">No se encontraron clientes</p>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ciudad</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Puntos</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Registro</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($customers as $customer): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold mr-3">
                                            <?php echo strtoupper(substr($customer['nombre'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($customer['nombre'] . ' ' . ($customer['apellidos'] ?? '')); ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($customer['email']): ?>
                                        <p class="text-sm text-gray-900">
                                            <i class="fas fa-envelope text-gray-400 mr-1"></i>
                                            <?php echo htmlspecialchars($customer['email']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($customer['telefono']): ?>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-phone text-gray-400 mr-1"></i>
                                            <?php echo htmlspecialchars($customer['telefono']); ?>
                                        </p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php echo htmlspecialchars($customer['ciudad'] ?? 'N/A'); ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-star mr-1"></i>
                                        <?php echo $customer['puntos_fidelidad']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-600">
                                    <?php echo date('d/m/Y', strtotime($customer['fecha_registro'])); ?>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="<?php echo BASE_URL; ?>/customers/view/<?php echo $customer['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/customers/edit/<?php echo $customer['id']; ?>" 
                                       class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
