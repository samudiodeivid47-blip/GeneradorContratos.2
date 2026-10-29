<?php
// Esta vista se usa tanto para el primer ingreso (formulario vacío)
// como para el reingreso tras errores de validación (conserva los
// datos ya digitados y muestra los mensajes de error).

$datosEmpresa    = $datosEmpresa    ?? [];
$datosTrabajador = $datosTrabajador ?? [];
$datosContrato   = $datosContrato   ?? [];
$todosLosErrores = $todosLosErrores ?? [];
$mensaje         = $mensaje ?? "";

// Helper para no repetir htmlspecialchars() en cada campo.
function val($arr, $clave)
{
    return htmlspecialchars($arr[$clave] ?? "", ENT_QUOTES, "UTF-8");
}

function errorCampo($errores, $clave)
{
    if (!empty($errores[$clave])) {
        return '<span class="error-campo">' . htmlspecialchars($errores[$clave]) . '</span>';
    }
    return "";
}

function claseError($errores, $clave)
{
    return !empty($errores[$clave]) ? "campo-invalido" : "";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar contrato por obra o labor</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<div class="contenedor">
    <header class="encabezado">
        <h1>Generador de Contratos por Obra o Labor Determinada</h1>
        <p class="subtitulo">Microempresas — Colombia</p>
    </header>

    <div class="aviso-legal">
        <strong>Aviso legal:</strong> esta herramienta genera un modelo de contrato con base en la
        información suministrada y en la normativa laboral incorporada al sistema. No sustituye la
        revisión de un abogado ni constituye asesoría jurídica. El empleador es responsable de
        verificar que las condiciones particulares de la relación laboral y la normativa vigente
        sean aplicables a su caso.
        <a href="vistas/normativa.php" target="_blank">Ver normativa y fuentes consultadas</a>.
    </div>

    <?php if (!empty($mensaje)) { ?>
        <div class="mensaje-error-general"><?= htmlspecialchars($mensaje) ?></div>
    <?php } ?>

    <?php if (!empty($todosLosErrores)) { ?>
        <div class="mensaje-error-general">
            Se encontraron <?= count($todosLosErrores) ?> campo(s) que deben corregirse antes de generar el contrato.
            Revise los mensajes marcados en rojo.
        </div>
    <?php } ?>

    <div class="indicador-pasos">
        <div class="paso activo" data-paso="1">1. Empleador</div>
        <div class="paso" data-paso="2">2. Trabajador</div>
        <div class="paso" data-paso="3">3. Contrato</div>
    </div>

    <form action="procesos/generarContrato.php" method="POST" id="formContrato" novalidate>

        <!-- ===================== PASO 1: EMPRESA ===================== -->
        <fieldset class="seccion-paso" data-seccion="1">
            <legend>Datos del empleador</legend>

            <div class="campo">
                <label>Tipo de empleador *</label>
                <select name="tipo_empleador" class="<?= claseError($todosLosErrores, 'tipo_empleador') ?>">
                    <option value="">Seleccione...</option>
                    <option value="natural" <?= ($datosEmpresa["tipo_empleador"] ?? "") === "natural" ? "selected" : "" ?>>Persona natural</option>
                    <option value="juridica" <?= ($datosEmpresa["tipo_empleador"] ?? "") === "juridica" ? "selected" : "" ?>>Persona jurídica</option>
                </select>
                <?= errorCampo($todosLosErrores, "tipo_empleador") ?>
            </div>

            <div class="campo">
                <label>Nombre o razón social *</label>
                <input type="text" name="razon_social" value="<?= val($datosEmpresa, 'razon_social') ?>" class="<?= claseError($todosLosErrores, 'razon_social') ?>">
                <?= errorCampo($todosLosErrores, "razon_social") ?>
            </div>

            <div class="campo">
                <label>NIT o documento de identificación *</label>
                <input type="text" name="nit" placeholder="900123456-7" value="<?= val($datosEmpresa, 'nit') ?>" class="<?= claseError($todosLosErrores, 'nit') ?>">
                <?= errorCampo($todosLosErrores, "nit") ?>
            </div>

            <div class="campo-grupo" id="grupoRepresentante">
                <div class="campo">
                    <label>Nombre del representante legal</label>
                    <input type="text" name="representante_legal" value="<?= val($datosEmpresa, 'representante_legal') ?>" class="<?= claseError($todosLosErrores, 'representante_legal') ?>">
                    <?= errorCampo($todosLosErrores, "representante_legal") ?>
                </div>
                <div class="campo">
                    <label>Documento del representante legal</label>
                    <input type="text" name="documento_representante" value="<?= val($datosEmpresa, 'documento_representante') ?>" class="<?= claseError($todosLosErrores, 'documento_representante') ?>">
                    <?= errorCampo($todosLosErrores, "documento_representante") ?>
                </div>
            </div>

            <div class="campo">
                <label>Dirección *</label>
                <input type="text" name="direccion_empresa" value="<?= val($datosEmpresa, 'direccion') ?>" class="<?= claseError($todosLosErrores, 'direccion') ?>">
                <?= errorCampo($todosLosErrores, "direccion") ?>
            </div>

            <div class="campo-grupo">
                <div class="campo">
                    <label>Ciudad *</label>
                    <input type="text" name="ciudad_empresa" value="<?= val($datosEmpresa, 'ciudad') ?>" class="<?= claseError($todosLosErrores, 'ciudad') ?>">
                    <?= errorCampo($todosLosErrores, "ciudad") ?>
                </div>
                <div class="campo">
                    <label>Departamento *</label>
                    <input type="text" name="departamento_empresa" value="<?= val($datosEmpresa, 'departamento') ?>" class="<?= claseError($todosLosErrores, 'departamento') ?>">
                    <?= errorCampo($todosLosErrores, "departamento") ?>
                </div>
            </div>

            <div class="campo-grupo">
                <div class="campo">
                    <label>Teléfono *</label>
                    <input type="text" name="telefono_empresa" value="<?= val($datosEmpresa, 'telefono') ?>" class="<?= claseError($todosLosErrores, 'telefono') ?>">
                    <?= errorCampo($todosLosErrores, "telefono") ?>
                </div>
                <div class="campo">
                    <label>Correo electrónico *</label>
                    <input type="email" name="correo_empresa" value="<?= val($datosEmpresa, 'correo') ?>" class="<?= claseError($todosLosErrores, 'correo') ?>">
                    <?= errorCampo($todosLosErrores, "correo") ?>
                </div>
            </div>

            <div class="campo">
                <label>Actividad económica *</label>
                <input type="text" name="actividad_economica" value="<?= val($datosEmpresa, 'actividad_economica') ?>" class="<?= claseError($todosLosErrores, 'actividad_economica') ?>">
                <?= errorCampo($todosLosErrores, "actividad_economica") ?>
            </div>

            <div class="campo">
                <label>ARL (si ya está afiliada)</label>
                <input type="text" name="arl" value="<?= val($datosEmpresa, 'arl') ?>">
            </div>

            <div class="navegacion-pasos">
                <button type="button" class="btn-siguiente">Siguiente →</button>
            </div>
        </fieldset>

        <!-- ===================== PASO 2: TRABAJADOR ===================== -->
        <fieldset class="seccion-paso oculto" data-seccion="2">
            <legend>Datos del trabajador</legend>

            <div class="campo">
                <label>Nombre completo *</label>
                <input type="text" name="nombre_completo" value="<?= val($datosTrabajador, 'nombre_completo') ?>" class="<?= claseError($todosLosErrores, 'nombre_completo') ?>">
                <?= errorCampo($todosLosErrores, "nombre_completo") ?>
            </div>

            <div class="campo-grupo">
                <div class="campo">
                    <label>Tipo de documento *</label>
                    <select name="tipo_documento" class="<?= claseError($todosLosErrores, 'tipo_documento') ?>">
                        <option value="">Seleccione...</option>
                        <?php foreach (["CC" => "Cédula de ciudadanía", "CE" => "Cédula de extranjería", "PPT" => "Permiso por Protección Temporal", "PEP" => "Permiso Especial de Permanencia", "PASAPORTE" => "Pasaporte"] as $codigo => $etiqueta) { ?>
                            <option value="<?= $codigo ?>" <?= ($datosTrabajador["tipo_documento"] ?? "") === $codigo ? "selected" : "" ?>><?= $etiqueta ?></option>
                        <?php } ?>
                    </select>
                    <?= errorCampo($todosLosErrores, "tipo_documento") ?>
                </div>
                <div class="campo">
                    <label>Número de documento *</label>
                    <input type="text" name="numero_documento" value="<?= val($datosTrabajador, 'numero_documento') ?>" class="<?= claseError($todosLosErrores, 'numero_documento') ?>">
                    <?= errorCampo($todosLosErrores, "numero_documento") ?>
                </div>
            </div>

            <div class="campo">
                <label>Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="<?= val($datosTrabajador, 'fecha_nacimiento') ?>" class="<?= claseError($todosLosErrores, 'fecha_nacimiento') ?>">
                <?= errorCampo($todosLosErrores, "fecha_nacimiento") ?>
                <small>Si el trabajador es menor de edad, se requiere autorización del inspector de trabajo. Consulte este caso con un profesional.</small>
            </div>

            <div class="campo">
                <label>Dirección *</label>
                <input type="text" name="direccion_trabajador" value="<?= val($datosTrabajador, 'direccion') ?>" class="<?= claseError($todosLosErrores, 'direccion') ?>">
                <?= errorCampo($todosLosErrores, "direccion") ?>
            </div>

            <div class="campo-grupo">
                <div class="campo">
                    <label>Ciudad *</label>
                    <input type="text" name="ciudad_trabajador" value="<?= val($datosTrabajador, 'ciudad') ?>" class="<?= claseError($todosLosErrores, 'ciudad') ?>">
                    <?= errorCampo($todosLosErrores, "ciudad") ?>
                </div>
                <div class="campo">
                    <label>Teléfono *</label>
                    <input type="text" name="telefono_trabajador" value="<?= val($datosTrabajador, 'telefono') ?>" class="<?= claseError($todosLosErrores, 'telefono') ?>">
                    <?= errorCampo($todosLosErrores, "telefono") ?>
                </div>
            </div>

            <div class="campo">
                <label>Correo electrónico *</label>
                <input type="email" name="correo_trabajador" value="<?= val($datosTrabajador, 'correo') ?>" class="<?= claseError($todosLosErrores, 'correo') ?>">
                <?= errorCampo($todosLosErrores, "correo") ?>
            </div>

            <div class="campo-grupo">
                <div class="campo">
                    <label>EPS</label>
                    <input type="text" name="eps" value="<?= val($datosTrabajador, 'eps') ?>">
                </div>
                <div class="campo">
                    <label>Fondo de pensión</label>
                    <input type="text" name="fondo_pension" value="<?= val($datosTrabajador, 'fondo_pension') ?>">
                </div>
            </div>

            <div class="navegacion-pasos">
                <button type="button" class="btn-anterior">← Anterior</button>
                <button type="button" class="btn-siguiente">Siguiente →</button>
            </div>
        </fieldset>

        <!-- ===================== PASO 3: CONTRATO ===================== -->
        <fieldset class="seccion-paso oculto" data-seccion="3">
            <legend>Datos del contrato</legend>

            <div class="campo">
                <label>Descripción precisa y detallada de la obra o labor *</label>
                <textarea name="descripcion_obra" rows="4" class="<?= claseError($todosLosErrores, 'descripcion_obra') ?>"><?= val($datosContrato, 'descripcion_obra') ?></textarea>
                <small>La ley exige que la obra o labor se identifique de forma precisa y detallada, no genérica.</small>
                <?= errorCampo($todosLosErrores, "descripcion_obra") ?>
            </div>

            <div class="campo">
                <label>Cargo *</label>
                <input type="text" name="cargo" value="<?= val($datosContrato, 'cargo') ?>" class="<?= claseError($todosLosErrores, 'cargo') ?>">
                <?= errorCampo($todosLosErrores, "cargo") ?>
            </div>

            <div class="campo">
                <label>Funciones *</label>
                <textarea name="funciones" rows="3" class="<?= claseError($todosLosErrores, 'funciones') ?>"><?= val($datosContrato, 'funciones') ?></textarea>
                <?= errorCampo($todosLosErrores, "funciones") ?>
            </div>

            <div class="campo">
                <label>Lugar de ejecución *</label>
                <input type="text" name="lugar_ejecucion" value="<?= val($datosContrato, 'lugar_ejecucion') ?>" class="<?= claseError($todosLosErrores, 'lugar_ejecucion') ?>">
                <?= errorCampo($todosLosErrores, "lugar_ejecucion") ?>
            </div>

            <div class="campo-grupo">
                <div class="campo">
                    <label>Salario mensual (COP) *</label>
                    <input type="number" step="1" name="salario" value="<?= val($datosContrato, 'salario') ?>" class="<?= claseError($todosLosErrores, 'salario') ?>">
                    <?= errorCampo($todosLosErrores, "salario") ?>
                </div>
                <div class="campo">
                    <label>Auxilio de transporte (COP)</label>
                    <input type="number" step="1" name="auxilio_transporte" value="<?= val($datosContrato, 'auxilio_transporte') ?>">
                </div>
            </div>

            <div class="campo-grupo">
                <div class="campo">
                    <label>Periodicidad de pago *</label>
                    <select name="periodicidad_pago" class="<?= claseError($todosLosErrores, 'periodicidad_pago') ?>">
                        <option value="">Seleccione...</option>
                        <option value="semanal" <?= ($datosContrato["periodicidad_pago"] ?? "") === "semanal" ? "selected" : "" ?>>Semanal</option>
                        <option value="quincenal" <?= ($datosContrato["periodicidad_pago"] ?? "") === "quincenal" ? "selected" : "" ?>>Quincenal</option>
                        <option value="mensual" <?= ($datosContrato["periodicidad_pago"] ?? "") === "mensual" ? "selected" : "" ?>>Mensual</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Forma de pago *</label>
                    <select name="forma_pago" class="<?= claseError($todosLosErrores, 'forma_pago') ?>">
                        <option value="">Seleccione...</option>
                        <option value="consignacion" <?= ($datosContrato["forma_pago"] ?? "") === "consignacion" ? "selected" : "" ?>>Consignación</option>
                        <option value="transferencia" <?= ($datosContrato["forma_pago"] ?? "") === "transferencia" ? "selected" : "" ?>>Transferencia</option>
                        <option value="efectivo" <?= ($datosContrato["forma_pago"] ?? "") === "efectivo" ? "selected" : "" ?>>Efectivo</option>
                    </select>
                </div>
            </div>

            <div class="campo-grupo">
                <div class="campo">
                    <label>Jornada *</label>
                    <select name="jornada" class="<?= claseError($todosLosErrores, 'jornada') ?>">
                        <option value="">Seleccione...</option>
                        <option value="completa" <?= ($datosContrato["jornada"] ?? "") === "completa" ? "selected" : "" ?>>Completa</option>
                        <option value="parcial" <?= ($datosContrato["jornada"] ?? "") === "parcial" ? "selected" : "" ?>>Parcial</option>
                    </select>
                    <?= errorCampo($todosLosErrores, "jornada") ?>
                </div>
                <div class="campo">
                    <label>Horas semanales * (máx. <?= JORNADA_MAXIMA_SEMANAL ?>)</label>
                    <input type="number" step="0.5" name="horas_semanales" value="<?= val($datosContrato, 'horas_semanales') ?>" class="<?= claseError($todosLosErrores, 'horas_semanales') ?>">
                    <?= errorCampo($todosLosErrores, "horas_semanales") ?>
                </div>
            </div>

            <div class="campo">
                <label>Horario (opcional, se detalla si aplica)</label>
                <input type="text" name="horario" value="<?= val($datosContrato, 'horario') ?>" placeholder="Ej: lunes a viernes, 8:00 a.m. a 5:00 p.m.">
            </div>

            <div class="campo">
                <label>Fecha de inicio *</label>
                <input type="date" name="fecha_inicio" value="<?= val($datosContrato, 'fecha_inicio') ?>" class="<?= claseError($todosLosErrores, 'fecha_inicio') ?>">
                <?= errorCampo($todosLosErrores, "fecha_inicio") ?>
            </div>

            <div class="campo">
                <label>Condición de terminación (hito que finaliza la obra) *</label>
                <textarea name="condicion_terminacion" rows="3" class="<?= claseError($todosLosErrores, 'condicion_terminacion') ?>"><?= val($datosContrato, 'condicion_terminacion') ?></textarea>
                <?= errorCampo($todosLosErrores, "condicion_terminacion") ?>
            </div>

            <div class="campo campo-checkbox">
                <label>
                    <input type="checkbox" name="tiene_periodo_prueba" value="1" <?= !empty($datosContrato["tiene_periodo_prueba"]) ? "checked" : "" ?>>
                    Incluir período de prueba
                </label>
            </div>

            <div class="campo">
                <label>Duración del período de prueba (días)</label>
                <input type="number" name="duracion_periodo_prueba_dias" value="<?= val($datosContrato, 'duracion_periodo_prueba_dias') ?>" class="<?= claseError($todosLosErrores, 'duracion_periodo_prueba_dias') ?>">
                <?= errorCampo($todosLosErrores, "duracion_periodo_prueba_dias") ?>
            </div>

            <div class="campo">
                <label>Beneficios extralegales (opcional)</label>
                <textarea name="beneficios_extralegales" rows="2"><?= val($datosContrato, 'beneficios_extralegales') ?></textarea>
            </div>

            <div class="campo">
                <label>Observaciones adicionales</label>
                <textarea name="observaciones" rows="2"><?= val($datosContrato, 'observaciones') ?></textarea>
            </div>

            <div class="navegacion-pasos">
                <button type="button" class="btn-anterior">← Anterior</button>
                <button type="submit" class="btn-generar">Generar contrato</button>
            </div>
        </fieldset>
    </form>

    <p class="enlace-listado"><a href="vistas/listado.php">Ver contratos generados</a></p>
</div>

<script src="js/pasos.js"></script>
</body>
</html>
