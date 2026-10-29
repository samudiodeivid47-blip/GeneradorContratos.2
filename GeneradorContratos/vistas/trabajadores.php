<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../datos/trabajadorDAO.php";
require_once __DIR__ . "/_encabezado.php";
requerirLogin("login.php");

$q = mb_substr(trim($_GET["q"] ?? ""), 0, 60);
$trabajadores = listarTrabajadores($conexion, (int) usuarioActual()["id"], puedeSupervisar(), $q);
encabezadoPagina("Trabajadores", "../");
?>
    <h2>Trabajadores</h2>
    <div class="barra-acciones">
        <a href="trabajadorForm.php" class="btn-primario">Registrar trabajador</a>
        <form method="GET" class="form-en-linea">
            <input type="search" name="q" value="<?= h($q) ?>" placeholder="Buscar por nombre o documento">
            <button type="submit" class="btn-secundario">Buscar</button>
        </form>
    </div>
    <?php if (empty($trabajadores)) { ?>
        <p>No hay trabajadores registrados<?= $q !== "" ? " con ese criterio" : "" ?>.</p>
    <?php } else { ?>
    <div class="tabla-scroll"><table class="tabla-listado">
        <thead><tr><th>Nombre</th><th>Documento</th><th>Ciudad</th><th>Autorización de datos</th><th>Contratos</th><?php if (puedeSupervisar()) { ?><th>Registró</th><?php } ?><th></th></tr></thead>
        <tbody>
        <?php foreach ($trabajadores as $t) { ?>
            <tr>
                <td><?= h($t["nombre_completo"]) ?></td>
                <td><?= h($t["tipo_documento"] . " " . $t["numero_documento"]) ?></td>
                <td><?= h($t["ciudad"]) ?></td>
                <td><?= $t["autoriza_datos"]
                        ? '<span class="insignia riesgo-bajo">Autorizó ' . h(fechaCorta($t["autorizacion_fecha"])) . '</span>'
                        : '<span class="insignia riesgo-alto">Sin autorización</span>' ?></td>
                <td><?= (int) $t["contratos"] ?></td>
                <?php if (puedeSupervisar()) { ?><td><?= h($t["registrado_por"] ?? "—") ?></td><?php } ?>
                <td><a href="trabajadorForm.php?id=<?= (int) $t["id"] ?>">Ver / editar</a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table></div>
    <?php } ?>
<?php piePagina("../"); ?>
