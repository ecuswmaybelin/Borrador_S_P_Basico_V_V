<?php
/**
 * USUARIOS - Listar todos
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

try {
    $stmt = $conexion->query("
        SELECT id, nombre, usuario, rol, estado, fecha_creacion, ultimo_acceso
        FROM usuarios
        ORDER BY nombre ASC
    ");
    $usuarios = $stmt->fetchAll();

    echo json_encode($usuarios);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor"]);
}
