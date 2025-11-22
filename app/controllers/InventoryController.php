<?php
/**
 * Controlador de Inventario
 */
require_once BASE_PATH . '/app/helpers/Controller.php';

class InventoryController extends Controller {
    
    public function index() {
        $this->checkAuth();
        
        $inventoryModel = $this->model('Inventory');
        $branchModel = $this->model('Branch');
        $categoryModel = $this->model('Category');
        
        $filters = [
            'sucursal_id' => $_GET['sucursal'] ?? $_SESSION['user_sucursal_id'],
            'categoria_id' => $_GET['categoria'] ?? null,
            'search' => $_GET['search'] ?? null,
            'stock_bajo' => isset($_GET['stock_bajo']) ? 1 : 0
        ];
        
        $inventory = $inventoryModel->getAll($filters);
        $branches = $branchModel->getAll();
        $categories = $categoryModel->getAll();
        
        $this->view('inventory/index', [
            'title' => 'Inventario - Sistema de Inventario',
            'inventory' => $inventory,
            'branches' => $branches,
            'categories' => $categories,
            'filters' => $filters
        ]);
    }
    
    public function movements() {
        $this->checkAuth();
        
        $inventoryModel = $this->model('Inventory');
        $branchModel = $this->model('Branch');
        
        $filters = [
            'sucursal_id' => $_GET['sucursal'] ?? $_SESSION['user_sucursal_id'],
            'tipo' => $_GET['tipo'] ?? null,
            'fecha_desde' => $_GET['fecha_desde'] ?? null,
            'fecha_hasta' => $_GET['fecha_hasta'] ?? null
        ];
        
        $movements = $inventoryModel->getMovements($filters, 100);
        $branches = $branchModel->getAll();
        
        $this->view('inventory/movements', [
            'title' => 'Movimientos de Inventario',
            'movements' => $movements,
            'branches' => $branches,
            'filters' => $filters
        ]);
    }
    
    public function adjust() {
        $this->checkRole(['administrador', 'gerente_sucursal', 'almacenista']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inventoryModel = $this->model('Inventory');
            
            try {
                $inventoryModel->adjustStock(
                    $_POST['producto_id'],
                    $_POST['sucursal_id'],
                    (int)$_POST['cantidad'],
                    $_POST['tipo'],
                    $_POST['motivo'],
                    $_SESSION['user_id'],
                    $_POST['notas'] ?? null,
                    $_POST['variante_id'] ?? null
                );
                
                $this->json(['success' => true, 'message' => 'Inventario ajustado correctamente']);
            } catch (Exception $e) {
                $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
        }
    }
    
    public function lowstock() {
        $this->checkAuth();
        
        $inventoryModel = $this->model('Inventory');
        $branch_id = $_GET['sucursal'] ?? $_SESSION['user_sucursal_id'];
        
        $lowStockItems = $inventoryModel->getLowStockItems($branch_id);
        
        $branchModel = $this->model('Branch');
        $branches = $branchModel->getAll();
        
        $this->view('inventory/lowstock', [
            'title' => 'Productos con Stock Bajo',
            'lowStockItems' => $lowStockItems,
            'branches' => $branches,
            'selected_branch' => $branch_id
        ]);
    }
}
