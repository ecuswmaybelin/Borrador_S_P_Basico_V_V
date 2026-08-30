<?php
/**
 * VENTAS - Registrar nueva venta
 * 
 * Recibe el carrito completo y:
 * 1. Crea la venta
 * 2. Inserta el detalle de venta
 * 3. Reduce el stock de cada producto
 * 4. Genera comprobante basico
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Metodo no permitido"]);
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

$productosJSON = $_POST['productos'] ?? '[]';
$total = floatval($_POST['total'] ?? 0);
$usuario_id = $_SESSION['usuario_id'];

$productos = json_decode($productosJSON, true);

if (empty($productos) || $total <= 0) {
    echo json_encode(["success" => false, "message" => "Datos de venta incompletos"]);
    exit;
}

try {
    $conexion->beginTransaction();

    // 1. Crear la venta
    $stmt = $conexion->prepare("
        INSERT INTO ventas (usuario_id, total, estado)
        VALUES (:usuario_id, :total, 'completada')
    ");
    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':total' => $total
    ]);
    $venta_id = $conexion->lastInsertId();

    // 2. Insertar detalle y reducir stock
    foreach ($productos as $producto) {
        $producto_id = intval($producto['id']);
        $cantidad = intval($producto['cantidad']);
        $precio_unitario = floatval($producto['precio_unitario']);
        $subtotal = floatval($producto['subtotal']);

        // Insertar detalle
        $stmt = $conexion->prepare("
            INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal)
            VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario, :subtotal)
        ");
        $stmt->execute([
            ':venta_id' => $venta_id,
            ':producto_id' => $producto_id,
            ':cantidad' => $cantidad,
            ':precio_unitario' => $precio_unitario,
            ':subtotal' => $subtotal
        ]);

        // Reducir stock
        $stmt = $conexion->prepare("
            UPDATE productos SET stock = stock - :cantidad WHERE id = :id
        ");
        $stmt->execute([
            ':cantidad' => $cantidad,
            ':id' => $producto_id
        ]);

        // Registrar movimiento en inventario
        $stmt = $conexion->prepare("
            INSERT INTO movimientos_inventario (producto_id, tipo_movimiento, cantidad, motivo, usuario_id)
            VALUES (:producto_id, 'salida', :cantidad, :motivo, :usuario_id)
        ");
        $stmt->execute([
            ':producto_id' => $producto_id,
            ':cantidad' => $cantidad,
            ':motivo' => "Venta #$venta_id",
            ':usuario_id' => $usuario_id
        ]);
    }

    $conexion->commit();

    // Generar comprobante
    $comprobante = [
        'venta_id' => $venta_id,
        'total' => $total,
        'fecha' => date('d/m/Y H:i:s'),
        'vendedor' => $_SESSION['usuario_nombre'],
        'detalle' => $productos
    ];

    echo json_encode([
        "success" => true,
        "message" => "Venta registrada exitosamente",
        "venta_id" => $venta_id,
        "comprobante" => $comprobante
    ]);

} catch (PDOException $e) {
    $conexion->rollBack();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al registrar la venta: " . $e->getMessage()]);
}
