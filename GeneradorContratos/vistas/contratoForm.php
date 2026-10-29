<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../logica/servicioContrato.php";
require_once __DIR__ . "/_encabezado.php";
requerirLogin("login.php");

$yo = (int) usuarioActual()["id"];
$id = idGet();
$contrato = null;
if ($id) {
    $contrato = buscarContratoPorId($conexion, $id);
    if (!$contrato || !puedeVerContrato($contrato) || !contratoEditable($contrato)) {
        flash("error", "Ese contrato no se puede corregir (solo el autor, y solo si está rechazado o en borrador).");
        header("Location: listado.php");
        exit();
    }
}
[$dForm, $err] = recuperarFormulario("contrato");
$d = !empty($dForm) ? $dForm : ($contrato ?? []);
if (empty($d) && !$id) {
    $d = ["fecha_firma" => date("Y-m-d"), "labor_transitoria" => 1, "empresa_id" => idGet("empresa"), "trabajador_id" => idGet("trabajador")];
}

$empresas = listarEmpresas($conexion, $yo, puedeSupervisar());
$trabajadores = listarTrabajadores($conexion, $yo, puedeSupervisar());

encabezadoPagina($id ? "Corregir contrato" : "Nuevo contrato", "../");
?>
    <div class="barra-acciones"><a href="listado.php" class="btn-secundario">← Contratos</a></div>
    <h2><?= $id ? "Corregir contrato #" . (int) $id : "Nuevo contrato por obra o labor" ?></h2>

    <?php if ($id && !empty($contrato["observacion_revision"])) { ?>
        <div class="mensaje-error-general">Observación del supervisor: <?= h($contrato["observacion_revision"]) ?></div>
    <?php } ?>

    <?php if (empty($empresas) || empty($trabajadores)) { ?>
        <div class="aviso-legal">
            Para crear un contrato primero registre
            <?= empty($empresas) ? '<a href="empresaForm.php">una empresa</a>' : "" ?>
            <?= (empty($empresas) && empty($trabajadores)) ? " y " : "" ?>
            <?= empty($trabajadores) ? '<a href="trabajadorForm.php">un trabajador</a>' : "" ?>.
            Cada uno tiene su propio formulario. También puede usar el <a href="../asistente.php">asistente de 3 pasos</a>.
        </div>
    <?php } else { ?>
    <form method="POST" action="../procesos/guardarContrato.php" novalidate>
        <?= campoCsrf() ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">

        <fieldset class="caja-datos">
            <legend>Partes</legend>
            <div class="campo-grupo">
                <div class="campo">
                    <label>Empleador *</label>
                    <select name="empresa_id" class="<?= claseError($err, 'empresa_id') ?>">
                        <option value="">Seleccione...</option>
                        <?php foreach ($empresas as $e) { ?>
                            <option value="<?= (int) $e["id"] ?>" <?= seleccionado($d["empresa_id"] ?? "", $e["id"]) ?>><?= h($e["razon_social"] . " (" . $e["nit"] . ")") ?></option>
                        <?php } ?>
                    </select>
                    <?= errorCampo($err, "empresa_id") ?>
                    <small><a href="empresaForm.php">Registrar otra empresa</a></small>
                </div>
                <div class="campo">
                    <label>Trabajador *</label>
                    <select name="trabajador_id" class="<?= claseError($err, 'trabajador_id') ?>">
                        <option value="">Seleccione...</option>
                        <?php foreach ($trabajadores as $t) { ?>
                            <option value="<?= (int) $t["id"] ?>" <?= seleccionado($d["trabajador_id"] ?? "", $t["id"]) ?>><?= h($t["nombre_completo"] . " (" . $t["numero_documento"] . ")") ?></option>
                        <?php } ?>
                    </select>
                    <?= errorCampo($err, "trabajador_id") ?>
                    <small><a href="trabajadorForm.php">Registrar otro trabajador</a></small>
                </div>
            </div>
        </fieldset>

        <?php include __DIR__ . "/_camposContrato.php"; ?>
        <button type="submit" class="btn-generar"><?= $id ? "Corregir y reenviar a revisión" : "Generar contrato" ?></button>
    </form>
    <?php } ?>
<?php piePagina("../"); ?>
