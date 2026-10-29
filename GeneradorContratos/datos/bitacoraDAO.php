<?php
/*
 * DAO de la bitácora (solo lectura desde la aplicación).
 */
function listarBitacora($conexion, $accion = "", $limite = 200)
{
    $sql = "SELECT b.*, u.nombre_usuario FROM bitacora b LEFT JOIN usuario u ON u.id = b.usuario_id";
    if ($accion !== "") {
        $stmt = $conexion->prepare($sql . " WHERE b.accion = ? ORDER BY b.id DESC LIMIT ?");
        $stmt->bind_param("si", $accion, $limite);
    } else {
        $stmt = $conexion->prepare($sql . " ORDER BY b.id DESC LIMIT ?");
        $stmt->bind_param("i", $limite);
    }
    $stmt->execute();
    $filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $filas;
}

function accionesBitacora($conexion)
{
    return array_column($conexion->query("SELECT DISTINCT accion FROM bitacora ORDER BY accion")->fetch_all(MYSQLI_ASSOC), "accion");
}
?>
