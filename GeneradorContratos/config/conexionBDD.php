<?php
require_once __DIR__ . "/config.php";

$conexion = new mysqli(SERVIDOR, USUARIO, PASSWORD, BASE_DATOS);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>
