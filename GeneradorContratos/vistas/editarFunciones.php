<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/../datos/contratoDAO.php";

requerirLogin("login.php");

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: listado.php");
    exit();
}

$contrato = buscarContratoPorId($conexion, (int) $_GET["id"]);
if (!$contrato || !puedeVerContrato($contrato)) {
    header("Location: listado.php");
    exit();
}

// Si venimos de un intento fallido, se recupera lo que el usuario escribió.
$datos = $_SESSION["datos_funciones"] ?? [];
$errores = $_SESSION["errores_funciones"] ?? [];
unset($_SESSION["datos_funciones"], $_SESSION["errores_funciones"]);

$funcionesActuales = $datos["funciones"] ?? $contrato["funciones"];
$motivoActual = $datos["motivo"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar funciones del contrato</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<div class="contenedor acceso-ancho">
    <div class="barra-acciones no-imprimir">
        <a href="verContrato.php?id=<?= (int) $contrato["id"] ?>" class="btn-secundario">← Volver al contrato</a>
    </div>

    <h2>Editar funciones del trabajador</h2>
    <p class="subtitulo">
        Contrato No. <?= (int) $contrato["id"] ?> — <?= htmlspecialchars($contrato["nombre_completo"]) ?>
        (<?= htmlspecialchars($contrato["cargo"]) ?>)
    </p>

    <div class="aviso-legal">
        Solo se pueden modificar las funciones. El resto del contrato es fijo porque responde a los
        requisitos legales. El cambio queda registrado con su fecha, su autor y su motivo.
    </div>

    <form action="../procesos/guardarFunciones.php" method="POST">
        <input type="hidden" name="id" value="<?= (int) $contrato["id"] ?>">

        <div class="campo">
            <label for="funciones">Funciones del trabajador *</label>
            <textarea id="funciones" name="funciones" rows="7" required><?= htmlspecialchars($funcionesActuales) ?></textarea>
            <?php if (isset($errores["funciones"])) { ?>
                <span class="error-campo"><?= htmlspecialchars($errores["funciones"]) ?></span>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="motivo">Motivo del cambio * (obligatorio)</label>
            <input type="text" id="motivo" name="motivo" maxlength="255"
                   value="<?= htmlspecialchars($motivoActual) ?>" required>
            <?php if (isset($errores["motivo"])) { ?>
                <span class="error-campo"><?= htmlspecialchars($errores["motivo"]) ?></span>
            <?php } ?>
        </div>

        <button type="submit" class="btn-generar">Guardar cambio</button>
    </form>
</div>
</body>
</html>
