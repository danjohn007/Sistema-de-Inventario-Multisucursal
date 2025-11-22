<nav class="bg-white shadow-lg border-b border-gray-200">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo y título -->
            <div class="flex items-center space-x-4">
                <i class="fas fa-boxes text-3xl text-blue-600"></i>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Sistema de Inventario</h1>
                    <p class="text-xs text-gray-500">Productos Artesanales</p>
                </div>
            </div>
            
            <!-- Usuario y acciones -->
            <div class="flex items-center space-x-6">
                <!-- Notificaciones -->
                <div class="relative">
                    <button class="text-gray-600 hover:text-blue-600 relative">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                            3
                        </span>
                    </button>
                </div>
                
                <!-- Información del usuario -->
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-800"><?php echo $_SESSION['user_name']; ?></p>
                        <p class="text-xs text-gray-500"><?php echo ucfirst(str_replace('_', ' ', $_SESSION['user_role'])); ?></p>
                        <?php if ($_SESSION['user_sucursal_nombre']): ?>
                            <p class="text-xs text-blue-600">
                                <i class="fas fa-store text-xs"></i> <?php echo $_SESSION['user_sucursal_nombre']; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                        <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                    </div>
                </div>
                
                <!-- Botón de salir -->
                <a href="<?php echo BASE_URL; ?>/auth/logout" class="text-gray-600 hover:text-red-600 transition-colors">
                    <i class="fas fa-sign-out-alt text-xl"></i>
                </a>
            </div>
        </div>
    </div>
</nav>
