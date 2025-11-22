<?php
/**
 * Controlador de Configuración
 */
require_once BASE_PATH . '/app/helpers/Controller.php';

class SettingsController extends Controller {
    
    public function index() {
        $this->checkRole(['administrador']);
        
        $settingModel = $this->model('Setting');
        
        $categories = $settingModel->getCategories();
        $settings = [];
        
        foreach ($categories as $category) {
            $settings[$category] = $settingModel->getByCategory($category);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'setting_') === 0) {
                    $setting_key = substr($key, 8); // Remover 'setting_'
                    $settingModel->update($setting_key, $value);
                }
            }
            
            Session::setSuccess('Configuración actualizada exitosamente');
            $this->redirect('settings');
        }
        
        $this->view('settings/index', [
            'title' => 'Configuración del Sistema',
            'settings' => $settings,
            'categories' => $categories
        ]);
    }
    
    public function general() {
        $this->checkRole(['administrador']);
        
        $settingModel = $this->model('Setting');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settingModel->set('sitio_nombre', $_POST['sitio_nombre']);
            $settingModel->set('email_principal', $_POST['email_principal']);
            $settingModel->set('telefono_contacto', $_POST['telefono_contacto']);
            $settingModel->set('horario_atencion', $_POST['horario_atencion']);
            $settingModel->set('moneda', $_POST['moneda']);
            $settingModel->set('impuesto_iva', $_POST['impuesto_iva']);
            
            Session::setSuccess('Configuración general actualizada');
            $this->redirect('settings/general');
        }
        
        $settings = $settingModel->getByCategory('general');
        
        $this->view('settings/general', [
            'title' => 'Configuración General',
            'settings' => $settings
        ]);
    }
    
    public function appearance() {
        $this->checkRole(['administrador']);
        
        $settingModel = $this->model('Setting');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settingModel->set('color_primario', $_POST['color_primario']);
            $settingModel->set('color_secundario', $_POST['color_secundario']);
            $settingModel->set('color_acento', $_POST['color_acento']);
            
            Session::setSuccess('Colores actualizados exitosamente');
            $this->redirect('settings/appearance');
        }
        
        $settings = $settingModel->getByCategory('apariencia');
        
        $this->view('settings/appearance', [
            'title' => 'Apariencia del Sistema',
            'settings' => $settings
        ]);
    }
    
    public function email() {
        $this->checkRole(['administrador']);
        
        $settingModel = $this->model('Setting');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settingModel->set('email_principal', $_POST['email_principal']);
            
            Session::setSuccess('Configuración de email actualizada');
            $this->redirect('settings/email');
        }
        
        $settings = $settingModel->getByCategory('email');
        
        $this->view('settings/email', [
            'title' => 'Configuración de Email',
            'settings' => $settings
        ]);
    }
    
    public function payments() {
        $this->checkRole(['administrador']);
        
        $settingModel = $this->model('Setting');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settingModel->set('paypal_client_id', $_POST['paypal_client_id']);
            $settingModel->set('paypal_modo', $_POST['paypal_modo']);
            
            Session::setSuccess('Configuración de pagos actualizada');
            $this->redirect('settings/payments');
        }
        
        $settings = $settingModel->getByCategory('pagos');
        
        $this->view('settings/payments', [
            'title' => 'Configuración de Pagos',
            'settings' => $settings
        ]);
    }
}
