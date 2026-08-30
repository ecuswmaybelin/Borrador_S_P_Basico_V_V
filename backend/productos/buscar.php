<?php
/**
 * PRODUCTOS - Buscar por nombre
 * 
 * Busca productos cuyo nombre contenga el texto de busqueda.
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

$busqueda = trim($_GET['q'] ?? '');

if (empty($busqueda)) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $conexion->prepare("
        SELECT p.*, c.nombre as categoria_nombre
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.estado = 'activo'
        AND (p.nombre LIKE :busqueda OR p.descripcion LIKE :busqueda2)
        ORDER BY p.nombre ASC
        LIMIT 20
    ");

    $param = "%$busqueda%";
    $stmt->execute([':busqueda' => $param, ':busqueda2' => $param]);
    $productos = $stmt->fetchAll();

    echo json_encode($productos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor"]);
}
