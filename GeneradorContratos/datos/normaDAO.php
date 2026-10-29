<?php
function listarNormas($conexion)
{
    return $conexion->query("SELECT * FROM norma_legal ORDER BY id")->fetch_all(MYSQLI_ASSOC);
}
?>
