<?php
require_once __DIR__ . "/config/conexionBDD.php";
require_once __DIR__ . "/logica/sesion.php";
require_once __DIR__ . "/logica/vista.php";
require_once __DIR__ . "/vistas/_encabezado.php";

requerirLogin("vistas/login.php");

// Datos y errores devueltos por el proceso (cada bloque con sus propios errores).
$a = $_SESSION["asistente"] ?? [];
unset($_SESSION["asistente"]);
[$dEmpresa, $eEmpresa] = $a["empresa"] ?? [[], []];
[$dTrabajador, $eTrabajador] = $a["trabajador"] ?? [[], []];
[$dContrato, $eContrato] = $a["contrato"] ?? [[], []];

encabezadoPagina("Asistente de contrato", "");
?>
    <header class="encabezado">
        <h1>Asistente: contrato en 3 pasos</h1>
        <p class="subtitulo">Empleador, trabajador y contrato en un solo recorrido. Si prefiere registrar cada cosa por
            separado, use <a href="vistas/empresas.php">Empresas</a>, <a href="vistas/trabajadores.php">Trabajadores</a>
            y <a href="vistas/contratoForm.php">Nuevo contrato</a>.</p>
    </header>

    <div class="indicador-pasos">
        <div class="paso activo" data-paso="1">1. Empleador</div>
        <div class="paso" data-paso="2">2. Trabajador</div>
        <div class="paso" data-paso="3">3. Contrato</div>
    </div>

    <form action="procesos/generarContrato.php" method="POST" id="formContrato" novalidate>
        <?= campoCsrf() ?>

        <section class="seccion-paso">
            <h2>Datos del empleador</h2>
            <?php $d = $dEmpresa; $err = $eEmpresa; include __DIR__ . "/vistas/_camposEmpresa.php"; ?>
            <div class="navegacion-pasos">
                <span></span>
                <button type="button" class="btn-siguiente">Siguiente →</button>
            </div>
        </section>

        <section class="seccion-paso oculto">
            <h2>Datos del trabajador</h2>
            <?php $d = $dTrabajador; $err = $eTrabajador; $yaAutorizado = false; include __DIR__ . "/vistas/_camposTrabajador.php"; ?>
            <div class="navegacion-pasos">
                <button type="button" class="btn-anterior">← Anterior</button>
                <button type="button" class="btn-siguiente">Siguiente →</button>
            </div>
        </section>

        <section class="seccion-paso oculto">
            <h2>Condiciones del contrato</h2>
            <?php $d = $dContrato; $err = $eContrato; include __DIR__ . "/vistas/_camposContrato.php"; ?>
            <div class="navegacion-pasos">
                <button type="button" class="btn-anterior">← Anterior</button>
                <button type="submit" class="btn-generar">Generar contrato</button>
            </div>
        </section>
    </form>
<?php piePagina("", ["js/pasos.js"]); ?>
