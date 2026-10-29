<?php
/*
 * DAO de empresa (empleador).
 * Concentra todo el acceso SQL a la tabla empresa.
 */

function insertarEmpresa($conexion, $datos)
{
    $stmt = $conexion->prepare(
        "INSERT INTO empresa
            (tipo_empleador, razon_social, nit, representante_legal,
             documento_representante, direccion, ciudad, departamento,
             telefono, correo, actividad_economica, arl)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
    );

    $stmt->bind_param(
        "ssssssssssss",
        $datos["tipo_empleador"],
        $datos["razon_social"],
        $datos["nit"],
        $datos["representante_legal"],
        $datos["documento_representante"],
        $datos["direccion"],
        $datos["ciudad"],
        $datos["departamento"],
        $datos["telefono"],
        $datos["correo"],
        $datos["actividad_economica"],
        $datos["arl"]
    );

    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();

    return $id ?: false;
}

function buscarEmpresaPorNit($conexion, $nit)
{
    $stmt = $conexion->prepare("SELECT * FROM empresa WHERE nit = ?");
    $stmt->bind_param("s", $nit);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $stmt->close();
    return $fila;
}

function buscarEmpresaPorId($conexion, $id)
{
    $stmt = $conexion->prepare("SELECT * FROM empresa WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $stmt->close();
    return $fila;
}

function listarEmpresas($conexion)
{
    return $conexion->query("SELECT * FROM empresa ORDER BY razon_social");
}
?>
