<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../logica/festivos.php";
require_once __DIR__ . "/../datos/trabajadorDAO.php";
require_once __DIR__ . "/../datos/solicitudDAO.php";

/*
 * Registra una solicitud del titular de los datos (trabajador).
 * Plazos de la Ley 1581 de 2012 contados en DÍAS HÁBILES (sin sábados,
 * domingos ni festivos): consulta = 10 días (art. 14); reclamo, actualización,
 * rectificación, supresión o revocatoria = 15 días (art. 15).
 * Cada plazo puede prorrogarse una vez (5 y 8 días) con aviso al titular.
 */
requerirLogin("../vistas/login.php");
exigirPost("../vistas/solicitudes.php");
exigirCsrf("../vistas/solicitudes.php");

$trabajadorId = (isset($_POST["trabajador_id"]) && ctype_digit((string) $_POST["trabajador_id"])) ? (int) $_POST["trabajador_id"] : 0;
$tipo = $_POST["tipo"] ?? "";
$descripcion = is_string($_POST["descripcion"] ?? null) ? trim($_POST["descripcion"]) : "";
$tipos = ["consulta", "actualizacion", "rectificacion", "supresion", "revocatoria", "reclamo"];

$trabajador = $trabajadorId ? buscarTrabajadorPorId($conexion, $trabajadorId) : null;
$errores = [];
if (!$trabajador || !puedeVerTrabajador($trabajador)) {
    $errores["trabajador_id"] = "Seleccione un trabajador de la lista.";
}
if (!in_array($tipo, $tipos, true)) {
    $errores["tipo"] = "Seleccione el tipo de solicitud.";
}
if (mb_strlen($descripcion) < 10) {
    $errores["descripcion"] = "Describa la solicitud (mínimo 10 caracteres).";
}

if (!empty($errores)) {
    guardarFormulario("solicitud", ["trabajador_id" => $trabajadorId, "tipo" => $tipo, "descripcion" => $descripcion], $errores);
    header("Location: ../vistas/solicitudForm.php");
    exit();
}

$hoy = date("Y-m-d");
$limite = sumarDiasHabiles($hoy, $tipo === "consulta" ? 10 : 15);

try {
    $id = insertarSolicitud($conexion, $trabajadorId, $tipo, $descripcion, $hoy, $limite, (int) usuarioActual()["id"]);
    registrarBitacora($conexion, "solicitud_datos", "solicitud", $id, "Tipo $tipo; vence $limite");
    flash("ok", "Solicitud registrada. Debe responderse a más tardar el " . date("d/m/Y", strtotime($limite)) . " (días hábiles).");
} catch (Throwable $e) {
    error_log("guardarSolicitud: " . $e->getMessage());
    flash("error", "No fue posible registrar la solicitud.");
}
header("Location: ../vistas/solicitudes.php");
exit();
?>
