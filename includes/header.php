<?php
/**
 * HEADER - Cabecera HTML reutilizable
 * 
 * Se incluye en todas las paginas del sistema.
 * Contiene: DOCTYPE, head, apertura del body, container principal.
 */

// Asegurar que la sesion este iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determinar titulo de la pagina
$pagina_titulo = $pagina_titulo ?? "Panaderia";

// Cargar colores del tema desde la BD
$tema_colores = ['color_primario' => '#EC407A', 'color_primario_oscuro' => '#AD1457', 'color_fondo' => '#FCE4EC'];
try {
    require_once __DIR__ . '/../backend/config/conexion.php';
    $stmt_tema = $conexion->prepare("SELECT variable, valor FROM configuracion_tema");
    $stmt_tema->execute();
    while ($fila = $stmt_tema->fetch()) {
        $tema_colores[$fila['variable']] = $fila['valor'];
    }
} catch (Exception $e) {
    // Usar colores por defecto si hay error
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pagina_titulo; ?> - Panaderia</title>
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <style>
        :root {
            --color-primario: <?php echo $tema_colores['color_primario']; ?>;
            --color-primario-oscuro: <?php echo $tema_colores['color_primario_oscuro']; ?>;
            --color-fondo: <?php echo $tema_colores['color_fondo']; ?>;
        }
    </style>
    <script src="../frontend/js/app.js"></script>
</head>
<body>
    <div class="app-container">
