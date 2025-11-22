<?php
/**
 * Modelo de Sucursal
 */
class Branch {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($active_only = true) {
        $sql = "SELECT s.*, u.nombre as responsable_nombre, u.apellidos as responsable_apellidos
                FROM sucursales s
                LEFT JOIN usuarios u ON s.responsable_id = u.id
                WHERE 1=1";
        
        if ($active_only) {
            $sql .= " AND s.activo = 1";
        }
        
        $sql .= " ORDER BY s.nombre ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, u.nombre as responsable_nombre, u.apellidos as responsable_apellidos
            FROM sucursales s
            LEFT JOIN usuarios u ON s.responsable_id = u.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO sucursales (nombre, codigo, direccion, ciudad, estado, codigo_postal,
                                   telefono, email, responsable_id, horario_apertura, horario_cierre,
                                   capacidad_m2, capacidad_productos, activo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['nombre'],
            $data['codigo'],
            $data['direccion'],
            $data['ciudad'],
            $data['estado'],
            $data['codigo_postal'],
            $data['telefono'] ?? null,
            $data['email'] ?? null,
            $data['responsable_id'] ?? null,
            $data['horario_apertura'] ?? null,
            $data['horario_cierre'] ?? null,
            $data['capacidad_m2'] ?? null,
            $data['capacidad_productos'] ?? null,
            $data['activo'] ?? 1
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE sucursales SET
                nombre = ?, codigo = ?, direccion = ?, ciudad = ?, estado = ?, codigo_postal = ?,
                telefono = ?, email = ?, responsable_id = ?, horario_apertura = ?, horario_cierre = ?,
                capacidad_m2 = ?, capacidad_productos = ?, activo = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['nombre'],
            $data['codigo'],
            $data['direccion'],
            $data['ciudad'],
            $data['estado'],
            $data['codigo_postal'],
            $data['telefono'] ?? null,
            $data['email'] ?? null,
            $data['responsable_id'] ?? null,
            $data['horario_apertura'] ?? null,
            $data['horario_cierre'] ?? null,
            $data['capacidad_m2'] ?? null,
            $data['capacidad_productos'] ?? null,
            $data['activo'] ?? 1,
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE sucursales SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getInventoryStats($branch_id) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(DISTINCT i.producto_id) as total_productos,
                SUM(i.cantidad_actual) as total_items,
                SUM(i.cantidad_actual * p.precio_venta) as valor_inventario,
                COUNT(CASE WHEN i.cantidad_actual <= i.cantidad_minima THEN 1 END) as productos_stock_bajo
            FROM inventario i
            JOIN productos p ON i.producto_id = p.id
            WHERE i.sucursal_id = ?
        ");
        $stmt->execute([$branch_id]);
        return $stmt->fetch();
    }
}
