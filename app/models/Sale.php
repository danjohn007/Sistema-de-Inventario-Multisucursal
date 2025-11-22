<?php
/**
 * Modelo de Venta
 */
class Sale {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT v.*, s.nombre as sucursal_nombre, u.nombre as usuario_nombre,
                       c.nombre as cliente_nombre, c.apellidos as cliente_apellidos
                FROM ventas v
                JOIN sucursales s ON v.sucursal_id = s.id
                JOIN usuarios u ON v.usuario_id = u.id
                LEFT JOIN clientes c ON v.cliente_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['sucursal_id'])) {
            $sql .= " AND v.sucursal_id = ?";
            $params[] = $filters['sucursal_id'];
        }
        
        if (!empty($filters['estado'])) {
            $sql .= " AND v.estado = ?";
            $params[] = $filters['estado'];
        }
        
        if (!empty($filters['fecha_desde'])) {
            $sql .= " AND DATE(v.fecha_venta) >= ?";
            $params[] = $filters['fecha_desde'];
        }
        
        if (!empty($filters['fecha_hasta'])) {
            $sql .= " AND DATE(v.fecha_venta) <= ?";
            $params[] = $filters['fecha_hasta'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (v.folio LIKE ? OR c.nombre LIKE ? OR c.apellidos LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY v.fecha_venta DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT v.*, s.nombre as sucursal_nombre, u.nombre as usuario_nombre,
                   c.nombre as cliente_nombre, c.apellidos as cliente_apellidos
            FROM ventas v
            JOIN sucursales s ON v.sucursal_id = s.id
            JOIN usuarios u ON v.usuario_id = u.id
            LEFT JOIN clientes c ON v.cliente_id = c.id
            WHERE v.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function findByFolio($folio) {
        $stmt = $this->db->prepare("SELECT * FROM ventas WHERE folio = ?");
        $stmt->execute([$folio]);
        return $stmt->fetch();
    }
    
    public function getDetails($sale_id) {
        $stmt = $this->db->prepare("
            SELECT vd.*, p.nombre as producto_nombre, p.codigo_barras,
                   v.nombre as variante_nombre
            FROM venta_detalle vd
            JOIN productos p ON vd.producto_id = p.id
            LEFT JOIN producto_variantes v ON vd.variante_id = v.id
            WHERE vd.venta_id = ?
        ");
        $stmt->execute([$sale_id]);
        return $stmt->fetchAll();
    }
    
    public function create($data, $items) {
        $this->db->beginTransaction();
        
        try {
            // Generar folio único
            $folio = $this->generateFolio();
            
            // Crear venta
            $stmt = $this->db->prepare("
                INSERT INTO ventas (folio, sucursal_id, usuario_id, cliente_id, subtotal, descuento,
                                   impuestos, total, metodo_pago, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $folio,
                $data['sucursal_id'],
                $data['usuario_id'],
                $data['cliente_id'] ?? null,
                $data['subtotal'],
                $data['descuento'] ?? 0,
                $data['impuestos'] ?? 0,
                $data['total'],
                $data['metodo_pago'],
                $data['estado'] ?? 'completada'
            ]);
            
            $sale_id = $this->db->lastInsertId();
            
            // Insertar items y actualizar inventario
            $inventoryModel = new Inventory();
            
            foreach ($items as $item) {
                // Insertar detalle de venta
                $stmt = $this->db->prepare("
                    INSERT INTO venta_detalle (venta_id, producto_id, variante_id, cantidad,
                                              precio_unitario, descuento, subtotal)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $sale_id,
                    $item['producto_id'],
                    $item['variante_id'] ?? null,
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $item['descuento'] ?? 0,
                    $item['subtotal']
                ]);
                
                // Actualizar inventario
                $inventoryModel->adjustStock(
                    $item['producto_id'],
                    $data['sucursal_id'],
                    $item['cantidad'],
                    'salida',
                    'venta',
                    $data['usuario_id'],
                    'Venta ' . $folio,
                    $item['variante_id'] ?? null
                );
            }
            
            // Agregar puntos de fidelidad al cliente
            if (!empty($data['cliente_id'])) {
                $customerModel = new Customer();
                $points = floor($data['total'] / 100); // 1 punto por cada $100
                $customerModel->addPoints($data['cliente_id'], $points);
            }
            
            $this->db->commit();
            return $sale_id;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function cancel($sale_id, $user_id) {
        $this->db->beginTransaction();
        
        try {
            // Obtener venta
            $sale = $this->findById($sale_id);
            if (!$sale || $sale['estado'] === 'cancelada') {
                throw new Exception("Venta no válida para cancelar");
            }
            
            // Obtener detalles
            $details = $this->getDetails($sale_id);
            
            // Devolver inventario
            $inventoryModel = new Inventory();
            
            foreach ($details as $detail) {
                $inventoryModel->adjustStock(
                    $detail['producto_id'],
                    $sale['sucursal_id'],
                    $detail['cantidad'],
                    'entrada',
                    'devolucion',
                    $user_id,
                    'Cancelación de venta ' . $sale['folio'],
                    $detail['variante_id'] ?? null
                );
            }
            
            // Actualizar estado de venta
            $stmt = $this->db->prepare("UPDATE ventas SET estado = 'cancelada' WHERE id = ?");
            $stmt->execute([$sale_id]);
            
            // Restar puntos de fidelidad
            if (!empty($sale['cliente_id'])) {
                $customerModel = new Customer();
                $points = floor($sale['total'] / 100);
                $customerModel->addPoints($sale['cliente_id'], -$points);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    private function generateFolio() {
        $date = date('Ymd');
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM ventas WHERE DATE(fecha_venta) = CURDATE()");
        $stmt->execute();
        $count = $stmt->fetch()['total'] + 1;
        
        return 'VTA-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
    
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM ventas v WHERE 1=1";
        $params = [];
        
        if (!empty($filters['sucursal_id'])) {
            $sql .= " AND v.sucursal_id = ?";
            $params[] = $filters['sucursal_id'];
        }
        
        if (!empty($filters['estado'])) {
            $sql .= " AND v.estado = ?";
            $params[] = $filters['estado'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }
}
