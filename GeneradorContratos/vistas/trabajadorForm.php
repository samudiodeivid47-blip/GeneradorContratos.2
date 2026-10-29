<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../datos/trabajadorDAO.php";
require_once __DIR__ . "/_encabezado.php";
requerirLogin("login.php");

$id = idGet();
$trabajador = null;
if ($id) {
    $trabajador = buscarTrabajadorPorId($conexion, $id);
    if (!$trabajador || !puedeVerTrabajador($trabajador)) {
        flash("error", "El trabajador no existe o no tiene permiso para verlo.");
        header("Location: trabajadores.php");
        exit();
    }
}
[$dForm, $err] = recuperarFormulario("trabajador");
$d = !empty($dForm) ? $dForm : ($trabajador ?? []);
$yaAutorizado = $trabajador && (int) $trabajador["autoriza_datos"] === 1;

encabezadoPagina($id ? "Editar trabajador" : "Registrar trabajador", "../");
?>
    <div class="barra-acciones">
        <a href="trabajadores.php" class="btn-secundario">← Trabajadores</a>
        <?php if ($id) { ?><a href="solicitudForm.php?trabajador=<?= (int) $id ?>" class="btn-secundario">Registrar solicitud de datos</a><?php } ?>
    </div>
    <h2><?= $id ? "Editar trabajador" : "Registrar trabajador" ?></h2>
    <form method="POST" action="../procesos/guardarTrabajador.php" novalidate>
        <?= campoCsrf() ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
        <?php include __DIR__ . "/_camposTrabajador.php"; ?>
        <button type="submit" class="btn-generar"><?= $id ? "Guardar cambios" : "Registrar trabajador" ?></button>
    </form>
<?php piePagina("../"); ?>
