<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../datos/trabajadorDAO.php";
require_once __DIR__ . "/_encabezado.php";
requerirLogin("login.php");

[$d, $err] = recuperarFormulario("solicitud");
if (empty($d)) {
    $d = ["trabajador_id" => idGet("trabajador")];
}
$trabajadores = listarTrabajadores($conexion, (int) usuarioActual()["id"], puedeSupervisar());
encabezadoPagina("Registrar solicitud", "../");
?>
    <div class="barra-acciones"><a href="solicitudes.php" class="btn-secundario">← Solicitudes</a></div>
    <h2>Registrar solicitud de un titular</h2>
    <form method="POST" action="../procesos/guardarSolicitud.php" novalidate>
        <?= campoCsrf() ?>
        <div class="campo"><label>Trabajador (titular) *</label>
            <select name="trabajador_id" class="<?= claseError($err, 'trabajador_id') ?>">
                <option value="">Seleccione...</option>
                <?php foreach ($trabajadores as $t) { ?>
                    <option value="<?= (int) $t["id"] ?>" <?= seleccionado($d["trabajador_id"] ?? "", $t["id"]) ?>><?= h($t["nombre_completo"] . " (" . $t["numero_documento"] . ")") ?></option>
                <?php } ?>
            </select><?= errorCampo($err, "trabajador_id") ?></div>
        <div class="campo"><label>Tipo de solicitud *</label>
            <select name="tipo" class="<?= claseError($err, 'tipo') ?>">
                <option value="">Seleccione...</option>
                <?php foreach (["consulta" => "Consulta (10 días hábiles)", "reclamo" => "Reclamo (15 días hábiles)", "actualizacion" => "Actualización (15)", "rectificacion" => "Rectificación (15)", "supresion" => "Supresión (15)", "revocatoria" => "Revocatoria de la autorización (15)"] as $k => $et) { ?>
                    <option value="<?= $k ?>" <?= seleccionado($d["tipo"] ?? "", $k) ?>><?= h($et) ?></option>
                <?php } ?>
            </select><?= errorCampo($err, "tipo") ?></div>
        <div class="campo"><label>Descripción *</label>
            <textarea name="descripcion" rows="4" class="<?= claseError($err, 'descripcion') ?>"><?= val($d, 'descripcion') ?></textarea><?= errorCampo($err, "descripcion") ?></div>
        <button type="submit" class="btn-generar">Registrar solicitud</button>
    </form>
<?php piePagina("../"); ?>
