<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../datos/usuarioDAO.php";

/*
 * Inicio de sesión.
 * El mensaje de error es el mismo si el usuario no existe o si la clave
 * es incorrecta, para no revelar qué usuarios están registrados.
 */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../vistas/login.php");
    exit();
}

$nombreUsuario = trim($_POST["nombre_usuario"] ?? "");
$clave = $_POST["clave"] ?? "";

$usuario = $nombreUsuario !== "" ? buscarUsuarioPorNombre($conexion, $nombreUsuario) : null;

if ($usuario && (int) $usuario["activo"] === 1 && password_verify($clave, $usuario["clave_hash"])) {
    // Se genera un identificador de sesión nuevo al autenticarse
    // (previene la fijación de sesión).
    session_regenerate_id(true);

    $_SESSION["usuario"] = [
        "id"              => (int) $usuario["id"],
        "nombre_usuario"  => $usuario["nombre_usuario"],
        "nombre_completo" => $usuario["nombre_completo"],
        "rol"             => $usuario["rol"],
    ];

    header("Location: ../index.php");
    exit();
}

$_SESSION["error_login"] = "Usuario o contraseña incorrectos.";
$_SESSION["login_usuario"] = $nombreUsuario;
header("Location: ../vistas/login.php");
exit();
?>
