<aside class="w-64 bg-white shadow-lg border-r border-gray-200 min-h-screen">
    <div class="p-4">
        <div class="space-y-1">
            <!-- Dashboard -->
            <a href="<?php echo BASE_URL; ?>/dashboard" class="sidebar-link">
                <i class="fas fa-home w-6"></i>
                <span>Dashboard</span>
            </a>
            
            <!-- Productos -->
            <div class="mt-6 mb-2 px-4 text-xs font-semibold text-gray-500 uppercase">
                Gestión de Productos
            </div>
            <a href="<?php echo BASE_URL; ?>/products" class="sidebar-link">
                <i class="fas fa-box w-6"></i>
                <span>Productos</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/products/categories" class="sidebar-link">
                <i class="fas fa-tags w-6"></i>
                <span>Categorías</span>
            </a>
            
            <!-- Inventario -->
            <div class="mt-6 mb-2 px-4 text-xs font-semibold text-gray-500 uppercase">
                Inventario
            </div>
            <a href="<?php echo BASE_URL; ?>/inventory" class="sidebar-link">
                <i class="fas fa-warehouse w-6"></i>
                <span>Inventario</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/inventory/movements" class="sidebar-link">
                <i class="fas fa-exchange-alt w-6"></i>
                <span>Movimientos</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/transfers" class="sidebar-link">
                <i class="fas fa-truck w-6"></i>
                <span>Transferencias</span>
            </a>
            
            <!-- Ventas -->
            <div class="mt-6 mb-2 px-4 text-xs font-semibold text-gray-500 uppercase">
                Ventas
            </div>
            <a href="<?php echo BASE_URL; ?>/pos" class="sidebar-link">
                <i class="fas fa-cash-register w-6"></i>
                <span>Punto de Venta</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/pos/sales" class="sidebar-link">
                <i class="fas fa-receipt w-6"></i>
                <span>Ventas</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/customers" class="sidebar-link">
                <i class="fas fa-users w-6"></i>
                <span>Clientes</span>
            </a>
            
            <!-- Compras -->
            <div class="mt-6 mb-2 px-4 text-xs font-semibold text-gray-500 uppercase">
                Compras
            </div>
            <a href="<?php echo BASE_URL; ?>/suppliers" class="sidebar-link">
                <i class="fas fa-user-tie w-6"></i>
                <span>Artesanos</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/suppliers/orders" class="sidebar-link">
                <i class="fas fa-file-invoice w-6"></i>
                <span>Órdenes</span>
            </a>
            
            <!-- Sucursales -->
            <?php if (in_array($_SESSION['user_role'], ['administrador', 'gerente_sucursal'])): ?>
            <div class="mt-6 mb-2 px-4 text-xs font-semibold text-gray-500 uppercase">
                Multisucursal
            </div>
            <a href="<?php echo BASE_URL; ?>/branches" class="sidebar-link">
                <i class="fas fa-store w-6"></i>
                <span>Sucursales</span>
            </a>
            <?php endif; ?>
            
            <!-- Reportes -->
            <div class="mt-6 mb-2 px-4 text-xs font-semibold text-gray-500 uppercase">
                Reportes
            </div>
            <a href="<?php echo BASE_URL; ?>/reports/inventory" class="sidebar-link">
                <i class="fas fa-chart-bar w-6"></i>
                <span>Inventario</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/reports/sales" class="sidebar-link">
                <i class="fas fa-chart-line w-6"></i>
                <span>Ventas</span>
            </a>
            
            <!-- Administración -->
            <?php if ($_SESSION['user_role'] === 'administrador'): ?>
            <div class="mt-6 mb-2 px-4 text-xs font-semibold text-gray-500 uppercase">
                Administración
            </div>
            <a href="<?php echo BASE_URL; ?>/users" class="sidebar-link">
                <i class="fas fa-user-cog w-6"></i>
                <span>Usuarios</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/settings" class="sidebar-link">
                <i class="fas fa-cog w-6"></i>
                <span>Configuración</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
</aside>
