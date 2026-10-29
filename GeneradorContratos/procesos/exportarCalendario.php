<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../logica/calendario.php";

/*
 * Exporta las fechas de los contratos visibles (firma, inicio, fin de prueba y
 * terminación estimada) a un archivo .ics para Google Calendar, Outlook, etc.
 * Solo contratos que el usuario puede ver.
 */
requerirLogin("../vistas/login.php");

$desde = date("Y-m-d", strtotime("-1 month"));
$hasta = date("Y-m-d", strtotime("+2 years"));
$eventos = eventosContratos($conexion, $desde, $hasta, (int) usuarioActual()["id"], puedeSupervisar());

function icsTexto($t)
{
    return str_replace(["\\", ";", ",", "\r\n", "\n"], ["\\\\", "\\;", "\\,", "\\n", "\\n"], (string) $t);
}

$lineas = ["BEGIN:VCALENDAR", "VERSION:2.0", "PRODID:-//ContractPro//ES", "CALSCALE:GREGORIAN", "X-WR-CALNAME:ContractPro - fechas de contratos"];
foreach ($eventos as $fecha => $lista) {
    foreach ($lista as $i => $ev) {
        if (!in_array($ev["tipo"], ["firma", "inicio", "fin_prueba", "fin_estimado"], true)) {
            continue;
        }
        $dia = str_replace("-", "", $fecha);
        $siguiente = date("Ymd", strtotime($fecha . " +1 day"));
        $lineas[] = "BEGIN:VEVENT";
        $lineas[] = "UID:contrato" . $ev["contrato_id"] . "-" . $ev["tipo"] . "-" . $dia . "@contractpro";
        $lineas[] = "DTSTAMP:" . gmdate("Ymd\THis\Z");
        $lineas[] = "DTSTART;VALUE=DATE:" . $dia;
        $lineas[] = "DTEND;VALUE=DATE:" . $siguiente;
        $lineas[] = "SUMMARY:" . icsTexto($ev["titulo"]);
        $lineas[] = "DESCRIPTION:" . icsTexto($ev["detalle"]);
        $lineas[] = "END:VEVENT";
    }
}
$lineas[] = "END:VCALENDAR";

registrarBitacora($conexion, "calendario_exportar", "contrato", null, "Archivo .ics generado");

header("Content-Type: text/calendar; charset=utf-8");
header("Content-Disposition: attachment; filename=\"contractpro-fechas.ics\"");
echo implode("\r\n", $lineas) . "\r\n";
?>
