<?php
/**
 * LOGIN - PROCESAMIENTO (con debug)
 */

session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Metodo no permitido"]);
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';

if (empty($usuario) || empty($contrasena)) {
    echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios"]);
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

try {
    $stmt = $conexion->prepare("SELECT id, nombre, usuario, contrasena, rol, estado FROM usuarios WHERE usuario = :usuario LIMIT 1");
    $stmt->execute([':usuario' => $usuario]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(["success" => false, "message" => "Usuario o contrasena incorrectos"]);
        exit;
    }

    if ($user['estado'] !== 'activo') {
        echo json_encode(["success" => false, "message" => "Cuenta deshabilitada"]);
        exit;
    }

    if (!password_verify($contrasena, $user['contrasena'])) {
        echo json_encode(["success" => false, "message" => "Usuario o contrasena incorrectos"]);
        exit;
    }

    $stmt = $conexion->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id");
    $stmt->execute([':id' => $user['id']]);

    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['usuario_nombre'] = $user['nombre'];
    $_SESSION['usuario_usuario'] = $user['usuario'];
    $_SESSION['usuario_rol'] = $user['rol'];

    echo json_encode([
        "success" => true,
        "message" => "Inicio de sesion exitoso",
        "redirect" => $user['rol'] === 'admin' ? "../pages/index.php" : "../pages/ventas.php",
        "rol" => $user['rol']
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error del servidor"]);
}
