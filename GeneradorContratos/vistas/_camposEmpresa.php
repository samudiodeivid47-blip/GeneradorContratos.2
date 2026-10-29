<?php /* Campos del empleador. Variables: $d (datos), $err (errores). */ ?>
<div class="campo">
    <label>Tipo de empleador *</label>
    <select name="tipo_empleador" class="<?= claseError($err, 'tipo_empleador') ?>">
        <option value="">Seleccione...</option>
        <option value="natural" <?= seleccionado($d["tipo_empleador"] ?? "", "natural") ?>>Persona natural</option>
        <option value="juridica" <?= seleccionado($d["tipo_empleador"] ?? "", "juridica") ?>>Persona jurídica</option>
    </select>
    <?= errorCampo($err, "tipo_empleador") ?>
</div>
<div class="campo">
    <label>Nombre o razón social *</label>
    <input type="text" name="razon_social" maxlength="150" value="<?= val($d, 'razon_social') ?>" class="<?= claseError($err, 'razon_social') ?>">
    <?= errorCampo($err, "razon_social") ?>
</div>
<div class="campo">
    <label>NIT o documento de identificación *</label>
    <input type="text" name="nit" placeholder="900123456-7" value="<?= val($d, 'nit') ?>" class="<?= claseError($err, 'nit') ?>">
    <?= errorCampo($err, "nit") ?>
</div>
<div class="campo-grupo" id="grupoRepresentante">
    <div class="campo">
        <label>Nombre del representante legal</label>
        <input type="text" name="representante_legal" value="<?= val($d, 'representante_legal') ?>" class="<?= claseError($err, 'representante_legal') ?>">
        <?= errorCampo($err, "representante_legal") ?>
    </div>
    <div class="campo">
        <label>Documento del representante legal</label>
        <input type="text" name="documento_representante" value="<?= val($d, 'documento_representante') ?>" class="<?= claseError($err, 'documento_representante') ?>">
        <?= errorCampo($err, "documento_representante") ?>
    </div>
</div>
<div class="campo">
    <label>Dirección *</label>
    <input type="text" name="direccion_empresa" value="<?= val($d, 'direccion') ?>" class="<?= claseError($err, 'direccion') ?>">
    <?= errorCampo($err, "direccion") ?>
</div>
<div class="campo-grupo">
    <div class="campo">
        <label>Ciudad *</label>
        <input type="text" name="ciudad_empresa" value="<?= val($d, 'ciudad') ?>" class="<?= claseError($err, 'ciudad') ?>">
        <?= errorCampo($err, "ciudad") ?>
    </div>
    <div class="campo">
        <label>Departamento *</label>
        <input type="text" name="departamento_empresa" value="<?= val($d, 'departamento') ?>" class="<?= claseError($err, 'departamento') ?>">
        <?= errorCampo($err, "departamento") ?>
    </div>
</div>
<div class="campo-grupo">
    <div class="campo">
        <label>Teléfono *</label>
        <input type="text" name="telefono_empresa" value="<?= val($d, 'telefono') ?>" class="<?= claseError($err, 'telefono') ?>">
        <?= errorCampo($err, "telefono") ?>
    </div>
    <div class="campo">
        <label>Correo electrónico *</label>
        <input type="email" name="correo_empresa" value="<?= val($d, 'correo') ?>" class="<?= claseError($err, 'correo') ?>">
        <?= errorCampo($err, "correo") ?>
    </div>
</div>
<div class="campo">
    <label>Actividad económica *</label>
    <input type="text" name="actividad_economica" value="<?= val($d, 'actividad_economica') ?>" class="<?= claseError($err, 'actividad_economica') ?>">
    <?= errorCampo($err, "actividad_economica") ?>
</div>
<div class="campo-grupo">
    <div class="campo">
        <label>ARL (si ya está afiliada)</label>
        <input type="text" name="arl" value="<?= val($d, 'arl') ?>" class="<?= claseError($err, 'arl') ?>">
        <small>Sin ARL el contrato queda con riesgo grave.</small>
        <?= errorCampo($err, "arl") ?>
    </div>
    <div class="campo">
        <label>Clase de riesgo ARL</label>
        <select name="clase_riesgo" class="<?= claseError($err, 'clase_riesgo') ?>">
            <option value="">No sé / sin definir</option>
            <?php foreach ([1 => "I (mínimo)", 2 => "II (bajo)", 3 => "III (medio)", 4 => "IV (alto)", 5 => "V (máximo)"] as $n => $et) { ?>
                <option value="<?= $n ?>" <?= seleccionado($d["clase_riesgo"] ?? "", $n) ?>><?= h($et) ?></option>
            <?php } ?>
        </select>
        <?= errorCampo($err, "clase_riesgo") ?>
    </div>
</div>
