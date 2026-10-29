<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../datos/usuarioDAO.php";

requerirLogin("../vistas/login.php");
exigirPost("../index.php");
exigirCsrf("../index.php");

aceptarPoliticaUsuario($conexion, (int) usuarioActual()["id"], POLITICA_VERSION);
$_SESSION["usuario"]["politica_ok"] = true;
registrarBitacora($conexion, "politica_aceptada", "usuario", (int) usuarioActual()["id"], "Versión " . POLITICA_VERSION);
flash("ok", "Gracias. Quedó registrada su aceptación de la política de tratamiento de datos.");
header("Location: ../index.php");
exit();
?>
