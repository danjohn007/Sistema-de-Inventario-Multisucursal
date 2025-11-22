-- Sistema de Inventario Multisucursal para Productos Artesanales
-- Base de datos: inventario_multisucursal
-- Versión: MySQL 5.7+

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS `inventario_multisucursal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `inventario_multisucursal`;

-- =====================================================
-- TABLA: usuarios
-- =====================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `telefono` VARCHAR(20),
  `rol` ENUM('administrador', 'gerente_sucursal', 'vendedor', 'almacenista', 'artesano') NOT NULL DEFAULT 'vendedor',
  `sucursal_id` INT(11) DEFAULT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `sucursal_id` (`sucursal_id`),
  KEY `rol` (`rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: sucursales
-- =====================================================
CREATE TABLE IF NOT EXISTS `sucursales` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `codigo` VARCHAR(20) NOT NULL,
  `direccion` TEXT NOT NULL,
  `ciudad` VARCHAR(100) NOT NULL,
  `estado` VARCHAR(100) NOT NULL,
  `codigo_postal` VARCHAR(10) NOT NULL,
  `telefono` VARCHAR(20),
  `email` VARCHAR(100),
  `responsable_id` INT(11) DEFAULT NULL,
  `horario_apertura` TIME DEFAULT NULL,
  `horario_cierre` TIME DEFAULT NULL,
  `capacidad_m2` DECIMAL(10,2) DEFAULT NULL,
  `capacidad_productos` INT(11) DEFAULT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `responsable_id` (`responsable_id`),
  KEY `estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: categorias
-- =====================================================
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `descripcion` TEXT,
  `icono` VARCHAR(50),
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: artesanos (proveedores)
-- =====================================================
CREATE TABLE IF NOT EXISTS `artesanos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100),
  `nombre_comercial` VARCHAR(150),
  `especialidad` VARCHAR(150),
  `telefono` VARCHAR(20),
  `email` VARCHAR(100),
  `direccion` TEXT,
  `ciudad` VARCHAR(100),
  `estado` VARCHAR(100),
  `region_origen` VARCHAR(100),
  `tecnicas` TEXT,
  `certificaciones` TEXT,
  `terminos_colaboracion` TEXT,
  `calificacion` DECIMAL(3,2) DEFAULT 0.00,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `estado` (`estado`),
  KEY `especialidad` (`especialidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: productos
-- =====================================================
CREATE TABLE IF NOT EXISTS `productos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `codigo_barras` VARCHAR(50),
  `nombre` VARCHAR(200) NOT NULL,
  `descripcion` TEXT,
  `categoria_id` INT(11) NOT NULL,
  `artesano_id` INT(11) DEFAULT NULL,
  `materiales` TEXT,
  `tecnica_elaboracion` TEXT,
  `tiempo_produccion_dias` INT(11) DEFAULT NULL,
  `region_origen` VARCHAR(100),
  `dimensiones` VARCHAR(100),
  `peso_kg` DECIMAL(10,3),
  `instrucciones_cuidado` TEXT,
  `precio_compra` DECIMAL(10,2) DEFAULT 0.00,
  `precio_venta` DECIMAL(10,2) NOT NULL,
  `es_edicion_limitada` TINYINT(1) DEFAULT 0,
  `total_piezas_edicion` INT(11) DEFAULT NULL,
  `requiere_certificado` TINYINT(1) DEFAULT 0,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_barras` (`codigo_barras`),
  KEY `categoria_id` (`categoria_id`),
  KEY `artesano_id` (`artesano_id`),
  KEY `nombre` (`nombre`),
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  CONSTRAINT `fk_productos_artesano` FOREIGN KEY (`artesano_id`) REFERENCES `artesanos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: producto_imagenes
-- =====================================================
CREATE TABLE IF NOT EXISTS `producto_imagenes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `producto_id` INT(11) NOT NULL,
  `ruta_imagen` VARCHAR(255) NOT NULL,
  `es_principal` TINYINT(1) DEFAULT 0,
  `orden` INT(11) DEFAULT 0,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `fk_imagenes_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: producto_variantes
-- =====================================================
CREATE TABLE IF NOT EXISTS `producto_variantes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `producto_id` INT(11) NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `color` VARCHAR(50),
  `tamano` VARCHAR(50),
  `diseno` VARCHAR(100),
  `codigo_variante` VARCHAR(50),
  `precio_adicional` DECIMAL(10,2) DEFAULT 0.00,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `fk_variantes_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: inventario
-- =====================================================
CREATE TABLE IF NOT EXISTS `inventario` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `producto_id` INT(11) NOT NULL,
  `variante_id` INT(11) DEFAULT NULL,
  `sucursal_id` INT(11) NOT NULL,
  `cantidad_actual` INT(11) NOT NULL DEFAULT 0,
  `cantidad_minima` INT(11) NOT NULL DEFAULT 5,
  `cantidad_maxima` INT(11) NOT NULL DEFAULT 100,
  `ubicacion_fisica` VARCHAR(100),
  `numero_pieza_unica` VARCHAR(50),
  `fecha_actualizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventario_unico` (`producto_id`, `sucursal_id`, `variante_id`),
  KEY `sucursal_id` (`sucursal_id`),
  KEY `variante_id` (`variante_id`),
  CONSTRAINT `fk_inventario_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `fk_inventario_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`),
  CONSTRAINT `fk_inventario_variante` FOREIGN KEY (`variante_id`) REFERENCES `producto_variantes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: movimientos_inventario
-- =====================================================
CREATE TABLE IF NOT EXISTS `movimientos_inventario` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tipo` ENUM('entrada', 'salida', 'ajuste', 'transferencia') NOT NULL,
  `motivo` ENUM('compra', 'venta', 'devolucion', 'merma', 'obsequio', 'produccion', 'ajuste_inventario', 'transferencia_entrada', 'transferencia_salida') NOT NULL,
  `producto_id` INT(11) NOT NULL,
  `variante_id` INT(11) DEFAULT NULL,
  `sucursal_id` INT(11) NOT NULL,
  `cantidad` INT(11) NOT NULL,
  `cantidad_anterior` INT(11) NOT NULL,
  `cantidad_nueva` INT(11) NOT NULL,
  `usuario_id` INT(11) NOT NULL,
  `referencia` VARCHAR(100),
  `notas` TEXT,
  `fecha_movimiento` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  KEY `sucursal_id` (`sucursal_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `tipo` (`tipo`),
  KEY `fecha_movimiento` (`fecha_movimiento`),
  CONSTRAINT `fk_movimientos_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `fk_movimientos_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`),
  CONSTRAINT `fk_movimientos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: transferencias
-- =====================================================
CREATE TABLE IF NOT EXISTS `transferencias` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `folio` VARCHAR(50) NOT NULL,
  `sucursal_origen_id` INT(11) NOT NULL,
  `sucursal_destino_id` INT(11) NOT NULL,
  `usuario_solicita_id` INT(11) NOT NULL,
  `usuario_aprueba_id` INT(11) DEFAULT NULL,
  `usuario_recibe_id` INT(11) DEFAULT NULL,
  `estado` ENUM('solicitada', 'aprobada', 'en_transito', 'recibida', 'cancelada') NOT NULL DEFAULT 'solicitada',
  `fecha_solicitud` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_aprobacion` TIMESTAMP NULL DEFAULT NULL,
  `fecha_envio` TIMESTAMP NULL DEFAULT NULL,
  `fecha_recepcion` TIMESTAMP NULL DEFAULT NULL,
  `notas` TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `folio` (`folio`),
  KEY `sucursal_origen_id` (`sucursal_origen_id`),
  KEY `sucursal_destino_id` (`sucursal_destino_id`),
  KEY `estado` (`estado`),
  CONSTRAINT `fk_transferencias_origen` FOREIGN KEY (`sucursal_origen_id`) REFERENCES `sucursales` (`id`),
  CONSTRAINT `fk_transferencias_destino` FOREIGN KEY (`sucursal_destino_id`) REFERENCES `sucursales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: transferencia_detalle
-- =====================================================
CREATE TABLE IF NOT EXISTS `transferencia_detalle` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `transferencia_id` INT(11) NOT NULL,
  `producto_id` INT(11) NOT NULL,
  `variante_id` INT(11) DEFAULT NULL,
  `cantidad_solicitada` INT(11) NOT NULL,
  `cantidad_enviada` INT(11) DEFAULT 0,
  `cantidad_recibida` INT(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `transferencia_id` (`transferencia_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `fk_detalle_transferencia` FOREIGN KEY (`transferencia_id`) REFERENCES `transferencias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: ordenes_compra
-- =====================================================
CREATE TABLE IF NOT EXISTS `ordenes_compra` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `folio` VARCHAR(50) NOT NULL,
  `artesano_id` INT(11) NOT NULL,
  `sucursal_id` INT(11) NOT NULL,
  `usuario_id` INT(11) NOT NULL,
  `estado` ENUM('pendiente', 'confirmada', 'en_produccion', 'completada', 'cancelada') NOT NULL DEFAULT 'pendiente',
  `fecha_pedido` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_entrega_estimada` DATE DEFAULT NULL,
  `fecha_entrega_real` DATE DEFAULT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `impuestos` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `notas` TEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `folio` (`folio`),
  KEY `artesano_id` (`artesano_id`),
  KEY `sucursal_id` (`sucursal_id`),
  KEY `estado` (`estado`),
  CONSTRAINT `fk_ordenes_artesano` FOREIGN KEY (`artesano_id`) REFERENCES `artesanos` (`id`),
  CONSTRAINT `fk_ordenes_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: orden_compra_detalle
-- =====================================================
CREATE TABLE IF NOT EXISTS `orden_compra_detalle` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `orden_compra_id` INT(11) NOT NULL,
  `producto_id` INT(11) NOT NULL,
  `variante_id` INT(11) DEFAULT NULL,
  `cantidad` INT(11) NOT NULL,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orden_compra_id` (`orden_compra_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `fk_detalle_orden` FOREIGN KEY (`orden_compra_id`) REFERENCES `ordenes_compra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_orden_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: clientes
-- =====================================================
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100),
  `email` VARCHAR(100),
  `telefono` VARCHAR(20),
  `direccion` TEXT,
  `ciudad` VARCHAR(100),
  `estado` VARCHAR(100),
  `fecha_nacimiento` DATE DEFAULT NULL,
  `puntos_fidelidad` INT(11) DEFAULT 0,
  `notas` TEXT,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `telefono` (`telefono`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: ventas
-- =====================================================
CREATE TABLE IF NOT EXISTS `ventas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `folio` VARCHAR(50) NOT NULL,
  `sucursal_id` INT(11) NOT NULL,
  `usuario_id` INT(11) NOT NULL,
  `cliente_id` INT(11) DEFAULT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `descuento` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `impuestos` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `metodo_pago` ENUM('efectivo', 'tarjeta', 'transferencia', 'paypal', 'mixto') NOT NULL,
  `estado` ENUM('completada', 'cancelada', 'devolucion') NOT NULL DEFAULT 'completada',
  `fecha_venta` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `folio` (`folio`),
  KEY `sucursal_id` (`sucursal_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `fecha_venta` (`fecha_venta`),
  CONSTRAINT `fk_ventas_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`),
  CONSTRAINT `fk_ventas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_ventas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: venta_detalle
-- =====================================================
CREATE TABLE IF NOT EXISTS `venta_detalle` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `venta_id` INT(11) NOT NULL,
  `producto_id` INT(11) NOT NULL,
  `variante_id` INT(11) DEFAULT NULL,
  `cantidad` INT(11) NOT NULL,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `descuento` DECIMAL(10,2) DEFAULT 0.00,
  `subtotal` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `venta_id` (`venta_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `fk_detalle_venta` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_venta_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: control_calidad
-- =====================================================
CREATE TABLE IF NOT EXISTS `control_calidad` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `producto_id` INT(11) NOT NULL,
  `tipo_inspeccion` ENUM('recepcion', 'produccion', 'reparacion', 'conservacion') NOT NULL,
  `usuario_id` INT(11) NOT NULL,
  `calificacion` ENUM('excelente', 'bueno', 'aceptable', 'rechazado') NOT NULL,
  `observaciones` TEXT,
  `fotos` TEXT,
  `fecha_inspeccion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `fk_calidad_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `fk_calidad_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: certificados_autenticidad
-- =====================================================
CREATE TABLE IF NOT EXISTS `certificados_autenticidad` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `producto_id` INT(11) NOT NULL,
  `numero_certificado` VARCHAR(50) NOT NULL,
  `numero_pieza` VARCHAR(50),
  `total_edicion` INT(11),
  `artesano_id` INT(11) NOT NULL,
  `fecha_elaboracion` DATE NOT NULL,
  `descripcion_tecnica` TEXT,
  `materiales_certificados` TEXT,
  `fecha_emision` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_certificado` (`numero_certificado`),
  KEY `producto_id` (`producto_id`),
  KEY `artesano_id` (`artesano_id`),
  CONSTRAINT `fk_certificado_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `fk_certificado_artesano` FOREIGN KEY (`artesano_id`) REFERENCES `artesanos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: notificaciones
-- =====================================================
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` INT(11) NOT NULL,
  `tipo` ENUM('stock_bajo', 'transferencia', 'orden_compra', 'venta', 'sistema') NOT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `mensaje` TEXT NOT NULL,
  `referencia_id` INT(11) DEFAULT NULL,
  `leida` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `leida` (`leida`),
  CONSTRAINT `fk_notificaciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: configuracion
-- =====================================================
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `clave` VARCHAR(100) NOT NULL,
  `valor` TEXT,
  `descripcion` TEXT,
  `tipo` ENUM('texto', 'numero', 'booleano', 'color', 'email', 'url') DEFAULT 'texto',
  `categoria` VARCHAR(50) DEFAULT 'general',
  `fecha_actualizacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERTAR DATOS DE EJEMPLO - ESTADO DE QUERÉTARO
-- =====================================================

-- Usuario Administrador (password: admin123)
INSERT INTO `usuarios` (`nombre`, `apellidos`, `email`, `password`, `telefono`, `rol`, `activo`) VALUES
('Administrador', 'del Sistema', 'admin@inventario.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '4421234567', 'administrador', 1);

-- Sucursales en Querétaro
INSERT INTO `sucursales` (`nombre`, `codigo`, `direccion`, `ciudad`, `estado`, `codigo_postal`, `telefono`, `email`, `horario_apertura`, `horario_cierre`, `capacidad_m2`, `capacidad_productos`, `activo`) VALUES
('Centro Histórico', 'QRO-CENTRO', 'Calle 5 de Mayo #38, Centro', 'Santiago de Querétaro', 'Querétaro', '76000', '4421111111', 'centro@artesanias.com', '09:00:00', '20:00:00', 150.00, 500, 1),
('Plaza Constitución', 'QRO-CONST', 'Av. Constituyentes #30, El Retablo', 'Santiago de Querétaro', 'Querétaro', '76090', '4422222222', 'constitucion@artesanias.com', '10:00:00', '21:00:00', 200.00, 800, 1),
('San Juan del Río', 'SJR-001', 'Juárez #15, Centro', 'San Juan del Río', 'Querétaro', '76800', '4273333333', 'sanjuan@artesanias.com', '09:00:00', '19:00:00', 120.00, 400, 1),
('Tequisquiapan', 'TEQ-001', 'Plaza Principal #5', 'Tequisquiapan', 'Querétaro', '76750', '4144444444', 'tequisquiapan@artesanias.com', '09:00:00', '20:00:00', 180.00, 600, 1);

-- Categorías de productos artesanales
INSERT INTO `categorias` (`nombre`, `descripcion`, `icono`, `activo`) VALUES
('Cerámica', 'Artesanías elaboradas en barro y cerámica', '🏺', 1),
('Textiles', 'Bordados, tejidos y telas artesanales', '🧵', 1),
('Madera', 'Tallados y productos de madera', '🪵', 1),
('Metalistería', 'Trabajo en metal, plata y cobre', '⚒️', 1),
('Joyería', 'Piezas de joyería artesanal', '💎', 1),
('Cestería', 'Canastas y productos tejidos', '🧺', 1),
('Vidrio Soplado', 'Artesanías en vidrio', '🔮', 1),
('Papel Maché', 'Figuras y decoraciones en papel', '🎨', 1);

-- Artesanos de Querétaro
INSERT INTO `artesanos` (`nombre`, `apellidos`, `nombre_comercial`, `especialidad`, `telefono`, `email`, `direccion`, `ciudad`, `estado`, `region_origen`, `tecnicas`, `calificacion`, `activo`) VALUES
('María', 'González Pérez', 'Cerámica María', 'Alfarería tradicional', '4421112233', 'maria@ceramica.com', 'Barrio de la Cruz', 'Santiago de Querétaro', 'Querétaro', 'Centro', 'Torno alfarero, quema en horno de leña', 4.80, 1),
('José Luis', 'Martínez Ramírez', 'Textiles Tradicionales', 'Bordado queretano', '4272223344', 'jose@textiles.com', 'Ejido La Lira', 'San Juan del Río', 'Querétaro', 'San Juan del Río', 'Bordado a mano, telar de pedal', 4.50, 1),
('Roberto', 'Sánchez Torres', 'Tallados Don Roberto', 'Tallado en madera', '4143334455', 'roberto@tallados.com', 'Centro', 'Tequisquiapan', 'Querétaro', 'Tequisquiapan', 'Tallado, torno de madera', 4.70, 1),
('Ana Patricia', 'Hernández López', 'Joyas Coloniales', 'Joyería en plata', '4424445566', 'ana@joyas.com', 'Barrio del Tepetate', 'Santiago de Querétaro', 'Querétaro', 'Centro', 'Filigrana, fundición', 4.90, 1);

-- Productos artesanales
INSERT INTO `productos` (`codigo_barras`, `nombre`, `descripcion`, `categoria_id`, `artesano_id`, `materiales`, `tecnica_elaboracion`, `tiempo_produccion_dias`, `region_origen`, `dimensiones`, `peso_kg`, `precio_compra`, `precio_venta`, `es_edicion_limitada`, `activo`) VALUES
('7501234567890', 'Maceta de Barro Grande', 'Maceta de barro cocido con diseños tradicionales queretanos', 1, 1, 'Barro rojo, esmaltes naturales', 'Torno alfarero, quema tradicional', 7, 'Centro de Querétaro', '30cm alto x 25cm diámetro', 2.500, 180.00, 450.00, 0, 1),
('7501234567891', 'Rebozo Bordado', 'Rebozo de algodón con bordado tradicional', 2, 2, 'Algodón 100%, hilo de seda', 'Telar de pedal, bordado a mano', 15, 'San Juan del Río', '180cm x 80cm', 0.300, 450.00, 1200.00, 0, 1),
('7501234567892', 'Tabla de Picar Artesanal', 'Tabla de madera de mezquite tallada', 3, 3, 'Madera de mezquite, aceite mineral', 'Tallado a mano', 3, 'Tequisquiapan', '40cm x 25cm x 2cm', 1.200, 250.00, 650.00, 0, 1),
('7501234567893', 'Aretes de Plata Coloniales', 'Aretes de plata .925 con técnica de filigrana', 5, 4, 'Plata .925, piedras semipreciosas', 'Filigrana, soldadura fina', 2, 'Centro de Querétaro', '4cm largo', 0.015, 380.00, 850.00, 0, 1),
('7501234567894', 'Jarrón Decorativo Azul', 'Jarrón de cerámica con esmalte azul cobalto', 1, 1, 'Barro, esmalte azul cobalto', 'Torno, esmaltado', 5, 'Centro de Querétaro', '35cm alto x 15cm diámetro', 1.800, 220.00, 580.00, 0, 1),
('7501234567895', 'Frutero de Madera', 'Frutero tallado en madera de parota', 3, 3, 'Madera de parota, barniz natural', 'Tallado, torneado', 4, 'Tequisquiapan', '30cm diámetro x 10cm alto', 0.800, 180.00, 480.00, 0, 1);

-- Variantes de productos
INSERT INTO `producto_variantes` (`producto_id`, `nombre`, `color`, `tamano`, `codigo_variante`, `precio_adicional`, `activo`) VALUES
(1, 'Maceta Grande Roja', 'Rojo', 'Grande', 'MAC-GDE-ROJO', 0.00, 1),
(1, 'Maceta Grande Natural', 'Natural', 'Grande', 'MAC-GDE-NAT', 0.00, 1),
(2, 'Rebozo Negro', 'Negro', 'Estándar', 'REB-NEG', 0.00, 1),
(2, 'Rebozo Azul', 'Azul', 'Estándar', 'REB-AZUL', 50.00, 1),
(3, 'Tabla Mediana', 'Natural', 'Mediana', 'TAB-MED', -100.00, 1),
(3, 'Tabla Grande', 'Natural', 'Grande', 'TAB-GDE', 0.00, 1);

-- Inventario inicial por sucursal
INSERT INTO `inventario` (`producto_id`, `variante_id`, `sucursal_id`, `cantidad_actual`, `cantidad_minima`, `cantidad_maxima`, `ubicacion_fisica`) VALUES
-- Centro Histórico
(1, 1, 1, 15, 5, 50, 'Anaquel A-1'),
(1, 2, 1, 12, 5, 50, 'Anaquel A-2'),
(2, 3, 1, 8, 3, 30, 'Vitrina B-1'),
(2, 4, 1, 6, 3, 30, 'Vitrina B-2'),
(3, 5, 1, 20, 5, 40, 'Anaquel C-1'),
(4, NULL, 1, 10, 2, 20, 'Joyería D-1'),
-- Plaza Constitución
(1, 1, 2, 25, 10, 80, 'Sección A-3'),
(2, 3, 2, 12, 5, 40, 'Vitrina Principal'),
(3, 6, 2, 18, 8, 50, 'Madera-1'),
(4, NULL, 2, 15, 3, 25, 'Joyería-2'),
(5, NULL, 2, 10, 5, 30, 'Cerámica-1'),
-- San Juan del Río
(1, 1, 3, 10, 5, 40, 'Bodega A'),
(2, 3, 3, 15, 5, 35, 'Exhibición Principal'),
(3, 5, 3, 12, 5, 30, 'Sección Cocina'),
-- Tequisquiapan
(3, 6, 4, 25, 10, 60, 'Madera Principal'),
(5, NULL, 4, 8, 3, 25, 'Cerámica-A'),
(6, NULL, 4, 14, 5, 40, 'Cocina-B');

-- Clientes de ejemplo
INSERT INTO `clientes` (`nombre`, `apellidos`, `email`, `telefono`, `ciudad`, `estado`, `puntos_fidelidad`, `activo`) VALUES
('Laura', 'Ramírez García', 'laura.ramirez@email.com', '4421234567', 'Santiago de Querétaro', 'Querétaro', 150, 1),
('Carlos', 'López Mendoza', 'carlos.lopez@email.com', '4422345678', 'Santiago de Querétaro', 'Querétaro', 280, 1),
('Patricia', 'Sánchez Ruiz', 'patricia.sanchez@email.com', '4273456789', 'San Juan del Río', 'Querétaro', 95, 1);

-- Configuración del sistema
INSERT INTO `configuracion` (`clave`, `valor`, `descripcion`, `tipo`, `categoria`) VALUES
('sitio_nombre', 'Artesanías de Querétaro', 'Nombre del sitio web', 'texto', 'general'),
('sitio_logo', '', 'Ruta del logotipo', 'url', 'general'),
('email_principal', 'info@artesanias.com', 'Email principal del sistema', 'email', 'email'),
('telefono_contacto', '442-123-4567', 'Teléfono de contacto', 'texto', 'contacto'),
('horario_atencion', 'Lunes a Domingo 9:00 - 20:00', 'Horario de atención', 'texto', 'contacto'),
('color_primario', '#2563eb', 'Color primario del tema', 'color', 'apariencia'),
('color_secundario', '#7c3aed', 'Color secundario del tema', 'color', 'apariencia'),
('color_acento', '#f59e0b', 'Color de acento', 'color', 'apariencia'),
('paypal_client_id', '', 'Client ID de PayPal', 'texto', 'pagos'),
('paypal_modo', 'sandbox', 'Modo de PayPal (sandbox/live)', 'texto', 'pagos'),
('api_qr', '', 'API para generar códigos QR', 'url', 'integraciones'),
('moneda', 'MXN', 'Moneda del sistema', 'texto', 'general'),
('impuesto_iva', '16', 'Porcentaje de IVA', 'numero', 'general');

-- Crear índices adicionales para mejorar rendimiento
CREATE INDEX idx_productos_categoria ON productos(categoria_id);
CREATE INDEX idx_inventario_sucursal_producto ON inventario(sucursal_id, producto_id);
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_movimientos_fecha ON movimientos_inventario(fecha_movimiento);
