<?php
/**
 * Controlador de Usuarios
 */
require_once BASE_PATH . '/app/helpers/Controller.php';

class UsersController extends Controller {
    
    public function index() {
        $this->checkRole(['administrador']);
        
        $userModel = $this->model('User');
        
        $filters = [
            'rol' => $_GET['rol'] ?? null,
            'sucursal_id' => $_GET['sucursal'] ?? null
        ];
        
        $users = $userModel->getAll($filters);
        
        $branchModel = $this->model('Branch');
        $branches = $branchModel->getAll();
        
        $this->view('users/index', [
            'title' => 'Usuarios - Sistema de Inventario',
            'users' => $users,
            'branches' => $branches,
            'filters' => $filters
        ]);
    }
    
    public function view($id) {
        $this->checkRole(['administrador']);
        
        $userModel = $this->model('User');
        $user = $userModel->findById($id);
        
        if (!$user) {
            Session::setError('Usuario no encontrado');
            $this->redirect('users');
        }
        
        $this->view('users/view', [
            'title' => $user['nombre'] . ' - Detalle del Usuario',
            'user' => $user
        ]);
    }
    
    public function create() {
        $this->checkRole(['administrador']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = $this->model('User');
            
            // Validar email único
            if ($userModel->findByEmail($_POST['email'])) {
                Session::setError('El email ya está registrado');
                $this->redirect('users/create');
                return;
            }
            
            $data = [
                'nombre' => $_POST['nombre'],
                'apellidos' => $_POST['apellidos'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'telefono' => $_POST['telefono'] ?? null,
                'rol' => $_POST['rol'],
                'sucursal_id' => $_POST['sucursal_id'] ?? null,
                'activo' => 1
            ];
            
            if ($userModel->create($data)) {
                Session::setSuccess('Usuario creado exitosamente');
                $this->redirect('users');
            } else {
                Session::setError('Error al crear el usuario');
            }
        }
        
        $branchModel = $this->model('Branch');
        $branches = $branchModel->getAll();
        
        $this->view('users/create', [
            'title' => 'Crear Usuario - Sistema de Inventario',
            'branches' => $branches
        ]);
    }
    
    public function edit($id) {
        $this->checkRole(['administrador']);
        
        $userModel = $this->model('User');
        $user = $userModel->findById($id);
        
        if (!$user) {
            Session::setError('Usuario no encontrado');
            $this->redirect('users');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar email único (excepto el propio)
            $existingUser = $userModel->findByEmail($_POST['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                Session::setError('El email ya está registrado');
                $this->redirect('users/edit/' . $id);
                return;
            }
            
            $data = [
                'nombre' => $_POST['nombre'],
                'apellidos' => $_POST['apellidos'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'] ?? null,
                'rol' => $_POST['rol'],
                'sucursal_id' => $_POST['sucursal_id'] ?? null,
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];
            
            // Si se proporciona nueva contraseña
            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }
            
            if ($userModel->update($id, $data)) {
                Session::setSuccess('Usuario actualizado exitosamente');
                $this->redirect('users/view/' . $id);
            } else {
                Session::setError('Error al actualizar el usuario');
            }
        }
        
        $branchModel = $this->model('Branch');
        $branches = $branchModel->getAll();
        
        $this->view('users/edit', [
            'title' => 'Editar Usuario - Sistema de Inventario',
            'user' => $user,
            'branches' => $branches
        ]);
    }
    
    public function delete($id) {
        $this->checkRole(['administrador']);
        
        // No permitir eliminar el propio usuario
        if ($id == $_SESSION['user_id']) {
            Session::setError('No puedes eliminar tu propio usuario');
            $this->redirect('users');
            return;
        }
        
        $userModel = $this->model('User');
        
        if ($userModel->delete($id)) {
            Session::setSuccess('Usuario desactivado exitosamente');
        } else {
            Session::setError('Error al desactivar el usuario');
        }
        
        $this->redirect('users');
    }
    
    public function profile() {
        $this->checkAuth();
        
        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => $_POST['nombre'],
                'apellidos' => $_POST['apellidos'],
                'email' => $_POST['email'],
                'telefono' => $_POST['telefono'] ?? null,
                'rol' => $user['rol'], // No cambiar rol
                'sucursal_id' => $user['sucursal_id'], // No cambiar sucursal
                'activo' => 1
            ];
            
            // Si se proporciona nueva contraseña
            if (!empty($_POST['password'])) {
                if ($_POST['password'] !== $_POST['password_confirm']) {
                    Session::setError('Las contraseñas no coinciden');
                    $this->redirect('users/profile');
                    return;
                }
                $data['password'] = $_POST['password'];
            }
            
            if ($userModel->update($_SESSION['user_id'], $data)) {
                // Actualizar sesión
                $_SESSION['user_name'] = $data['nombre'] . ' ' . $data['apellidos'];
                $_SESSION['user_email'] = $data['email'];
                
                Session::setSuccess('Perfil actualizado exitosamente');
                $this->redirect('users/profile');
            } else {
                Session::setError('Error al actualizar el perfil');
            }
        }
        
        $this->view('users/profile', [
            'title' => 'Mi Perfil - Sistema de Inventario',
            'user' => $user
        ]);
    }
}
