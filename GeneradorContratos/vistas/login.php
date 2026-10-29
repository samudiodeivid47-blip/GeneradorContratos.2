<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../logica/sesion.php";

// Si ya hay sesión iniciada, no tiene sentido ver el login.
if (estaAutenticado()) {
    header("Location: ../index.php");
    exit();
}

$mensaje = $_SESSION["mensaje_login"] ?? "";
$error = $_SESSION["error_login"] ?? "";
$usuarioEscrito = $_SESSION["login_usuario"] ?? "";
unset($_SESSION["mensaje_login"], $_SESSION["error_login"], $_SESSION["login_usuario"]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - ContractPro</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<div class="contenedor acceso">
    <h2>ContractPro</h2>
    <p class="subtitulo">Inicie sesión para generar y consultar contratos de obra o labor.</p>

    <?php if ($mensaje !== "") { ?>
        <div class="mensaje-ok"><?= htmlspecialchars($mensaje) ?></div>
    <?php } ?>
    <?php if ($error !== "") { ?>
        <div class="mensaje-error-general"><?= htmlspecialchars($error) ?></div>
    <?php } ?>

    <form action="../procesos/iniciarSesion.php" method="POST">
        <div class="campo">
            <label for="nombre_usuario">Usuario</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" maxlength="30"
                   value="<?= htmlspecialchars($usuarioEscrito) ?>" required autofocus>
        </div>
        <div class="campo">
            <label for="clave">Contraseña</label>
            <input type="password" id="clave" name="clave" required>
        </div>
        <button type="submit" class="btn-generar">Ingresar</button>
    </form>

    <p>¿No tiene cuenta? <a href="registro.php">Regístrese aquí</a>.</p>
</div>
</body>
</html>
