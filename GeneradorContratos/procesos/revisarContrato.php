<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../logica/servicioContrato.php";

/*
 * Revisión del supervisor: aprobar, rechazar o anular un contrato.
 * Reglas de control:
 *  - Solo supervisor o administrador.
 *  - Nadie revisa un contrato que generó él mismo (separación de funciones).
 *  - No se aprueba mientras queden factores de riesgo GRAVES sin mitigar o aceptar.
 *  - Rechazar o anular exige una observación.
 */
requerirRol(["supervisor", "administrador"], "../vistas/login.php", "../index.php");
exigirPost("../vistas/supervision.php");
exigirCsrf("../vistas/supervision.php");

$id = (isset($_POST["id"]) && ctype_digit((string) $_POST["id"])) ? (int) $_POST["id"] : 0;
$contrato = $id ? buscarContratoPorId($conexion, $id) : null;
if (!$contrato) {
    flash("error", "El contrato no existe.");
    header("Location: ../vistas/supervision.php");
    exit();
}
$volver = "../vistas/verContrato.php?id=" . $id;

$decision = $_POST["decision"] ?? "";
$observacion = is_string($_POST["observacion"] ?? null) ? trim($_POST["observacion"]) : "";

if (esPropietario($contrato, "usuario_id")) {
    flash("error", "No puede revisar un contrato que usted mismo generó. Pida a otro supervisor o al administrador.");
    header("Location: " . $volver);
    exit();
}
if (!in_array($decision, ["aprobar", "rechazar", "anular"], true)) {
    flash("error", "Acción no válida.");
    header("Location: " . $volver);
    exit();
}
if ($decision !== "anular" && $contrato["estado"] !== "pendiente_revision") {
    flash("error", "Solo se pueden aprobar o rechazar contratos pendientes de revisión.");
    header("Location: " . $volver);
    exit();
}
if ($contrato["estado"] === "anulado") {
    flash("error", "El contrato ya está anulado.");
    header("Location: " . $volver);
    exit();
}
if (($decision === "rechazar" || $decision === "anular") && mb_strlen($observacion) < 10) {
    flash("error", "Escriba la observación (mínimo 10 caracteres): el autor la necesita para corregir.");
    header("Location: " . $volver);
    exit();
}
if (mb_strlen($observacion) > 500) {
    flash("error", "La observación no puede superar 500 caracteres.");
    header("Location: " . $volver);
    exit();
}

try {
    if ($decision === "aprobar") {
        // Se recalcula el riesgo con los datos de hoy antes de decidir.
        evaluarYGuardarRiesgo($conexion, $id);
        $graves = contarGravesAbiertos($conexion, $id);
        if ($graves > 0) {
            flash("error", "No se puede aprobar: hay $graves factor(es) de riesgo grave sin controlar. Márquelos como mitigados o aceptados (con nota) primero.");
            header("Location: " . $volver);
            exit();
        }
        cambiarEstadoContrato($conexion, $id, "aprobado", (int) usuarioActual()["id"], $observacion !== "" ? $observacion : null);
        registrarBitacora($conexion, "contrato_aprobar", "contrato", $id, $observacion);
        flash("ok", "Contrato aprobado. Ya puede imprimirse como versión definitiva.");
    } elseif ($decision === "rechazar") {
        cambiarEstadoContrato($conexion, $id, "rechazado", (int) usuarioActual()["id"], $observacion);
        registrarBitacora($conexion, "contrato_rechazar", "contrato", $id, $observacion);
        flash("ok", "Contrato rechazado. El autor podrá corregirlo y reenviarlo.");
    } else {
        cambiarEstadoContrato($conexion, $id, "anulado", (int) usuarioActual()["id"], $observacion);
        registrarBitacora($conexion, "contrato_anular", "contrato", $id, $observacion);
        flash("ok", "Contrato anulado.");
    }
} catch (Throwable $e) {
    error_log("revisarContrato: " . $e->getMessage());
    flash("error", "No fue posible registrar la decisión. Intente de nuevo.");
}

header("Location: " . $volver);
exit();
?>
