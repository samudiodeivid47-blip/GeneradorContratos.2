<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../logica/validarUsuario.php";
require_once __DIR__ . "/../datos/usuarioDAO.php";

/*
 * Administración de usuarios (solo administrador):
 * crear cuenta, cambiar perfil (rol), activar/desactivar, desbloquear.
 * Protecciones: no puede quitarse a sí mismo el rol ni desactivarse, y
 * siempre debe quedar al menos un administrador activo.
 */
requerirRol(["administrador"], "../vistas/login.php", "../index.php");
exigirPost("../vistas/usuarios.php");
exigirCsrf("../vistas/usuarios.php");

$accion = $_POST["accion"] ?? "";
$yo = (int) usuarioActual()["id"];
$id = (isset($_POST["id"]) && ctype_digit((string) $_POST["id"])) ? (int) $_POST["id"] : 0;
$roles = ["administrador", "supervisor", "usuario"];

try {
    if ($accion === "crear") {
        $datos = [
            "nombre_completo" => is_string($_POST["nombre_completo"] ?? null) ? trim($_POST["nombre_completo"]) : "",
            "nombre_usuario"  => is_string($_POST["nombre_usuario"] ?? null) ? trim($_POST["nombre_usuario"]) : "",
            "correo"          => is_string($_POST["correo"] ?? null) ? trim($_POST["correo"]) : "",
        ];
        $rol = $_POST["rol"] ?? "usuario";
        $clave = is_string($_POST["clave"] ?? null) ? $_POST["clave"] : "";
        // La política la acepta el propio usuario en su primer ingreso: aquí se marca como aceptada para pasar la validación.
        $errores = validarRegistroUsuario($datos, $clave, $clave, true);
        if (!in_array($rol, $roles, true)) {
            $errores["rol"] = "Perfil no válido.";
        }
        if (empty($errores["nombre_usuario"]) && buscarUsuarioPorNombre($conexion, $datos["nombre_usuario"])) {
            $errores["nombre_usuario"] = "Ese nombre de usuario ya está en uso.";
        }
        if (!empty($errores)) {
            guardarFormulario("usuario_nuevo", $datos + ["rol" => $rol], $errores);
            header("Location: ../vistas/usuarios.php#nuevo");
            exit();
        }
        $nuevoId = insertarUsuario($conexion, $datos["nombre_usuario"], $datos["nombre_completo"], $datos["correo"],
                                   password_hash($clave, PASSWORD_DEFAULT), $rol, null);
        registrarBitacora($conexion, "usuario_crear", "usuario", $nuevoId ?: null, $datos["nombre_usuario"] . " con perfil " . $rol);
        flash("ok", "Cuenta creada. El usuario deberá aceptar la política de datos en su primer ingreso y cambiar la clave temporal.");
    } else {
        $objetivo = $id ? buscarUsuarioPorId($conexion, $id) : null;
        if (!$objetivo) {
            flash("error", "El usuario no existe.");
        } elseif ($accion === "rol") {
            $rol = $_POST["rol"] ?? "";
            if (!in_array($rol, $roles, true)) {
                flash("error", "Perfil no válido.");
            } elseif ($id === $yo) {
                flash("error", "No puede cambiar su propio perfil (evita quedarse sin administrador).");
            } elseif ($objetivo["rol"] === "administrador" && $rol !== "administrador" && contarAdministradoresActivos($conexion) <= 1) {
                flash("error", "Debe quedar al menos un administrador activo.");
            } else {
                cambiarRolUsuario($conexion, $id, $rol);
                registrarBitacora($conexion, "usuario_rol", "usuario", $id, $objetivo["nombre_usuario"] . ": " . $objetivo["rol"] . " → " . $rol);
                flash("ok", "Perfil de " . $objetivo["nombre_usuario"] . " cambiado a " . nombreRol($rol) . ".");
            }
        } elseif ($accion === "activo") {
            $activo = !empty($_POST["activo"]) ? 1 : 0;
            if ($id === $yo) {
                flash("error", "No puede desactivar su propia cuenta.");
            } elseif ($objetivo["rol"] === "administrador" && !$activo && contarAdministradoresActivos($conexion) <= 1) {
                flash("error", "Debe quedar al menos un administrador activo.");
            } else {
                cambiarActivoUsuario($conexion, $id, $activo);
                registrarBitacora($conexion, $activo ? "usuario_activar" : "usuario_desactivar", "usuario", $id, $objetivo["nombre_usuario"]);
                flash("ok", "Cuenta de " . $objetivo["nombre_usuario"] . ($activo ? " activada." : " desactivada. Sus contratos se conservan."));
            }
        } elseif ($accion === "desbloquear") {
            desbloquearUsuario($conexion, $id);
            registrarBitacora($conexion, "usuario_desbloquear", "usuario", $id, $objetivo["nombre_usuario"]);
            flash("ok", "Cuenta de " . $objetivo["nombre_usuario"] . " desbloqueada.");
        } else {
            flash("error", "Acción no válida.");
        }
    }
} catch (Throwable $e) {
    error_log("gestionarUsuario: " . $e->getMessage());
    flash("error", "No fue posible completar la acción.");
}

header("Location: ../vistas/usuarios.php");
exit();
?>
