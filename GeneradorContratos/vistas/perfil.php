<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../logica/vista.php";
require_once __DIR__ . "/../datos/usuarioDAO.php";
require_once __DIR__ . "/_encabezado.php";
requerirLogin("login.php");

$u = buscarUsuarioPorId($conexion, (int) usuarioActual()["id"]);
[$dForm, $err] = recuperarFormulario("perfil");
[, $errClave] = recuperarFormulario("clave");
$d = !empty($dForm) ? $dForm : $u;
encabezadoPagina("Mi perfil", "../");
?>
    <h2>Mi perfil</h2>
    <div class="resumen-contrato">
        <p>Usuario: <strong><?= h($u["nombre_usuario"]) ?></strong> · Perfil: <strong><?= h(nombreRol($u["rol"])) ?></strong></p>
        <p>Último acceso: <?= h(fechaHoraCorta($u["ultimo_acceso"])) ?> · Política de datos aceptada:
           <?= $u["politica_aceptada_en"] ? h(fechaHoraCorta($u["politica_aceptada_en"])) . " (v" . h($u["politica_version"]) . ")" : "pendiente" ?></p>
    </div>

    <form method="POST" action="../procesos/actualizarPerfil.php" novalidate>
        <?= campoCsrf() ?>
        <div class="campo"><label>Nombre completo *</label>
            <input type="text" name="nombre_completo" value="<?= val($d, 'nombre_completo') ?>" class="<?= claseError($err, 'nombre_completo') ?>"><?= errorCampo($err, "nombre_completo") ?></div>
        <div class="campo-grupo">
            <div class="campo"><label>Correo</label>
                <input type="email" name="correo" value="<?= val($d, 'correo') ?>" class="<?= claseError($err, 'correo') ?>"><?= errorCampo($err, "correo") ?></div>
            <div class="campo"><label>Teléfono</label>
                <input type="text" name="telefono" value="<?= val($d, 'telefono') ?>" class="<?= claseError($err, 'telefono') ?>"><?= errorCampo($err, "telefono") ?></div>
        </div>
        <div class="campo"><label>Cargo</label>
            <input type="text" name="cargo" value="<?= val($d, 'cargo') ?>" class="<?= claseError($err, 'cargo') ?>"><?= errorCampo($err, "cargo") ?></div>
        <button type="submit" class="btn-primario">Guardar perfil</button>
    </form>

    <h3 id="clave">Cambiar contraseña</h3>
    <form method="POST" action="../procesos/cambiarClave.php" autocomplete="off" novalidate>
        <?= campoCsrf() ?>
        <div class="campo"><label>Contraseña actual *</label>
            <input type="password" name="clave_actual" class="<?= claseError($errClave, 'clave_actual') ?>"><?= errorCampo($errClave, "clave_actual") ?></div>
        <div class="campo-grupo">
            <div class="campo"><label>Contraseña nueva *</label>
                <input type="password" name="clave_nueva" class="<?= claseError($errClave, 'clave_nueva') ?>">
                <small>Mínimo 10 caracteres con mayúsculas, minúsculas y números.</small><?= errorCampo($errClave, "clave_nueva") ?></div>
            <div class="campo"><label>Repetir contraseña nueva *</label>
                <input type="password" name="clave_nueva2" class="<?= claseError($errClave, 'clave_nueva2') ?>"><?= errorCampo($errClave, "clave_nueva2") ?></div>
        </div>
        <button type="submit" class="btn-primario">Cambiar contraseña</button>
    </form>
<?php piePagina("../"); ?>
