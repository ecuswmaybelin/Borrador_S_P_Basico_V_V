<?php
/**
 * REPORTES - Ventas por fecha
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

$fecha_inicio = $_GET['inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fin'] ?? date('Y-m-d');

try {
    $stmt = $conexion->prepare("
        SELECT v.id, v.total, v.estado, v.fecha_venta, u.nombre as vendedor
        FROM ventas v
        JOIN usuarios u ON v.usuario_id = u.id
        WHERE DATE(v.fecha_venta) BETWEEN :inicio AND :fin
        ORDER BY v.fecha_venta DESC
    ");
    $stmt->execute([':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
    $ventas = $stmt->fetchAll();

    // Calcular total
    $total = array_sum(array_column($ventas, 'total'));

    echo json_encode([
        "ventas" => $ventas,
        "total" => number_format($total, 2),
        "cantidad" => count($ventas)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor"]);
}
