<?php
/*
 * Formato de AUTORIZACIÓN para el tratamiento de datos personales del
 * trabajador (Ley 1581 de 2012). Se imprime desde la página del contrato
 * para que el trabajador lo firme. Modelo general: debe revisarlo un abogado.
 */
function generarAutorizacionDatos($c)
{
    $h = fn($t) => htmlspecialchars((string) $t, ENT_QUOTES, "UTF-8");
    ob_start();
    ?>
    <article class="documento-contrato">
        <header class="encabezado">
            <h1>AUTORIZACIÓN PARA EL TRATAMIENTO DE DATOS PERSONALES</h1>
            <p class="doc-fecha">Ley 1581 de 2012 — Política versión <?= $h(POLITICA_VERSION) ?></p>
        </header>
        <section>
            <p>
                Yo, <strong><?= $h($c["nombre_completo"]) ?></strong>, identificado(a) con
                <?= $h($c["tipo_documento"]) ?> No. <?= $h($c["numero_documento"]) ?>, autorizo de manera previa,
                expresa e informada a <strong><?= $h($c["razon_social"]) ?></strong> (NIT <?= $h($c["nit"]) ?>),
                con domicilio en <?= $h($c["empresa_direccion"]) ?>, <?= $h($c["empresa_ciudad"]) ?>
                (correo <?= $h($c["empresa_correo"]) ?>, teléfono <?= $h($c["empresa_telefono"]) ?>),
                para recolectar, almacenar, usar y consultar mis datos personales.
            </p>
        </section>
        <section>
            <h2>Finalidades</h2>
            <p>Elaborar, ejecutar y archivar mi contrato de trabajo; afiliarme y reportar mis novedades al sistema de
               seguridad social (EPS, pensión, ARL); pagar salarios y prestaciones; cumplir obligaciones legales y
               atender requerimientos de autoridades.</p>
        </section>
        <section>
            <h2>Mis derechos</h2>
            <p>Conozco que puedo conocer, actualizar y rectificar mis datos; solicitar prueba de esta autorización;
               ser informado del uso que se les da; presentar quejas ante la Superintendencia de Industria y Comercio;
               revocar esta autorización y solicitar la supresión de mis datos cuando no exista un deber legal de
               conservarlos. Puedo ejercerlos escribiendo a <?= $h($c["empresa_correo"]) ?>. Las consultas se responden
               en 10 días hábiles y los reclamos en 15 días hábiles. [VERIFICAR]</p>
            <p>Mis datos <strong>no son datos sensibles</strong> salvo que yo decida suministrarlos; no estoy obligado
               a responder preguntas sobre datos sensibles.</p>
        </section>
        <section class="doc-firmas">
            <p>En constancia, firmo en <?= $h($c["empresa_ciudad"]) ?>, el ____ / ____ / ________.</p>
            <div class="firmas-grid">
                <div class="firma-bloque">
                    <div class="firma-linea"></div>
                    <p>TITULAR DE LOS DATOS<br><?= $h($c["nombre_completo"]) ?><br><?= $h($c["tipo_documento"]) ?> <?= $h($c["numero_documento"]) ?></p>
                </div>
            </div>
        </section>
        <footer class="doc-aviso">
            <p>Modelo general generado por ContractPro. No sustituye la revisión de un abogado.</p>
        </footer>
    </article>
    <?php
    return ob_get_clean();
}
?>
