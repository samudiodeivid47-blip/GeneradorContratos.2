<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../datos/bitacoraDAO.php";
require_once __DIR__ . "/_encabezado.php";
requerirRol(["supervisor", "administrador"], "login.php", "../index.php");

$acciones = accionesBitacora($conexion);
$accion = in_array($_GET["accion"] ?? "", $acciones, true) ? $_GET["accion"] : "";
$filas = listarBitacora($conexion, $accion, 200);
encabezadoPagina("Bitácora", "../");
?>
    <h2>Bitácora de seguridad y tratamiento de datos</h2>
    <p>Últimos 200 eventos. No se pueden editar ni borrar desde la aplicación.</p>
    <form method="GET" class="form-en-linea">
        <select name="accion" data-autoenviar>
            <option value="">Todas las acciones</option>
            <?php foreach ($acciones as $a) { ?><option value="<?= h($a) ?>" <?= seleccionado($accion, $a) ?>><?= h($a) ?></option><?php } ?>
        </select>
        <button type="submit" class="btn-secundario">Filtrar</button>
    </form>
    <div class="tabla-scroll"><table class="tabla-listado">
        <thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Entidad</th><th>Detalle</th><th>IP</th></tr></thead>
        <tbody>
        <?php foreach ($filas as $f) { ?>
            <tr>
                <td><?= h(fechaHoraCorta($f["fecha"])) ?></td>
                <td><?= h($f["nombre_usuario"] ?? "—") ?></td>
                <td><?= h($f["accion"]) ?></td>
                <td><?= h($f["entidad"] . ($f["entidad_id"] ? " #" . $f["entidad_id"] : "")) ?></td>
                <td><?= h($f["detalle"]) ?></td>
                <td><?= h($f["ip"]) ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table></div>
<?php piePagina("../"); ?>
