<?php
/**
 * Clase base para todos los controladores
 */
class Controller {
    
    protected function view($view, $data = []) {
        // Extraer datos como variables
        extract($data);
        
        // Cargar vista
        $view_file = BASE_PATH . '/app/views/' . $view . '.php';
        
        if (file_exists($view_file)) {
            require_once $view_file;
        } else {
            die("Vista no encontrada: " . $view);
        }
    }
    
    protected function model($model) {
        $model_file = BASE_PATH . '/app/models/' . $model . '.php';
        
        if (file_exists($model_file)) {
            require_once $model_file;
            return new $model();
        } else {
            die("Modelo no encontrado: " . $model);
        }
    }
    
    protected function redirect($url) {
        header('Location: ' . BASE_URL . '/' . $url);
        exit;
    }
    
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }
    }
    
    protected function checkRole($allowed_roles) {
        $this->checkAuth();
        
        if (!in_array($_SESSION['user_role'], $allowed_roles)) {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta sección';
            $this->redirect('dashboard');
        }
    }
}
