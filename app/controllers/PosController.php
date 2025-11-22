<?php
/**
 * Controlador de Punto de Venta (POS)
 */
require_once BASE_PATH . '/app/helpers/Controller.php';

class PosController extends Controller {
    
    public function index() {
        $this->checkRole(['administrador', 'gerente_sucursal', 'vendedor']);
        
        // Verificar que el usuario tenga sucursal asignada
        if (empty($_SESSION['user_sucursal_id'])) {
            Session::setError('Debes tener una sucursal asignada para usar el POS');
            $this->redirect('dashboard');
        }
        
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        $customerModel = $this->model('Customer');
        
        $categories = $categoryModel->getAll();
        $products = $productModel->getAll(['activo' => 1], 100);
        $customers = $customerModel->getAll(['activo' => 1]);
        
        $this->view('pos/index', [
            'title' => 'Punto de Venta - Sistema de Inventario',
            'categories' => $categories,
            'products' => $products,
            'customers' => $customers
        ]);
    }
    
    public function process() {
        $this->checkRole(['administrador', 'gerente_sucursal', 'vendedor']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método no permitido'], 405);
        }
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate JSON data
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Datos JSON inválidos');
            }
            
            if (!is_array($data) || empty($data['items']) || !is_array($data['items'])) {
                throw new Exception('No hay productos en la venta');
            }
            
            // Validate required fields
            if (!isset($data['subtotal']) || !isset($data['total']) || !isset($data['metodo_pago'])) {
                throw new Exception('Datos incompletos');
            }
            
            $saleModel = $this->model('Sale');
            
            $saleData = [
                'sucursal_id' => $_SESSION['user_sucursal_id'],
                'usuario_id' => $_SESSION['user_id'],
                'cliente_id' => $data['cliente_id'] ?? null,
                'subtotal' => $data['subtotal'],
                'descuento' => $data['descuento'] ?? 0,
                'impuestos' => $data['impuestos'] ?? 0,
                'total' => $data['total'],
                'metodo_pago' => $data['metodo_pago'],
                'estado' => 'completada'
            ];
            
            $sale_id = $saleModel->create($saleData, $data['items']);
            
            $sale = $saleModel->findById($sale_id);
            
            $this->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'sale_id' => $sale_id,
                'folio' => $sale['folio']
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    public function sales() {
        $this->checkAuth();
        
        $saleModel = $this->model('Sale');
        $branchModel = $this->model('Branch');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'sucursal_id' => $_GET['sucursal'] ?? null,
            'estado' => $_GET['estado'] ?? null,
            'fecha_desde' => $_GET['fecha_desde'] ?? null,
            'fecha_hasta' => $_GET['fecha_hasta'] ?? null,
            'search' => $_GET['search'] ?? null
        ];
        
        // Si no es admin, solo ver ventas de su sucursal
        if ($_SESSION['user_role'] !== 'administrador' && !empty($_SESSION['user_sucursal_id'])) {
            $filters['sucursal_id'] = $_SESSION['user_sucursal_id'];
        }
        
        $sales = $saleModel->getAll($filters, $limit, $offset);
        $total_sales = $saleModel->count($filters);
        $total_pages = ceil($total_sales / $limit);
        
        $branches = $branchModel->getAll();
        
        $this->view('pos/sales', [
            'title' => 'Ventas - Sistema de Inventario',
            'sales' => $sales,
            'branches' => $branches,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'filters' => $filters
        ]);
    }
    
    public function view($id) {
        $this->checkAuth();
        
        $saleModel = $this->model('Sale');
        $sale = $saleModel->findById($id);
        
        if (!$sale) {
            Session::setError('Venta no encontrada');
            $this->redirect('pos/sales');
        }
        
        // Verificar permisos
        if ($_SESSION['user_role'] !== 'administrador' && 
            $sale['sucursal_id'] != $_SESSION['user_sucursal_id']) {
            Session::setError('No tienes permisos para ver esta venta');
            $this->redirect('pos/sales');
        }
        
        $details = $saleModel->getDetails($id);
        
        $this->view('pos/view', [
            'title' => 'Venta ' . $sale['folio'] . ' - Detalle',
            'sale' => $sale,
            'details' => $details
        ]);
    }
    
    public function cancel($id) {
        $this->checkRole(['administrador', 'gerente_sucursal']);
        
        try {
            $saleModel = $this->model('Sale');
            $saleModel->cancel($id, $_SESSION['user_id']);
            
            Session::setSuccess('Venta cancelada exitosamente');
        } catch (Exception $e) {
            Session::setError('Error al cancelar la venta: ' . $e->getMessage());
        }
        
        $this->redirect('pos/view/' . $id);
    }
    
    public function receipt($id) {
        $this->checkAuth();
        
        $saleModel = $this->model('Sale');
        $sale = $saleModel->findById($id);
        
        if (!$sale) {
            Session::setError('Venta no encontrada');
            $this->redirect('pos/sales');
        }
        
        $details = $saleModel->getDetails($id);
        
        $this->view('pos/receipt', [
            'title' => 'Ticket - Venta ' . $sale['folio'],
            'sale' => $sale,
            'details' => $details
        ]);
    }
}
