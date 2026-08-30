<?php
/**
 * VENTAS - Listar ventas
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
        SELECT v.id, v.total, v.estado, v.fecha_venta, u.nombre as vendedor
        FROM ventas v
        JOIN usuarios u ON v.usuario_id = u.id
        ORDER BY v.fecha_venta DESC
        LIMIT 50
    ");
    $ventas = $stmt->fetchAll();

    echo json_encode($ventas);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor"]);
}
