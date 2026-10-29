<?php require_once __DIR__ . "/../config/config.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Normativa y fuentes consultadas</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<div class="contenedor">
    <div class="barra-acciones no-imprimir">
        <a href="../index.php" class="btn-secundario">← Volver</a>
    </div>

    <h2>Normativa y fuentes consultadas</h2>

    <p>
        Este sistema incorpora reglas generales basadas en las siguientes normas y fuentes oficiales.
        Se recomienda verificar directamente en la fuente oficial antes de tomar decisiones jurídicas,
        ya que la normativa laboral puede modificarse.
    </p>

    <ul class="lista-normativa">
        <li>
            <strong>Código Sustantivo del Trabajo (Colombia)</strong> — régimen general del contrato de
            trabajo, jornada, prestaciones sociales y terminación del contrato.
            <br><a href="https://www.suin-juriscol.gov.co/viewDocument.asp?id=30019323" target="_blank">Consultar en SUIN-Juriscol</a>
        </li>
        <li>
            <strong>Ley 2466 de 2025</strong> — modifica y precisa las condiciones del contrato de
            trabajo por duración de obra o labor determinada, incluyendo la exigencia de descripción
            precisa y detallada de la obra o labor.
            <br><a href="https://www.suin-juriscol.gov.co/viewDocument.asp?id=30055086" target="_blank">Consultar en SUIN-Juriscol</a>
        </li>
        <li>
            <strong>Ley 2101 de 2021</strong> — reducción gradual de la jornada máxima legal semanal
            (42 horas semanales según el cronograma de implementación vigente).
            <br><a href="https://www.funcionpublica.gov.co/eva/gestornormativo/norma.php" target="_blank">Consultar en Función Pública</a>
        </li>
        <li>
            <strong>Decretos anuales de salario mínimo y auxilio de transporte</strong> — cifras
            vigentes para <?= (int) date("Y") ?>: consultar Ministerio del Trabajo y Presidencia de la
            República para confirmar el decreto vigente al momento del uso.
            <br><a href="https://www.mintrabajo.gov.co/" target="_blank">Ministerio del Trabajo</a>
        </li>
        <li>
            <strong>Ley 1581 de 2012</strong> — régimen general de protección de datos personales,
            aplicable al tratamiento de los datos del trabajador y del empleador dentro del sistema.
            <br><a href="https://www.suin-juriscol.gov.co/" target="_blank">Consultar en SUIN-Juriscol</a>
        </li>
        <li>
            <strong>Sistema General de Riesgos Laborales</strong> (Ley 1562 de 2012 y normas
            reglamentarias) — obligaciones del empleador en materia de afiliación a ARL y Seguridad y
            Salud en el Trabajo (SG-SST).
            <br><a href="https://www.mintrabajo.gov.co/" target="_blank">Ministerio del Trabajo</a>
        </li>
    </ul>

    <div class="aviso-legal">
        <strong>Advertencia:</strong> el desarrollo de este sistema no incluyó una investigación jurídica
        exhaustiva artículo por artículo de cada norma citada. Las cláusulas generadas son un modelo de
        referencia general y deben ser revisadas por un abogado laboralista antes de su uso en un caso
        real, especialmente en lo relativo a indemnizaciones, período de prueba y recargos por trabajo
        suplementario, nocturno, dominical o festivo.
    </div>
</div>
</body>
</html>
