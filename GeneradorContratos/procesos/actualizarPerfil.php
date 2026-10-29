<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../logica/validarUsuario.php";
require_once __DIR__ . "/../datos/usuarioDAO.php";

requerirLogin("../vistas/login.php");
exigirPost("../vistas/perfil.php");
exigirCsrf("../vistas/perfil.php");

$datos = [];
foreach (["nombre_completo", "correo", "telefono", "cargo"] as $c) {
    $datos[$c] = is_string($_POST[$c] ?? null) ? trim($_POST[$c]) : "";
}
$errores = validarPerfil($datos);

if (!empty($errores)) {
    guardarFormulario("perfil", $datos, $errores);
    header("Location: ../vistas/perfil.php");
    exit();
}

$id = (int) usuarioActual()["id"];
actualizarPerfil($conexion, $id, $datos["nombre_completo"], $datos["correo"], $datos["telefono"], $datos["cargo"]);
$_SESSION["usuario"]["nombre_completo"] = $datos["nombre_completo"];
registrarBitacora($conexion, "perfil_editar", "usuario", $id, "Datos de perfil actualizados");
flash("ok", "Perfil actualizado.");
header("Location: ../vistas/perfil.php");
exit();
?>
