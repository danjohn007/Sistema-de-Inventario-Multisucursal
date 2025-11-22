<?php
/**
 * Modelo de Producto
 */
class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre, a.nombre_comercial as artesano_nombre
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN artesanos a ON p.artesano_id = a.id
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['categoria_id'])) {
            $sql .= " AND p.categoria_id = ?";
            $params[] = $filters['categoria_id'];
        }
        
        if (!empty($filters['artesano_id'])) {
            $sql .= " AND p.artesano_id = ?";
            $params[] = $filters['artesano_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ? OR p.codigo_barras LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (isset($filters['activo'])) {
            $sql .= " AND p.activo = ?";
            $params[] = $filters['activo'];
        }
        
        $sql .= " ORDER BY p.nombre ASC";
        
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
            SELECT p.*, c.nombre as categoria_nombre, a.nombre_comercial as artesano_nombre
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            LEFT JOIN artesanos a ON p.artesano_id = a.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO productos (codigo_barras, nombre, descripcion, categoria_id, artesano_id,
                                  materiales, tecnica_elaboracion, tiempo_produccion_dias, region_origen,
                                  dimensiones, peso_kg, instrucciones_cuidado, precio_compra, precio_venta,
                                  es_edicion_limitada, total_piezas_edicion, requiere_certificado, activo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['codigo_barras'] ?? null,
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['categoria_id'],
            $data['artesano_id'] ?? null,
            $data['materiales'] ?? null,
            $data['tecnica_elaboracion'] ?? null,
            $data['tiempo_produccion_dias'] ?? null,
            $data['region_origen'] ?? null,
            $data['dimensiones'] ?? null,
            $data['peso_kg'] ?? null,
            $data['instrucciones_cuidado'] ?? null,
            $data['precio_compra'] ?? 0,
            $data['precio_venta'],
            $data['es_edicion_limitada'] ?? 0,
            $data['total_piezas_edicion'] ?? null,
            $data['requiere_certificado'] ?? 0,
            $data['activo'] ?? 1
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE productos SET
                codigo_barras = ?, nombre = ?, descripcion = ?, categoria_id = ?, artesano_id = ?,
                materiales = ?, tecnica_elaboracion = ?, tiempo_produccion_dias = ?, region_origen = ?,
                dimensiones = ?, peso_kg = ?, instrucciones_cuidado = ?, precio_compra = ?, precio_venta = ?,
                es_edicion_limitada = ?, total_piezas_edicion = ?, requiere_certificado = ?, activo = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['codigo_barras'] ?? null,
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['categoria_id'],
            $data['artesano_id'] ?? null,
            $data['materiales'] ?? null,
            $data['tecnica_elaboracion'] ?? null,
            $data['tiempo_produccion_dias'] ?? null,
            $data['region_origen'] ?? null,
            $data['dimensiones'] ?? null,
            $data['peso_kg'] ?? null,
            $data['instrucciones_cuidado'] ?? null,
            $data['precio_compra'] ?? 0,
            $data['precio_venta'],
            $data['es_edicion_limitada'] ?? 0,
            $data['total_piezas_edicion'] ?? null,
            $data['requiere_certificado'] ?? 0,
            $data['activo'] ?? 1,
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE productos SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getVariants($product_id) {
        $stmt = $this->db->prepare("SELECT * FROM producto_variantes WHERE producto_id = ? AND activo = 1");
        $stmt->execute([$product_id]);
        return $stmt->fetchAll();
    }
    
    public function getImages($product_id) {
        $stmt = $this->db->prepare("SELECT * FROM producto_imagenes WHERE producto_id = ? ORDER BY orden, es_principal DESC");
        $stmt->execute([$product_id]);
        return $stmt->fetchAll();
    }
    
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM productos WHERE 1=1";
        $params = [];
        
        if (!empty($filters['categoria_id'])) {
            $sql .= " AND categoria_id = ?";
            $params[] = $filters['categoria_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (isset($filters['activo'])) {
            $sql .= " AND activo = ?";
            $params[] = $filters['activo'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }
}
