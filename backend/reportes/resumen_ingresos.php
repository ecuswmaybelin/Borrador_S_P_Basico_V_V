<?php
/**
 * DASHBOARD - API de datos
 * 
 * Devuelve estadisticas del dashboard en formato JSON.
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
    // Ventas de hoy
    $stmt = $conexion->prepare("
        SELECT COUNT(*) as total, COALESCE(SUM(total), 0) as ingresos
        FROM ventas
        WHERE DATE(fecha_venta) = CURRENT_DATE AND estado = 'completada'
    ");
    $stmt->execute();
    $ventasHoy = $stmt->fetch();

    // Total productos activos
    $stmt = $conexion->query("SELECT COUNT(*) as total FROM productos WHERE estado = 'activo'");
    $totalProductos = $stmt->fetch();

    // Productos con stock bajo
    $stmt = $conexion->query("SELECT COUNT(*) as total FROM productos WHERE stock <= stock_minimo AND estado = 'activo'");
    $stockBajo = $stmt->fetch();

    // Ultimas 5 ventas
    $stmt = $conexion->query("
        SELECT v.id, u.nombre as vendedor, v.total, v.fecha_venta, v.estado
        FROM ventas v
        JOIN usuarios u ON v.usuario_id = u.id
        ORDER BY v.fecha_venta DESC
        LIMIT 5
    ");
    $ultimasVentas = $stmt->fetchAll();

    echo json_encode([
        "ventasHoy" => (int)$ventasHoy['total'],
        "ingresosHoy" => number_format($ventasHoy['ingresos'], 2),
        "totalProductos" => (int)$totalProductos['total'],
        "stockBajo" => (int)$stockBajo['total'],
        "ultimasVentas" => $ultimasVentas
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor"]);
}
