<?php
/**
 * INVENTARIO - Listar stock
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

try {
    $stmt = $conexion->query("
        SELECT p.id, p.nombre, p.stock, p.stock_minimo, c.nombre as categoria_nombre
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.estado = 'activo'
        ORDER BY p.nombre ASC
    ");
    $productos = $stmt->fetchAll();

    echo json_encode($productos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor"]);
}
