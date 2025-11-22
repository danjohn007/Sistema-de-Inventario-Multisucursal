<?php
/**
 * Controlador de Autenticación
 */
require_once BASE_PATH . '/app/helpers/Controller.php';

class AuthController extends Controller {
    
    public function login() {
        // Si ya está autenticado, redirigir
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                Session::setError('Por favor ingresa email y contraseña');
                $this->view('auth/login');
                return;
            }
            
            $userModel = $this->model('User');
            $user = $userModel->authenticate($email, $password);
            
            if ($user) {
                // Crear sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellidos'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['rol'];
                $_SESSION['user_sucursal_id'] = $user['sucursal_id'];
                $_SESSION['user_sucursal_nombre'] = $user['sucursal_nombre'];
                
                Session::setSuccess('Bienvenido ' . $_SESSION['user_name']);
                $this->redirect('dashboard');
            } else {
                Session::setError('Email o contraseña incorrectos');
                $this->view('auth/login');
            }
        } else {
            $this->view('auth/login');
        }
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('auth/login');
    }
}
