<?php
/**
 * Modelo de Cliente
 */
class Customer {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT * FROM clientes WHERE 1=1";
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (nombre LIKE ? OR apellidos LIKE ? OR email LIKE ? OR telefono LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (isset($filters['activo'])) {
            $sql .= " AND activo = ?";
            $params[] = $filters['activo'];
        }
        
        $sql .= " ORDER BY nombre, apellidos ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function findByPhone($phone) {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE telefono = ?");
        $stmt->execute([$phone]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO clientes (nombre, apellidos, email, telefono, direccion, ciudad, estado,
                                 fecha_nacimiento, puntos_fidelidad, notas, activo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['nombre'],
            $data['apellidos'] ?? null,
            $data['email'] ?? null,
            $data['telefono'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $data['estado'] ?? null,
            $data['fecha_nacimiento'] ?? null,
            $data['puntos_fidelidad'] ?? 0,
            $data['notas'] ?? null,
            $data['activo'] ?? 1
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE clientes SET
                nombre = ?, apellidos = ?, email = ?, telefono = ?, direccion = ?,
                ciudad = ?, estado = ?, fecha_nacimiento = ?, notas = ?, activo = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['nombre'],
            $data['apellidos'] ?? null,
            $data['email'] ?? null,
            $data['telefono'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $data['estado'] ?? null,
            $data['fecha_nacimiento'] ?? null,
            $data['notas'] ?? null,
            $data['activo'] ?? 1,
            $id
        ]);
    }
    
    public function addPoints($customer_id, $points) {
        $stmt = $this->db->prepare("
            UPDATE clientes SET puntos_fidelidad = puntos_fidelidad + ? WHERE id = ?
        ");
        return $stmt->execute([$points, $customer_id]);
    }
    
    public function getPurchaseHistory($customer_id, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT v.*, s.nombre as sucursal_nombre, u.nombre as vendedor_nombre
            FROM ventas v
            JOIN sucursales s ON v.sucursal_id = s.id
            JOIN usuarios u ON v.usuario_id = u.id
            WHERE v.cliente_id = ?
            ORDER BY v.fecha_venta DESC
            LIMIT ?
        ");
        $stmt->execute([$customer_id, $limit]);
        return $stmt->fetchAll();
    }
    
    public function getStats($customer_id) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_compras,
                COALESCE(SUM(total), 0) as total_gastado,
                COALESCE(AVG(total), 0) as promedio_compra
            FROM ventas
            WHERE cliente_id = ? AND estado = 'completada'
        ");
        $stmt->execute([$customer_id]);
        return $stmt->fetch();
    }
}
