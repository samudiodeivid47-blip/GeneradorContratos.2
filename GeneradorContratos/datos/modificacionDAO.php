<?php
/*
 * DAO de modificaciones (auditoría).
 * Guarda y consulta el historial de cambios hechos a un contrato.
 */

function insertarModificacion($conexion, $contratoId, $usuarioId, $campo, $valorAnterior, $valorNuevo, $motivo)
{
    $stmt = $conexion->prepare(
        "INSERT INTO contrato_modificacion
            (contrato_id, usuario_id, campo, valor_anterior, valor_nuevo, motivo)
         VALUES (?,?,?,?,?,?)"
    );
    $stmt->bind_param("iissss", $contratoId, $usuarioId, $campo, $valorAnterior, $valorNuevo, $motivo);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function actualizarFunciones($conexion, $contratoId, $funciones)
{
    $stmt = $conexion->prepare("UPDATE contrato SET funciones = ? WHERE id = ?");
    $stmt->bind_param("si", $funciones, $contratoId);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function listarModificaciones($conexion, $contratoId)
{
    $stmt = $conexion->prepare(
        "SELECT m.*, u.nombre_completo, u.nombre_usuario
         FROM contrato_modificacion m
         INNER JOIN usuario u ON m.usuario_id = u.id
         WHERE m.contrato_id = ?
         ORDER BY m.id DESC"
    );
    $stmt->bind_param("i", $contratoId);
    $stmt->execute();
    $filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $filas;
}
?>
