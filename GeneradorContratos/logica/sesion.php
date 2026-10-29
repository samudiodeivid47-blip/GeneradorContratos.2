<?php
/*
 * Funciones de sesión y control de acceso.
 * La sesión (session_start) ya se inicia en config/config.php.
 *
 * $_SESSION["usuario"] guarda solo lo necesario: id, nombre de usuario,
 * nombre completo y rol. La clave nunca se guarda en la sesión.
 */

function usuarioActual()
{
    return $_SESSION["usuario"] ?? null;
}

function estaAutenticado()
{
    return !empty($_SESSION["usuario"]["id"]);
}

function esAdministrador()
{
    return estaAutenticado() && ($_SESSION["usuario"]["rol"] ?? "") === "administrador";
}

function esSupervisor()
{
    return estaAutenticado() && in_array(($_SESSION["usuario"]["rol"] ?? ""), ["administrador", "supervisor"], true);
}

function puedeGestionarUsuarios()
{
    return esAdministrador();
}

function requerirRol($rolesPermitidos, $rutaInicio = "../index.php")
{
    if (!estaAutenticado()) {
        header("Location: ../vistas/login.php");
        exit();
    }
    if (!in_array($_SESSION["usuario"]["rol"] ?? "", $rolesPermitidos, true)) {
        http_response_code(403);
        exit("Acceso denegado: no tiene permisos para esta función.");
    }
}

function tokenCSRF()
{
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

function validarCSRF($token)
{
    return is_string($token) && !empty($_SESSION["csrf_token"]) && hash_equals($_SESSION["csrf_token"], $token);
}

// Si no hay sesión iniciada, manda al login y detiene la ejecución.
// $rutaLogin cambia según desde qué carpeta se llame (ver cada archivo).
function requerirLogin($rutaLogin)
{
    if (!estaAutenticado()) {
        $_SESSION["mensaje_login"] = "Inicie sesión para continuar.";
        header("Location: " . $rutaLogin);
        exit();
    }
}

// Un usuario normal solo puede ver/editar los contratos que él generó.
// El administrador puede ver todos.
function puedeVerContrato($contrato)
{
    if (esSupervisor() || esAdministrador()) {
        return true;
    }
    $usuario = usuarioActual();
    return $usuario !== null
        && isset($contrato["usuario_id"])
        && (int) $contrato["usuario_id"] === (int) $usuario["id"];
}
?>
