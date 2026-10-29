<?php
/*
 * CONTROL DE RIESGO del contrato.
 *
 * Revisa los datos del contrato, la empresa y el trabajador y devuelve una
 * lista de factores de riesgo con puntos y una recomendación. La suma de
 * puntos da el nivel: bajo (0-24), medio (25-49) o alto (50 o más).
 *
 * IMPORTANTE: es un apoyo para el supervisor, no un concepto jurídico. Los
 * puntos son criterios de este sistema (no vienen de una norma). Lo que sí
 * viene de una norma se indica en la descripción del factor.
 *
 * Un factor con 20 puntos o más es "grave": el supervisor debe marcarlo como
 * mitigado o aceptado (con nota) antes de aprobar el contrato.
 */
const RIESGO_PUNTOS_GRAVE = 20;

function nivelDeRiesgo($puntaje)
{
    if ($puntaje >= 50) {
        return "alto";
    }
    return $puntaje >= 25 ? "medio" : "bajo";
}

/*
 * $c = datos del contrato (salario, fechas, descripción, etc.)
 * $e = empresa (arl, clase_riesgo)
 * $t = trabajador (eps, fondo_pension, fecha_nacimiento, autoriza_datos)
 * $parametros = fila de parametros_legales
 * $sucesivos = cuántos contratos vigentes tiene ya este trabajador con esta empresa
 */
