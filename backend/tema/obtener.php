<?php
/**
 * TEMA - Obtener configuracion de colores
 * 
 * Devuelve los 3 colores principales del tema en JSON.
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
    $stmt = $conexion->prepare("SELECT variable, valor FROM configuracion_tema");
    $stmt->execute();
    $filas = $stmt->fetchAll();

    $tema = [];
    foreach ($filas as $fila) {
        $tema[$fila['variable']] = $fila['valor'];
    }

    echo json_encode($tema);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor"]);
}
