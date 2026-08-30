<?php
/**
 * PRODUCTOS - Crear nuevo producto
 * 
 * Recibe datos por POST y crea un nuevo producto.
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

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$stock_minimo = intval($_POST['stock_minimo'] ?? 5);
$categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;

// Validaciones
if (empty($nombre)) {
    echo json_encode(["success" => false, "message" => "El nombre es obligatorio"]);
    exit;
}

if ($precio < 0) {
    echo json_encode(["success" => false, "message" => "El precio no puede ser negativo"]);
    exit;
}

if ($stock < 0) {
    echo json_encode(["success" => false, "message" => "El stock no puede ser negativo"]);
    exit;
}

try {
    $stmt = $conexion->prepare("
        INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo, categoria_id)
        VALUES (:nombre, :descripcion, :precio, :stock, :stock_minimo, :categoria_id)
    ");

    $stmt->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion,
        ':precio' => $precio,
        ':stock' => $stock,
        ':stock_minimo' => $stock_minimo,
        ':categoria_id' => $categoria_id
    ]);

    $nuevoId = $conexion->lastInsertId();

    echo json_encode([
        "success" => true,
        "message" => "Producto creado exitosamente",
        "id" => $nuevoId
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error del servidor"]);
}
