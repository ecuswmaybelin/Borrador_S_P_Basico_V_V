<?php
/**
 * INVENTARIO - Listar movimientos
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
        SELECT m.*, p.nombre as producto_nombre, u.nombre as usuario_nombre
        FROM movimientos_inventario m
        JOIN productos p ON m.producto_id = p.id
        JOIN usuarios u ON m.usuario_id = u.id
        ORDER BY m.fecha_movimiento DESC
        LIMIT 50
    ");
    $movimientos = $stmt->fetchAll();

    echo json_encode($movimientos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor"]);
}
