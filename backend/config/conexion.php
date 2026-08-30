<?php
/**
 * CONEXIÓN A LA BASE DE DATOS
 * Compatible con Render y entorno local
 */

// Datos de conexión mediante variables de entorno
$db_servidor = getenv("DB_HOST") ?: "localhost";
$db_puerto   = getenv("DB_PORT") ?: "5432";
$db_usuario  = getenv("DB_USER") ?: "postgres";
$db_contrasena = getenv("DB_PASS") ?: "2222";
$db_nombre   = getenv("DB_NAME") ?: "panaderia";

try {

    $conexion = new PDO(
        "pgsql:host=$db_servidor;port=$db_puerto;dbname=$db_nombre",
        $db_usuario,
        $db_contrasena,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $e) {

    // Registrar el error en los logs de Render
    error_log("Error de conexión PostgreSQL: " . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        "error" => "Error de conexión con la base de datos"
    ]);

    exit;
}
