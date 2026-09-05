-- =====================================================
-- SISTEMA DE GESTION DE PANADERIA "AQUI NADIE SE RINDE"
-- Base de datos: panaderia
-- Motor: PostgreSQL
-- =====================================================

-- Crear base de datos (ejecutar como superusuario)
-- CREATE DATABASE panaderia ENCODING 'UTF8';

-- =====================================================
-- TABLA: usuarios
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL DEFAULT 'vendedor' CHECK (rol IN ('admin', 'vendedor')),
    estado VARCHAR(20) NOT NULL DEFAULT 'activo' CHECK (estado IN ('activo', 'inactivo')),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL
);

-- =====================================================
-- TABLA: categorias
-- =====================================================
CREATE TABLE IF NOT EXISTS categorias (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

-- =====================================================
-- TABLA: productos
-- =====================================================
CREATE TABLE IF NOT EXISTS productos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 5,
    categoria_id INT,
    imagen VARCHAR(255) DEFAULT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'activo' CHECK (estado IN ('activo', 'inactivo')),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE UNIQUE INDEX IF NOT EXISTS idx_productos_nombre_unico ON productos (LOWER(TRIM(nombre)));

-- =====================================================
-- TABLA: movimientos_inventario
-- =====================================================
CREATE TABLE IF NOT EXISTS movimientos_inventario (
    id SERIAL PRIMARY KEY,
    producto_id INT NOT NULL,
    tipo_movimiento VARCHAR(20) NOT NULL CHECK (tipo_movimiento IN ('entrada', 'salida', 'ajuste')),
    cantidad INT NOT NULL,
    motivo VARCHAR(255),
    usuario_id INT NOT NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- =====================================================
-- TABLA: ventas
-- =====================================================
CREATE TABLE IF NOT EXISTS ventas (
    id SERIAL PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado VARCHAR(20) NOT NULL DEFAULT 'completada' CHECK (estado IN ('completada', 'anulada')),
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- =====================================================
-- TABLA: detalle_venta
-- =====================================================
CREATE TABLE IF NOT EXISTS detalle_venta (
    id SERIAL PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- =====================================================
-- DATOS DE PRUEBA
-- =====================================================

-- Usuarios del sistema
-- Admin: Angelo Zambrano / Juniorapp123
-- Vendedores: contrasena 123456
INSERT INTO usuarios (nombre, usuario, contrasena, rol, estado) VALUES
('Angelo Zambrano', 'angelo', '$2y$10$EdEJrrfn4jnbjpJ9vD/vOuYw9BTDlhgyojygcnn8y1OCe1zqt90nq', 'admin', 'activo'),
('Maria Lopez', 'maria', '$2y$10$U/cJDcFOMllnsZQkltILaeS6ZOJ6bXcU4f9BNMK7CH0KKq7pP2zjC', 'vendedor', 'activo'),
('Carlos Ruiz', 'carlos', '$2y$10$U/cJDcFOMllnsZQkltILaeS6ZOJ6bXcU4f9BNMK7CH0KKq7pP2zjC', 'vendedor', 'activo');

INSERT INTO categorias (nombre, descripcion) VALUES
('Panes', 'Todo tipo de panes artesanales'),
('Pasteleria', 'Tortas, galletas y postres'),
('Bebidas', 'Cafes, jugos y otras bebidas'),
('Snacks', 'Bocadillos y productos de picar');

INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo, categoria_id) VALUES
('Pan Frances', 'Pan frances tradicional', 0.50, 100, 20, 1),
('Pan Integral', 'Pan integral con semillas', 1.20, 50, 10, 1),
('Croissant', 'Croissant de mantequilla', 1.50, 40, 10, 1),
('Torta de Chocolate', 'Torta de chocolate con ganache', 25.00, 10, 3, 2),
('Galletas de Avena', 'Pack de 6 galletas de avena', 3.50, 30, 8, 2),
('Cafe Americano', 'Cafe americano 250ml', 2.00, 80, 15, 3),
('Jugo de Naranja', 'Jugo natural de naranja', 3.00, 40, 10, 3),
('Empanada de Queso', 'Empanada rellena de queso', 2.50, 35, 10, 4),
('Sandwich de Pollo', 'Sandwich con pechuga de pollo', 4.00, 25, 5, 4),
('Media Luna', 'Media luna rellena de crema', 1.80, 45, 10, 1);
