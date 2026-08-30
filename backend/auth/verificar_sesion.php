<?php
/**
 * VERIFICAR SESION
 * 
 * Se incluye en cada pagina protegida.
 * Verifica que el usuario haya iniciado sesion.
 * Si no ha iniciado sesion, lo redirige al login.
 */

// Iniciar sesion si no esta activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay sesion activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

// Variables de sesion disponibles en todas las paginas
$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario_nombre'];
$usuario_rol = $_SESSION['usuario_rol'];
