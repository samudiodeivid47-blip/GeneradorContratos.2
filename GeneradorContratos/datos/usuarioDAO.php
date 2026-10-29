<?php
/*
 * DAO de usuario.
 * Concentra todo el acceso SQL a la tabla usuario.
 */

function buscarUsuarioPorNombre($conexion, $nombreUsuario)
{
    $stmt = $conexion->prepare("SELECT * FROM usuario WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $nombreUsuario);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $fila;
}

function contarUsuarios($conexion)
{
    $fila = $conexion->query("SELECT COUNT(*) AS total FROM usuario")->fetch_assoc();
    return (int) $fila["total"];
}

// Devuelve el id nuevo o false si no se pudo insertar
// (por ejemplo, si el nombre de usuario ya existe).
function insertarUsuario($conexion, $nombreUsuario, $nombreCompleto, $claveHash, $rol)
{
    try {
        $stmt = $conexion->prepare(
            "INSERT INTO usuario (nombre_completo, nombre_usuario, clave_hash, rol)
             VALUES (?,?,?,?)"
        );
        $stmt->bind_param("ssss", $nombreCompleto, $nombreUsuario, $claveHash, $rol);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id ?: false;
    } catch (Throwable $e) {
        return false;
    }
}
?>
