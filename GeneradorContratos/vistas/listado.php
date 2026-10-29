<?php
require_once __DIR__ . "/../config/conexionBDD.php";
require_once __DIR__ . "/../datos/contratoDAO.php";

$resultado = listarContratos($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contratos generados</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<div class="contenedor">
    <div class="barra-acciones">
        <a href="../index.php" class="btn-secundario">← Nuevo contrato</a>
    </div>

    <h2>Contratos generados</h2>

    <table class="tabla-listado">
        <tr>
            <th>ID</th>
            <th>Empresa</th>
            <th>Trabajador</th>
            <th>Cargo</th>
            <th>Fecha de inicio</th>
            <th>Estado</th>
            <th></th>
        </tr>
        <?php while ($fila = $resultado->fetch_assoc()) { ?>
            <tr>
                <td><?= (int) $fila["id"] ?></td>
                <td><?= htmlspecialchars($fila["razon_social"]) ?></td>
                <td><?= htmlspecialchars($fila["nombre_completo"]) ?></td>
                <td><?= htmlspecialchars($fila["cargo"]) ?></td>
                <td><?= date("d/m/Y", strtotime($fila["fecha_inicio"])) ?></td>
                <td><?= htmlspecialchars($fila["estado"]) ?></td>
                <td><a href="verContrato.php?id=<?= (int) $fila["id"] ?>">Ver / Imprimir</a></td>
            </tr>
        <?php } ?>
    </table>
    <?php if ($resultado->num_rows === 0) { ?>
        <p>Todavía no se ha generado ningún contrato.</p>
    <?php } ?>
</div>
</body>
</html>
