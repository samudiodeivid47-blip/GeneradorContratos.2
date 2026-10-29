<?php
/*
 * DAO de solicitudes de datos personales (derechos del titular, Ley 1581 de 2012).
 */

function insertarSolicitud($conexion, $trabajadorId, $tipo, $descripcion, $fechaRecepcion, $fechaLimite, $usuarioId)
{
    $stmt = $conexion->prepare(
        "INSERT INTO solicitud_datos (trabajador_id, tipo, descripcion, fecha_recepcion, fecha_limite, registrada_por)
         VALUES (?,?,?,?,?,?)"
    );
    $stmt->bind_param("issssi", $trabajadorId, $tipo, $descripcion, $fechaRecepcion, $fechaLimite, $usuarioId);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    return $id ?: false;
}

function buscarSolicitudPorId($conexion, $id)
{
    $stmt = $conexion->prepare(
        "SELECT s.*, t.nombre_completo, t.tipo_documento, t.numero_documento, t.correo, t.creado_por AS trabajador_creado_por,
                a.nombre_completo AS atendida_nombre, g.nombre_completo AS registrada_nombre
           FROM solicitud_datos s
           INNER JOIN trabajador t ON t.id = s.trabajador_id
           LEFT JOIN usuario a ON a.id = s.atendida_por
           LEFT JOIN usuario g ON g.id = s.registrada_por
          WHERE s.id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $fila;
}

function listarSolicitudes($conexion, $usuarioId, $verTodas)
{
    $sql = "SELECT s.*, t.nombre_completo, t.numero_documento
              FROM solicitud_datos s INNER JOIN trabajador t ON t.id = s.trabajador_id";
    if ($verTodas) {
        $stmt = $conexion->prepare($sql . " ORDER BY FIELD(s.estado,'recibida','en_tramite','resuelta','rechazada'), s.fecha_limite");
    } else {
        $stmt = $conexion->prepare($sql . " WHERE s.registrada_por = ? ORDER BY FIELD(s.estado,'recibida','en_tramite','resuelta','rechazada'), s.fecha_limite");
        $stmt->bind_param("i", $usuarioId);
    }
    $stmt->execute();
    $filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $filas;
}

function responderSolicitud($conexion, $id, $estado, $respuesta, $usuarioId)
{
    $stmt = $conexion->prepare(
        "UPDATE solicitud_datos SET estado = ?, respuesta = ?, atendida_por = ?,
                fecha_respuesta = IF(? IN ('resuelta','rechazada'), NOW(), fecha_respuesta) WHERE id = ?"
    );
    $stmt->bind_param("ssisi", $estado, $respuesta, $usuarioId, $estado, $id);
    $stmt->execute();
    $stmt->close();
}

// Solicitudes abiertas con vencimiento en el rango (para el calendario).
function solicitudesParaCalendario($conexion, $desde, $hasta, $usuarioId, $verTodas)
{
    $sql = "SELECT s.id, s.tipo, s.fecha_limite, t.nombre_completo
              FROM solicitud_datos s INNER JOIN trabajador t ON t.id = s.trabajador_id
             WHERE s.estado IN ('recibida','en_tramite') AND s.fecha_limite BETWEEN ? AND ?";
    if (!$verTodas) {
        $stmt = $conexion->prepare($sql . " AND s.registrada_por = ?");
        $stmt->bind_param("ssi", $desde, $hasta, $usuarioId);
    } else {
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ss", $desde, $hasta);
    }
    $stmt->execute();
    $filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $filas;
}

function contarSolicitudesAbiertas($conexion, $usuarioId, $verTodas)
{
    if ($verTodas) {
        return (int) $conexion->query("SELECT COUNT(*) AS n FROM solicitud_datos WHERE estado IN ('recibida','en_tramite')")->fetch_assoc()["n"];
    }
    $stmt = $conexion->prepare("SELECT COUNT(*) AS n FROM solicitud_datos WHERE estado IN ('recibida','en_tramite') AND registrada_por = ?");
    $stmt->bind_param("i", $usuarioId);
    $stmt->execute();
    $n = (int) $stmt->get_result()->fetch_assoc()["n"];
    $stmt->close();
    return $n;
}
?>
