<?php
/*
 * Encabezado común: <head>, menú según el perfil y mensajes.
 * Uso: encabezadoPagina("Título", "../");  ($raiz = "../" en vistas/, "" en la raíz)
 */
function encabezadoPagina($titulo, $raiz = "../", $conMenu = true, $claseContenedor = "")
{
    $u = usuarioActual();
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($titulo) ?> - ContractPro</title>
    <link rel="stylesheet" href="<?= $raiz ?>css/estilos.css">
</head>
<body>
<?php if ($conMenu && $u) { ?>
<nav class="menu no-imprimir">
    <a class="marca" href="<?= $raiz ?>index.php">ContractPro</a>
    <a href="<?= $raiz ?>index.php">Panel</a>
    <a href="<?= $raiz ?>vistas/listado.php">Contratos</a>
    <a href="<?= $raiz ?>vistas/contratoForm.php">Nuevo contrato</a>
    <a href="<?= $raiz ?>vistas/empresas.php">Empresas</a>
    <a href="<?= $raiz ?>vistas/trabajadores.php">Trabajadores</a>
    <a href="<?= $raiz ?>vistas/calendario.php">Calendario</a>
    <a href="<?= $raiz ?>vistas/solicitudes.php">Datos personales</a>
    <?php if (puedeSupervisar()) { ?>
        <a href="<?= $raiz ?>vistas/supervision.php">Supervisión</a>
        <a href="<?= $raiz ?>vistas/bitacora.php">Bitácora</a>
    <?php } ?>
    <?php if (esAdministrador()) { ?>
        <a href="<?= $raiz ?>vistas/usuarios.php">Usuarios</a>
    <?php } ?>
    <a href="<?= $raiz ?>vistas/normativa.php">Normativa</a>
    <span class="menu-derecha">
        <a href="<?= $raiz ?>vistas/perfil.php"><?= h($u["nombre_completo"]) ?> · <?= h(nombreRol($u["rol"])) ?></a>
        <form method="POST" action="<?= $raiz ?>procesos/cerrarSesion.php" class="form-en-linea">
            <?= campoCsrf() ?>
            <button type="submit" class="btn-enlace">Cerrar sesión</button>
        </form>
    </span>
</nav>
<?php } ?>
<div class="contenedor <?= h($claseContenedor) ?>">
<?php
    $f = leerFlash();
    if ($f) {
        echo '<div class="' . ($f[0] === "ok" ? "mensaje-ok" : "mensaje-error-general") . ' no-imprimir">' . h($f[1]) . '</div>';
    }
}

function piePagina($raiz = "../", $scripts = [])
{
    ?>
</div>
<script src="<?= $raiz ?>js/app.js"></script>
<?php foreach ($scripts as $s) { ?>
<script src="<?= $raiz ?><?= h($s) ?>"></script>
<?php } ?>
</body>
</html>
<?php
}
?>
