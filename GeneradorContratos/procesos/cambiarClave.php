<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../logica/validarUsuario.php";
require_once __DIR__ . "/../datos/usuarioDAO.php";

// Cambio de contraseña: exige la contraseña actual y aplica la regla de contraseña segura.
requerirLogin("../vistas/login.php");
exigirPost("../vistas/perfil.php");
exigirCsrf("../vistas/perfil.php");

$actual = is_string($_POST["clave_actual"] ?? null) ? $_POST["clave_actual"] : "";
$nueva  = is_string($_POST["clave_nueva"] ?? null) ? $_POST["clave_nueva"] : "";
$nueva2 = is_string($_POST["clave_nueva2"] ?? null) ? $_POST["clave_nueva2"] : "";

$u = buscarUsuarioPorId($conexion, (int) usuarioActual()["id"]);
$errores = [];

if (!$u || !password_verify($actual, $u["clave_hash"])) {
    $errores["clave_actual"] = "La contraseña actual no es correcta.";
}
$errorNueva = validarClaveSegura($nueva, $u["nombre_usuario"] ?? "");
if ($errorNueva) {
    $errores["clave_nueva"] = $errorNueva;
} elseif ($nueva === $actual) {
    $errores["clave_nueva"] = "La nueva contraseña debe ser diferente de la actual.";
}
if ($nueva !== $nueva2) {
    $errores["clave_nueva2"] = "Las contraseñas no coinciden.";
}

if (!empty($errores)) {
    guardarFormulario("clave", [], $errores);
    header("Location: ../vistas/perfil.php#clave");
    exit();
}

actualizarClave($conexion, (int) $u["id"], password_hash($nueva, PASSWORD_DEFAULT));
session_regenerate_id(true);   // el identificador de sesión cambia al cambiar la clave
registrarBitacora($conexion, "clave_cambiar", "usuario", (int) $u["id"], "Contraseña cambiada");
flash("ok", "Contraseña cambiada correctamente.");
header("Location: ../vistas/perfil.php");
exit();
?>
