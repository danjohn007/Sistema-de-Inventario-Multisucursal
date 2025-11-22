<?php
/**
 * Modelo de Categoría
 */
class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($active_only = true) {
        $sql = "SELECT * FROM categorias WHERE 1=1";
        
        if ($active_only) {
            $sql .= " AND activo = 1";
        }
        
        $sql .= " ORDER BY nombre ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO categorias (nombre, descripcion, icono, activo)
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['icono'] ?? null,
            $data['activo'] ?? 1
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE categorias SET nombre = ?, descripcion = ?, icono = ?, activo = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['icono'] ?? null,
            $data['activo'] ?? 1,
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE categorias SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getProductCount($category_id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM productos WHERE categoria_id = ? AND activo = 1");
        $stmt->execute([$category_id]);
        return $stmt->fetch()['total'];
    }
}
