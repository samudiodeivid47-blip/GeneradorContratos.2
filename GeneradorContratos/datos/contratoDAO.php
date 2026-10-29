<?php
/*
 * DAO de contrato.
 * Concentra todo el acceso SQL a la tabla contrato.
 */

function insertarContrato($conexion, $datos)
{
    $stmt = $conexion->prepare(
        "INSERT INTO contrato
            (empresa_id, trabajador_id, descripcion_obra, cargo, funciones,
             lugar_ejecucion, salario, auxilio_transporte, periodicidad_pago,
             forma_pago, jornada, horas_semanales, horario, fecha_inicio,
             condicion_terminacion, tiene_periodo_prueba,
             duracion_periodo_prueba_dias, beneficios_extralegales,
             observaciones, estado)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'generado')"
    );

    $stmt->bind_param(
        "iissssddsssdsssiiss",
        $datos["empresa_id"],
        $datos["trabajador_id"],
        $datos["descripcion_obra"],
        $datos["cargo"],
        $datos["funciones"],
        $datos["lugar_ejecucion"],
        $datos["salario"],
        $datos["auxilio_transporte"],
        $datos["periodicidad_pago"],
        $datos["forma_pago"],
        $datos["jornada"],
        $datos["horas_semanales"],
        $datos["horario"],
        $datos["fecha_inicio"],
        $datos["condicion_terminacion"],
        $datos["tiene_periodo_prueba"],
        $datos["duracion_periodo_prueba_dias"],
        $datos["beneficios_extralegales"],
        $datos["observaciones"]
    );

    // Nota: bind_param con 19 valores requiere una cadena de tipos con
    // exactamente 19 caracteres en el mismo orden que las columnas:
    // i,i,s,s,s,s,d,d,s,s,s,d,s,s,s,i,i,s,s = "iissssddsssdsssiiss"
    // (verificado programáticamente contra la lista de columnas).

    $stmt->execute();

    if ($stmt->error) {
        $stmt->close();
        return false;
    }

    $id = $stmt->insert_id;
    $stmt->close();

    // Se marca la fecha de generación una vez insertado el registro.
    $conexion->query("UPDATE contrato SET fecha_generacion = NOW() WHERE id = $id");

    return $id ?: false;
}

function buscarContratoPorId($conexion, $id)
{
    $stmt = $conexion->prepare(
        "SELECT c.*,
                e.tipo_empleador, e.razon_social, e.nit, e.representante_legal,
                e.documento_representante, e.direccion AS empresa_direccion,
                e.ciudad AS empresa_ciudad, e.departamento AS empresa_departamento,
                e.telefono AS empresa_telefono, e.correo AS empresa_correo,
                e.actividad_economica, e.arl,
                t.nombre_completo, t.tipo_documento, t.numero_documento,
                t.direccion AS trabajador_direccion, t.ciudad AS trabajador_ciudad,
                t.telefono AS trabajador_telefono, t.correo AS trabajador_correo,
                t.eps, t.fondo_pension
         FROM contrato c
         INNER JOIN empresa e ON c.empresa_id = e.id
         INNER JOIN trabajador t ON c.trabajador_id = t.id
         WHERE c.id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $stmt->close();
    return $fila;
}

function listarContratos($conexion)
{
    return $conexion->query(
        "SELECT c.id, c.cargo, c.fecha_inicio, c.estado,
                e.razon_social, t.nombre_completo
         FROM contrato c
         INNER JOIN empresa e ON c.empresa_id = e.id
         INNER JOIN trabajador t ON c.trabajador_id = t.id
         ORDER BY c.fecha_registro DESC"
    );
}

function obtenerParametrosLegales($conexion, $anio)
{
    $stmt = $conexion->prepare("SELECT * FROM parametros_legales WHERE anio = ?");
    $stmt->bind_param("i", $anio);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $stmt->close();

    // Si no hay parámetros para el año solicitado, se usa el más reciente
    // disponible en lugar de fallar, dejando la advertencia al usuario.
    if (!$fila) {
        $fila = $conexion->query(
            "SELECT * FROM parametros_legales ORDER BY anio DESC LIMIT 1"
        )->fetch_assoc();
    }

    return $fila;
}
?>
