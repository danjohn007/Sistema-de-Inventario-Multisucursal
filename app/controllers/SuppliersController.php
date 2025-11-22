<?php
/**
 * Controlador de Artesanos/Proveedores
 */
require_once BASE_PATH . '/app/helpers/Controller.php';

class SuppliersController extends Controller {
    
    public function index() {
        $this->checkAuth();
        
        $supplierModel = $this->model('Supplier');
        
        $filters = [
            'search' => $_GET['search'] ?? null,
            'estado' => $_GET['estado'] ?? null,
            'activo' => 1
        ];
        
        $suppliers = $supplierModel->getAll($filters);
        
        // Obtener conteo de productos por artesano
        foreach ($suppliers as &$supplier) {
            $supplier['product_count'] = $supplierModel->getProductCount($supplier['id']);
        }
        
        $this->view('suppliers/index', [
            'title' => 'Artesanos - Sistema de Inventario',
            'suppliers' => $suppliers,
            'filters' => $filters
        ]);
    }
    
    public function view($id) {
        $this->checkAuth();
        
        $supplierModel = $this->model('Supplier');
        $supplier = $supplierModel->findById($id);
        
        if (!$supplier) {
            Session::setError('Artesano no encontrado');
            $this->redirect('suppliers');
        }
        
        $product_count = $supplierModel->getProductCount($id);
        $orders = $supplierModel->getOrders($id);
        
        $this->view('suppliers/view', [
            'title' => $supplier['nombre_comercial'] . ' - Detalle del Artesano',
            'supplier' => $supplier,
            'product_count' => $product_count,
            'orders' => $orders
        ]);
    }
    
    public function create() {
        $this->checkRole(['administrador', 'gerente_sucursal']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $supplierModel = $this->model('Supplier');
            
            $data = [
                'nombre' => $_POST['nombre'],
                'apellidos' => $_POST['apellidos'] ?? null,
                'nombre_comercial' => $_POST['nombre_comercial'] ?? null,
                'especialidad' => $_POST['especialidad'] ?? null,
                'telefono' => $_POST['telefono'] ?? null,
                'email' => $_POST['email'] ?? null,
                'direccion' => $_POST['direccion'] ?? null,
                'ciudad' => $_POST['ciudad'] ?? null,
                'estado' => $_POST['estado'] ?? null,
                'region_origen' => $_POST['region_origen'] ?? null,
                'tecnicas' => $_POST['tecnicas'] ?? null,
                'certificaciones' => $_POST['certificaciones'] ?? null,
                'terminos_colaboracion' => $_POST['terminos_colaboracion'] ?? null,
                'calificacion' => $_POST['calificacion'] ?? 0,
                'activo' => 1
            ];
            
            if ($supplierModel->create($data)) {
                Session::setSuccess('Artesano creado exitosamente');
                $this->redirect('suppliers');
            } else {
                Session::setError('Error al crear el artesano');
            }
        }
        
        $this->view('suppliers/create', [
            'title' => 'Crear Artesano - Sistema de Inventario'
        ]);
    }
    
    public function edit($id) {
        $this->checkRole(['administrador', 'gerente_sucursal']);
        
        $supplierModel = $this->model('Supplier');
        $supplier = $supplierModel->findById($id);
        
        if (!$supplier) {
            Session::setError('Artesano no encontrado');
            $this->redirect('suppliers');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => $_POST['nombre'],
                'apellidos' => $_POST['apellidos'] ?? null,
                'nombre_comercial' => $_POST['nombre_comercial'] ?? null,
                'especialidad' => $_POST['especialidad'] ?? null,
                'telefono' => $_POST['telefono'] ?? null,
                'email' => $_POST['email'] ?? null,
                'direccion' => $_POST['direccion'] ?? null,
                'ciudad' => $_POST['ciudad'] ?? null,
                'estado' => $_POST['estado'] ?? null,
                'region_origen' => $_POST['region_origen'] ?? null,
                'tecnicas' => $_POST['tecnicas'] ?? null,
                'certificaciones' => $_POST['certificaciones'] ?? null,
                'terminos_colaboracion' => $_POST['terminos_colaboracion'] ?? null,
                'calificacion' => $_POST['calificacion'] ?? 0,
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];
            
            if ($supplierModel->update($id, $data)) {
                Session::setSuccess('Artesano actualizado exitosamente');
                $this->redirect('suppliers/view/' . $id);
            } else {
                Session::setError('Error al actualizar el artesano');
            }
        }
        
        $this->view('suppliers/edit', [
            'title' => 'Editar Artesano - Sistema de Inventario',
            'supplier' => $supplier
        ]);
    }
}
