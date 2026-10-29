<?php
/*
 * Calendario legal de Colombia: festivos y fechas laborales.
 *
 * Los festivos se calculan con la Ley 51 de 1983 (Ley Emiliani): algunos
 * se quedan en su fecha y otros se trasladan al lunes siguiente; los de
 * Semana Santa y los que dependen de la Pascua se calculan con la fecha
 * de Pascua. No se consulta ninguna fuente externa.
 * [VERIFICAR cada año contra el calendario oficial del Ministerio del Trabajo.]
 */

// Fecha del Domingo de Pascua (algoritmo de Meeus/Jones/Butcher).
function domingoPascua($anio)
{
    $a = $anio % 19;
    $b = intdiv($anio, 100);
    $c = $anio % 100;
    $d = intdiv($b, 4);
    $e = $b % 4;
    $f = intdiv($b + 8, 25);
    $g = intdiv($b - $f + 1, 3);
    $hh = (19 * $a + $b - $d - $g + 15) % 30;
    $i = intdiv($c, 4);
    $k = $c % 4;
    $l = (32 + 2 * $e + 2 * $i - $hh - $k) % 7;
    $m = intdiv($a + 11 * $hh + 22 * $l, 451);
    $mes = intdiv($hh + $l - 7 * $m + 114, 31);
    $dia = (($hh + $l - 7 * $m + 114) % 31) + 1;
    return new DateTimeImmutable(sprintf("%04d-%02d-%02d", $anio, $mes, $dia));
}

// Si la fecha no es lunes, la pasa al lunes siguiente (regla de la Ley Emiliani).
function alLunesSiguiente(DateTimeImmutable $f)
{
    $dow = (int) $f->format("N"); // 1 = lunes ... 7 = domingo
    return $dow === 1 ? $f : $f->modify("+" . (8 - $dow) . " days");
}

// Devuelve [ "YYYY-MM-DD" => "Nombre del festivo" ] para el año.
function festivosColombia($anio)
{
    static $cache = [];
    if (isset($cache[$anio])) {
        return $cache[$anio];
    }

    $f = [];
    // Si dos festivos caen el mismo día se muestran juntos.
    $poner = function ($fecha, $nombre) use (&$f) {
        $f[$fecha] = isset($f[$fecha]) ? $f[$fecha] . " / " . $nombre : $nombre;
    };
    $fijo = function ($mes, $dia, $nombre) use ($poner, $anio) {
        $poner(sprintf("%04d-%02d-%02d", $anio, $mes, $dia), $nombre);
    };
    $movible = function ($mes, $dia, $nombre) use ($poner, $anio) {
        $d = alLunesSiguiente(new DateTimeImmutable(sprintf("%04d-%02d-%02d", $anio, $mes, $dia)));
        $poner($d->format("Y-m-d"), $nombre);
    };

    // Fijos (no se trasladan)
    $fijo(1, 1, "Año Nuevo");
    $fijo(5, 1, "Día del Trabajo");
    $fijo(7, 20, "Día de la Independencia");
    $fijo(8, 7, "Batalla de Boyacá");
    $fijo(12, 8, "Inmaculada Concepción");
    $fijo(12, 25, "Navidad");

    // Trasladados al lunes (Ley Emiliani)
    $movible(1, 6, "Día de Reyes");
    $movible(3, 19, "Día de San José");
    $movible(6, 29, "San Pedro y San Pablo");
    $movible(8, 15, "Asunción de la Virgen");
    $movible(10, 12, "Día de la Raza");
    $movible(11, 1, "Todos los Santos");
    $movible(11, 11, "Independencia de Cartagena");

    // Dependen de la Pascua
    $pascua = domingoPascua($anio);
    $poner($pascua->modify("-3 days")->format("Y-m-d"), "Jueves Santo");
    $poner($pascua->modify("-2 days")->format("Y-m-d"), "Viernes Santo");
    $poner(alLunesSiguiente($pascua->modify("+39 days"))->format("Y-m-d"), "Ascensión del Señor");
    $poner(alLunesSiguiente($pascua->modify("+60 days"))->format("Y-m-d"), "Corpus Christi");
    $poner(alLunesSiguiente($pascua->modify("+68 days"))->format("Y-m-d"), "Sagrado Corazón de Jesús");

    ksort($f);
    return $cache[$anio] = $f;
}

function esFestivo($fechaYmd)
{
    $anio = (int) substr($fechaYmd, 0, 4);
    return isset(festivosColombia($anio)[$fechaYmd]);
}

// Día hábil = lunes a viernes que no sea festivo.
function esDiaHabil(DateTimeImmutable $f)
{
    return (int) $f->format("N") <= 5 && !esFestivo($f->format("Y-m-d"));
}

// Suma $n días hábiles a una fecha (Y-m-d) y devuelve Y-m-d.
// Se usa para el plazo de las solicitudes de datos personales.
function sumarDiasHabiles($fechaYmd, $n)
{
    $f = new DateTimeImmutable($fechaYmd);
    $sumados = 0;
    while ($sumados < $n) {
        $f = $f->modify("+1 day");
        if (esDiaHabil($f)) {
            $sumados++;
        }
    }
    return $f->format("Y-m-d");
}

// Fechas laborales recurrentes y hitos legales del año (informativas).
// [VERIFICAR] La fecha exacta puede cambiar por norma posterior.
function fechasLegalesAnio($anio)
{
    $e = [
        sprintf("%04d-01-31", $anio) => "Plazo para pagar intereses sobre cesantías al trabajador",
        sprintf("%04d-02-14", $anio) => "Plazo para consignar las cesantías en el fondo",
        sprintf("%04d-06-30", $anio) => "Plazo de la prima de servicios (primer semestre)",
        sprintf("%04d-12-20", $anio) => "Plazo de la prima de servicios (segundo semestre)",
    ];
    if ($anio === 2026) {
        $e["2026-07-15"] = "Jornada máxima de 42 horas semanales (Ley 2101 de 2021)";
    }
    ksort($e);
    return $e;
}
?>
