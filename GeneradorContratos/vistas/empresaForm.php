<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../datos/empresaDAO.php";
require_once __DIR__ . "/_encabezado.php";
requerirLogin("login.php");

$id = idGet();
$empresa = null;
if ($id) {
    $empresa = buscarEmpresaPorId($conexion, $id);
    if (!$empresa || !puedeVerEmpresa($empresa)) {
        flash("error", "La empresa no existe o no tiene permiso para verla.");
        header("Location: empresas.php");
        exit();
    }
}
[$dForm, $err] = recuperarFormulario("empresa");
// Datos a mostrar: lo digitado (si hubo errores) o lo guardado.
$d = !empty($dForm) ? $dForm : ($empresa ?? []);

encabezadoPagina($id ? "Editar empresa" : "Registrar empresa", "../");
?>
    <div class="barra-acciones"><a href="empresas.php" class="btn-secundario">← Empresas</a></div>
    <h2><?= $id ? "Editar empresa" : "Registrar empresa" ?></h2>
    <form method="POST" action="../procesos/guardarEmpresa.php" novalidate>
        <?= campoCsrf() ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
        <?php include __DIR__ . "/_camposEmpresa.php"; ?>
        <button type="submit" class="btn-generar"><?= $id ? "Guardar cambios" : "Registrar empresa" ?></button>
    </form>
<?php piePagina("../"); ?>
