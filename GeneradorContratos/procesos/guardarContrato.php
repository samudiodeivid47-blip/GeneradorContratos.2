<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../logica/entradas.php";
require_once __DIR__ . "/../logica/validarContrato.php";
require_once __DIR__ . "/../logica/servicioContrato.php";

/*
 * Formulario SEPARADO de contrato: el contrato se arma con una empresa y un
 * trabajador que ya están registrados (cada uno con su propio formulario).
 * Sirve para crear un contrato nuevo y para corregir uno rechazado.
 */
requerirLogin("../vistas/login.php");
exigirPost("../vistas/listado.php");
exigirCsrf("../vistas/listado.php");

$id = (isset($_POST["id"]) && ctype_digit((string) $_POST["id"])) ? (int) $_POST["id"] : 0;
$volver = "../vistas/contratoForm.php" . ($id ? "?id=$id" : "");

$contratoActual = null;
if ($id) {
    $contratoActual = buscarContratoPorId($conexion, $id);
    if (!$contratoActual || !puedeVerContrato($contratoActual) || !contratoEditable($contratoActual)) {
        flash("error", "Ese contrato no se puede corregir (solo el autor puede hacerlo, y solo si está rechazado o en borrador).");
        header("Location: ../vistas/listado.php");
        exit();
    }
}

$datos = leerContratoPost();
$datos["empresa_id"] = (isset($_POST["empresa_id"]) && ctype_digit((string) $_POST["empresa_id"])) ? (int) $_POST["empresa_id"] : 0;
$datos["trabajador_id"] = (isset($_POST["trabajador_id"]) && ctype_digit((string) $_POST["trabajador_id"])) ? (int) $_POST["trabajador_id"] : 0;

$parametros = obtenerParametrosLegales($conexion, (int) date("Y"));
$errores = validarContrato($datos, $parametros);

$empresa = $datos["empresa_id"] ? buscarEmpresaPorId($conexion, $datos["empresa_id"]) : null;
if (!$empresa || !puedeVerEmpresa($empresa)) {
    $errores["empresa_id"] = "Seleccione una empresa de la lista.";
}
$trabajador = $datos["trabajador_id"] ? buscarTrabajadorPorId($conexion, $datos["trabajador_id"]) : null;
if (!$trabajador || !puedeVerTrabajador($trabajador)) {
    $errores["trabajador_id"] = "Seleccione un trabajador de la lista.";
}

if (!empty($errores)) {
    guardarFormulario("contrato", $datos, $errores);
    header("Location: " . $volver);
    exit();
}

try {
    if ($id) {
        $datos["empresa_id"] = (int) $datos["empresa_id"];
        actualizarContrato($conexion, $id, $datos);
        $r = evaluarYGuardarRiesgo($conexion, $id);
        registrarBitacora($conexion, "contrato_corregir", "contrato", $id,
            "Corregido tras rechazo; vuelve a revisión. Riesgo " . $r["nivel"] . " (" . $r["puntaje"] . ")");
        flash("ok", "Contrato corregido y enviado de nuevo a revisión del supervisor.");
        $destino = $id;
    } else {
        $destino = crearContratoYEvaluar($conexion, $datos, $datos["empresa_id"], $datos["trabajador_id"], (int) usuarioActual()["id"]);
        flash("ok", "Contrato generado. Queda pendiente de revisión del supervisor antes de imprimirse como definitivo.");
    }
} catch (Throwable $e) {
    error_log("guardarContrato: " . $e->getMessage());
    guardarFormulario("contrato", $datos, ["descripcion_obra" => "No fue posible guardar el contrato. Intente de nuevo."]);
    header("Location: " . $volver);
    exit();
}

header("Location: ../vistas/verContrato.php?id=" . (int) $destino);
exit();
?>
