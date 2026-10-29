<?php
/*
 * Arma los eventos del calendario a partir de las fechas de los contratos,
 * los vencimientos de solicitudes de datos, los festivos y las fechas legales.
 * Cada evento: ["tipo", "titulo", "detalle", "contrato_id"|null, "enlace"|null].
 */
require_once __DIR__ . "/festivos.php";
require_once __DIR__ . "/../datos/contratoDAO.php";
require_once __DIR__ . "/../datos/solicitudDAO.php";

// Solo eventos de contratos: [ "Y-m-d" => [evento, ...] ] dentro del rango.
function eventosContratos($conexion, $desde, $hasta, $usuarioId, $verTodos)
{
    $ev = [];
    $poner = function ($fecha, $tipo, $titulo, $detalle, $cid) use (&$ev, $desde, $hasta) {
        if ($fecha && $fecha >= $desde && $fecha <= $hasta) {
            $ev[$fecha][] = ["tipo" => $tipo, "titulo" => $titulo, "detalle" => $detalle,
                             "contrato_id" => $cid, "enlace" => "verContrato.php?id=" . $cid];
        }
    };
    foreach (contratosParaCalendario($conexion, $desde, $hasta, $usuarioId, $verTodos) as $c) {
        $quien = $c["nombre_completo"] . " — " . $c["razon_social"];
        $n = "Contrato #" . $c["id"];
        $poner($c["fecha_firma"], "firma", "Firma: $n", $quien, $c["id"]);
        $poner($c["fecha_inicio"], "inicio", "Inicio: $n", $quien, $c["id"]);
        if ($c["tiene_periodo_prueba"] && $c["duracion_periodo_prueba_dias"]) {
            // Último día del período de prueba, contando el día de inicio como día 1.
            $fin = date("Y-m-d", strtotime($c["fecha_inicio"] . " +" . ((int) $c["duracion_periodo_prueba_dias"] - 1) . " days"));
            $poner($fin, "fin_prueba", "Fin de prueba: $n", $quien, $c["id"]);
        }
        $poner($c["fecha_terminacion_estimada"], "fin_estimado", "Fin estimado: $n", $quien, $c["id"]);
    }
    return $ev;
}

// Todo lo que se pinta en un mes: contratos + solicitudes + festivos + fechas legales.
function eventosDelMes($conexion, $anio, $mes, $usuarioId, $verTodos)
{
    $desde = sprintf("%04d-%02d-01", $anio, $mes);
    $hasta = date("Y-m-t", strtotime($desde));
    $ev = eventosContratos($conexion, $desde, $hasta, $usuarioId, $verTodos);

    foreach (solicitudesParaCalendario($conexion, $desde, $hasta, $usuarioId, $verTodos) as $s) {
        $ev[$s["fecha_limite"]][] = ["tipo" => "solicitud", "titulo" => "Vence solicitud de datos #" . $s["id"],
            "detalle" => ucfirst($s["tipo"]) . " — " . $s["nombre_completo"], "contrato_id" => null,
            "enlace" => "solicitudes.php?id=" . $s["id"]];
    }
    foreach (festivosColombia($anio) as $f => $nombre) {
        if ($f >= $desde && $f <= $hasta) {
            $ev[$f][] = ["tipo" => "festivo", "titulo" => $nombre, "detalle" => "Festivo en Colombia", "contrato_id" => null, "enlace" => null];
        }
    }
    foreach (fechasLegalesAnio($anio) as $f => $texto) {
        if ($f >= $desde && $f <= $hasta) {
            $ev[$f][] = ["tipo" => "legal", "titulo" => $texto, "detalle" => "Fecha legal [VERIFICAR]", "contrato_id" => null, "enlace" => null];
        }
    }
    ksort($ev);
    return $ev;
}

// Próximos eventos de contratos y solicitudes (para el panel).
function proximosEventos($conexion, $usuarioId, $verTodos, $dias = 30)
{
    $desde = date("Y-m-d");
    $hasta = date("Y-m-d", strtotime("+$dias days"));
    $ev = eventosContratos($conexion, $desde, $hasta, $usuarioId, $verTodos);
    foreach (solicitudesParaCalendario($conexion, $desde, $hasta, $usuarioId, $verTodos) as $s) {
        $ev[$s["fecha_limite"]][] = ["tipo" => "solicitud", "titulo" => "Vence solicitud de datos #" . $s["id"],
            "detalle" => ucfirst($s["tipo"]) . " — " . $s["nombre_completo"], "contrato_id" => null, "enlace" => "solicitudes.php?id=" . $s["id"]];
    }
    ksort($ev);
    return $ev;
}
?>
