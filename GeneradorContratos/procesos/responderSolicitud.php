<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../datos/solicitudDAO.php";
require_once __DIR__ . "/../datos/trabajadorDAO.php";
require_once __DIR__ . "/../logica/servicioContrato.php";

/*
 * Respuesta a una solicitud de datos personales (supervisor/administrador).
 * - Revocatoria resuelta: se retira la autorización del trabajador y sus
 *   contratos se reevalúan (aparece el factor "sin autorización de datos").
 * - Supresión: el sistema NO borra datos automáticamente, porque los
 *   contratos laborales deben conservarse mientras exista un deber legal.
 *   El supervisor decide y deja la respuesta escrita. [VERIFICAR con un abogado]
 */
requerirRol(["supervisor", "administrador"], "../vistas/login.php", "../index.php");
exigirPost("../vistas/solicitudes.php");
exigirCsrf("../vistas/solicitudes.php");

$id = (isset($_POST["id"]) && ctype_digit((string) $_POST["id"])) ? (int) $_POST["id"] : 0;
$s = $id ? buscarSolicitudPorId($conexion, $id) : null;
$volver = "../vistas/solicitudes.php?id=" . $id;

if (!$s) {
    flash("error", "La solicitud no existe.");
    header("Location: ../vistas/solicitudes.php");
    exit();
}

$estado = $_POST["estado"] ?? "";
$respuesta = is_string($_POST["respuesta"] ?? null) ? trim($_POST["respuesta"]) : "";

if (!in_array($estado, ["en_tramite", "resuelta", "rechazada"], true)) {
    flash("error", "Estado no válido.");
} elseif (in_array($s["estado"], ["resuelta", "rechazada"], true)) {
    flash("error", "La solicitud ya fue cerrada.");
} elseif ($estado !== "en_tramite" && mb_strlen($respuesta) < 10) {
    flash("error", "Escriba la respuesta para el titular (mínimo 10 caracteres).");
} else {
    try {
        $conexion->begin_transaction();
        responderSolicitud($conexion, $id, $estado, $respuesta !== "" ? $respuesta : null, (int) usuarioActual()["id"]);
        if ($estado === "resuelta" && $s["tipo"] === "revocatoria") {
            revocarAutorizacionDatos($conexion, (int) $s["trabajador_id"]);
            reevaluarContratosDe($conexion, "trabajador_id", (int) $s["trabajador_id"]);
        }
        registrarBitacora($conexion, "solicitud_responder", "solicitud", $id, "Estado $estado");
        $conexion->commit();
        flash("ok", "Solicitud actualizada: " . $estado . ".");
    } catch (Throwable $e) {
        $conexion->rollback();
        error_log("responderSolicitud: " . $e->getMessage());
        flash("error", "No fue posible guardar la respuesta.");
    }
}
header("Location: " . $volver);
exit();
?>
