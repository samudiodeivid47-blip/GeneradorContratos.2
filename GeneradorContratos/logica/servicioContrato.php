<?php
/*
 * Servicio de contratos: reúne los pasos que comparten el asistente de
 * 3 pasos y el formulario separado de contrato, para no duplicar lógica.
 */
require_once __DIR__ . "/riesgo.php";
require_once __DIR__ . "/seguridad.php";
require_once __DIR__ . "/../datos/contratoDAO.php";
require_once __DIR__ . "/../datos/empresaDAO.php";
require_once __DIR__ . "/../datos/trabajadorDAO.php";
require_once __DIR__ . "/../datos/riesgoDAO.php";

// Calcula el riesgo del contrato con los datos que hay HOY en la base y lo guarda.
function evaluarYGuardarRiesgo($conexion, $contratoId)
{
    $c = buscarContratoPorId($conexion, $contratoId);
    if (!$c) {
        return null;
    }
    $parametros = obtenerParametrosLegales($conexion, (int) date("Y"));
    $sucesivos = contarContratosSucesivos($conexion, (int) $c["empresa_id"], (int) $c["trabajador_id"], $contratoId);

    $factores = evaluarRiesgoContrato($c, $c, $c, $parametros, $sucesivos);
    $puntaje = array_sum(array_column($factores, "puntos"));
    $nivel = nivelDeRiesgo($puntaje);

    sincronizarRiesgo($conexion, $contratoId, $factores);
    guardarResultadoRiesgo($conexion, $contratoId, $puntaje, $nivel);
    return ["puntaje" => $puntaje, "nivel" => $nivel, "factores" => $factores];
}

// Vuelve a evaluar los contratos activos de una empresa o de un trabajador
// (se llama al cambiar sus datos: ARL, EPS, autorización, etc.).
function reevaluarContratosDe($conexion, $columna, $id)
{
    if (!in_array($columna, ["empresa_id", "trabajador_id"], true)) {
        return;
    }
    $stmt = $conexion->prepare("SELECT id FROM contrato WHERE $columna = ? AND estado <> 'anulado'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $ids = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), "id");
    $stmt->close();
    foreach ($ids as $cid) {
        evaluarYGuardarRiesgo($conexion, (int) $cid);
    }
}

// Inserta el contrato, evalúa el riesgo y deja el rastro en la bitácora.
function crearContratoYEvaluar($conexion, $datosContrato, $empresaId, $trabajadorId, $usuarioId)
{
    $datosContrato["empresa_id"] = $empresaId;
    $datosContrato["trabajador_id"] = $trabajadorId;
    $id = insertarContrato($conexion, $datosContrato, $usuarioId);
    if ($id) {
        $r = evaluarYGuardarRiesgo($conexion, $id);
        registrarBitacora($conexion, "contrato_crear", "contrato", $id,
            "Riesgo " . ($r["nivel"] ?? "?") . " (" . ($r["puntaje"] ?? 0) . " puntos). Queda pendiente de revisión.");
    }
    return $id;
}

// ¿Puede el usuario actual corregir el contrato? Solo el autor (o el administrador)
// y solo mientras esté en borrador o rechazado.
function contratoEditable($contrato)
{
    return in_array($contrato["estado"], ["borrador", "rechazado"], true)
        && (esPropietario($contrato, "usuario_id") || esAdministrador());
}
?>
