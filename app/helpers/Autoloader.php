<?php
/**
 * Autoloader de clases
 */
class Autoloader {
    public static function register() {
        spl_autoload_register(function ($class) {
            // Directorios donde buscar las clases
            $directories = [
                BASE_PATH . '/app/controllers/',
                BASE_PATH . '/app/models/',
                BASE_PATH . '/app/helpers/'
            ];
            
            foreach ($directories as $directory) {
                $file = $directory . $class . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            return false;
        });
    }
}

Autoloader::register();
