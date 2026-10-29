<?php
/*
 * Ayudas para las vistas (formularios y mensajes).
 */
require_once __DIR__ . "/seguridad.php";

function val($arr, $clave)
{
    return h($arr[$clave] ?? "");
}

function errorCampo($errores, $clave)
{
    return !empty($errores[$clave])
        ? '<span class="error-campo">' . h($errores[$clave]) . '</span>'
        : "";
}

function claseError($errores, $clave)
{
    return !empty($errores[$clave]) ? "campo-invalido" : "";
}

function seleccionado($actual, $valor)
{
    return (string) $actual === (string) $valor ? "selected" : "";
}

function fechaCorta($fecha)
{
    return $fecha ? date("d/m/Y", strtotime($fecha)) : "—";
}

function fechaHoraCorta($fecha)
{
    return $fecha ? date("d/m/Y H:i", strtotime($fecha)) : "—";
}

function etiquetaEstado($estado)
{
    $mapa = [
        "borrador" => "Borrador",
        "pendiente_revision" => "Pendiente de revisión",
        "aprobado" => "Aprobado",
        "rechazado" => "Rechazado",
        "anulado" => "Anulado",
    ];
    return '<span class="insignia estado-' . h($estado) . '">' . h($mapa[$estado] ?? $estado) . '</span>';
}

function etiquetaRiesgo($nivel, $puntaje = null)
{
    $texto = ucfirst($nivel) . ($puntaje !== null ? " (" . (int) $puntaje . ")" : "");
    return '<span class="insignia riesgo-' . h($nivel) . '">' . h($texto) . '</span>';
}

function money($valor)
{
    return "$" . number_format((float) $valor, 0, ",", ".");
}

// Lee un entero positivo de $_GET; 0 si no es válido.
function idGet($clave = "id")
{
    return (isset($_GET[$clave]) && ctype_digit((string) $_GET[$clave])) ? (int) $_GET[$clave] : 0;
}

// Guarda lo digitado y los errores para volver a mostrar el formulario (Post-Redirect-Get).
function guardarFormulario($clave, $datos, $errores)
{
    $_SESSION["form"][$clave] = ["datos" => $datos, "errores" => $errores];
}

// Devuelve [datos, errores] y los borra de la sesión (se leen una sola vez).
function recuperarFormulario($clave)
{
    $f = $_SESSION["form"][$clave] ?? ["datos" => [], "errores" => []];
    unset($_SESSION["form"][$clave]);
    return [$f["datos"], $f["errores"]];
}
?>
