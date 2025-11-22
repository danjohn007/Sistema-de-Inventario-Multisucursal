<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>

<div class="flex">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>
    
    <main class="flex-1 p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Configuración del Sistema</h1>
            <p class="text-gray-600 mt-1">Personaliza y configura tu sistema de inventario</p>
        </div>
        
        <!-- Mensajes -->
        <?php if (Session::flash('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo Session::flash('success'); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo BASE_URL; ?>/settings">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Configuración General -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-cog text-blue-600 mr-2"></i>
                        Configuración General
                    </h2>
                    
                    <?php if (isset($settings['general'])): ?>
                        <?php foreach ($settings['general'] as $setting): ?>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <?php echo htmlspecialchars($setting['descripcion']); ?>
                                </label>
                                <?php if ($setting['tipo'] === 'texto' || $setting['tipo'] === 'email' || $setting['tipo'] === 'url'): ?>
                                    <input 
                                        type="<?php echo $setting['tipo'] === 'email' ? 'email' : 'text'; ?>"
                                        name="setting_<?php echo $setting['clave']; ?>"
                                        value="<?php echo htmlspecialchars($setting['valor']); ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    >
                                <?php elseif ($setting['tipo'] === 'numero'): ?>
                                    <input 
                                        type="number"
                                        name="setting_<?php echo $setting['clave']; ?>"
                                        value="<?php echo htmlspecialchars($setting['valor']); ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    >
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Apariencia -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-palette text-purple-600 mr-2"></i>
                        Apariencia
                    </h2>
                    
                    <?php if (isset($settings['apariencia'])): ?>
                        <?php foreach ($settings['apariencia'] as $setting): ?>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <?php echo htmlspecialchars($setting['descripcion']); ?>
                                </label>
                                <?php if ($setting['tipo'] === 'color'): ?>
                                    <div class="flex items-center space-x-2">
                                        <input 
                                            type="color"
                                            name="setting_<?php echo $setting['clave']; ?>"
                                            value="<?php echo htmlspecialchars($setting['valor']); ?>"
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer"
                                        >
                                        <input 
                                            type="text"
                                            value="<?php echo htmlspecialchars($setting['valor']); ?>"
                                            readonly
                                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                        >
                                    </div>
                                <?php else: ?>
                                    <input 
                                        type="text"
                                        name="setting_<?php echo $setting['clave']; ?>"
                                        value="<?php echo htmlspecialchars($setting['valor']); ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    >
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Email -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-envelope text-green-600 mr-2"></i>
                        Configuración de Email
                    </h2>
                    
                    <?php if (isset($settings['email'])): ?>
                        <?php foreach ($settings['email'] as $setting): ?>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <?php echo htmlspecialchars($setting['descripcion']); ?>
                                </label>
                                <input 
                                    type="<?php echo $setting['tipo'] === 'email' ? 'email' : 'text'; ?>"
                                    name="setting_<?php echo $setting['clave']; ?>"
                                    value="<?php echo htmlspecialchars($setting['valor']); ?>"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                >
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Pagos -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-credit-card text-yellow-600 mr-2"></i>
                        Configuración de Pagos
                    </h2>
                    
                    <?php if (isset($settings['pagos'])): ?>
                        <?php foreach ($settings['pagos'] as $setting): ?>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <?php echo htmlspecialchars($setting['descripcion']); ?>
                                </label>
                                <input 
                                    type="text"
                                    name="setting_<?php echo $setting['clave']; ?>"
                                    value="<?php echo htmlspecialchars($setting['valor']); ?>"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    <?php echo strpos($setting['clave'], 'secret') !== false ? 'type="password"' : ''; ?>
                                >
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Contacto -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-phone text-red-600 mr-2"></i>
                        Información de Contacto
                    </h2>
                    
                    <?php if (isset($settings['contacto'])): ?>
                        <?php foreach ($settings['contacto'] as $setting): ?>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <?php echo htmlspecialchars($setting['descripcion']); ?>
                                </label>
                                <input 
                                    type="text"
                                    name="setting_<?php echo $setting['clave']; ?>"
                                    value="<?php echo htmlspecialchars($setting['valor']); ?>"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                >
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Integraciones -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-plug text-indigo-600 mr-2"></i>
                        Integraciones
                    </h2>
                    
                    <?php if (isset($settings['integraciones'])): ?>
                        <?php foreach ($settings['integraciones'] as $setting): ?>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <?php echo htmlspecialchars($setting['descripcion']); ?>
                                </label>
                                <input 
                                    type="text"
                                    name="setting_<?php echo $setting['clave']; ?>"
                                    value="<?php echo htmlspecialchars($setting['valor']); ?>"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                >
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Botón de guardar -->
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg shadow-lg transition text-lg font-semibold">
                    <i class="fas fa-save mr-2"></i>Guardar Configuración
                </button>
            </div>
        </form>
    </main>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
