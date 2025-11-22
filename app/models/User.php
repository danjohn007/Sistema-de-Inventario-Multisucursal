<?php
/**
 * Modelo de Usuario
 */
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function authenticate($email, $password) {
        $stmt = $this->db->prepare("
            SELECT u.*, s.nombre as sucursal_nombre 
            FROM usuarios u 
            LEFT JOIN sucursales s ON u.sucursal_id = s.id 
            WHERE u.email = ? AND u.activo = 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Actualizar último acceso
            $this->updateLastAccess($user['id']);
            return $user;
        }
        
        return false;
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT u.*, s.nombre as sucursal_nombre 
            FROM usuarios u 
            LEFT JOIN sucursales s ON u.sucursal_id = s.id 
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT u.*, s.nombre as sucursal_nombre 
                FROM usuarios u 
                LEFT JOIN sucursales s ON u.sucursal_id = s.id 
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['rol'])) {
            $sql .= " AND u.rol = ?";
            $params[] = $filters['rol'];
        }
        
        if (!empty($filters['sucursal_id'])) {
            $sql .= " AND u.sucursal_id = ?";
            $params[] = $filters['sucursal_id'];
        }
        
        if (isset($filters['activo'])) {
            $sql .= " AND u.activo = ?";
            $params[] = $filters['activo'];
        }
        
        $sql .= " ORDER BY u.nombre, u.apellidos";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nombre, apellidos, email, password, telefono, rol, sucursal_id, activo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $stmt->execute([
            $data['nombre'],
            $data['apellidos'],
            $data['email'],
            $password_hash,
            $data['telefono'] ?? null,
            $data['rol'],
            $data['sucursal_id'] ?? null,
            $data['activo'] ?? 1
        ]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE usuarios SET 
                nombre = ?, apellidos = ?, email = ?, telefono = ?, 
                rol = ?, sucursal_id = ?, activo = ?";
        
        $params = [
            $data['nombre'],
            $data['apellidos'],
            $data['email'],
            $data['telefono'] ?? null,
            $data['rol'],
            $data['sucursal_id'] ?? null,
            $data['activo'] ?? 1
        ];
        
        // Si se proporciona nueva contraseña
        if (!empty($data['password'])) {
            $sql .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function delete($id) {
        // Soft delete
        $stmt = $this->db->prepare("UPDATE usuarios SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function updateLastAccess($id) {
        $stmt = $this->db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function changePassword($id, $new_password) {
        $stmt = $this->db->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        return $stmt->execute([$password_hash, $id]);
    }
}
