<?php
/**
 * Controlador del Dashboard
 */
require_once BASE_PATH . '/app/helpers/Controller.php';

class DashboardController extends Controller {
    
    public function index() {
        $this->checkAuth();
        
        $db = Database::getInstance()->getConnection();
        
        // Estadísticas generales
        $stats = [];
        
        // Total de productos
        $stmt = $db->query("SELECT COUNT(*) as total FROM productos WHERE activo = 1");
        $stats['total_productos'] = $stmt->fetch()['total'];
        
        // Total de sucursales
        $stmt = $db->query("SELECT COUNT(*) as total FROM sucursales WHERE activo = 1");
        $stats['total_sucursales'] = $stmt->fetch()['total'];
        
        // Total de inventario
        $stmt = $db->query("SELECT SUM(cantidad_actual) as total FROM inventario");
        $stats['total_inventario'] = $stmt->fetch()['total'] ?? 0;
        
        // Productos con stock bajo
        $stmt = $db->query("
            SELECT COUNT(*) as total 
            FROM inventario 
            WHERE cantidad_actual <= cantidad_minima
        ");
        $stats['productos_stock_bajo'] = $stmt->fetch()['total'];
        
        // Ventas del día (si hay)
        $stmt = $db->query("
            SELECT COUNT(*) as total, COALESCE(SUM(total), 0) as monto
            FROM ventas 
            WHERE DATE(fecha_venta) = CURDATE() AND estado = 'completada'
        ");
        $ventasHoy = $stmt->fetch();
        $stats['ventas_hoy'] = $ventasHoy['total'];
        $stats['monto_ventas_hoy'] = $ventasHoy['monto'];
        
        // Transferencias pendientes
        $stmt = $db->query("
            SELECT COUNT(*) as total 
            FROM transferencias 
            WHERE estado IN ('solicitada', 'en_transito')
        ");
        $stats['transferencias_pendientes'] = $stmt->fetch()['total'];
        
        // Productos más vendidos (últimos 30 días)
        $stmt = $db->query("
            SELECT p.nombre, SUM(vd.cantidad) as total_vendido
            FROM venta_detalle vd
            JOIN ventas v ON vd.venta_id = v.id
            JOIN productos p ON vd.producto_id = p.id
            WHERE v.fecha_venta >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            AND v.estado = 'completada'
            GROUP BY p.id, p.nombre
            ORDER BY total_vendido DESC
            LIMIT 5
        ");
        $stats['productos_mas_vendidos'] = $stmt->fetchAll();
        
        // Ventas por sucursal (últimos 30 días)
        $stmt = $db->query("
            SELECT s.nombre, COUNT(v.id) as total_ventas, COALESCE(SUM(v.total), 0) as monto_total
            FROM sucursales s
            LEFT JOIN ventas v ON s.id = v.sucursal_id 
                AND v.fecha_venta >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND v.estado = 'completada'
            WHERE s.activo = 1
            GROUP BY s.id, s.nombre
            ORDER BY monto_total DESC
        ");
        $stats['ventas_por_sucursal'] = $stmt->fetchAll();
        
        // Alertas recientes
        $stmt = $db->query("
            SELECT n.* 
            FROM notificaciones n
            WHERE n.usuario_id = ? AND n.leida = 0
            ORDER BY n.fecha_creacion DESC
            LIMIT 5
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $stats['alertas_recientes'] = $stmt->fetchAll();
        
        $this->view('dashboard/index', [
            'title' => 'Dashboard - Sistema de Inventario',
            'stats' => $stats
        ]);
    }
}
