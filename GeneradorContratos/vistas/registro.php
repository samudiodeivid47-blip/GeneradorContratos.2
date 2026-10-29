<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../logica/sesion.php";

if (estaAutenticado()) {
    header("Location: ../index.php");
    exit();
}

$datos = $_SESSION["datos_registro"] ?? [];
$errores = $_SESSION["errores_registro"] ?? [];
unset($_SESSION["datos_registro"], $_SESSION["errores_registro"]);

function valorReg($datos, $campo)
{
    return htmlspecialchars($datos[$campo] ?? "");
}
function errorReg($errores, $campo)
{
    return isset($errores[$campo])
        ? '<span class="error-campo">' . htmlspecialchars($errores[$campo]) . '</span>'
        : "";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta - ContractPro</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<div class="contenedor acceso">
    <h2>Crear cuenta</h2>
    <p class="subtitulo">Cree su usuario para generar contratos de obra o labor.</p>

    <form action="../procesos/registrarUsuario.php" method="POST">
        <div class="campo">
            <label for="nombre_completo">Nombre completo *</label>
            <input type="text" id="nombre_completo" name="nombre_completo" maxlength="150"
                   value="<?= valorReg($datos, "nombre_completo") ?>" required>
            <?= errorReg($errores, "nombre_completo") ?>
        </div>
        <div class="campo">
            <label for="nombre_usuario">Usuario * (4 a 30 caracteres: letras, números y _)</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" maxlength="30"
                   value="<?= valorReg($datos, "nombre_usuario") ?>" required>
            <?= errorReg($errores, "nombre_usuario") ?>
        </div>
        <div class="campo">
            <label for="clave">Contraseña * (mínimo 8, con letras y números)</label>
            <input type="password" id="clave" name="clave" required>
            <?= errorReg($errores, "clave") ?>
        </div>
        <div class="campo">
            <label for="clave2">Repita la contraseña *</label>
            <input type="password" id="clave2" name="clave2" required>
            <?= errorReg($errores, "clave2") ?>
        </div>
        <button type="submit" class="btn-generar">Crear cuenta</button>
    </form>

    <p>¿Ya tiene cuenta? <a href="login.php">Inicie sesión</a>.</p>
</div>
</body>
</html>
