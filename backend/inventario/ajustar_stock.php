<?php
/**
 * INVENTARIO - Ajustar stock
 * 
 * Registra un movimiento y actualiza el stock del producto.
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

$producto_id = intval($_POST['producto_id'] ?? 0);
$tipo = $_POST['tipo'] ?? '';
$cantidad = intval($_POST['cantidad'] ?? 0);
$motivo = trim($_POST['motivo'] ?? '');
$usuario_id = $_SESSION['usuario_id'];

// Validaciones
if ($producto_id <= 0) {
    echo json_encode(["success" => false, "message" => "Seleccione un producto"]);
    exit;
}

if (!in_array($tipo, ['entrada', 'salida', 'ajuste'])) {
    echo json_encode(["success" => false, "message" => "Tipo de movimiento no valido"]);
    exit;
}

if ($cantidad <= 0) {
    echo json_encode(["success" => false, "message" => "La cantidad debe ser mayor a 0"]);
    exit;
}

try {
    $conexion->beginTransaction();

    // Obtener stock actual
    $stmt = $conexion->prepare("SELECT stock FROM productos WHERE id = :id");
    $stmt->execute([':id' => $producto_id]);
    $producto = $stmt->fetch();

    if (!$producto) {
        $conexion->rollBack();
        echo json_encode(["success" => false, "message" => "Producto no encontrado"]);
        exit;
    }

    // Calcular nuevo stock
    $stockActual = $producto['stock'];
    switch ($tipo) {
        case 'entrada':
            $nuevoStock = $stockActual + $cantidad;
            break;
        case 'salida':
            $nuevoStock = $stockActual - $cantidad;
            if ($nuevoStock < 0) $nuevoStock = 0;
            break;
        case 'ajuste':
            $nuevoStock = $cantidad; // En ajuste, la cantidad es el nuevo valor
            break;
    }

    // Actualizar stock
    $stmt = $conexion->prepare("UPDATE productos SET stock = :stock WHERE id = :id");
    $stmt->execute([':stock' => $nuevoStock, ':id' => $producto_id]);

    // Registrar movimiento
    $stmt = $conexion->prepare("
        INSERT INTO movimientos_inventario (producto_id, tipo_movimiento, cantidad, motivo, usuario_id)
        VALUES (:producto_id, :tipo, :cantidad, :motivo, :usuario_id)
    ");
    $stmt->execute([
        ':producto_id' => $producto_id,
        ':tipo' => $tipo,
        ':cantidad' => $cantidad,
        ':motivo' => $motivo,
        ':usuario_id' => $usuario_id
    ]);

    $conexion->commit();

    echo json_encode([
        "success" => true,
        "message" => "Stock actualizado exitosamente",
        "nuevo_stock" => $nuevoStock
    ]);

} catch (PDOException $e) {
    $conexion->rollBack();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error del servidor"]);
}
