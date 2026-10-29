<?php
/*
 * Reglas de validación para el registro de usuarios.
 * Retorna un arreglo campo => mensaje de error (vacío = sin errores).
 */
function validarRegistroUsuario($datos, $clave, $clave2)
{
    $errores = [];

    $nombre = trim($datos["nombre_completo"] ?? "");
    if (mb_strlen($nombre) < 3) {
        $errores["nombre_completo"] = "Escriba su nombre completo (mínimo 3 caracteres).";
    }

    $usuario = trim($datos["nombre_usuario"] ?? "");
    if (!preg_match('/^[A-Za-z0-9_]{4,30}$/', $usuario)) {
        $errores["nombre_usuario"] = "El usuario debe tener de 4 a 30 caracteres: solo letras, números y guion bajo.";
    }

    if (strlen($clave) < 8) {
        $errores["clave"] = "La contraseña debe tener al menos 8 caracteres.";
    } elseif (strlen($clave) > 72) {
        // bcrypt (el algoritmo por defecto de password_hash) solo usa 72 bytes.
        $errores["clave"] = "La contraseña no puede superar 72 caracteres.";
    } elseif (!preg_match('/[A-Za-z]/', $clave) || !preg_match('/[0-9]/', $clave)) {
        $errores["clave"] = "La contraseña debe combinar letras y números.";
    }

    if ($clave !== $clave2) {
        $errores["clave2"] = "Las contraseñas no coinciden.";
    }

    return $errores;
}
?>
