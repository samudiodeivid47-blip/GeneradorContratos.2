<?php
/*
 * Reglas de validación para las condiciones del contrato por obra o
 * labor determinada.
 *
 * IMPORTANTE: estas validaciones son un apoyo operativo, no un concepto
 * jurídico. Se basan en reglas generales del Código Sustantivo del
 * Trabajo y la Ley 2466 de 2025 conocidas al momento del desarrollo.
 * No sustituyen la revisión de un abogado laboralista para el caso
 * concreto.
 */
function validarContrato($datos, $parametrosLegales)
{
    $errores = [];

    // ---------- Descripción de la obra o labor ----------
    // La Ley 2466 de 2025 exige que la obra o labor se describa de forma
    // "precisa y detallada". Se aplica un mínimo de caracteres y de
    // palabras como filtro operativo contra descripciones genéricas;
    // esto NO garantiza que la descripción sea jurídicamente suficiente,
    // solo evita los casos evidentemente insuficientes.
    $descripcion = trim($datos["descripcion_obra"] ?? "");
    if (empty($descripcion)) {
        $errores["descripcion_obra"] = "La descripción de la obra o labor es obligatoria.";
    } elseif (mb_strlen($descripcion) < 40) {
        $errores["descripcion_obra"] = "La descripción parece demasiado breve o genérica. La ley exige que la obra o labor se describa de forma precisa y detallada.";
    }

    if (empty(trim($datos["cargo"] ?? ""))) {
        $errores["cargo"] = "El cargo es obligatorio.";
    }

    $funciones = trim($datos["funciones"] ?? "");
    if (empty($funciones)) {
        $errores["funciones"] = "Las funciones del trabajador son obligatorias.";
    } elseif (mb_strlen($funciones) < 20) {
        $errores["funciones"] = "Describa las funciones con mayor detalle.";
    }

    if (empty(trim($datos["lugar_ejecucion"] ?? ""))) {
        $errores["lugar_ejecucion"] = "El lugar de ejecución es obligatorio.";
    }

    // ---------- Salario ----------
    $salario = filter_var($datos["salario"] ?? null, FILTER_VALIDATE_FLOAT);
    $salarioMinimo = (float) $parametrosLegales["salario_minimo_mensual"];

    if ($salario === false || $salario <= 0) {
        $errores["salario"] = "El salario debe ser un número mayor que cero.";
    } elseif ($salario < $salarioMinimo) {
        $errores["salario"] = "El salario ingresado (\$" . number_format($salario, 0, ",", ".") .
            ") es inferior al salario mínimo legal mensual vigente para " . $parametrosLegales["anio"] .
            " (\$" . number_format($salarioMinimo, 0, ",", ".") . "). " .
            "Esto solo es válido si el contrato es de tiempo parcial y el salario se calcula proporcionalmente; verifique este caso.";
    }

    // ---------- Jornada ----------
    $horas = filter_var($datos["horas_semanales"] ?? null, FILTER_VALIDATE_FLOAT);
    if ($horas === false || $horas <= 0) {
        $errores["horas_semanales"] = "Las horas semanales deben ser un número mayor que cero.";
    } elseif ($horas > JORNADA_MAXIMA_SEMANAL) {
        $errores["horas_semanales"] = "La jornada máxima legal vigente es de " . JORNADA_MAXIMA_SEMANAL .
            " horas semanales (Ley 2101 de 2021, reducción gradual). Verifique el cronograma vigente si esta validación quedó desactualizada.";
    }

    if (($datos["jornada"] ?? "") === "parcial" && $horas >= JORNADA_MAXIMA_SEMANAL) {
        $errores["jornada"] = "Seleccionó jornada parcial pero las horas semanales son iguales o mayores a la jornada máxima legal. Revise la coherencia.";
    }

    // ---------- Auxilio de transporte ----------
    // El auxilio de transporte legalmente corresponde a quienes devengan
    // hasta dos (2) salarios mínimos y no tienen transporte suministrado
    // por el empleador. Se muestra como advertencia, no como bloqueo,
    // porque depende de condiciones que el sistema no puede verificar
    // por completo (p. ej. si la empresa ya suministra transporte).
    $auxilio = filter_var($datos["auxilio_transporte"] ?? 0, FILTER_VALIDATE_FLOAT);
    $auxilioLegal = (float) $parametrosLegales["auxilio_transporte_mensual"];

    if ($salario !== false && $salario <= (2 * $salarioMinimo) && $auxilio == 0) {
        // No es un error bloqueante: se reporta como advertencia en la revisión legal.
    }

    // ---------- Fechas ----------
    $fechaInicio = DateTime::createFromFormat("Y-m-d", $datos["fecha_inicio"] ?? "");
    if (!$fechaInicio) {
        $errores["fecha_inicio"] = "La fecha de inicio no es válida.";
    }

    if (empty(trim($datos["condicion_terminacion"] ?? ""))) {
        $errores["condicion_terminacion"] = "Debe describirse la condición o hito que determina la finalización de la obra o labor.";
    }

    // ---------- Período de prueba ----------
    // En los contratos por obra o labor, el período de prueba es
    // procedente pero no puede exceder la duración total estimada de la
    // obra ni los límites generales del Código Sustantivo del Trabajo.
    // Este sistema no calcula automáticamente ese límite superior porque
    // depende de la duración estimada de la obra, que no siempre es
    // determinable de antemano; se deja como advertencia para revisión manual.
    if (!empty($datos["tiene_periodo_prueba"]) && $datos["tiene_periodo_prueba"] == 1) {
        $dias = filter_var($datos["duracion_periodo_prueba_dias"] ?? null, FILTER_VALIDATE_INT);
        if ($dias === false || $dias <= 0) {
            $errores["duracion_periodo_prueba_dias"] = "Debe indicar la duración del período de prueba en días.";
        }
    }

    return $errores;
}

