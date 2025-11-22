    <footer class="bg-white border-t border-gray-200 mt-8">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-wrap justify-between items-center">
                <div class="text-gray-600 text-sm">
                    &copy; <?php echo date('Y'); ?> Sistema de Inventario Multisucursal. Todos los derechos reservados.
                </div>
                <div class="text-gray-600 text-sm">
                    Versión 1.0 | Desarrollado con ❤️ para Querétaro
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Función para mostrar notificaciones
        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg z-50 transform transition-all duration-300`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
        
        // Confirmar acciones de eliminación
        function confirmDelete(message = '¿Estás seguro de eliminar este elemento?') {
            return confirm(message);
        }
    </script>
</body>
</html>
