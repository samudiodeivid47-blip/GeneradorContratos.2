<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../datos/contratoDAO.php";
require_once __DIR__ . "/../datos/modificacionDAO.php";

/*
 * Cambia las FUNCIONES del trabajador en un contrato ya generado.
 * Es lo único del contrato que se puede modificar. Cada cambio exige
 * un motivo y queda registrado en contrato_modificacion (qué había,
 * qué hay ahora, quién, cuándo y por qué).
 */

requerirLogin("../vistas/login.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../vistas/listado.php");
    exit();
}

$id = (isset($_POST["id"]) && is_numeric($_POST["id"])) ? (int) $_POST["id"] : 0;
$contrato = $id > 0 ? buscarContratoPorId($conexion, $id) : null;

if (!$contrato || !puedeVerContrato($contrato)) {
    header("Location: ../vistas/listado.php");
    exit();
}

$funciones = trim($_POST["funciones"] ?? "");
$motivo = trim($_POST["motivo"] ?? "");
$errores = [];

if (mb_strlen($funciones) < 20) {
    $errores["funciones"] = "Describa las funciones con más detalle (mínimo 20 caracteres).";
} elseif ($funciones === trim($contrato["funciones"])) {
    $errores["funciones"] = "No hay cambios: el texto es igual al actual.";
}

if (mb_strlen($motivo) < 10) {
    $errores["motivo"] = "Explique el motivo del cambio (mínimo 10 caracteres).";
} elseif (mb_strlen($motivo) > 255) {
    $errores["motivo"] = "El motivo no puede superar 255 caracteres.";
}

if (!empty($errores)) {
    $_SESSION["datos_funciones"] = ["funciones" => $funciones, "motivo" => $motivo];
    $_SESSION["errores_funciones"] = $errores;
    header("Location: ../vistas/editarFunciones.php?id=" . $id);
    exit();
}

try {
    // Transacción: o se guardan el cambio Y su registro, o no se guarda nada.
    $conexion->begin_transaction();

    $okActualizar = actualizarFunciones($conexion, $id, $funciones);
    $okRegistro = insertarModificacion(
        $conexion,
        $id,
        (int) usuarioActual()["id"],
        "funciones",
        $contrato["funciones"],
        $funciones,
        $motivo
    );

    if (!$okActualizar || !$okRegistro) {
        throw new Exception("No se pudo guardar el cambio.");
    }

    $conexion->commit();
} catch (Throwable $e) {
    $conexion->rollback();
    $_SESSION["datos_funciones"] = ["funciones" => $funciones, "motivo" => $motivo];
    $_SESSION["errores_funciones"] = ["funciones" => "No fue posible guardar el cambio. Intente de nuevo."];
    header("Location: ../vistas/editarFunciones.php?id=" . $id);
    exit();
}

$_SESSION["mensaje_ok"] = "Las funciones se actualizaron y el cambio quedó registrado en el historial.";
header("Location: ../vistas/verContrato.php?id=" . $id);
exit();
?>
