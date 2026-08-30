<?php
/**
 * REPORTES - Productos mas vendidos
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
        SELECT p.nombre, SUM(dv.cantidad) as total_vendidos,
               SUM(dv.subtotal) as total_ingresos
        FROM detalle_venta dv
        JOIN productos p ON dv.producto_id = p.id
        JOIN ventas v ON dv.venta_id = v.id
        WHERE v.estado = 'completada'
        GROUP BY p.id, p.nombre
        ORDER BY total_vendidos DESC
        LIMIT 10
    ");
    $productos = $stmt->fetchAll();

    echo json_encode($productos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor"]);
}
