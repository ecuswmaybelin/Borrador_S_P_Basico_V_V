<?php
/**
 * USUARIOS - Eliminar usuario (soft delete)
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Metodo no permitido"]);
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ID no valido"]);
    exit;
}

// No permitir eliminarse a si mismo
if ($id == $_SESSION['usuario_id']) {
    echo json_encode(["success" => false, "message" => "No puedes deshabilitar tu propia cuenta"]);
    exit;
}

try {
    $stmt = $conexion->prepare("UPDATE usuarios SET estado = 'inactivo' WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode([
        "success" => true,
        "message" => "Usuario deshabilitado exitosamente"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error del servidor"]);
}
