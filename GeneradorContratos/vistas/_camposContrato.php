<?php /* Campos del contrato (sin empresa ni trabajador). Variables: $d, $err. */ ?>
<div class="campo">
    <label>Descripción precisa y detallada de la obra o labor *</label>
    <textarea name="descripcion_obra" rows="4" class="<?= claseError($err, 'descripcion_obra') ?>"><?= val($d, 'descripcion_obra') ?></textarea>
    <small>La Ley 2466 de 2025 exige que sea precisa y detallada, no genérica.</small>
    <?= errorCampo($err, "descripcion_obra") ?>
</div>
<div class="campo campo-checkbox">
    <label>
        <input type="checkbox" name="labor_transitoria" value="1" <?= (!isset($d["labor_transitoria"]) || !empty($d["labor_transitoria"])) ? "checked" : "" ?>>
        La labor es temporal y no hace parte de la actividad permanente del empleador
    </label>
</div>
<div class="campo">
    <label>Cargo *</label>
    <input type="text" name="cargo" value="<?= val($d, 'cargo') ?>" class="<?= claseError($err, 'cargo') ?>">
    <?= errorCampo($err, "cargo") ?>
</div>
<div class="campo">
    <label>Funciones *</label>
    <textarea name="funciones" rows="3" class="<?= claseError($err, 'funciones') ?>"><?= val($d, 'funciones') ?></textarea>
    <?= errorCampo($err, "funciones") ?>
</div>
<div class="campo">
    <label>Lugar de ejecución *</label>
    <input type="text" name="lugar_ejecucion" value="<?= val($d, 'lugar_ejecucion') ?>" class="<?= claseError($err, 'lugar_ejecucion') ?>">
    <?= errorCampo($err, "lugar_ejecucion") ?>
</div>
<div class="campo-grupo">
    <div class="campo">
        <label>Salario mensual (COP) *</label>
        <input type="number" step="1" name="salario" value="<?= val($d, 'salario') ?>" class="<?= claseError($err, 'salario') ?>">
        <?= errorCampo($err, "salario") ?>
    </div>
    <div class="campo">
        <label>Auxilio de transporte (COP)</label>
        <input type="number" step="1" name="auxilio_transporte" value="<?= val($d, 'auxilio_transporte') ?>">
    </div>
</div>
<div class="campo-grupo">
    <div class="campo">
        <label>Periodicidad de pago *</label>
        <select name="periodicidad_pago" class="<?= claseError($err, 'periodicidad_pago') ?>">
            <option value="">Seleccione...</option>
            <?php foreach (["semanal" => "Semanal", "quincenal" => "Quincenal", "mensual" => "Mensual"] as $k => $et) { ?>
                <option value="<?= $k ?>" <?= seleccionado($d["periodicidad_pago"] ?? "", $k) ?>><?= $et ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="campo">
        <label>Forma de pago *</label>
        <select name="forma_pago" class="<?= claseError($err, 'forma_pago') ?>">
            <option value="">Seleccione...</option>
            <?php foreach (["consignacion" => "Consignación", "transferencia" => "Transferencia", "efectivo" => "Efectivo"] as $k => $et) { ?>
                <option value="<?= $k ?>" <?= seleccionado($d["forma_pago"] ?? "", $k) ?>><?= $et ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<div class="campo-grupo">
    <div class="campo">
        <label>Jornada *</label>
        <select name="jornada" class="<?= claseError($err, 'jornada') ?>">
            <option value="">Seleccione...</option>
            <option value="completa" <?= seleccionado($d["jornada"] ?? "", "completa") ?>>Completa</option>
            <option value="parcial" <?= seleccionado($d["jornada"] ?? "", "parcial") ?>>Parcial</option>
        </select>
        <?= errorCampo($err, "jornada") ?>
    </div>
    <div class="campo">
        <label>Horas semanales * (máx. <?= JORNADA_MAXIMA_SEMANAL ?>)</label>
        <input type="number" step="0.5" name="horas_semanales" value="<?= val($d, 'horas_semanales') ?>" class="<?= claseError($err, 'horas_semanales') ?>">
        <?= errorCampo($err, "horas_semanales") ?>
    </div>
</div>
<div class="campo">
    <label>Horario (opcional)</label>
    <input type="text" name="horario" value="<?= val($d, 'horario') ?>" placeholder="Ej: lunes a viernes, 8:00 a.m. a 5:00 p.m.">
</div>

<fieldset class="caja-datos">
    <legend>Fechas del contrato</legend>
    <div class="campo-grupo">
        <div class="campo">
            <label>Fecha de firma</label>
            <input type="date" name="fecha_firma" value="<?= val($d, 'fecha_firma') ?>" class="<?= claseError($err, 'fecha_firma') ?>">
            <small>Si la deja vacía se usa la de hoy. Debe ser anterior o igual a la de inicio.</small>
            <?= errorCampo($err, "fecha_firma") ?>
        </div>
        <div class="campo">
            <label>Fecha de inicio *</label>
            <input type="date" name="fecha_inicio" value="<?= val($d, 'fecha_inicio') ?>" class="<?= claseError($err, 'fecha_inicio') ?>">
            <?= errorCampo($err, "fecha_inicio") ?>
        </div>
    </div>
    <div class="campo">
        <label>Fecha estimada de terminación (opcional)</label>
        <input type="date" name="fecha_terminacion_estimada" value="<?= val($d, 'fecha_terminacion_estimada') ?>" class="<?= claseError($err, 'fecha_terminacion_estimada') ?>">
        <small>Solo es una referencia para el calendario: el contrato termina cuando termina la obra.</small>
        <?= errorCampo($err, "fecha_terminacion_estimada") ?>
    </div>
</fieldset>

<div class="campo">
    <label>Condición de terminación (hito que finaliza la obra) *</label>
    <textarea name="condicion_terminacion" rows="3" class="<?= claseError($err, 'condicion_terminacion') ?>"><?= val($d, 'condicion_terminacion') ?></textarea>
    <?= errorCampo($err, "condicion_terminacion") ?>
</div>
<div class="campo campo-checkbox">
    <label>
        <input type="checkbox" name="tiene_periodo_prueba" value="1" <?= !empty($d["tiene_periodo_prueba"]) ? "checked" : "" ?>>
        Incluir período de prueba
    </label>
</div>
<div class="campo">
    <label>Duración del período de prueba (días, máximo <?= PERIODO_PRUEBA_MAX_DIAS ?>)</label>
    <input type="number" name="duracion_periodo_prueba_dias" value="<?= val($d, 'duracion_periodo_prueba_dias') ?>" class="<?= claseError($err, 'duracion_periodo_prueba_dias') ?>">
    <?= errorCampo($err, "duracion_periodo_prueba_dias") ?>
</div>
<div class="campo">
    <label>Beneficios extralegales (opcional)</label>
    <textarea name="beneficios_extralegales" rows="2"><?= val($d, 'beneficios_extralegales') ?></textarea>
</div>
<div class="campo">
    <label>Observaciones adicionales</label>
    <textarea name="observaciones" rows="2"><?= val($d, 'observaciones') ?></textarea>
</div>
