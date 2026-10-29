<?php
require_once __DIR__ . "/../logica/riesgo.php";
/*
 * DAO del control de riesgo (tabla contrato_riesgo).
 */

// Sincroniza los factores calculados con los guardados: agrega los nuevos,
// actualiza los que siguen, y elimina los que ya no aplican. Conserva el
// estado (mitigado/aceptado) y la nota de los factores que siguen vigentes.
function sincronizarRiesgo($conexion, $contratoId, array $factores)
{
    $actuales = [];
    $stmt = $conexion->prepare("SELECT codigo FROM contrato_riesgo WHERE contrato_id = ?");
    $stmt->bind_param("i", $contratoId);
    $stmt->execute();
    foreach ($stmt->get_result() as $fila) {
        $actuales[$fila["codigo"]] = true;
    }
    $stmt->close();

    foreach ($factores as $codigo => $f) {
        if (isset($actuales[$codigo])) {
            $up = $conexion->prepare(
                "UPDATE contrato_riesgo SET descripcion = ?, recomendacion = ?, puntos = ?
                  WHERE contrato_id = ? AND codigo = ?"
            );
            $up->bind_param("ssiis", $f["descripcion"], $f["recomendacion"], $f["puntos"], $contratoId, $codigo);
            $up->execute();
            $up->close();
        } else {
            $in = $conexion->prepare(
                "INSERT INTO contrato_riesgo (contrato_id, codigo, descripcion, recomendacion, puntos)
                 VALUES (?,?,?,?,?)"
            );
            $in->bind_param("isssi", $contratoId, $codigo, $f["descripcion"], $f["recomendacion"], $f["puntos"]);
            $in->execute();
            $in->close();
        }
    }

    foreach (array_keys($actuales) as $codigo) {
        if (!isset($factores[$codigo])) {
            $del = $conexion->prepare("DELETE FROM contrato_riesgo WHERE contrato_id = ? AND codigo = ?");
            $del->bind_param("is", $contratoId, $codigo);
            $del->execute();
            $del->close();
        }
    }
}

function listarRiesgosContrato($conexion, $contratoId)
{
    $stmt = $conexion->prepare(
        "SELECT r.*, u.nombre_completo AS controlador
           FROM contrato_riesgo r LEFT JOIN usuario u ON r.controlado_por = u.id
          WHERE r.contrato_id = ? ORDER BY r.puntos DESC, r.codigo"
    );
    $stmt->bind_param("i", $contratoId);
    $stmt->execute();
    $filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $filas;
}

function buscarRiesgoPorId($conexion, $id)
{
    $stmt = $conexion->prepare("SELECT * FROM contrato_riesgo WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $fila;
}

function controlarFactorRiesgo($conexion, $id, $estado, $nota, $usuarioId)
{
    $stmt = $conexion->prepare(
        "UPDATE contrato_riesgo SET estado = ?, nota_control = ?, controlado_por = ?, fecha_control = NOW() WHERE id = ?"
    );
    $stmt->bind_param("ssii", $estado, $nota, $usuarioId, $id);
    $stmt->execute();
    $stmt->close();
}

// Cuántos factores graves siguen abiertos (impiden aprobar el contrato).
function contarGravesAbiertos($conexion, $contratoId)
{
    $min = RIESGO_PUNTOS_GRAVE;
    $stmt = $conexion->prepare(
        "SELECT COUNT(*) AS n FROM contrato_riesgo WHERE contrato_id = ? AND estado = 'abierto' AND puntos >= ?"
    );
    $stmt->bind_param("ii", $contratoId, $min);
    $stmt->execute();
    $n = (int) $stmt->get_result()->fetch_assoc()["n"];
    $stmt->close();
    return $n;
}

// Matriz del supervisor: cuántos contratos tienen cada factor abierto.
function matrizRiesgos($conexion)
{
    return $conexion->query(
        "SELECT r.codigo, MIN(r.descripcion) AS descripcion, MAX(r.puntos) AS puntos,
                SUM(r.estado = 'abierto') AS abiertos,
                SUM(r.estado = 'mitigado') AS mitigados,
                SUM(r.estado = 'aceptado') AS aceptados
           FROM contrato_riesgo r INNER JOIN contrato c ON c.id = r.contrato_id
          WHERE c.estado <> 'anulado'
          GROUP BY r.codigo ORDER BY abiertos DESC, puntos DESC"
    )->fetch_all(MYSQLI_ASSOC);
}
?>
