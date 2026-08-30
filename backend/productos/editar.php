<?php
/**
 * PRODUCTOS - Editar producto existente
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Metodo no permitido"]);
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

$id = intval($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$stock_minimo = intval($_POST['stock_minimo'] ?? 5);
$categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;

if (empty($nombre) || $id <= 0) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

try {
    $stmt = $conexion->prepare("
        UPDATE productos
        SET nombre = :nombre, descripcion = :descripcion, precio = :precio,
            stock = :stock, stock_minimo = :stock_minimo, categoria_id = :categoria_id
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id,
        ':nombre' => $nombre,
        ':descripcion' => $descripcion,
        ':precio' => $precio,
        ':stock' => $stock,
        ':stock_minimo' => $stock_minimo,
        ':categoria_id' => $categoria_id
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Producto actualizado exitosamente"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error del servidor"]);
}
