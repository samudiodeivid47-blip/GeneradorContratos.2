<?php
/*
 * Lectura de los datos enviados por los formularios (POST).
 * Los nombres de los campos son los mismos en el asistente de 3 pasos y
 * en los formularios separados, así se valida una sola vez.
 */

function textoPost($clave)
{
    $v = $_POST[$clave] ?? "";
    return is_string($v) ? trim($v) : "";
}

function leerEmpresaPost()
{
    return [
        "tipo_empleador"          => textoPost("tipo_empleador"),
        "razon_social"            => textoPost("razon_social"),
        "nit"                     => textoPost("nit"),
        "representante_legal"     => textoPost("representante_legal"),
        "documento_representante" => textoPost("documento_representante"),
        "direccion"               => textoPost("direccion_empresa"),
        "ciudad"                  => textoPost("ciudad_empresa"),
        "departamento"            => textoPost("departamento_empresa"),
        "telefono"                => textoPost("telefono_empresa"),
        "correo"                  => textoPost("correo_empresa"),
        "actividad_economica"     => textoPost("actividad_economica"),
        "arl"                     => textoPost("arl"),
        "clase_riesgo"            => textoPost("clase_riesgo") !== "" ? (int) textoPost("clase_riesgo") : null,
    ];
}

function leerTrabajadorPost()
{
    return [
        "nombre_completo"    => textoPost("nombre_completo"),
        "tipo_documento"     => textoPost("tipo_documento"),
        "numero_documento"   => textoPost("numero_documento"),
        "fecha_nacimiento"   => textoPost("fecha_nacimiento") ?: null,
        "direccion"          => textoPost("direccion_trabajador"),
        "ciudad"             => textoPost("ciudad_trabajador"),
        "telefono"           => textoPost("telefono_trabajador"),
        "correo"             => textoPost("correo_trabajador"),
        "eps"                => textoPost("eps"),
        "fondo_pension"      => textoPost("fondo_pension"),
        "autoriza_datos"     => isset($_POST["autoriza_datos"]) ? 1 : 0,
        "autorizacion_medio" => textoPost("autorizacion_medio"),
    ];
}

function leerContratoPost()
{
    return [
        "descripcion_obra"             => textoPost("descripcion_obra"),
        "cargo"                        => textoPost("cargo"),
        "funciones"                    => textoPost("funciones"),
        "lugar_ejecucion"              => textoPost("lugar_ejecucion"),
        "labor_transitoria"            => isset($_POST["labor_transitoria"]) ? 1 : 0,
        "salario"                      => textoPost("salario"),
        "auxilio_transporte"           => textoPost("auxilio_transporte") !== "" ? textoPost("auxilio_transporte") : 0,
        "periodicidad_pago"            => textoPost("periodicidad_pago"),
        "forma_pago"                   => textoPost("forma_pago"),
        "jornada"                      => textoPost("jornada"),
        "horas_semanales"              => textoPost("horas_semanales"),
        "horario"                      => textoPost("horario"),
        "fecha_firma"                  => textoPost("fecha_firma"),
        "fecha_inicio"                 => textoPost("fecha_inicio"),
        "fecha_terminacion_estimada"   => textoPost("fecha_terminacion_estimada"),
        "condicion_terminacion"        => textoPost("condicion_terminacion"),
        "tiene_periodo_prueba"         => isset($_POST["tiene_periodo_prueba"]) ? 1 : 0,
        "duracion_periodo_prueba_dias" => textoPost("duracion_periodo_prueba_dias") !== "" ? (int) textoPost("duracion_periodo_prueba_dias") : null,
        "beneficios_extralegales"      => textoPost("beneficios_extralegales"),
        "observaciones"                => textoPost("observaciones"),
    ];
}
?>
