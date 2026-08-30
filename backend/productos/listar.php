<?php
/**
 * PRODUCTOS - Listar todos
 * 
 * Devuelve lista de productos en JSON.
 * Soporta filtro por categoria.
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
    $categoria = $_GET['categoria'] ?? '';

    $sql = "
        SELECT p.*, c.nombre as categoria_nombre
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.estado = 'activo'
    ";

    $params = [];

    if (!empty($categoria)) {
        $sql .= " AND p.categoria_id = :categoria";
        $params[':categoria'] = $categoria;
    }

    $sql .= " ORDER BY p.nombre ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll();

    echo json_encode($productos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor"]);
}
