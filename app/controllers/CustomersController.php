<?php
/**
 * Controlador de Clientes
 */
require_once BASE_PATH . '/app/helpers/Controller.php';

class CustomersController extends Controller {
    
    public function index() {
        $this->checkAuth();
        
        $customerModel = $this->model('Customer');
        
        $filters = [
            'search' => $_GET['search'] ?? null,
            'activo' => 1
        ];
        
        $customers = $customerModel->getAll($filters);
        
        $this->view('customers/index', [
            'title' => 'Clientes - Sistema de Inventario',
            'customers' => $customers,
            'filters' => $filters
        ]);
    }
    
    public function view($id) {
        $this->checkAuth();
        
        $customerModel = $this->model('Customer');
        $customer = $customerModel->findById($id);
        
        if (!$customer) {
            Session::setError('Cliente no encontrado');
            $this->redirect('customers');
        }
        
        $purchaseHistory = $customerModel->getPurchaseHistory($id);
        $stats = $customerModel->getStats($id);
        
        $this->view('customers/view', [
            'title' => $customer['nombre'] . ' - Detalle del Cliente',
            'customer' => $customer,
            'purchaseHistory' => $purchaseHistory,
            'stats' => $stats
        ]);
    }
    
    public function create() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerModel = $this->model('Customer');
            
            $data = [
                'nombre' => $_POST['nombre'],
                'apellidos' => $_POST['apellidos'] ?? null,
                'email' => $_POST['email'] ?? null,
                'telefono' => $_POST['telefono'] ?? null,
                'direccion' => $_POST['direccion'] ?? null,
                'ciudad' => $_POST['ciudad'] ?? null,
                'estado' => $_POST['estado'] ?? null,
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                'notas' => $_POST['notas'] ?? null,
                'activo' => 1
            ];
            
            $customer_id = $customerModel->create($data);
            
            if ($customer_id) {
                Session::setSuccess('Cliente creado exitosamente');
                $this->redirect('customers/view/' . $customer_id);
            } else {
                Session::setError('Error al crear el cliente');
            }
        }
        
        $this->view('customers/create', [
            'title' => 'Crear Cliente - Sistema de Inventario'
        ]);
    }
    
    public function edit($id) {
        $this->checkAuth();
        
        $customerModel = $this->model('Customer');
        $customer = $customerModel->findById($id);
        
        if (!$customer) {
            Session::setError('Cliente no encontrado');
            $this->redirect('customers');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => $_POST['nombre'],
                'apellidos' => $_POST['apellidos'] ?? null,
                'email' => $_POST['email'] ?? null,
                'telefono' => $_POST['telefono'] ?? null,
                'direccion' => $_POST['direccion'] ?? null,
                'ciudad' => $_POST['ciudad'] ?? null,
                'estado' => $_POST['estado'] ?? null,
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                'notas' => $_POST['notas'] ?? null,
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];
            
            if ($customerModel->update($id, $data)) {
                Session::setSuccess('Cliente actualizado exitosamente');
                $this->redirect('customers/view/' . $id);
            } else {
                Session::setError('Error al actualizar el cliente');
            }
        }
        
        $this->view('customers/edit', [
            'title' => 'Editar Cliente - Sistema de Inventario',
            'customer' => $customer
        ]);
    }
    
    public function search() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        $search = $_GET['q'] ?? '';
        
        if (strlen($search) < 2) {
            $this->json(['success' => false, 'message' => 'Búsqueda muy corta'], 400);
        }
        
        $customerModel = $this->model('Customer');
        $customers = $customerModel->getAll(['search' => $search, 'activo' => 1]);
        
        $this->json([
            'success' => true,
            'customers' => $customers
        ]);
    }
}
