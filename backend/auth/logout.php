<?php
/**
 * LOGOUT
 * 
 * Destruye la sesion y redirige al login.
 */

session_start();
session_unset();
session_destroy();
header("Location: ../pages/login.php");
exit;