function evaluarRiesgoContrato($c, $e, $t, $parametros, $sucesivos = 0)
{
    $f = [];
    $agregar = function ($codigo, $puntos, $descripcion, $recomendacion) use (&$f) {
        $f[$codigo] = [
            "codigo" => $codigo,
            "puntos" => $puntos,
            "descripcion" => $descripcion,
            "recomendacion" => $recomendacion,
        ];
    };

    // ---- Naturaleza del contrato (Ley 2466 de 2025) ----
    if (mb_strlen(trim($c["descripcion_obra"] ?? "")) < 80) {
        $agregar("DESC_CORTA", 15,
            "La descripción de la obra o labor es corta. La Ley 2466 de 2025 exige que sea precisa y detallada.",
            "Ampliar la descripción: qué se hará, dónde, con qué alcance y cuál es el producto final.");
    }
    if (mb_strlen(trim($c["condicion_terminacion"] ?? "")) < 40) {
        $agregar("TERMINACION_VAGA", 10,
            "La condición que termina la obra o labor está poco definida.",
            "Escribir un hito objetivo y verificable (por ejemplo, entrega y recibo a satisfacción de la obra).");
    }
    if (empty($c["fecha_terminacion_estimada"])) {
        $agregar("SIN_FECHA_FIN", 5,
            "No hay fecha estimada de terminación; el calendario no podrá avisar el cierre de la obra.",
            "Registrar una fecha estimada (es solo una referencia; el contrato termina con la obra).");
    }
    if (isset($c["labor_transitoria"]) && (int) $c["labor_transitoria"] === 0) {
        $agregar("LABOR_PERMANENTE", 30,
            "Se indicó que la labor hace parte de la actividad permanente del empleador. En ese caso el contrato por obra o labor puede ser cuestionado y tratado como indefinido [VERIFICAR Ley 2466 de 2025].",
            "Revisar con un abogado laboralista si procede otra modalidad de contrato.");
    }
    if ($sucesivos > 0) {
        $agregar("CONTRATOS_SUCESIVOS", 20,
            "El trabajador ya tiene $sucesivos contrato(s) vigente(s) o en trámite con esta empresa. Los contratos sucesivos pueden indicar una labor permanente.",
            "Confirmar que se trata de una obra o labor nueva y diferente, y que esté descrita por escrito.");
    }

    // ---- Salario y pagos ----
    $salario = (float) ($c["salario"] ?? 0);
    $smmlv = (float) ($parametros["salario_minimo_mensual"] ?? 0);
    if ($salario > 0 && $smmlv > 0 && $salario <= 2 * $smmlv && (float) ($c["auxilio_transporte"] ?? 0) <= 0) {
        $agregar("SIN_AUXILIO", 20,
            "Devenga hasta 2 salarios mínimos y no se registró auxilio de transporte.",
            "Registrar el auxilio de transporte, o dejar constancia de que la empresa suministra el transporte.");
    }
    if (($c["forma_pago"] ?? "") === "efectivo") {
        $agregar("PAGO_EFECTIVO", 10,
            "El pago en efectivo deja menos evidencia de que se pagó.",
            "Preferir consignación o transferencia; si es efectivo, firmar recibos de pago.");
    }

    // ---- Fechas ----
    if (!empty($c["fecha_firma"]) && !empty($c["fecha_inicio"]) && $c["fecha_firma"] > $c["fecha_inicio"]) {
        $agregar("FIRMA_POSTERIOR", 15,
            "El contrato se firma después de la fecha de inicio: el trabajador empezó sin contrato escrito.",
            "Firmar antes de iniciar labores. Si ya se inició, documentar la fecha real de inicio.");
    }
    $dias = (int) ($c["duracion_periodo_prueba_dias"] ?? 0);
    if (!empty($c["tiene_periodo_prueba"]) && $dias > 30) {
        $agregar("PRUEBA_LARGA", 5,
            "El período de prueba supera 30 días (el máximo permitido es de 2 meses, art. 78 CST).",
            "Confirmar que el período de prueba esté justificado por escrito.");
    }

    // ---- Riesgos laborales y seguridad social ----
    if (empty(trim($e["arl"] ?? ""))) {
        $agregar("SIN_ARL", 30,
            "La empresa no tiene ARL registrada. El trabajador debe estar afiliado a la ARL antes de empezar a trabajar (Ley 1562 de 2012).",
            "Afiliar al trabajador a la ARL y registrar el nombre de la ARL de la empresa.");
    }
    $clase = (int) ($e["clase_riesgo"] ?? 0);
    if ($clase >= 4) {
        $agregar("CLASE_RIESGO_ALTA", $clase === 5 ? 20 : 15,
            "La empresa está en clase de riesgo ARL " . ["", "I", "II", "III", "IV", "V"][$clase] . " (actividad de alto riesgo).",
            "Reforzar el SG-SST: inducción en seguridad, elementos de protección personal y exámenes médicos ocupacionales.");
    }
    if (empty(trim($t["eps"] ?? ""))) {
        $agregar("SIN_EPS", 10,
            "No se registró la EPS del trabajador.",
            "Pedir la EPS al trabajador o afiliarlo antes de iniciar.");
    }
    if (empty(trim($t["fondo_pension"] ?? ""))) {
        $agregar("SIN_PENSION", 10,
            "No se registró el fondo de pensión del trabajador.",
            "Pedir el fondo de pensión al trabajador o afiliarlo antes de iniciar.");
    }

    // ---- Datos del trabajador ----
    if (!empty($t["fecha_nacimiento"])) {
        $edad = (new DateTime($t["fecha_nacimiento"]))->diff(new DateTime())->y;
        if ($edad < 18) {
            $agregar("MENOR_EDAD", 30,
                "El trabajador es menor de 18 años: se requiere autorización del inspector de trabajo y el tratamiento de sus datos exige cuidado especial (Ley 1098 de 2006; Ley 1581 de 2012).",
                "Tramitar la autorización del inspector de trabajo y la de sus representantes legales antes de firmar.");
        }
    }
    if (empty($t["autoriza_datos"])) {
        $agregar("SIN_AUTORIZACION_DATOS", 25,
            "No consta la autorización del trabajador para tratar sus datos personales (Ley 1581 de 2012).",
            "Obtener y registrar la autorización del trabajador (formato en la página del contrato).");
    }

    return $f;
}
?>