/*
 * Construye la sección de "Revisión legal" con tres niveles:
 * completos, advertencias y bloqueantes. No certifica validez jurídica.
 */
function construirRevisionLegal($datos, $errores, $parametrosLegales)
{
    $revision = [
        "completos" => [],
        "advertencias" => [],
        "bloqueantes" => [],
    ];

    // Bloqueantes = todo lo que viene de validarContrato()
    foreach ($errores as $campo => $mensaje) {
        $revision["bloqueantes"][] = $mensaje;
    }

    // Completos (informativo, cuando no hay error en ese campo)
    if (!isset($errores["descripcion_obra"])) {
        $revision["completos"][] = "Descripción de la obra o labor: cumple el mínimo de detalle exigido operativamente.";
    }
    if (!isset($errores["salario"])) {
        $revision["completos"][] = "Salario: igual o superior al mínimo legal vigente.";
    }
    if (!isset($errores["horas_semanales"])) {
        $revision["completos"][] = "Jornada: dentro del máximo legal vigente.";
    }

    // Advertencias (no bloquean, pero requieren revisión humana)
    $salario = filter_var($datos["salario"] ?? null, FILTER_VALIDATE_FLOAT);
    $auxilio = filter_var($datos["auxilio_transporte"] ?? 0, FILTER_VALIDATE_FLOAT);
    $salarioMinimo = (float) $parametrosLegales["salario_minimo_mensual"];

    if ($salario !== false && $salario <= (2 * $salarioMinimo) && $auxilio == 0) {
        $revision["advertencias"][] = "El trabajador devenga hasta 2 salarios mínimos y no se asignó auxilio de transporte. Verifique si le corresponde por ley (salvo que la empresa suministre transporte).";
    }

    if (!empty($datos["tiene_periodo_prueba"]) && $datos["tiene_periodo_prueba"] == 1) {
        $revision["advertencias"][] = "Se incluyó período de prueba. Verifique que su duración no exceda los límites legales ni la duración estimada de la obra.";
    }

    if (empty(trim($datos["beneficios_extralegales"] ?? ""))) {
        $revision["advertencias"][] = "No se registraron beneficios extralegales (opcional, solo si aplica).";
    }

    $revision["advertencias"][] = "Este sistema no verifica automáticamente la clasificación de riesgo laboral ante la ARL ni el dígito de verificación oficial del NIT ante la DIAN.";

    return $revision;
}
?>
