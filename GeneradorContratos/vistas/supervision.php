<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../logica/servicioContrato.php";
require_once __DIR__ . "/_encabezado.php";
requerirRol(["supervisor", "administrador"], "login.php", "../index.php");

$yo = (int) usuarioActual()["id"];
$cola = listarContratos($conexion, $yo, true, "pendiente_revision");
// Primero los de más riesgo.
usort($cola, fn($a, $b) => (int) $b["puntaje_riesgo"] <=> (int) $a["puntaje_riesgo"]);
$matriz = matrizRiesgos($conexion);
$actividad = actividadPorUsuario($conexion);

encabezadoPagina("Supervisión", "../");
?>
    <h2>Panel de supervisión</h2>

    <h3>Cola de revisión (mayor riesgo primero)</h3>
    <?php if (empty($cola)) { ?>
        <p>No hay contratos pendientes de revisión.</p>
    <?php } else { ?>
    <div class="tabla-scroll"><table class="tabla-listado">
        <thead><tr><th>#</th><th>Trabajador</th><th>Empresa</th><th>Autor</th><th>Riesgo</th><th>Graves abiertos</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($cola as $c) { ?>
            <tr>
                <td><?= (int) $c["id"] ?></td>
                <td><?= h($c["nombre_completo"]) ?></td>
                <td><?= h($c["razon_social"]) ?></td>
                <td><?= h($c["generado_por"] ?? "—") ?></td>
                <td><?= etiquetaRiesgo($c["nivel_riesgo"], $c["puntaje_riesgo"]) ?></td>
                <td><?= (int) $c["graves_abiertos"] ?></td>
                <td><a href="verContrato.php?id=<?= (int) $c["id"] ?>#riesgo"><?= (int) $c["usuario_id"] === $yo ? "Ver" : "Revisar" ?></a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table></div>
    <?php } ?>

    <h3>Matriz de riesgos (contratos no anulados)</h3>
    <?php if (empty($matriz)) { ?>
        <p>Aún no hay factores de riesgo registrados.</p>
    <?php } else { ?>
    <div class="tabla-scroll"><table class="tabla-listado">
        <thead><tr><th>Factor</th><th>Puntos</th><th>Abiertos</th><th>Mitigados</th><th>Aceptados</th></tr></thead>
        <tbody>
        <?php foreach ($matriz as $f) { ?>
            <tr>
                <td><strong><?= h($f["codigo"]) ?></strong><br><small><?= h($f["descripcion"]) ?></small></td>
                <td><?= (int) $f["puntos"] ?></td>
                <td><?= (int) $f["abiertos"] ?></td>
                <td><?= (int) $f["mitigados"] ?></td>
                <td><?= (int) $f["aceptados"] ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table></div>
    <?php } ?>

    <h3>Actividad por usuario</h3>
    <div class="tabla-scroll"><table class="tabla-listado">
        <thead><tr><th>Usuario</th><th>Perfil</th><th>Contratos</th><th>Pendientes</th><th>Rechazados</th><th>Riesgo alto</th><th>Último</th></tr></thead>
        <tbody>
        <?php foreach ($actividad as $a) { ?>
            <tr>
                <td><?= h($a["nombre_completo"]) ?> <small>(<?= h($a["nombre_usuario"]) ?>)</small></td>
                <td><?= h(nombreRol($a["rol"])) ?></td>
                <td><?= (int) $a["contratos"] ?></td>
                <td><?= (int) $a["pendientes"] ?></td>
                <td><?= (int) $a["rechazados"] ?></td>
                <td><?= (int) $a["riesgo_alto"] ?></td>
                <td><?= h(fechaCorta($a["ultimo"])) ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table></div>
<?php piePagina("../"); ?>
