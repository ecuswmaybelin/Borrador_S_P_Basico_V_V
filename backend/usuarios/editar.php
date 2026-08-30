<?php
/**
 * USUARIOS - Editar usuario
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
$nombre = trim($_POST['nombre'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';
$rol = $_POST['rol'] ?? 'vendedor';
$estado = $_POST['estado'] ?? 'activo';

if (empty($nombre) || empty($usuario) || $id <= 0) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

try {
    // Verificar que el usuario no exista para otro ID
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = :usuario AND id != :id");
    $stmt->execute([':usuario' => $usuario, ':id' => $id]);
    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Este nombre de usuario ya existe"]);
        exit;
    }

    // Si se proporciona contrasena, actualizarla
    if (!empty($contrasena)) {
        if (strlen($contrasena) < 6) {
            echo json_encode(["success" => false, "message" => "La contrasena debe tener al menos 6 caracteres"]);
            exit;
        }
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare("
            UPDATE usuarios SET nombre = :nombre, usuario = :usuario, contrasena = :contrasena,
                   rol = :rol, estado = :estado
            WHERE id = :id
        ");
        $stmt->execute([
            ':id' => $id, ':nombre' => $nombre, ':usuario' => $usuario,
            ':contrasena' => $hash, ':rol' => $rol, ':estado' => $estado
        ]);
    } else {
        $stmt = $conexion->prepare("
            UPDATE usuarios SET nombre = :nombre, usuario = :usuario,
                   rol = :rol, estado = :estado
            WHERE id = :id
        ");
        $stmt->execute([
            ':id' => $id, ':nombre' => $nombre, ':usuario' => $usuario,
            ':rol' => $rol, ':estado' => $estado
        ]);
    }

    echo json_encode([
        "success" => true,
        "message" => "Usuario actualizado exitosamente"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error del servidor"]);
}
