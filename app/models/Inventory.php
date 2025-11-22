<?php
/**
 * Modelo de Inventario
 */
class Inventory {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT i.*, p.nombre as producto_nombre, p.codigo_barras, p.precio_venta,
                       s.nombre as sucursal_nombre, s.codigo as sucursal_codigo,
                       v.nombre as variante_nombre, c.nombre as categoria_nombre
                FROM inventario i
                JOIN productos p ON i.producto_id = p.id
                JOIN sucursales s ON i.sucursal_id = s.id
                LEFT JOIN producto_variantes v ON i.variante_id = v.id
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['sucursal_id'])) {
            $sql .= " AND i.sucursal_id = ?";
            $params[] = $filters['sucursal_id'];
        }
        
        if (!empty($filters['producto_id'])) {
            $sql .= " AND i.producto_id = ?";
            $params[] = $filters['producto_id'];
        }
        
        if (!empty($filters['categoria_id'])) {
            $sql .= " AND p.categoria_id = ?";
            $params[] = $filters['categoria_id'];
        }
        
        if (!empty($filters['stock_bajo'])) {
            $sql .= " AND i.cantidad_actual <= i.cantidad_minima";
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.nombre LIKE ? OR p.codigo_barras LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY s.nombre, p.nombre ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function findByProductAndBranch($product_id, $branch_id, $variant_id = null) {
        $sql = "SELECT i.*, p.nombre as producto_nombre, s.nombre as sucursal_nombre
                FROM inventario i
                JOIN productos p ON i.producto_id = p.id
                JOIN sucursales s ON i.sucursal_id = s.id
                WHERE i.producto_id = ? AND i.sucursal_id = ?";
        
        $params = [$product_id, $branch_id];
        
        if ($variant_id !== null) {
            $sql .= " AND i.variante_id = ?";
            $params[] = $variant_id;
        } else {
            $sql .= " AND i.variante_id IS NULL";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    public function createOrUpdate($data) {
        // Verificar si ya existe
        $existing = $this->findByProductAndBranch(
            $data['producto_id'],
            $data['sucursal_id'],
            $data['variante_id'] ?? null
        );
        
        if ($existing) {
            // Actualizar
            $stmt = $this->db->prepare("
                UPDATE inventario SET
                    cantidad_actual = ?, cantidad_minima = ?, cantidad_maxima = ?,
                    ubicacion_fisica = ?, numero_pieza_unica = ?
                WHERE id = ?
            ");
            
            return $stmt->execute([
                $data['cantidad_actual'],
                $data['cantidad_minima'] ?? 5,
                $data['cantidad_maxima'] ?? 100,
                $data['ubicacion_fisica'] ?? null,
                $data['numero_pieza_unica'] ?? null,
                $existing['id']
            ]);
        } else {
            // Crear
            $stmt = $this->db->prepare("
                INSERT INTO inventario (producto_id, variante_id, sucursal_id, cantidad_actual,
                                       cantidad_minima, cantidad_maxima, ubicacion_fisica, numero_pieza_unica)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $data['producto_id'],
                $data['variante_id'] ?? null,
                $data['sucursal_id'],
                $data['cantidad_actual'],
                $data['cantidad_minima'] ?? 5,
                $data['cantidad_maxima'] ?? 100,
                $data['ubicacion_fisica'] ?? null,
                $data['numero_pieza_unica'] ?? null
            ]);
        }
    }
    
    public function adjustStock($product_id, $branch_id, $quantity, $type, $reason, $user_id, $notes = null, $variant_id = null) {
        $this->db->beginTransaction();
        
        try {
            // Obtener inventario actual
            $inventory = $this->findByProductAndBranch($product_id, $branch_id, $variant_id);
            
            if (!$inventory) {
                throw new Exception("Inventario no encontrado");
            }
            
            $cantidad_anterior = $inventory['cantidad_actual'];
            $cantidad_nueva = $cantidad_anterior;
            
            // Calcular nueva cantidad según tipo de movimiento
            if ($type === 'entrada') {
                $cantidad_nueva = $cantidad_anterior + $quantity;
            } elseif ($type === 'salida') {
                $cantidad_nueva = $cantidad_anterior - $quantity;
                if ($cantidad_nueva < 0) {
                    throw new Exception("Stock insuficiente");
                }
            } elseif ($type === 'ajuste') {
                $cantidad_nueva = $quantity;
            }
            
            // Actualizar inventario
            $stmt = $this->db->prepare("UPDATE inventario SET cantidad_actual = ? WHERE id = ?");
            $stmt->execute([$cantidad_nueva, $inventory['id']]);
            
            // Registrar movimiento
            $stmt = $this->db->prepare("
                INSERT INTO movimientos_inventario (tipo, motivo, producto_id, variante_id, sucursal_id,
                                                    cantidad, cantidad_anterior, cantidad_nueva, usuario_id, notas)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $type,
                $reason,
                $product_id,
                $variant_id,
                $branch_id,
                $quantity,
                $cantidad_anterior,
                $cantidad_nueva,
                $user_id,
                $notes
            ]);
            
            // Verificar si stock está bajo y crear notificación
            if ($cantidad_nueva <= $inventory['cantidad_minima']) {
                $this->createLowStockNotification($product_id, $branch_id, $cantidad_nueva);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function getMovements($filters = [], $limit = 50) {
        $sql = "SELECT m.*, p.nombre as producto_nombre, s.nombre as sucursal_nombre,
                       u.nombre as usuario_nombre, v.nombre as variante_nombre
                FROM movimientos_inventario m
                JOIN productos p ON m.producto_id = p.id
                JOIN sucursales s ON m.sucursal_id = s.id
                JOIN usuarios u ON m.usuario_id = u.id
                LEFT JOIN producto_variantes v ON m.variante_id = v.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['sucursal_id'])) {
            $sql .= " AND m.sucursal_id = ?";
            $params[] = $filters['sucursal_id'];
        }
        
        if (!empty($filters['producto_id'])) {
            $sql .= " AND m.producto_id = ?";
            $params[] = $filters['producto_id'];
        }
        
        if (!empty($filters['tipo'])) {
            $sql .= " AND m.tipo = ?";
            $params[] = $filters['tipo'];
        }
        
        if (!empty($filters['fecha_desde'])) {
            $sql .= " AND DATE(m.fecha_movimiento) >= ?";
            $params[] = $filters['fecha_desde'];
        }
        
        if (!empty($filters['fecha_hasta'])) {
            $sql .= " AND DATE(m.fecha_movimiento) <= ?";
            $params[] = $filters['fecha_hasta'];
        }
        
        $sql .= " ORDER BY m.fecha_movimiento DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    private function createLowStockNotification($product_id, $branch_id, $cantidad) {
        $stmt = $this->db->prepare("
            SELECT p.nombre, s.nombre as sucursal
            FROM productos p, sucursales s
            WHERE p.id = ? AND s.id = ?
        ");
        $stmt->execute([$product_id, $branch_id]);
        $data = $stmt->fetch();
        
        // Obtener usuarios de la sucursal y administradores
        $stmt = $this->db->prepare("
            SELECT id FROM usuarios 
            WHERE (sucursal_id = ? OR rol = 'administrador') AND activo = 1
        ");
        $stmt->execute([$branch_id]);
        $users = $stmt->fetchAll();
        
        foreach ($users as $user) {
            $stmt = $this->db->prepare("
                INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, referencia_id)
                VALUES (?, 'stock_bajo', ?, ?, ?)
            ");
            
            $stmt->execute([
                $user['id'],
                'Stock Bajo: ' . $data['nombre'],
                'El producto ' . $data['nombre'] . ' en ' . $data['sucursal'] . ' tiene solo ' . $cantidad . ' unidades.',
                $product_id
            ]);
        }
    }
    
    public function getLowStockItems($branch_id = null) {
        $sql = "SELECT i.*, p.nombre as producto_nombre, s.nombre as sucursal_nombre,
                       p.precio_venta, c.nombre as categoria_nombre
                FROM inventario i
                JOIN productos p ON i.producto_id = p.id
                JOIN sucursales s ON i.sucursal_id = s.id
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE i.cantidad_actual <= i.cantidad_minima AND p.activo = 1";
        
        $params = [];
        
        if ($branch_id) {
            $sql .= " AND i.sucursal_id = ?";
            $params[] = $branch_id;
        }
        
        $sql .= " ORDER BY (i.cantidad_actual / NULLIF(i.cantidad_minima, 0)) ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
