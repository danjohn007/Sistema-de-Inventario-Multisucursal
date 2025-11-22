<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/app/views/layouts/navbar.php'; ?>

<div class="flex">
    <?php require_once BASE_PATH . '/app/views/layouts/sidebar.php'; ?>
    
    <main class="flex-1 p-8 bg-gray-50">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Punto de Venta</h1>
            <p class="text-gray-600 mt-1">Sucursal: <?php echo $_SESSION['user_sucursal_nombre']; ?></p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Productos -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-4">
                        <input 
                            type="text" 
                            id="product-search" 
                            placeholder="Buscar producto por nombre o código..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[600px] overflow-y-auto">
                        <?php foreach ($products as $product): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 cursor-pointer transition product-card"
                                 data-id="<?php echo $product['id']; ?>"
                                 data-name="<?php echo htmlspecialchars($product['nombre']); ?>"
                                 data-price="<?php echo $product['precio_venta']; ?>">
                                <div class="flex items-center mb-2">
                                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 text-sm"><?php echo htmlspecialchars($product['nombre']); ?></h4>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($product['categoria_nombre']); ?></p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-green-600">$<?php echo number_format($product['precio_venta'], 2); ?></span>
                                    <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['nombre']); ?>', <?php echo $product['precio_venta']; ?>)"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Carrito -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Carrito de Compra</h3>
                    
                    <!-- Cliente -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cliente (Opcional)</label>
                        <select id="customer-select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Cliente General</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>">
                                    <?php echo htmlspecialchars($customer['nombre'] . ' ' . ($customer['apellidos'] ?? '')); ?>
                                    <?php if ($customer['puntos_fidelidad'] > 0): ?>
                                        (<?php echo $customer['puntos_fidelidad']; ?> pts)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Items del carrito -->
                    <div id="cart-items" class="mb-4 max-h-64 overflow-y-auto">
                        <p class="text-gray-500 text-center py-4">No hay productos en el carrito</p>
                    </div>
                    
                    <!-- Totales -->
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span id="subtotal">$0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>IVA (16%):</span>
                            <span id="tax">$0.00</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-gray-800 border-t border-gray-300 pt-2">
                            <span>Total:</span>
                            <span id="total">$0.00</span>
                        </div>
                    </div>
                    
                    <!-- Método de pago -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                        <select id="payment-method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    
                    <!-- Botones -->
                    <div class="mt-6 space-y-2">
                        <button onclick="processSale()" id="btn-process-sale" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition"
                                disabled>
                            <i class="fas fa-check-circle mr-2"></i>Procesar Venta
                        </button>
                        <button onclick="clearCart()" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            <i class="fas fa-trash mr-2"></i>Limpiar Carrito
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
let cart = [];
const TAX_RATE = 0.16;

// Buscar productos
document.getElementById('product-search').addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const name = card.dataset.name.toLowerCase();
        if (name.includes(search)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

function addToCart(productId, productName, price) {
    const existingItem = cart.find(item => item.producto_id === productId);
    
    if (existingItem) {
        existingItem.cantidad++;
    } else {
        cart.push({
            producto_id: productId,
            nombre: productName,
            precio_unitario: price,
            cantidad: 1,
            subtotal: price
        });
    }
    
    updateCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

function updateQuantity(index, quantity) {
    if (quantity <= 0) {
        removeFromCart(index);
        return;
    }
    cart[index].cantidad = parseInt(quantity);
    cart[index].subtotal = cart[index].precio_unitario * cart[index].cantidad;
    updateCart();
}

function updateCart() {
    const cartItems = document.getElementById('cart-items');
    
    if (cart.length === 0) {
        cartItems.innerHTML = '<p class="text-gray-500 text-center py-4">No hay productos en el carrito</p>';
        document.getElementById('btn-process-sale').disabled = true;
    } else {
        let html = '';
        cart.forEach((item, index) => {
            html += `
                <div class="border-b border-gray-200 pb-3 mb-3">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <p class="font-medium text-sm text-gray-800">${item.nombre}</p>
                            <p class="text-sm text-green-600">$${item.precio_unitario.toFixed(2)}</p>
                        </div>
                        <button onclick="removeFromCart(${index})" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <button onclick="updateQuantity(${index}, ${item.cantidad - 1})" 
                                    class="bg-gray-200 hover:bg-gray-300 w-6 h-6 rounded flex items-center justify-center">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <input type="number" value="${item.cantidad}" min="1"
                                   onchange="updateQuantity(${index}, this.value)"
                                   class="w-12 text-center border border-gray-300 rounded">
                            <button onclick="updateQuantity(${index}, ${item.cantidad + 1})" 
                                    class="bg-gray-200 hover:bg-gray-300 w-6 h-6 rounded flex items-center justify-center">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                        <span class="font-semibold text-gray-800">$${item.subtotal.toFixed(2)}</span>
                    </div>
                </div>
            `;
        });
        cartItems.innerHTML = html;
        document.getElementById('btn-process-sale').disabled = false;
    }
    
    // Calcular totales
    const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
    const tax = subtotal * TAX_RATE;
    const total = subtotal + tax;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '$' + tax.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}

function clearCart() {
    if (cart.length === 0) return;
    
    if (confirm('¿Estás seguro de limpiar el carrito?')) {
        cart = [];
        updateCart();
    }
}

async function processSale() {
    if (cart.length === 0) {
        alert('El carrito está vacío');
        return;
    }
    
    const customerId = document.getElementById('customer-select').value;
    const paymentMethod = document.getElementById('payment-method').value;
    
    const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
    const tax = subtotal * TAX_RATE;
    const total = subtotal + tax;
    
    const saleData = {
        cliente_id: customerId || null,
        metodo_pago: paymentMethod,
        subtotal: subtotal,
        impuestos: tax,
        total: total,
        items: cart
    };
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/pos/process', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(saleData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Venta procesada exitosamente!\nFolio: ' + result.folio);
            cart = [];
            updateCart();
            
            // Abrir ticket en nueva ventana
            window.open('<?php echo BASE_URL; ?>/pos/receipt/' + result.sale_id, '_blank');
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error al procesar la venta: ' + error.message);
    }
}
</script>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>
