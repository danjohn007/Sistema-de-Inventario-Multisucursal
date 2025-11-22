<?php
/**
 * Controlador de Reportes
 */
require_once BASE_PATH . '/app/helpers/Controller.php';

class ReportsController extends Controller {
    
    public function inventory() {
        $this->checkAuth();
        
        $db = Database::getInstance()->getConnection();
        
        $branch_id = $_GET['sucursal'] ?? null;
        $category_id = $_GET['categoria'] ?? null;
        
        // Reporte de inventario por sucursal
        $sql = "SELECT s.nombre as sucursal, 
                       COUNT(DISTINCT i.producto_id) as total_productos,
                       SUM(i.cantidad_actual) as total_items,
                       SUM(i.cantidad_actual * p.precio_venta) as valor_total,
                       COUNT(CASE WHEN i.cantidad_actual <= i.cantidad_minima THEN 1 END) as stock_bajo
                FROM sucursales s
                LEFT JOIN inventario i ON s.id = i.sucursal_id
                LEFT JOIN productos p ON i.producto_id = p.id
                WHERE s.activo = 1";
        
        $params = [];
        
        if ($branch_id) {
            $sql .= " AND s.id = ?";
            $params[] = $branch_id;
        }
        
        if ($category_id) {
            $sql .= " AND p.categoria_id = ?";
            $params[] = $category_id;
        }
        
        $sql .= " GROUP BY s.id, s.nombre ORDER BY s.nombre";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $inventory_by_branch = $stmt->fetchAll();
        
        // Productos con más stock
        $stmt = $db->prepare("
            SELECT p.nombre, SUM(i.cantidad_actual) as total_stock, 
                   SUM(i.cantidad_actual * p.precio_venta) as valor
            FROM productos p
            JOIN inventario i ON p.id = i.producto_id
            WHERE p.activo = 1
            GROUP BY p.id, p.nombre
            ORDER BY total_stock DESC
            LIMIT 10
        ");
        $stmt->execute();
        $top_stock = $stmt->fetchAll();
        
        // Productos con stock bajo
        $stmt = $db->prepare("
            SELECT p.nombre, s.nombre as sucursal, i.cantidad_actual, i.cantidad_minima
            FROM inventario i
            JOIN productos p ON i.producto_id = p.id
            JOIN sucursales s ON i.sucursal_id = s.id
            WHERE i.cantidad_actual <= i.cantidad_minima AND p.activo = 1
            ORDER BY (i.cantidad_actual / NULLIF(i.cantidad_minima, 0)) ASC
            LIMIT 20
        ");
        $stmt->execute();
        $low_stock = $stmt->fetchAll();
        
        $branchModel = $this->model('Branch');
        $branches = $branchModel->getAll();
        
        $categoryModel = $this->model('Category');
        $categories = $categoryModel->getAll();
        
        $this->view('reports/inventory', [
            'title' => 'Reporte de Inventario',
            'inventory_by_branch' => $inventory_by_branch,
            'top_stock' => $top_stock,
            'low_stock' => $low_stock,
            'branches' => $branches,
            'categories' => $categories,
            'selected_branch' => $branch_id,
            'selected_category' => $category_id
        ]);
    }
    
    public function sales() {
        $this->checkAuth();
        
        $db = Database::getInstance()->getConnection();
        
        $fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
        $fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');
        $branch_id = $_GET['sucursal'] ?? null;
        
        // Ventas por día
        $sql = "SELECT DATE(fecha_venta) as fecha, 
                       COUNT(*) as total_ventas,
                       SUM(total) as monto_total
                FROM ventas
                WHERE estado = 'completada'
                  AND DATE(fecha_venta) BETWEEN ? AND ?";
        
        $params = [$fecha_desde, $fecha_hasta];
        
        if ($branch_id) {
            $sql .= " AND sucursal_id = ?";
            $params[] = $branch_id;
        }
        
        $sql .= " GROUP BY DATE(fecha_venta) ORDER BY fecha";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $sales_by_day = $stmt->fetchAll();
        
        // Ventas por sucursal
        $sql = "SELECT s.nombre as sucursal,
                       COUNT(v.id) as total_ventas,
                       SUM(v.total) as monto_total,
                       AVG(v.total) as promedio_venta
                FROM sucursales s
                LEFT JOIN ventas v ON s.id = v.sucursal_id 
                    AND v.estado = 'completada'
                    AND DATE(v.fecha_venta) BETWEEN ? AND ?
                WHERE s.activo = 1
                GROUP BY s.id, s.nombre
                ORDER BY monto_total DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$fecha_desde, $fecha_hasta]);
        $sales_by_branch = $stmt->fetchAll();
        
        // Productos más vendidos
        $sql = "SELECT p.nombre, 
                       SUM(vd.cantidad) as cantidad_vendida,
                       SUM(vd.subtotal) as total_vendido
                FROM venta_detalle vd
                JOIN ventas v ON vd.venta_id = v.id
                JOIN productos p ON vd.producto_id = p.id
                WHERE v.estado = 'completada'
                  AND DATE(v.fecha_venta) BETWEEN ? AND ?";
        
        $params = [$fecha_desde, $fecha_hasta];
        
        if ($branch_id) {
            $sql .= " AND v.sucursal_id = ?";
            $params[] = $branch_id;
        }
        
        $sql .= " GROUP BY p.id, p.nombre
                  ORDER BY cantidad_vendida DESC
                  LIMIT 10";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $top_products = $stmt->fetchAll();
        
        // Métodos de pago
        $sql = "SELECT metodo_pago, COUNT(*) as total, SUM(total) as monto
                FROM ventas
                WHERE estado = 'completada'
                  AND DATE(fecha_venta) BETWEEN ? AND ?";
        
        $params = [$fecha_desde, $fecha_hasta];
        
        if ($branch_id) {
            $sql .= " AND sucursal_id = ?";
            $params[] = $branch_id;
        }
        
        $sql .= " GROUP BY metodo_pago";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $payment_methods = $stmt->fetchAll();
        
        $branchModel = $this->model('Branch');
        $branches = $branchModel->getAll();
        
        $this->view('reports/sales', [
            'title' => 'Reporte de Ventas',
            'sales_by_day' => $sales_by_day,
            'sales_by_branch' => $sales_by_branch,
            'top_products' => $top_products,
            'payment_methods' => $payment_methods,
            'branches' => $branches,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'selected_branch' => $branch_id
        ]);
    }
}
