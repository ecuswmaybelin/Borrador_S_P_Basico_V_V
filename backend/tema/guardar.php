<?php
/**
 * TEMA - Guardar configuracion de colores
 * 
 * Actualiza los 3 colores principales del tema.
 * Solo administradores pueden usar este endpoint.
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

if ($_SESSION['usuario_rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado - solo administradores"]);
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

try {
    $datos = json_decode(file_get_contents('php://input'), true);

    if (!$datos) {
        http_response_code(400);
        echo json_encode(["error" => "Datos no validos"]);
        exit;
    }

    $colores_permitidos = ['color_primario', 'color_primario_oscuro', 'color_fondo'];
    $stmt = $conexion->prepare("UPDATE configuracion_tema SET valor = :valor WHERE variable = :variable");

    foreach ($colores_permitidos as $color) {
        if (isset($datos[$color])) {
            $valor = $datos[$color];
            if (preg_match('/^#[0-9A-Fa-f]{6}$/', $valor)) {
                $stmt->execute([':valor' => $valor, ':variable' => $color]);
            }
        }
    }

    echo json_encode(["mensaje" => "Colores guardados correctamente"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor"]);
}
