<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../logica/entradas.php";
require_once __DIR__ . "/../logica/validarEmpresa.php";
require_once __DIR__ . "/../logica/servicioContrato.php";

requerirLogin("../vistas/login.php");
exigirPost("../vistas/empresas.php");
exigirCsrf("../vistas/empresas.php");

$id = (isset($_POST["id"]) && ctype_digit((string) $_POST["id"])) ? (int) $_POST["id"] : 0;
$volver = "../vistas/empresaForm.php" . ($id ? "?id=$id" : "");

$existente = null;
if ($id) {
    $existente = buscarEmpresaPorId($conexion, $id);
    if (!$existente || !puedeVerEmpresa($existente)) {
        flash("error", "La empresa no existe o no tiene permiso para modificarla.");
        header("Location: ../vistas/empresas.php");
        exit();
    }
}

$datos = leerEmpresaPost();
$errores = validarEmpresa($datos);

if (empty($errores["nit"])) {
    $otra = buscarEmpresaPorNit($conexion, $datos["nit"]);
    if ($otra && (int) $otra["id"] !== $id) {
        // No se dice quién la registró: solo que el NIT ya existe.
        $errores["nit"] = "Ya existe una empresa registrada con ese NIT.";
    }
}

if (!empty($errores)) {
    guardarFormulario("empresa", $datos, $errores);
    header("Location: " . $volver);
    exit();
}

try {
    if ($id) {
        actualizarEmpresa($conexion, $id, $datos);
        reevaluarContratosDe($conexion, "empresa_id", $id);   // ARL/clase de riesgo pudieron cambiar
        registrarBitacora($conexion, "empresa_editar", "empresa", $id, $datos["razon_social"]);
        flash("ok", "Empresa actualizada. Se reevaluó el riesgo de sus contratos.");
    } else {
        $id = insertarEmpresa($conexion, $datos, (int) usuarioActual()["id"]);
        registrarBitacora($conexion, "empresa_crear", "empresa", $id, $datos["razon_social"]);
        flash("ok", "Empresa registrada correctamente.");
    }
} catch (Throwable $e) {
    error_log("guardarEmpresa: " . $e->getMessage());
    guardarFormulario("empresa", $datos, ["razon_social" => "No fue posible guardar la empresa. Intente de nuevo."]);
    header("Location: " . $volver);
    exit();
}

header("Location: ../vistas/empresas.php");
exit();
?>
