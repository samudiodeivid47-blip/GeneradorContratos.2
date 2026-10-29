<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../logica/entradas.php";
require_once __DIR__ . "/../logica/validarTrabajador.php";
require_once __DIR__ . "/../logica/servicioContrato.php";

requerirLogin("../vistas/login.php");
exigirPost("../vistas/trabajadores.php");
exigirCsrf("../vistas/trabajadores.php");

$id = (isset($_POST["id"]) && ctype_digit((string) $_POST["id"])) ? (int) $_POST["id"] : 0;
$volver = "../vistas/trabajadorForm.php" . ($id ? "?id=$id" : "");

$existente = null;
if ($id) {
    $existente = buscarTrabajadorPorId($conexion, $id);
    if (!$existente || !puedeVerTrabajador($existente)) {
        flash("error", "El trabajador no existe o no tiene permiso para modificarlo.");
        header("Location: ../vistas/trabajadores.php");
        exit();
    }
}

$datos = leerTrabajadorPost();
$yaAutorizado = $existente && (int) $existente["autoriza_datos"] === 1;

// Si la autorización ya consta no se vuelve a exigir; si no consta, hay que registrarla.
$errores = validarTrabajador($datos, !$yaAutorizado);

if (empty($errores["numero_documento"]) && empty($errores["tipo_documento"])) {
    $otro = buscarTrabajadorPorDocumento($conexion, $datos["tipo_documento"], $datos["numero_documento"]);
    if ($otro && (int) $otro["id"] !== $id) {
        $errores["numero_documento"] = "Ya existe un trabajador registrado con ese documento.";
    }
}

if (!empty($errores)) {
    guardarFormulario("trabajador", $datos, $errores);
    header("Location: " . $volver);
    exit();
}

try {
    if ($id) {
        actualizarTrabajador($conexion, $id, $datos, (int) usuarioActual()["id"], $yaAutorizado);
        reevaluarContratosDe($conexion, "trabajador_id", $id);
        registrarBitacora($conexion, "trabajador_editar", "trabajador", $id, "Datos del trabajador actualizados");
        if (!$yaAutorizado && !empty($datos["autoriza_datos"])) {
            registrarBitacora($conexion, "autorizacion_datos", "trabajador", $id, "Medio: " . $datos["autorizacion_medio"] . "; política " . POLITICA_VERSION);
        }
        flash("ok", "Trabajador actualizado. Se reevaluó el riesgo de sus contratos.");
    } else {
        $id = insertarTrabajador($conexion, $datos, (int) usuarioActual()["id"]);
        registrarBitacora($conexion, "trabajador_crear", "trabajador", $id, "Trabajador registrado");
        registrarBitacora($conexion, "autorizacion_datos", "trabajador", $id, "Medio: " . $datos["autorizacion_medio"] . "; política " . POLITICA_VERSION);
        flash("ok", "Trabajador registrado con su autorización de tratamiento de datos.");
    }
} catch (Throwable $e) {
    error_log("guardarTrabajador: " . $e->getMessage());
    guardarFormulario("trabajador", $datos, ["nombre_completo" => "No fue posible guardar al trabajador. Intente de nuevo."]);
    header("Location: " . $volver);
    exit();
}

header("Location: ../vistas/trabajadores.php");
exit();
?>
