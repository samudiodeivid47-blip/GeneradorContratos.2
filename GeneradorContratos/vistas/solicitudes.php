<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../datos/solicitudDAO.php";
require_once __DIR__ . "/_encabezado.php";
requerirLogin("login.php");

$yo = (int) usuarioActual()["id"];
$tipos = ["consulta" => "Consulta", "actualizacion" => "Actualización", "rectificacion" => "Rectificación",
          "supresion" => "Supresión", "revocatoria" => "Revocatoria de la autorización", "reclamo" => "Reclamo"];

$id = idGet();
$detalle = $id ? buscarSolicitudPorId($conexion, $id) : null;
if ($detalle && !puedeSupervisar() && (int) $detalle["registrada_por"] !== $yo) {
    $detalle = null;
}
$lista = listarSolicitudes($conexion, $yo, puedeSupervisar());
$hoy = date("Y-m-d");

encabezadoPagina("Datos personales", "../");
?>
    <h2>Derechos sobre datos personales</h2>
    <p>Aquí se registran y responden las solicitudes de los trabajadores sobre sus datos (Ley 1581 de 2012). Plazos en días hábiles:
       consulta 10, reclamo y demás solicitudes 15. <a href="politicaDatos.php">Ver política</a></p>
    <div class="barra-acciones"><a href="solicitudForm.php" class="btn-primario">Registrar solicitud</a></div>

    <?php if ($detalle) { $cerrada = in_array($detalle["estado"], ["resuelta", "rechazada"], true); ?>
    <section class="caja-datos">
        <h3>Solicitud #<?= (int) $detalle["id"] ?> — <?= h($tipos[$detalle["tipo"]] ?? $detalle["tipo"]) ?></h3>
        <p>Titular: <strong><?= h($detalle["nombre_completo"]) ?></strong> (<?= h($detalle["tipo_documento"] . " " . $detalle["numero_documento"]) ?>)</p>
        <p>Recibida el <?= h(fechaCorta($detalle["fecha_recepcion"])) ?> · Vence el <strong><?= h(fechaCorta($detalle["fecha_limite"])) ?></strong>
           <?= (!$cerrada && $detalle["fecha_limite"] < $hoy) ? '<span class="insignia riesgo-alto">Vencida</span>' : "" ?></p>
        <p><?= nl2br(h($detalle["descripcion"])) ?></p>
        <p>Estado: <span class="insignia estado-<?= h($detalle["estado"] === "resuelta" ? "aprobado" : ($detalle["estado"] === "rechazada" ? "rechazado" : "pendiente_revision")) ?>"><?= h(ucfirst(str_replace("_", " ", $detalle["estado"]))) ?></span></p>
        <?php if ($detalle["respuesta"]) { ?>
            <p><em>Respuesta (<?= h($detalle["atendida_nombre"] ?? "") ?>, <?= h(fechaHoraCorta($detalle["fecha_respuesta"])) ?>):</em> <?= nl2br(h($detalle["respuesta"])) ?></p>
        <?php } ?>
        <?php if (puedeSupervisar() && !$cerrada) { ?>
            <form method="POST" action="../procesos/responderSolicitud.php">
                <?= campoCsrf() ?>
                <input type="hidden" name="id" value="<?= (int) $detalle["id"] ?>">
                <div class="campo"><label>Respuesta al titular</label>
                    <textarea name="respuesta" rows="3"></textarea>
                    <small>Revocatoria resuelta: se retira la autorización y se reevalúa el riesgo. Supresión: el sistema no borra datos solos; decida según el deber legal de conservar los contratos.</small></div>
                <div class="campo"><label>Estado</label>
                    <select name="estado">
                        <option value="en_tramite">En trámite</option>
                        <option value="resuelta">Resuelta</option>
                        <option value="rechazada">Rechazada (con motivo)</option>
                    </select></div>
                <button type="submit" class="btn-generar">Guardar</button>
            </form>
        <?php } ?>
    </section>
    <?php } ?>

    <?php if (empty($lista)) { ?>
        <p>No hay solicitudes registradas.</p>
    <?php } else { ?>
    <div class="tabla-scroll"><table class="tabla-listado">
        <thead><tr><th>#</th><th>Titular</th><th>Tipo</th><th>Recibida</th><th>Vence</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($lista as $s) { $abierta = in_array($s["estado"], ["recibida", "en_tramite"], true); ?>
            <tr>
                <td><?= (int) $s["id"] ?></td>
                <td><?= h($s["nombre_completo"]) ?></td>
                <td><?= h($tipos[$s["tipo"]] ?? $s["tipo"]) ?></td>
                <td><?= h(fechaCorta($s["fecha_recepcion"])) ?></td>
                <td><?= h(fechaCorta($s["fecha_limite"])) ?> <?= ($abierta && $s["fecha_limite"] < $hoy) ? '<span class="insignia riesgo-alto">Vencida</span>' : "" ?></td>
                <td><?= h(ucfirst(str_replace("_", " ", $s["estado"]))) ?></td>
                <td><a href="solicitudes.php?id=<?= (int) $s["id"] ?>">Abrir</a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table></div>
    <?php } ?>
<?php piePagina("../"); ?>
