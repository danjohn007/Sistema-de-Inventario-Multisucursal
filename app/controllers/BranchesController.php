<?php
/**
 * Controlador de Sucursales
 */
require_once BASE_PATH . '/app/helpers/Controller.php';

class BranchesController extends Controller {
    
    public function index() {
        $this->checkRole(['administrador', 'gerente_sucursal']);
        
        $branchModel = $this->model('Branch');
        $branches = $branchModel->getAll(false);
        
        // Obtener estadísticas de inventario para cada sucursal
        foreach ($branches as &$branch) {
            $branch['stats'] = $branchModel->getInventoryStats($branch['id']);
        }
        
        $this->view('branches/index', [
            'title' => 'Sucursales - Sistema de Inventario',
            'branches' => $branches
        ]);
    }
    
    public function view($id) {
        $this->checkRole(['administrador', 'gerente_sucursal']);
        
        $branchModel = $this->model('Branch');
        $branch = $branchModel->findById($id);
        
        if (!$branch) {
            Session::setError('Sucursal no encontrada');
            $this->redirect('branches');
        }
        
        $stats = $branchModel->getInventoryStats($id);
        
        $this->view('branches/view', [
            'title' => $branch['nombre'] . ' - Detalle de Sucursal',
            'branch' => $branch,
            'stats' => $stats
        ]);
    }
    
    public function create() {
        $this->checkRole(['administrador']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $branchModel = $this->model('Branch');
            
            $data = [
                'nombre' => $_POST['nombre'],
                'codigo' => $_POST['codigo'],
                'direccion' => $_POST['direccion'],
                'ciudad' => $_POST['ciudad'],
                'estado' => $_POST['estado'],
                'codigo_postal' => $_POST['codigo_postal'],
                'telefono' => $_POST['telefono'] ?? null,
                'email' => $_POST['email'] ?? null,
                'responsable_id' => $_POST['responsable_id'] ?? null,
                'horario_apertura' => $_POST['horario_apertura'] ?? null,
                'horario_cierre' => $_POST['horario_cierre'] ?? null,
                'capacidad_m2' => $_POST['capacidad_m2'] ?? null,
                'capacidad_productos' => $_POST['capacidad_productos'] ?? null,
                'activo' => 1
            ];
            
            if ($branchModel->create($data)) {
                Session::setSuccess('Sucursal creada exitosamente');
                $this->redirect('branches');
            } else {
                Session::setError('Error al crear la sucursal');
            }
        }
        
        $userModel = $this->model('User');
        $managers = $userModel->getAll(['rol' => 'gerente_sucursal']);
        
        $this->view('branches/create', [
            'title' => 'Crear Sucursal - Sistema de Inventario',
            'managers' => $managers
        ]);
    }
    
    public function edit($id) {
        $this->checkRole(['administrador']);
        
        $branchModel = $this->model('Branch');
        $branch = $branchModel->findById($id);
        
        if (!$branch) {
            Session::setError('Sucursal no encontrada');
            $this->redirect('branches');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => $_POST['nombre'],
                'codigo' => $_POST['codigo'],
                'direccion' => $_POST['direccion'],
                'ciudad' => $_POST['ciudad'],
                'estado' => $_POST['estado'],
                'codigo_postal' => $_POST['codigo_postal'],
                'telefono' => $_POST['telefono'] ?? null,
                'email' => $_POST['email'] ?? null,
                'responsable_id' => $_POST['responsable_id'] ?? null,
                'horario_apertura' => $_POST['horario_apertura'] ?? null,
                'horario_cierre' => $_POST['horario_cierre'] ?? null,
                'capacidad_m2' => $_POST['capacidad_m2'] ?? null,
                'capacidad_productos' => $_POST['capacidad_productos'] ?? null,
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];
            
            if ($branchModel->update($id, $data)) {
                Session::setSuccess('Sucursal actualizada exitosamente');
                $this->redirect('branches/view/' . $id);
            } else {
                Session::setError('Error al actualizar la sucursal');
            }
        }
        
        $userModel = $this->model('User');
        $managers = $userModel->getAll(['rol' => 'gerente_sucursal']);
        
        $this->view('branches/edit', [
            'title' => 'Editar Sucursal - Sistema de Inventario',
            'branch' => $branch,
            'managers' => $managers
        ]);
    }
}
