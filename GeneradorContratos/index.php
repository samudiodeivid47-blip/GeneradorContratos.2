<?php
require_once __DIR__ . "/config/conexionBDD.php";

// Si el proceso guardó datos/errores en sesión (formulario reenviado con
// errores), se recuperan aquí para repoblar el formulario. Se leen y se
// eliminan de inmediato: la sesión no debe retener información personal
// más tiempo del necesario para esta redirección.
$datosEmpresa    = $_SESSION["datosEmpresa"]    ?? [];
$datosTrabajador = $_SESSION["datosTrabajador"] ?? [];
$datosContrato   = $_SESSION["datosContrato"]   ?? [];
$todosLosErrores = $_SESSION["errores"]         ?? [];
$mensaje         = $_SESSION["mensaje"]         ?? "";

unset($_SESSION["datosEmpresa"], $_SESSION["datosTrabajador"], $_SESSION["datosContrato"], $_SESSION["errores"], $_SESSION["mensaje"]);

require __DIR__ . "/vistas/formulario.php";
?>
