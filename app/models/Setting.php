<?php
/**
 * Modelo de Configuración
 */
class Setting {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM configuracion ORDER BY categoria, clave");
        return $stmt->fetchAll();
    }
    
    public function getByCategory($category) {
        $stmt = $this->db->prepare("SELECT * FROM configuracion WHERE categoria = ? ORDER BY clave");
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }
    
    public function get($key, $default = null) {
        $stmt = $this->db->prepare("SELECT valor FROM configuracion WHERE clave = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        return $result ? $result['valor'] : $default;
    }
    
    public function set($key, $value) {
        $stmt = $this->db->prepare("
            INSERT INTO configuracion (clave, valor, fecha_actualizacion)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE valor = ?, fecha_actualizacion = NOW()
        ");
        
        return $stmt->execute([$key, $value, $value]);
    }
    
    public function update($key, $value) {
        $stmt = $this->db->prepare("UPDATE configuracion SET valor = ? WHERE clave = ?");
        return $stmt->execute([$value, $key]);
    }
    
    public function getCategories() {
        $stmt = $this->db->query("SELECT DISTINCT categoria FROM configuracion ORDER BY categoria");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
