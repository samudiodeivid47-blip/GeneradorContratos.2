<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../datos/contratoDAO.php";
require_once __DIR__ . "/../plantillas/autorizacionDatos.php";
require_once __DIR__ . "/_encabezado.php";
requerirLogin("login.php");

$id = idGet("contrato");
$c = $id ? buscarContratoPorId($conexion, $id) : null;
if (!$c || !puedeVerContrato($c)) {
    flash("error", "El contrato no existe o no tiene permiso para verlo.");
    header("Location: listado.php");
    exit();
}
registrarBitacora($conexion, "autorizacion_imprimir", "trabajador", (int) $c["trabajador_id"], "Formato de autorización generado");
encabezadoPagina("Autorización de datos", "../");
?>
    <div class="barra-acciones no-imprimir">
        <a href="verContrato.php?id=<?= $id ?>" class="btn-secundario">← Contrato</a>
        <button type="button" class="btn-primario" data-imprimir>Imprimir</button>
    </div>
    <div id="documentoContrato"><?= generarAutorizacionDatos($c) ?></div>
<?php piePagina("../"); ?>
