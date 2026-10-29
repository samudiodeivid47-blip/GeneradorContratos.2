<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../datos/empresaDAO.php";
require_once __DIR__ . "/../datos/trabajadorDAO.php";
require_once __DIR__ . "/../datos/contratoDAO.php";
require_once __DIR__ . "/../logica/validarEmpresa.php";
require_once __DIR__ . "/../logica/validarTrabajador.php";
require_once __DIR__ . "/../logica/validarContrato.php";

/*
 * Este proceso recibe el formulario completo (empresa + trabajador +
 * contrato) en un único envío, valida cada bloque por separado y, si
 * todo es correcto, persiste los tres registros relacionados y genera
 * el contrato. Si hay errores, regresa al formulario mostrando los
 * mensajes junto al campo correspondiente (sin perder lo ya digitado).
 */

$datosEmpresa = [
    "tipo_empleador"           => $_POST["tipo_empleador"] ?? "",
    "razon_social"              => trim($_POST["razon_social"] ?? ""),
    "nit"                        => trim($_POST["nit"] ?? ""),
    "representante_legal"       => trim($_POST["representante_legal"] ?? ""),
    "documento_representante"   => trim($_POST["documento_representante"] ?? ""),
    "direccion"                  => trim($_POST["direccion_empresa"] ?? ""),
    "ciudad"                     => trim($_POST["ciudad_empresa"] ?? ""),
    "departamento"               => trim($_POST["departamento_empresa"] ?? ""),
    "telefono"                   => trim($_POST["telefono_empresa"] ?? ""),
    "correo"                     => trim($_POST["correo_empresa"] ?? ""),
    "actividad_economica"       => trim($_POST["actividad_economica"] ?? ""),
    "arl"                        => trim($_POST["arl"] ?? ""),
];

$datosTrabajador = [
    "nombre_completo"    => trim($_POST["nombre_completo"] ?? ""),
    "tipo_documento"      => $_POST["tipo_documento"] ?? "",
    "numero_documento"    => trim($_POST["numero_documento"] ?? ""),
    "fecha_nacimiento"    => trim($_POST["fecha_nacimiento"] ?? "") ?: null,
    "direccion"            => trim($_POST["direccion_trabajador"] ?? ""),
    "ciudad"               => trim($_POST["ciudad_trabajador"] ?? ""),
    "telefono"             => trim($_POST["telefono_trabajador"] ?? ""),
    "correo"               => trim($_POST["correo_trabajador"] ?? ""),
    "eps"                  => trim($_POST["eps"] ?? ""),
    "fondo_pension"       => trim($_POST["fondo_pension"] ?? ""),
];

$datosContrato = [
    "descripcion_obra"              => trim($_POST["descripcion_obra"] ?? ""),
    "cargo"                          => trim($_POST["cargo"] ?? ""),
    "funciones"                      => trim($_POST["funciones"] ?? ""),
    "lugar_ejecucion"               => trim($_POST["lugar_ejecucion"] ?? ""),
    "salario"                        => $_POST["salario"] ?? "",
    "auxilio_transporte"            => $_POST["auxilio_transporte"] ?? 0,
    "periodicidad_pago"             => $_POST["periodicidad_pago"] ?? "",
    "forma_pago"                     => $_POST["forma_pago"] ?? "",
    "jornada"                        => $_POST["jornada"] ?? "",
    "horas_semanales"               => $_POST["horas_semanales"] ?? "",
    "horario"                        => trim($_POST["horario"] ?? ""),
    "fecha_inicio"                   => $_POST["fecha_inicio"] ?? "",
    "condicion_terminacion"         => trim($_POST["condicion_terminacion"] ?? ""),
    "tiene_periodo_prueba"          => isset($_POST["tiene_periodo_prueba"]) ? 1 : 0,
    "duracion_periodo_prueba_dias" => !empty($_POST["duracion_periodo_prueba_dias"]) ? (int) $_POST["duracion_periodo_prueba_dias"] : null,
    "beneficios_extralegales"       => trim($_POST["beneficios_extralegales"] ?? ""),
    "observaciones"                  => trim($_POST["observaciones"] ?? ""),
];

$anioActual = (int) date("Y");
$parametrosLegales = obtenerParametrosLegales($conexion, $anioActual);

$erroresEmpresa    = validarEmpresa($datosEmpresa);
$erroresTrabajador = validarTrabajador($datosTrabajador);
$erroresContrato   = validarContrato($datosContrato, $parametrosLegales);

$todosLosErrores = array_merge($erroresEmpresa, $erroresTrabajador, $erroresContrato);
$revisionLegal = construirRevisionLegal($datosContrato, $erroresContrato, $parametrosLegales);

if (!empty($todosLosErrores)) {
    // Se regresa al formulario conservando lo digitado y mostrando errores,
    // mediante el patrón Post-Redirect-Get: se guarda todo en sesión y se
    // redirige a index.php, que es el único punto que sirve el formulario.
    $_SESSION["datosEmpresa"]    = $datosEmpresa;
    $_SESSION["datosTrabajador"] = $datosTrabajador;
    $_SESSION["datosContrato"]   = $datosContrato;
    $_SESSION["errores"]         = $todosLosErrores;
    header("Location: ../index.php");
    exit();
}

// ---------- Persistencia ----------
// Se reutiliza la empresa/trabajador si ya existían (por NIT/documento),
// evitando duplicados cuando el mismo empleador genera varios contratos.
$empresaExistente = buscarEmpresaPorNit($conexion, $datosEmpresa["nit"]);
$empresaId = $empresaExistente ? $empresaExistente["id"] : insertarEmpresa($conexion, $datosEmpresa);

$trabajadorExistente = buscarTrabajadorPorDocumento($conexion, $datosTrabajador["tipo_documento"], $datosTrabajador["numero_documento"]);
$trabajadorId = $trabajadorExistente ? $trabajadorExistente["id"] : insertarTrabajador($conexion, $datosTrabajador);

if (!$empresaId || !$trabajadorId) {
    $_SESSION["datosEmpresa"]    = $datosEmpresa;
    $_SESSION["datosTrabajador"] = $datosTrabajador;
    $_SESSION["datosContrato"]   = $datosContrato;
    $_SESSION["mensaje"]         = "No fue posible registrar la empresa o el trabajador: " . $conexion->error;
    header("Location: ../index.php");
    exit();
}

$datosContrato["empresa_id"] = $empresaId;
$datosContrato["trabajador_id"] = $trabajadorId;

$contratoId = insertarContrato($conexion, $datosContrato);

if (!$contratoId) {
    $_SESSION["datosEmpresa"]    = $datosEmpresa;
    $_SESSION["datosTrabajador"] = $datosTrabajador;
    $_SESSION["datosContrato"]   = $datosContrato;
    $_SESSION["mensaje"]         = "No fue posible generar el contrato: " . $conexion->error;
    header("Location: ../index.php");
    exit();
}

header("Location: ../vistas/verContrato.php?id=" . $contratoId);
exit();
?>
