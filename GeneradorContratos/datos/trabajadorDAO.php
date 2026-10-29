<?php
/*
 * DAO de trabajador.
 * Concentra todo el acceso SQL a la tabla trabajador.
 */

function insertarTrabajador($conexion, $datos)
{
    $stmt = $conexion->prepare(
        "INSERT INTO trabajador
            (nombre_completo, tipo_documento, numero_documento,
             fecha_nacimiento, direccion, ciudad, telefono, correo,
             eps, fondo_pension)
         VALUES (?,?,?,?,?,?,?,?,?,?)"
    );

    $stmt->bind_param(
        "ssssssssss",
        $datos["nombre_completo"],
        $datos["tipo_documento"],
        $datos["numero_documento"],
        $datos["fecha_nacimiento"],
        $datos["direccion"],
        $datos["ciudad"],
        $datos["telefono"],
        $datos["correo"],
        $datos["eps"],
        $datos["fondo_pension"]
    );

    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();

    return $id ?: false;
}

function buscarTrabajadorPorDocumento($conexion, $tipoDocumento, $numeroDocumento)
{
    $stmt = $conexion->prepare(
        "SELECT * FROM trabajador WHERE tipo_documento = ? AND numero_documento = ?"
    );
    $stmt->bind_param("ss", $tipoDocumento, $numeroDocumento);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $stmt->close();
    return $fila;
}

function buscarTrabajadorPorId($conexion, $id)
{
    $stmt = $conexion->prepare("SELECT * FROM trabajador WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $stmt->close();
    return $fila;
}

function listarTrabajadores($conexion)
{
    return $conexion->query("SELECT * FROM trabajador ORDER BY nombre_completo");
}
?>
