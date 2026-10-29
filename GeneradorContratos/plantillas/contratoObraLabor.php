<?php
/*
 * Plantilla del contrato individual de trabajo por duración de obra o
 * labor determinada. Recibe el arreglo devuelto por buscarContratoPorId()
 * (que ya trae unidos los datos de empresa y trabajador) y retorna el
 * HTML completo del documento con los datos reemplazados.
 *
 * ADVERTENCIA JURÍDICA: esta plantilla es un modelo general construido
 * con reglas conocidas del Código Sustantivo del Trabajo y la Ley 2466
 * de 2025. No sustituye la revisión de un abogado. Las cláusulas
 * marcadas con [VERIFICAR] deben confirmarse contra la normativa vigente
 * y las condiciones particulares del caso antes de firmar el contrato.
 */
function generarTextoContrato($c)
{
    $tipoEmpleador = $c["tipo_empleador"] === "juridica" ? "persona jurídica" : "persona natural";

    $identificacionEmpleador = $c["tipo_empleador"] === "juridica"
        ? "<strong>{$c["razon_social"]}</strong>, identificada con NIT {$c["nit"]}, " .
          "representada legalmente por {$c["representante_legal"]}, " .
          "identificado(a) con documento No. {$c["documento_representante"]}"
        : "<strong>{$c["razon_social"]}</strong>, identificado(a) con documento No. {$c["nit"]}";

    $fechaInicio = date("d/m/Y", strtotime($c["fecha_inicio"]));
    $fechaHoy = date("d/m/Y");

    $salarioTexto = "$" . number_format($c["salario"], 0, ",", ".");
    $auxilioTexto = $c["auxilio_transporte"] > 0
        ? "$" . number_format($c["auxilio_transporte"], 0, ",", ".")
        : "No aplica según lo registrado (verificar procedencia legal)";

    $periodoPrueba = $c["tiene_periodo_prueba"]
        ? "Las partes pactan un período de prueba de {$c["duracion_periodo_prueba_dias"]} días calendario, " .
          "contados a partir de la fecha de inicio, durante el cual cualquiera de las partes podrá dar por " .
          "terminado el contrato unilateralmente. [VERIFICAR: la duración no debe exceder los límites legales " .
          "ni la duración estimada de la obra o labor]."
        : "Las partes acuerdan que este contrato no tendrá período de prueba.";

    $beneficios = !empty($c["beneficios_extralegales"])
        ? nl2br(htmlspecialchars($c["beneficios_extralegales"]))
        : "No se pactan beneficios extralegales adicionales a los previstos por la ley.";

    $observaciones = !empty($c["observaciones"])
        ? nl2br(htmlspecialchars($c["observaciones"]))
        : "Sin observaciones adicionales.";

    $horario = !empty($c["horario"])
        ? htmlspecialchars($c["horario"])
        : "Se establecerá de común acuerdo dentro de los límites legales, respetando el máximo de " . JORNADA_MAXIMA_SEMANAL . " horas semanales.";

    ob_start();
    ?>
    <article class="documento-contrato">
        <header class="doc-header">
            <h1>CONTRATO INDIVIDUAL DE TRABAJO POR DURACIÓN DE OBRA O LABOR DETERMINADA</h1>
            <p class="doc-fecha">Elaborado el <?= $fechaHoy ?></p>
        </header>

        <section>
            <p>
                Entre los suscritos, a saber: de una parte
                <?= $identificacionEmpleador ?>, en calidad de EMPLEADOR, con domicilio en
                <?= htmlspecialchars($c["empresa_ciudad"]) ?>, <?= htmlspecialchars($c["empresa_departamento"]) ?>,
                dirección <?= htmlspecialchars($c["empresa_direccion"]) ?>; y de otra parte
                <strong><?= htmlspecialchars($c["nombre_completo"]) ?></strong>, identificado(a) con
                <?= htmlspecialchars($c["tipo_documento"]) ?> No. <?= htmlspecialchars($c["numero_documento"]) ?>,
                domiciliado(a) en <?= htmlspecialchars($c["trabajador_ciudad"]) ?>,
                en calidad de TRABAJADOR, hemos convenido celebrar el presente contrato de trabajo por duración
                de obra o labor determinada, el cual se regirá por las disposiciones del Código Sustantivo del
                Trabajo, la Ley 2466 de 2025 y demás normas concordantes, y en especial por las siguientes
                cláusulas:
            </p>
        </section>

        <section>
            <h2>PRIMERA — OBJETO DEL CONTRATO Y DESCRIPCIÓN DE LA OBRA O LABOR</h2>
            <p>
                El EMPLEADOR contrata los servicios personales del TRABAJADOR para la ejecución de la siguiente
                obra o labor, descrita de forma precisa y detallada conforme lo exige la ley:
            </p>
            <blockquote><?= nl2br(htmlspecialchars($c["descripcion_obra"])) ?></blockquote>
        </section>

        <section>
            <h2>SEGUNDA — CARGO Y FUNCIONES</h2>
            <p>El TRABAJADOR se desempeñará en el cargo de <strong><?= htmlspecialchars($c["cargo"]) ?></strong>, con las siguientes funciones:</p>
            <blockquote><?= nl2br(htmlspecialchars($c["funciones"])) ?></blockquote>
        </section>

        <section>
            <h2>TERCERA — LUGAR DE TRABAJO</h2>
            <p>El trabajo se ejecutará en: <?= htmlspecialchars($c["lugar_ejecucion"]) ?>.</p>
        </section>

        <section>
            <h2>CUARTA — FECHA DE INICIO, DURACIÓN Y TERMINACIÓN</h2>
            <p>
                El presente contrato inicia el <strong><?= $fechaInicio ?></strong> y su duración está determinada
                por el tiempo que demande la ejecución de la obra o labor descrita en la cláusula primera.
                El contrato terminará cuando ocurra la siguiente condición:
            </p>
            <blockquote><?= nl2br(htmlspecialchars($c["condicion_terminacion"])) ?></blockquote>
            <p>
                [VERIFICAR] Conforme a la Ley 2466 de 2025, el empleador deberá informar al trabajador la
                finalización de la obra o labor con la antelación que establezca la normativa vigente al
                momento de la terminación.
            </p>
        </section>

        <section>
            <h2>QUINTA — PERÍODO DE PRUEBA</h2>
            <p><?= $periodoPrueba ?></p>
        </section>

        <section>
            <h2>SEXTA — REMUNERACIÓN Y FORMA DE PAGO</h2>
            <p>
                El EMPLEADOR pagará al TRABAJADOR un salario mensual de <strong><?= $salarioTexto ?></strong>,
                cancelado de forma <?= htmlspecialchars($c["periodicidad_pago"]) ?> mediante
                <?= htmlspecialchars($c["forma_pago"]) ?>.
            </p>
            <p>Auxilio de transporte: <strong><?= $auxilioTexto ?></strong>.</p>
        </section>

        <section>
            <h2>SÉPTIMA — JORNADA DE TRABAJO</h2>
            <p>
                El TRABAJADOR laborará <?= $c["jornada"] === "completa" ? "jornada completa" : "jornada parcial" ?>
                de <strong><?= $c["horas_semanales"] ?> horas semanales</strong>, dentro del máximo legal vigente
                de <?= JORNADA_MAXIMA_SEMANAL ?> horas semanales. Horario: <?= $horario ?>.
            </p>
            <p>
                [VERIFICAR] El trabajo suplementario, nocturno, dominical y festivo que llegare a ejecutarse se
                reconocerá y pagará conforme a los recargos establecidos en el Código Sustantivo del Trabajo
                vigentes al momento de causarse.
            </p>
        </section>

        <section>
            <h2>OCTAVA — PRESTACIONES SOCIALES, VACACIONES Y SEGURIDAD SOCIAL</h2>
            <p>
                El TRABAJADOR tendrá derecho a las prestaciones sociales, vacaciones y demás beneficios
                establecidos en la ley para los trabajadores vinculados mediante contrato de trabajo.
                El EMPLEADOR afiliará y realizará los aportes correspondientes al Sistema General de
                Seguridad Social Integral (salud, pensión) y al Sistema General de Riesgos Laborales,
                conforme a la normativa vigente.
            </p>
            <p>
                EPS registrada: <?= !empty($c["eps"]) ? htmlspecialchars($c["eps"]) : "Por definir por el trabajador" ?>.
                Fondo de pensión registrado: <?= !empty($c["fondo_pension"]) ? htmlspecialchars($c["fondo_pension"]) : "Por definir por el trabajador" ?>.
                ARL de la empresa: <?= !empty($c["arl"]) ? htmlspecialchars($c["arl"]) : "Por definir" ?>.
            </p>
        </section>

        <section>
            <h2>NOVENA — OBLIGACIONES DEL EMPLEADOR</h2>
            <ul>
                <li>Pagar la remuneración pactada en los términos convenidos.</li>
                <li>Afiliar y realizar los aportes a seguridad social integral y riesgos laborales.</li>
                <li>Suministrar condiciones de trabajo seguras conforme al Sistema de Gestión de Seguridad y Salud en el Trabajo (SG-SST).</li>
                <li>Respetar la dignidad y los derechos fundamentales del trabajador.</li>
                <li>Expedir certificaciones laborales cuando el trabajador lo solicite.</li>
            </ul>
        </section>

        <section>
            <h2>DÉCIMA — OBLIGACIONES DEL TRABAJADOR</h2>
            <ul>
                <li>Ejecutar la obra o labor contratada con dedicación y cuidado.</li>
                <li>Cumplir las instrucciones del empleador relacionadas con el trabajo.</li>
                <li>Guardar la confidencialidad de la información de la empresa a la que tenga acceso.</li>
                <li>Cumplir las normas de seguridad y salud en el trabajo aplicables.</li>
            </ul>
        </section>

        <section>
            <h2>UNDÉCIMA — TERMINACIÓN DEL CONTRATO</h2>
            <p>
                El contrato terminará por la finalización de la obra o labor contratada, o por las demás
                causales previstas en el Código Sustantivo del Trabajo. [VERIFICAR] En caso de terminación
                sin justa causa antes de la finalización de la obra, procederán las indemnizaciones que
                correspondan conforme a la ley vigente al momento de la terminación; este documento no
                calcula dicho valor por depender de circunstancias particulares del caso.
            </p>
        </section>

        <section>
            <h2>DUODÉCIMA — CONFIDENCIALIDAD Y PROTECCIÓN DE DATOS PERSONALES</h2>
            <p>
                Las partes se obligan a mantener la confidencialidad de la información sensible a la que
                tengan acceso con ocasión del contrato. El EMPLEADOR tratará los datos personales del
                TRABAJADOR conforme a la Ley 1581 de 2012 y demás normas de protección de datos personales
                aplicables, únicamente para fines relacionados con la relación laboral.
            </p>
        </section>

        <section>
            <h2>DECIMOTERCERA — BENEFICIOS EXTRALEGALES</h2>
            <p><?= $beneficios ?></p>
        </section>

        <section>
            <h2>DECIMOCUARTA — MODIFICACIONES Y NOTIFICACIONES</h2>
            <p>
                Toda modificación al presente contrato deberá constar por escrito y ser suscrita por ambas
                partes. Las notificaciones se enviarán a las direcciones y correos electrónicos registrados
                por cada parte en este documento.
            </p>
        </section>

        <section>
            <h2>DECIMOQUINTA — LEGISLACIÓN APLICABLE</h2>
            <p>
                El presente contrato se rige por la Constitución Política de Colombia, el Código Sustantivo
                del Trabajo, la Ley 2466 de 2025 y demás normas laborales concordantes vigentes en Colombia.
            </p>
        </section>

        <section>
            <h2>OBSERVACIONES</h2>
            <p><?= $observaciones ?></p>
        </section>

        <section class="doc-firmas">
            <p>Para constancia se firma en <?= htmlspecialchars($c["empresa_ciudad"]) ?>, a los <?= $fechaHoy ?>.</p>
            <div class="firmas-grid">
                <div class="firma-bloque">
                    <div class="firma-linea"></div>
                    <p>EMPLEADOR<br><?= htmlspecialchars($c["razon_social"]) ?></p>
                </div>
                <div class="firma-bloque">
                    <div class="firma-linea"></div>
                    <p>TRABAJADOR<br><?= htmlspecialchars($c["nombre_completo"]) ?></p>
                </div>
            </div>
        </section>

        <footer class="doc-aviso">
            <p>
                Esta herramienta genera un modelo de contrato con base en la información suministrada y en
                la normativa laboral incorporada al sistema. No sustituye la revisión de un abogado ni
                constituye asesoría jurídica. El empleador es responsable de verificar que las condiciones
                particulares de la relación laboral y la normativa vigente sean aplicables a su caso.
                Las secciones marcadas [VERIFICAR] requieren confirmación adicional antes de la firma.
            </p>
        </footer>
    </article>
    <?php
    return ob_get_clean();
}
?>
