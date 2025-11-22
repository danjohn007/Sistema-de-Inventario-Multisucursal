<?php
/**
 * Helper para manejo de sesiones y mensajes flash
 */
class Session {
    
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    public static function has($key) {
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    public static function destroy() {
        session_destroy();
    }
    
    // Mensajes flash
    public static function flash($key, $message = null) {
        if ($message === null) {
            // Obtener y eliminar
            $value = self::get('flash_' . $key);
            self::remove('flash_' . $key);
            return $value;
        } else {
            // Establecer
            self::set('flash_' . $key, $message);
        }
    }
    
    public static function setSuccess($message) {
        self::flash('success', $message);
    }
    
    public static function setError($message) {
        self::flash('error', $message);
    }
    
    public static function setWarning($message) {
        self::flash('warning', $message);
    }
    
    public static function setInfo($message) {
        self::flash('info', $message);
    }
}
