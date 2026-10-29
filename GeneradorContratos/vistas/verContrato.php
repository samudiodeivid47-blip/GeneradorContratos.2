<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../datos/contratoDAO.php";
require_once __DIR__ . "/../plantillas/contratoObraLabor.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: ../index.php");
    exit();
}

$contrato = buscarContratoPorId($conexion, (int) $_GET["id"]);

if (!$contrato) {
    header("Location: ../index.php");
    exit();
}

$htmlContrato = generarTextoContrato($contrato);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato generado - <?= htmlspecialchars($contrato["nombre_completo"]) ?></title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<div class="contenedor">
    <div class="barra-acciones no-imprimir">
        <a href="../index.php" class="btn-secundario">← Nuevo contrato</a>
        <a href="listado.php" class="btn-secundario">Ver listado</a>
        <button type="button" onclick="window.print()" class="btn-primario">🖨️ Imprimir / Guardar como PDF</button>
        <button type="button" id="btnEditar" class="btn-secundario">✏️ Editar contenido</button>
        <button type="button" id="btnDescargarTexto" class="btn-secundario">⬇️ Descargar (.doc editable)</button>
    </div>

    <div class="nota-edicion no-imprimir" id="notaEdicion" style="display:none;">
        El contenido ahora es editable directamente sobre el documento. Los cambios que hagas aquí
        <strong>no se guardan en la base de datos</strong>; solo afectan lo que imprimas o descargues
        en esta sesión del navegador.
    </div>

    <div id="documentoContrato" contenteditable="false">
        <?= $htmlContrato ?>
    </div>
</div>

<script>
    const btnEditar = document.getElementById("btnEditar");
    const documento = document.getElementById("documentoContrato");
    const notaEdicion = document.getElementById("notaEdicion");
    let editando = false;

    btnEditar.addEventListener("click", function () {
        editando = !editando;
        documento.setAttribute("contenteditable", editando ? "true" : "false");
        documento.classList.toggle("editable", editando);
        notaEdicion.style.display = editando ? "block" : "none";
        btnEditar.textContent = editando ? "✅ Terminar edición" : "✏️ Editar contenido";
    });

    // Descarga del contenido como archivo .doc (HTML válido para Word),
    // que es la vía más simple de "formato editable" sin depender de
    // librerías externas ni de conexión a internet.
    document.getElementById("btnDescargarTexto").addEventListener("click", function () {
        const contenido = documento.innerHTML;
        const plantilla = "<html xmlns:o='urn:schemas-microsoft-com:office:office' " +
            "xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>" +
            "<head><meta charset='utf-8'><title>Contrato</title></head><body>" + contenido + "</body></html>";
        const blob = new Blob(['\ufeff', plantilla], { type: 'application/msword' });
        const enlace = document.createElement("a");
        enlace.href = URL.createObjectURL(blob);
        enlace.download = "contrato_<?= (int) $contrato['id'] ?>.doc";
        document.body.appendChild(enlace);
        enlace.click();
        document.body.removeChild(enlace);
    });
</script>
</body>
</html>
