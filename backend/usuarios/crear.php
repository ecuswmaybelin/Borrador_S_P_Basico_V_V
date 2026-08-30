<?php
/**
 * USUARIOS - Crear nuevo usuario
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

$nombre = trim($_POST['nombre'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';
$rol = $_POST['rol'] ?? 'vendedor';
$estado = $_POST['estado'] ?? 'activo';

// Validaciones
if (empty($nombre) || empty($usuario) || empty($contrasena)) {
    echo json_encode(["success" => false, "message" => "Nombre, usuario y contrasena son obligatorios"]);
    exit;
}

if (!in_array($rol, ['admin', 'vendedor'])) {
    echo json_encode(["success" => false, "message" => "Rol no valido"]);
    exit;
}

if (strlen($contrasena) < 6) {
    echo json_encode(["success" => false, "message" => "La contrasena debe tener al menos 6 caracteres"]);
    exit;
}

try {
    // Verificar que el usuario no exista
    $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = :usuario");
    $stmt->execute([':usuario' => $usuario]);
    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Este nombre de usuario ya existe"]);
        exit;
    }

    $hash = password_hash($contrasena, PASSWORD_DEFAULT);

    $stmt = $conexion->prepare("
        INSERT INTO usuarios (nombre, usuario, contrasena, rol, estado)
        VALUES (:nombre, :usuario, :contrasena, :rol, :estado)
    ");

    $stmt->execute([
        ':nombre' => $nombre,
        ':usuario' => $usuario,
        ':contrasena' => $hash,
        ':rol' => $rol,
        ':estado' => $estado
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Usuario creado exitosamente"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error del servidor"]);
}
