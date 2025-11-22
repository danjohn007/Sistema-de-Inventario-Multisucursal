<?php
/**
 * Controlador de Productos
 */
require_once BASE_PATH . '/app/helpers/Controller.php';

class ProductsController extends Controller {
    
    public function index() {
        $this->checkAuth();
        
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'categoria_id' => $_GET['categoria'] ?? null,
            'search' => $_GET['search'] ?? null,
            'activo' => 1
        ];
        
        $products = $productModel->getAll($filters, $limit, $offset);
        $total_products = $productModel->count($filters);
        $total_pages = ceil($total_products / $limit);
        
        $categories = $categoryModel->getAll();
        
        $this->view('products/index', [
            'title' => 'Productos - Sistema de Inventario',
            'products' => $products,
            'categories' => $categories,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'filters' => $filters
        ]);
    }
    
    public function view($id) {
        $this->checkAuth();
        
        $productModel = $this->model('Product');
        $product = $productModel->findById($id);
        
        if (!$product) {
            Session::setError('Producto no encontrado');
            $this->redirect('products');
        }
        
        $variants = $productModel->getVariants($id);
        $images = $productModel->getImages($id);
        
        $this->view('products/view', [
            'title' => $product['nombre'] . ' - Detalle del Producto',
            'product' => $product,
            'variants' => $variants,
            'images' => $images
        ]);
    }
    
    public function create() {
        $this->checkRole(['administrador', 'gerente_sucursal']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productModel = $this->model('Product');
            
            $data = [
                'codigo_barras' => $_POST['codigo_barras'] ?? null,
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? null,
                'categoria_id' => $_POST['categoria_id'],
                'artesano_id' => $_POST['artesano_id'] ?? null,
                'materiales' => $_POST['materiales'] ?? null,
                'tecnica_elaboracion' => $_POST['tecnica_elaboracion'] ?? null,
                'tiempo_produccion_dias' => $_POST['tiempo_produccion_dias'] ?? null,
                'region_origen' => $_POST['region_origen'] ?? null,
                'dimensiones' => $_POST['dimensiones'] ?? null,
                'peso_kg' => $_POST['peso_kg'] ?? null,
                'instrucciones_cuidado' => $_POST['instrucciones_cuidado'] ?? null,
                'precio_compra' => $_POST['precio_compra'] ?? 0,
                'precio_venta' => $_POST['precio_venta'],
                'es_edicion_limitada' => isset($_POST['es_edicion_limitada']) ? 1 : 0,
                'total_piezas_edicion' => $_POST['total_piezas_edicion'] ?? null,
                'requiere_certificado' => isset($_POST['requiere_certificado']) ? 1 : 0,
                'activo' => 1
            ];
            
            $product_id = $productModel->create($data);
            
            if ($product_id) {
                Session::setSuccess('Producto creado exitosamente');
                $this->redirect('products/view/' . $product_id);
            } else {
                Session::setError('Error al crear el producto');
                $this->redirect('products/create');
            }
        } else {
            $categoryModel = $this->model('Category');
            $categories = $categoryModel->getAll();
            
            // Modelo de artesanos si existe
            $artesanos = [];
            if (file_exists(BASE_PATH . '/app/models/Supplier.php')) {
                $supplierModel = $this->model('Supplier');
                $artesanos = $supplierModel->getAll();
            }
            
            $this->view('products/create', [
                'title' => 'Crear Producto - Sistema de Inventario',
                'categories' => $categories,
                'artesanos' => $artesanos
            ]);
        }
    }
    
    public function edit($id) {
        $this->checkRole(['administrador', 'gerente_sucursal']);
        
        $productModel = $this->model('Product');
        $product = $productModel->findById($id);
        
        if (!$product) {
            Session::setError('Producto no encontrado');
            $this->redirect('products');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'codigo_barras' => $_POST['codigo_barras'] ?? null,
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'] ?? null,
                'categoria_id' => $_POST['categoria_id'],
                'artesano_id' => $_POST['artesano_id'] ?? null,
                'materiales' => $_POST['materiales'] ?? null,
                'tecnica_elaboracion' => $_POST['tecnica_elaboracion'] ?? null,
                'tiempo_produccion_dias' => $_POST['tiempo_produccion_dias'] ?? null,
                'region_origen' => $_POST['region_origen'] ?? null,
                'dimensiones' => $_POST['dimensiones'] ?? null,
                'peso_kg' => $_POST['peso_kg'] ?? null,
                'instrucciones_cuidado' => $_POST['instrucciones_cuidado'] ?? null,
                'precio_compra' => $_POST['precio_compra'] ?? 0,
                'precio_venta' => $_POST['precio_venta'],
                'es_edicion_limitada' => isset($_POST['es_edicion_limitada']) ? 1 : 0,
                'total_piezas_edicion' => $_POST['total_piezas_edicion'] ?? null,
                'requiere_certificado' => isset($_POST['requiere_certificado']) ? 1 : 0,
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];
            
            if ($productModel->update($id, $data)) {
                Session::setSuccess('Producto actualizado exitosamente');
                $this->redirect('products/view/' . $id);
            } else {
                Session::setError('Error al actualizar el producto');
            }
        }
        
        $categoryModel = $this->model('Category');
        $categories = $categoryModel->getAll();
        
        $artesanos = [];
        if (file_exists(BASE_PATH . '/app/models/Supplier.php')) {
            $supplierModel = $this->model('Supplier');
            $artesanos = $supplierModel->getAll();
        }
        
        $this->view('products/edit', [
            'title' => 'Editar Producto - Sistema de Inventario',
            'product' => $product,
            'categories' => $categories,
            'artesanos' => $artesanos
        ]);
    }
    
    public function delete($id) {
        $this->checkRole(['administrador']);
        
        $productModel = $this->model('Product');
        
        if ($productModel->delete($id)) {
            Session::setSuccess('Producto eliminado exitosamente');
        } else {
            Session::setError('Error al eliminar el producto');
        }
        
        $this->redirect('products');
    }
    
    public function categories() {
        $this->checkAuth();
        
        $categoryModel = $this->model('Category');
        $categories = $categoryModel->getAll(false);
        
        // Obtener conteo de productos por categoría
        foreach ($categories as &$category) {
            $category['product_count'] = $categoryModel->getProductCount($category['id']);
        }
        
        $this->view('products/categories', [
            'title' => 'Categorías - Sistema de Inventario',
            'categories' => $categories
        ]);
    }
}
