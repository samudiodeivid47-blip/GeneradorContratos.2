<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../datos/empresaDAO.php";
require_once __DIR__ . "/_encabezado.php";
requerirLogin("login.php");

$q = mb_substr(trim($_GET["q"] ?? ""), 0, 60);
$empresas = listarEmpresas($conexion, (int) usuarioActual()["id"], puedeSupervisar(), $q);
encabezadoPagina("Empresas", "../");
?>
    <h2>Empresas (empleadores)</h2>
    <div class="barra-acciones">
        <a href="empresaForm.php" class="btn-primario">Registrar empresa</a>
        <form method="GET" class="form-en-linea">
            <input type="search" name="q" value="<?= h($q) ?>" placeholder="Buscar por nombre o NIT">
            <button type="submit" class="btn-secundario">Buscar</button>
        </form>
    </div>
    <?php if (empty($empresas)) { ?>
        <p>No hay empresas registradas<?= $q !== "" ? " con ese criterio" : "" ?>.</p>
    <?php } else { ?>
    <div class="tabla-scroll"><table class="tabla-listado">
        <thead><tr><th>Razón social</th><th>NIT</th><th>Ciudad</th><th>ARL / clase</th><th>Contratos</th><?php if (puedeSupervisar()) { ?><th>Registró</th><?php } ?><th></th></tr></thead>
        <tbody>
        <?php foreach ($empresas as $e) { ?>
            <tr>
                <td><?= h($e["razon_social"]) ?></td>
                <td><?= h($e["nit"]) ?></td>
                <td><?= h($e["ciudad"]) ?></td>
                <td><?= $e["arl"] ? h($e["arl"]) : '<span class="insignia riesgo-alto">Sin ARL</span>' ?>
                    <?= $e["clase_riesgo"] ? " · clase " . h(["", "I", "II", "III", "IV", "V"][(int) $e["clase_riesgo"]]) : "" ?></td>
                <td><?= (int) $e["contratos"] ?></td>
                <?php if (puedeSupervisar()) { ?><td><?= h($e["registrada_por"] ?? "—") ?></td><?php } ?>
                <td><a href="empresaForm.php?id=<?= (int) $e["id"] ?>">Ver / editar</a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table></div>
    <?php } ?>
<?php piePagina("../"); ?>
