<?php
// Configuración general del sistema.
// Si cambia el entorno (servidor, credenciales), solo se modifica este archivo.

define("SERVIDOR", "localhost");
define("USUARIO", "root");
define("PASSWORD", "");
define("BASE_DATOS", "contratos_obra_labor");

// Jornada máxima legal vigente en horas semanales.
// Ley 2101 de 2021: reducción gradual hasta 42 horas semanales
// (42 horas desde el 15 de julio de 2026). Verificar vigencia antes
// de usar en producción, ya que el cronograma de reducción legal
// pudo variar entre la fecha de este desarrollo y su uso real.
define("JORNADA_MAXIMA_SEMANAL", 42);

// Zona horaria para fechas del sistema.
date_default_timezone_set("America/Bogota");

// Sesión: se usa únicamente para conservar errores de validación y los
// datos digitados por el usuario entre la redirección de un proceso y
// el formulario (patrón Post-Redirect-Get). No se almacena información
// sensible de forma permanente; los datos de la sesión se leen una sola
// vez en index.php y luego se eliminan.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
