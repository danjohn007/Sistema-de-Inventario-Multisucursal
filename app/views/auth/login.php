<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <!-- Logo y título -->
            <div class="text-center mb-8">
                <div class="inline-block bg-white p-4 rounded-full shadow-lg mb-4">
                    <i class="fas fa-boxes text-5xl text-blue-600"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    Sistema de Inventario Multisucursal
                </h1>
                <p class="text-gray-600">Productos Artesanales de Querétaro</p>
            </div>
            
            <!-- Formulario de login -->
            <div class="bg-white rounded-xl shadow-2xl p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">
                    Iniciar Sesión
                </h2>
                
                <?php if (Session::flash('error')): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo Session::flash('error'); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (Session::flash('success')): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo Session::flash('success'); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo BASE_URL; ?>/auth/login">
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">
                            <i class="fas fa-envelope mr-2"></i>Correo Electrónico
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="tu@email.com"
                        >
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">
                            <i class="fas fa-lock mr-2"></i>Contraseña
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="••••••••"
                        >
                    </div>
                    
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-lg hover:shadow-xl"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Usuario de prueba: <code class="bg-gray-100 px-2 py-1 rounded">admin@inventario.com</code><br>
                        Contraseña: <code class="bg-gray-100 px-2 py-1 rounded">admin123</code>
                    </p>
                </div>
            </div>
            
            <!-- Información adicional -->
            <div class="mt-6 text-center text-gray-600 text-sm">
                <p>
                    <i class="fas fa-shield-alt mr-1"></i>
                    Conexión segura y protegida
                </p>
            </div>
        </div>
    </div>
</body>
</html>
