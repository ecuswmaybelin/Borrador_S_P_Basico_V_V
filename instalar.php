<?php
/**
 * INSTALADOR DEL SISTEMA - PostgreSQL
 * Ejecutar UNA SOLA VEZ y luego ELIMINAR este archivo.
 */

$servidor = getenv('DB_HOST') ?: "localhost";
$usuario_root = "postgres";
$contrasena_root = "2222";
$nombre_bd = "panaderia";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Instalador - Panaderia</title>
    <style>
        body { font-family: Arial, sans-serif; background: #FCE4EC; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); max-width: 550px; }
        h1 { color: #AD1457; text-align: center; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #F44336; font-weight: bold; }
        .info { color: #1565C0; }
        .btn { display: inline-block; padding: 12px 25px; background: #EC407A; color: white; text-decoration: none; border-radius: 8px; margin-top: 15px; }
        .btn:hover { background: #AD1457; }
        .credenciales { text-align: left; background: #FCE4EC; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .warning { color: #F44336; font-weight: bold; margin-top: 20px; text-align: center; }
        .paso { padding: 5px 0; }
        .paso-ok::before { content: "OK "; color: #4CAF50; font-weight: bold; }
        .paso-fail::before { content: "ERROR "; color: #F44336; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h1>Sistema de Panaderia</h1>
    <p style="text-align:center;">Instalador automatico - PostgreSQL</p>
    <hr>
<?php

function mostrarPaso($mensaje, $ok = true) {
    $clase = $ok ? 'paso-ok' : 'paso-fail';
    echo "<div class='paso $clase'>$mensaje</div>";
    flush();
}

try {
    // PASO 1: Conectar a PostgreSQL (sin seleccionar BD)
    $pdo = new PDO("pgsql:host=$servidor;port=5432", $usuario_root, $contrasena_root);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    mostrarPaso("Conectado a PostgreSQL");

    // PASO 2: Eliminar BD anterior
    $pdo->exec("DROP DATABASE IF EXISTS $nombre_bd");
    mostrarPaso("Base de datos anterior eliminada");

    // PASO 3: Crear base de datos
    $pdo->exec("CREATE DATABASE $nombre_bd ENCODING 'UTF8'");
    mostrarPaso("Base de datos '$nombre_bd' creada");

    // PASO 4: Conectar a la base de datos creada
    $pdo = new PDO("pgsql:host=$servidor;port=5432;dbname=$nombre_bd", $usuario_root, $contrasena_root);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    mostrarPaso("Conectado a base de datos '$nombre_bd'");

    // PASO 5: Crear tabla usuarios
    $pdo->exec("CREATE TABLE usuarios (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        usuario VARCHAR(50) NOT NULL UNIQUE,
        contrasena VARCHAR(255) NOT NULL,
        rol VARCHAR(20) NOT NULL DEFAULT 'vendedor' CHECK (rol IN ('admin', 'vendedor')),
        estado VARCHAR(20) NOT NULL DEFAULT 'activo' CHECK (estado IN ('activo', 'inactivo')),
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ultimo_acceso TIMESTAMP NULL
    )");
    mostrarPaso("Tabla 'usuarios' creada");

    // PASO 6: Crear tabla categorias
    $pdo->exec("CREATE TABLE categorias (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT
    )");
    mostrarPaso("Tabla 'categorias' creada");

    // PASO 7: Crear tabla productos
    $pdo->exec("CREATE TABLE productos (
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
        FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL ON UPDATE CASCADE
    )");
    mostrarPaso("Tabla 'productos' creada");

    // PASO 8: Crear tabla movimientos_inventario
    $pdo->exec("CREATE TABLE movimientos_inventario (
        id SERIAL PRIMARY KEY,
        producto_id INT NOT NULL,
        tipo_movimiento VARCHAR(20) NOT NULL CHECK (tipo_movimiento IN ('entrada', 'salida', 'ajuste')),
        cantidad INT NOT NULL,
        motivo VARCHAR(255),
        usuario_id INT NOT NULL,
        fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
    )");
    mostrarPaso("Tabla 'movimientos_inventario' creada");

    // PASO 9: Crear tabla ventas
    $pdo->exec("CREATE TABLE ventas (
        id SERIAL PRIMARY KEY,
        usuario_id INT NOT NULL,
        total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        estado VARCHAR(20) NOT NULL DEFAULT 'completada' CHECK (estado IN ('completada', 'anulada')),
        fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
    )");
    mostrarPaso("Tabla 'ventas' creada");

    // PASO 10: Crear tabla detalle_venta
    $pdo->exec("CREATE TABLE detalle_venta (
        id SERIAL PRIMARY KEY,
        venta_id INT NOT NULL,
        producto_id INT NOT NULL,
        cantidad INT NOT NULL DEFAULT 1,
        precio_unitario DECIMAL(10,2) NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE ON UPDATE CASCADE
    )");
    mostrarPaso("Tabla 'detalle_venta' creada");

    // PASO 11: Crear tabla configuracion_tema
    $pdo->exec("CREATE TABLE configuracion_tema (
        id SERIAL PRIMARY KEY,
        variable VARCHAR(50) NOT NULL UNIQUE,
        valor VARCHAR(20) NOT NULL,
        descripcion VARCHAR(100)
    )");
    $pdo->exec("INSERT INTO configuracion_tema (variable, valor, descripcion) VALUES
        ('color_primario', '#EC407A', 'Color principal de la app'),
        ('color_primario_oscuro', '#AD1457', 'Color del sidebar y titulos'),
        ('color_fondo', '#FCE4EC', 'Fondo de pagina')");
    mostrarPaso("Tabla 'configuracion_tema' creada");

    // PASO 12: Insertar usuarios (hashes generados con password_hash)
    $hash_admin = password_hash('Juniorapp123', PASSWORD_DEFAULT);
    $hash_vendedor = password_hash('123456', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, usuario, contrasena, rol, estado) VALUES (?, ?, ?, ?, ?)");
    
    $stmt->execute(['Angelo Zambrano', 'angelo', $hash_admin, 'admin', 'activo']);
    $stmt->execute(['Maria Lopez', 'maria', $hash_vendedor, 'vendedor', 'activo']);
    $stmt->execute(['Carlos Ruiz', 'carlos', $hash_vendedor, 'vendedor', 'activo']);
    mostrarPaso("3 usuarios insertados");

    // PASO 13: Insertar categorias
    $stmt = $pdo->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
    $stmt->execute(['Panes', 'Todo tipo de panes artesanales']);
    $stmt->execute(['Pasteleria', 'Tortas, galletas y postres']);
    $stmt->execute(['Bebidas', 'Cafes, jugos y otras bebidas']);
    $stmt->execute(['Snacks', 'Bocadillos y productos de picar']);
    mostrarPaso("4 categorias insertadas");

    // PASO 14: Insertar productos
    $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo, categoria_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute(['Pan Frances', 'Pan frances tradicional', 0.50, 100, 20, 1]);
    $stmt->execute(['Pan Integral', 'Pan integral con semillas', 1.20, 50, 10, 1]);
    $stmt->execute(['Croissant', 'Croissant de mantequilla', 1.50, 40, 10, 1]);
    $stmt->execute(['Torta de Chocolate', 'Torta de chocolate con ganache', 25.00, 10, 3, 2]);
    $stmt->execute(['Galletas de Avena', 'Pack de 6 galletas de avena', 3.50, 30, 8, 2]);
    $stmt->execute(['Cafe Americano', 'Cafe americano 250ml', 2.00, 80, 15, 3]);
    $stmt->execute(['Jugo de Naranja', 'Jugo natural de naranja', 3.00, 40, 10, 3]);
    $stmt->execute(['Empanada de Queso', 'Empanada rellena de queso', 2.50, 35, 10, 4]);
    $stmt->execute(['Sandwich de Pollo', 'Sandwich con pechuga de pollo', 4.00, 25, 5, 4]);
    $stmt->execute(['Media Luna', 'Media luna rellena de crema', 1.80, 45, 10, 1]);
    mostrarPaso("10 productos insertados");

    // INSTALACION COMPLETA
    echo "<hr>";
    echo "<h2 class='success' style='text-align:center;'>Instalacion completada</h2>";
    
    echo "<div class='credenciales'>";
    echo "<h3>Credenciales de prueba:</h3>";
    echo "<p><strong>Admin:</strong> angelo / Juniorapp123</p>";
    echo "<p><strong>Vendedor:</strong> maria / 123456</p>";
    echo "<p><strong>Vendedor:</strong> carlos / 123456</p>";
    echo "</div>";
    
    echo "<div style='text-align:center;'>";
    echo "<a href='pages/login.php' class='btn'>Ir al Login</a>";
    echo "</div>";
    echo "<p class='warning'>IMPORTANTE: Elimina este archivo instalar.php por seguridad</p>";

} catch (PDOException $e) {
    mostrarPaso($e->getMessage(), false);
}
?>
</div>
</body>
</html>
