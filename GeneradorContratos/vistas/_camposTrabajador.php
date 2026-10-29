<?php /* Campos del trabajador. Variables: $d, $err, $yaAutorizado (bool). */ ?>
<div class="campo">
    <label>Nombre completo *</label>
    <input type="text" name="nombre_completo" maxlength="150" value="<?= val($d, 'nombre_completo') ?>" class="<?= claseError($err, 'nombre_completo') ?>">
    <?= errorCampo($err, "nombre_completo") ?>
</div>
<div class="campo-grupo">
    <div class="campo">
        <label>Tipo de documento *</label>
        <select name="tipo_documento" class="<?= claseError($err, 'tipo_documento') ?>">
            <option value="">Seleccione...</option>
            <?php foreach (["CC" => "Cédula de ciudadanía", "CE" => "Cédula de extranjería", "PPT" => "Permiso por Protección Temporal", "PEP" => "Permiso Especial de Permanencia", "PASAPORTE" => "Pasaporte"] as $cod => $et) { ?>
                <option value="<?= $cod ?>" <?= seleccionado($d["tipo_documento"] ?? "", $cod) ?>><?= h($et) ?></option>
            <?php } ?>
        </select>
        <?= errorCampo($err, "tipo_documento") ?>
    </div>
    <div class="campo">
        <label>Número de documento *</label>
        <input type="text" name="numero_documento" value="<?= val($d, 'numero_documento') ?>" class="<?= claseError($err, 'numero_documento') ?>">
        <?= errorCampo($err, "numero_documento") ?>
    </div>
</div>
<div class="campo">
    <label>Fecha de nacimiento</label>
    <input type="date" name="fecha_nacimiento" value="<?= val($d, 'fecha_nacimiento') ?>" class="<?= claseError($err, 'fecha_nacimiento') ?>">
    <?= errorCampo($err, "fecha_nacimiento") ?>
    <small>Si es menor de 18 años se requiere autorización del inspector de trabajo; el contrato queda con riesgo grave.</small>
</div>
<div class="campo">
    <label>Dirección *</label>
    <input type="text" name="direccion_trabajador" value="<?= val($d, 'direccion') ?>" class="<?= claseError($err, 'direccion') ?>">
    <?= errorCampo($err, "direccion") ?>
</div>
<div class="campo-grupo">
    <div class="campo">
        <label>Ciudad *</label>
        <input type="text" name="ciudad_trabajador" value="<?= val($d, 'ciudad') ?>" class="<?= claseError($err, 'ciudad') ?>">
        <?= errorCampo($err, "ciudad") ?>
    </div>
    <div class="campo">
        <label>Teléfono *</label>
        <input type="text" name="telefono_trabajador" value="<?= val($d, 'telefono') ?>" class="<?= claseError($err, 'telefono') ?>">
        <?= errorCampo($err, "telefono") ?>
    </div>
</div>
<div class="campo">
    <label>Correo electrónico *</label>
    <input type="email" name="correo_trabajador" value="<?= val($d, 'correo') ?>" class="<?= claseError($err, 'correo') ?>">
    <?= errorCampo($err, "correo") ?>
</div>
<div class="campo-grupo">
    <div class="campo">
        <label>EPS</label>
        <input type="text" name="eps" value="<?= val($d, 'eps') ?>">
    </div>
    <div class="campo">
        <label>Fondo de pensión</label>
        <input type="text" name="fondo_pension" value="<?= val($d, 'fondo_pension') ?>">
    </div>
</div>

<fieldset class="caja-datos">
    <legend>Autorización de tratamiento de datos personales (Ley 1581 de 2012)</legend>
    <?php if (!empty($yaAutorizado)) { ?>
        <p class="mensaje-ok">La autorización del trabajador ya está registrada. Para retirarla debe presentarse una solicitud de revocatoria en «Datos personales».</p>
    <?php } else { ?>
        <p>Los datos del trabajador solo pueden guardarse con su autorización previa, expresa e informada.
           Lea la <a href="politicaDatos.php" target="_blank">política de tratamiento</a> con el trabajador.</p>
        <div class="campo campo-checkbox">
            <label>
                <input type="checkbox" name="autoriza_datos" value="1" <?= !empty($d["autoriza_datos"]) ? "checked" : "" ?>>
                Confirmo que el trabajador autorizó el tratamiento de sus datos personales *
            </label>
            <?= errorCampo($err, "autoriza_datos") ?>
        </div>
        <div class="campo">
            <label>¿Cómo se obtuvo la autorización? *</label>
            <select name="autorizacion_medio" class="<?= claseError($err, 'autorizacion_medio') ?>">
                <option value="">Seleccione...</option>
                <option value="escrito" <?= seleccionado($d["autorizacion_medio"] ?? "", "escrito") ?>>Por escrito (formato firmado)</option>
                <option value="electronico" <?= seleccionado($d["autorizacion_medio"] ?? "", "electronico") ?>>Por medio electrónico</option>
            </select>
            <?= errorCampo($err, "autorizacion_medio") ?>
            <small>Guarde la prueba de la autorización: puede imprimir el formato desde la página de un contrato.</small>
        </div>
    <?php } ?>
</fieldset>
