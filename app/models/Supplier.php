<?php
/**
 * Modelo de Artesano/Proveedor
 */
class Supplier {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT * FROM artesanos WHERE 1=1";
        $params = [];
        
        if (!empty($filters['especialidad'])) {
            $sql .= " AND especialidad LIKE ?";
            $params[] = '%' . $filters['especialidad'] . '%';
        }
        
        if (!empty($filters['estado'])) {
            $sql .= " AND estado = ?";
            $params[] = $filters['estado'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (nombre LIKE ? OR apellidos LIKE ? OR nombre_comercial LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (isset($filters['activo'])) {
            $sql .= " AND activo = ?";
            $params[] = $filters['activo'];
        }
        
        $sql .= " ORDER BY nombre_comercial, nombre ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM artesanos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO artesanos (nombre, apellidos, nombre_comercial, especialidad, telefono, email,
                                  direccion, ciudad, estado, region_origen, tecnicas, certificaciones,
                                  terminos_colaboracion, calificacion, activo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['nombre'],
            $data['apellidos'] ?? null,
            $data['nombre_comercial'] ?? null,
            $data['especialidad'] ?? null,
            $data['telefono'] ?? null,
            $data['email'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $data['estado'] ?? null,
            $data['region_origen'] ?? null,
            $data['tecnicas'] ?? null,
            $data['certificaciones'] ?? null,
            $data['terminos_colaboracion'] ?? null,
            $data['calificacion'] ?? 0,
            $data['activo'] ?? 1
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE artesanos SET
                nombre = ?, apellidos = ?, nombre_comercial = ?, especialidad = ?, telefono = ?,
                email = ?, direccion = ?, ciudad = ?, estado = ?, region_origen = ?, tecnicas = ?,
                certificaciones = ?, terminos_colaboracion = ?, calificacion = ?, activo = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['nombre'],
            $data['apellidos'] ?? null,
            $data['nombre_comercial'] ?? null,
            $data['especialidad'] ?? null,
            $data['telefono'] ?? null,
            $data['email'] ?? null,
            $data['direccion'] ?? null,
            $data['ciudad'] ?? null,
            $data['estado'] ?? null,
            $data['region_origen'] ?? null,
            $data['tecnicas'] ?? null,
            $data['certificaciones'] ?? null,
            $data['terminos_colaboracion'] ?? null,
            $data['calificacion'] ?? 0,
            $data['activo'] ?? 1,
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE artesanos SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getProductCount($supplier_id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM productos WHERE artesano_id = ? AND activo = 1");
        $stmt->execute([$supplier_id]);
        return $stmt->fetch()['total'];
    }
    
    public function getOrders($supplier_id, $status = null) {
        $sql = "SELECT oc.*, s.nombre as sucursal_nombre, u.nombre as usuario_nombre
                FROM ordenes_compra oc
                JOIN sucursales s ON oc.sucursal_id = s.id
                JOIN usuarios u ON oc.usuario_id = u.id
                WHERE oc.artesano_id = ?";
        
        $params = [$supplier_id];
        
        if ($status) {
            $sql .= " AND oc.estado = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY oc.fecha_pedido DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
