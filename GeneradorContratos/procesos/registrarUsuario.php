<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../datos/usuarioDAO.php";
require_once __DIR__ . "/../logica/validarUsuario.php";

/*
 * Registro de un usuario nuevo.
 * - Valida los datos en el servidor.
 * - Guarda la contraseña con password_hash (nunca en texto plano).
 * - El primer usuario registrado queda como "administrador".
 */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../vistas/registro.php");
    exit();
}

$datos = [
    "nombre_completo" => trim($_POST["nombre_completo"] ?? ""),
    "nombre_usuario"  => trim($_POST["nombre_usuario"] ?? ""),
];
$clave  = $_POST["clave"] ?? "";
$clave2 = $_POST["clave2"] ?? "";

$errores = validarRegistroUsuario($datos, $clave, $clave2);

if (empty($errores["nombre_usuario"]) && buscarUsuarioPorNombre($conexion, $datos["nombre_usuario"])) {
    $errores["nombre_usuario"] = "Ese nombre de usuario ya está en uso. Elija otro.";
}

if (!empty($errores)) {
    // Se devuelven los datos escritos, pero NUNCA las contraseñas.
    $_SESSION["datos_registro"] = $datos;
    $_SESSION["errores_registro"] = $errores;
    header("Location: ../vistas/registro.php");
    exit();
}

$rol = contarUsuarios($conexion) === 0 ? "administrador" : "usuario";
$claveHash = password_hash($clave, PASSWORD_DEFAULT);

$id = insertarUsuario($conexion, $datos["nombre_usuario"], $datos["nombre_completo"], $claveHash, $rol);

if (!$id) {
    $_SESSION["datos_registro"] = $datos;
    $_SESSION["errores_registro"] = ["nombre_usuario" => "No fue posible crear la cuenta. Intente de nuevo."];
    header("Location: ../vistas/registro.php");
    exit();
}

$_SESSION["mensaje_login"] = "Cuenta creada correctamente. Ya puede iniciar sesión.";
header("Location: ../vistas/login.php");
exit();
?>
