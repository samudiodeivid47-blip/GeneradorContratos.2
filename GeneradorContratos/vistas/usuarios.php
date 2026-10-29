<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../datos/usuarioDAO.php";
require_once __DIR__ . "/_encabezado.php";
requerirRol(["administrador"], "login.php", "../index.php");

$usuarios = listarUsuarios($conexion);
[$d, $err] = recuperarFormulario("usuario_nuevo");
$yo = (int) usuarioActual()["id"];
encabezadoPagina("Usuarios", "../");
?>
    <h2>Usuarios y perfiles</h2>
    <p><strong>Administrador</strong>: todo. <strong>Supervisor</strong>: revisa contratos, controla riesgos y atiende solicitudes de datos.
       <strong>Usuario</strong>: genera contratos y ve solo lo que registró.</p>

    <div class="tabla-scroll"><table class="tabla-listado">
        <thead><tr><th>Usuario</th><th>Perfil</th><th>Estado</th><th>Último acceso</th><th>Política</th><th>Contratos</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($usuarios as $u) { $esYo = (int) $u["id"] === $yo; ?>
            <tr>
                <td><?= h($u["nombre_completo"]) ?><br><small><?= h($u["nombre_usuario"]) ?><?= $u["correo"] ? " · " . h($u["correo"]) : "" ?></small></td>
                <td>
                    <?php if ($esYo) { echo h(nombreRol($u["rol"])) . " (usted)"; } else { ?>
                    <form method="POST" action="../procesos/gestionarUsuario.php" class="form-en-linea">
                        <?= campoCsrf() ?>
                        <input type="hidden" name="accion" value="rol"><input type="hidden" name="id" value="<?= (int) $u["id"] ?>">
                        <select name="rol" data-autoenviar>
                            <?php foreach (["usuario", "supervisor", "administrador"] as $r) { ?>
                                <option value="<?= $r ?>" <?= seleccionado($u["rol"], $r) ?>><?= h(nombreRol($r)) ?></option>
                            <?php } ?>
                        </select>
                    </form>
                    <?php } ?>
                </td>
                <td><?= $u["activo"] ? '<span class="insignia riesgo-bajo">Activa</span>' : '<span class="insignia riesgo-alto">Desactivada</span>' ?>
                    <?= cuentaBloqueada($u) ? '<span class="insignia riesgo-medio">Bloqueada</span>' : "" ?></td>
                <td><?= h(fechaHoraCorta($u["ultimo_acceso"])) ?></td>
                <td><?= $u["politica_version"] ? "v" . h($u["politica_version"]) : '<span class="insignia riesgo-medio">Pendiente</span>' ?></td>
                <td><?= (int) $u["contratos"] ?></td>
                <td>
                    <?php if (!$esYo) { ?>
                    <form method="POST" action="../procesos/gestionarUsuario.php" class="form-en-linea">
                        <?= campoCsrf() ?>
                        <input type="hidden" name="accion" value="activo"><input type="hidden" name="id" value="<?= (int) $u["id"] ?>">
                        <input type="hidden" name="activo" value="<?= $u["activo"] ? 0 : 1 ?>">
                        <button type="submit" class="btn-secundario"><?= $u["activo"] ? "Desactivar" : "Activar" ?></button>
                    </form>
                    <?php } ?>
                    <?php if (cuentaBloqueada($u)) { ?>
                    <form method="POST" action="../procesos/gestionarUsuario.php" class="form-en-linea">
                        <?= campoCsrf() ?>
                        <input type="hidden" name="accion" value="desbloquear"><input type="hidden" name="id" value="<?= (int) $u["id"] ?>">
                        <button type="submit" class="btn-secundario">Desbloquear</button>
                    </form>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table></div>

    <h3 id="nuevo">Crear cuenta</h3>
    <form method="POST" action="../procesos/gestionarUsuario.php" autocomplete="off">
        <?= campoCsrf() ?>
        <input type="hidden" name="accion" value="crear">
        <div class="campo-grupo">
            <div class="campo"><label>Nombre completo *</label>
                <input type="text" name="nombre_completo" value="<?= val($d, 'nombre_completo') ?>" class="<?= claseError($err, 'nombre_completo') ?>"><?= errorCampo($err, "nombre_completo") ?></div>
            <div class="campo"><label>Usuario *</label>
                <input type="text" name="nombre_usuario" value="<?= val($d, 'nombre_usuario') ?>" class="<?= claseError($err, 'nombre_usuario') ?>"><?= errorCampo($err, "nombre_usuario") ?></div>
        </div>
        <div class="campo-grupo">
            <div class="campo"><label>Correo</label>
                <input type="email" name="correo" value="<?= val($d, 'correo') ?>" class="<?= claseError($err, 'correo') ?>"><?= errorCampo($err, "correo") ?></div>
            <div class="campo"><label>Perfil *</label>
                <select name="rol">
                    <?php foreach (["usuario", "supervisor", "administrador"] as $r) { ?>
                        <option value="<?= $r ?>" <?= seleccionado($d["rol"] ?? "usuario", $r) ?>><?= h(nombreRol($r)) ?></option>
                    <?php } ?>
                </select></div>
        </div>
        <div class="campo"><label>Contraseña temporal *</label>
            <input type="password" name="clave" class="<?= claseError($err, 'clave') ?>">
            <small>Mínimo 10 caracteres con mayúsculas, minúsculas y números. Entréguela por un medio seguro.</small>
            <?= errorCampo($err, "clave") ?></div>
        <button type="submit" class="btn-primario">Crear cuenta</button>
    </form>
<?php piePagina("../"); ?>
