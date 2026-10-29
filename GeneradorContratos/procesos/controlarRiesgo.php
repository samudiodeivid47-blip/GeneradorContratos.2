<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../logica/servicioContrato.php";

/*
 * Control de riesgo (supervisor/administrador):
 *  - "reevaluar": recalcula los factores con los datos actuales.
 *  - "controlar": marca un factor como mitigado o aceptado (con nota), o lo reabre.
 * Igual que en la revisión, nadie controla el riesgo de un contrato que generó él mismo.
 */
requerirRol(["supervisor", "administrador"], "../vistas/login.php", "../index.php");
exigirPost("../vistas/supervision.php");
exigirCsrf("../vistas/supervision.php");

$contratoId = (isset($_POST["contrato_id"]) && ctype_digit((string) $_POST["contrato_id"])) ? (int) $_POST["contrato_id"] : 0;
$contrato = $contratoId ? buscarContratoPorId($conexion, $contratoId) : null;
if (!$contrato) {
    flash("error", "El contrato no existe.");
    header("Location: ../vistas/supervision.php");
    exit();
}
$volver = "../vistas/verContrato.php?id=" . $contratoId . "#riesgo";

if (esPropietario($contrato, "usuario_id")) {
    flash("error", "No puede controlar el riesgo de un contrato que usted mismo generó.");
    header("Location: " . $volver);
    exit();
}

$accion = $_POST["accion"] ?? "";
try {
    if ($accion === "reevaluar") {
        $r = evaluarYGuardarRiesgo($conexion, $contratoId);
        registrarBitacora($conexion, "riesgo_reevaluar", "contrato", $contratoId, "Nivel " . $r["nivel"] . " (" . $r["puntaje"] . ")");
        flash("ok", "Riesgo reevaluado: nivel " . $r["nivel"] . " (" . $r["puntaje"] . " puntos).");
    } elseif ($accion === "controlar") {
        $factorId = (isset($_POST["factor_id"]) && ctype_digit((string) $_POST["factor_id"])) ? (int) $_POST["factor_id"] : 0;
        $factor = $factorId ? buscarRiesgoPorId($conexion, $factorId) : null;
        $estado = $_POST["estado"] ?? "";
        $nota = is_string($_POST["nota"] ?? null) ? trim($_POST["nota"]) : "";

        if (!$factor || (int) $factor["contrato_id"] !== $contratoId) {
            flash("error", "El factor de riesgo no existe.");
        } elseif (!in_array($estado, ["abierto", "mitigado", "aceptado"], true)) {
            flash("error", "Estado no válido.");
        } elseif ($estado !== "abierto" && mb_strlen($nota) < 10) {
            flash("error", "Escriba la nota del control (mínimo 10 caracteres): qué se hizo o por qué se acepta.");
        } elseif (mb_strlen($nota) > 300) {
            flash("error", "La nota no puede superar 300 caracteres.");
        } else {
            controlarFactorRiesgo($conexion, $factorId, $estado, $estado === "abierto" ? null : $nota, (int) usuarioActual()["id"]);
            registrarBitacora($conexion, "riesgo_control", "contrato", $contratoId, $factor["codigo"] . " → " . $estado . ($nota !== "" ? ": " . $nota : ""));
            flash("ok", "Factor " . $factor["codigo"] . " marcado como " . $estado . ".");
        }
    } else {
        flash("error", "Acción no válida.");
    }
} catch (Throwable $e) {
    error_log("controlarRiesgo: " . $e->getMessage());
    flash("error", "No fue posible guardar el control de riesgo.");
}

header("Location: " . $volver);
exit();
?>
