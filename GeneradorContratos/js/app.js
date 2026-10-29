// JavaScript común. No usa código en línea (la política de seguridad CSP lo bloquea).
document.addEventListener("DOMContentLoaded", function () {
    // Botones "Imprimir"
    document.querySelectorAll("[data-imprimir]").forEach(function (b) {
        b.addEventListener("click", function () { window.print(); });
    });

    // Formularios que piden confirmación antes de enviarse.
    document.querySelectorAll("form[data-confirmar]").forEach(function (f) {
        f.addEventListener("submit", function (e) {
            if (!window.confirm(f.getAttribute("data-confirmar"))) e.preventDefault();
        });
    });

    // Representante legal solo para persona jurídica.
    const tipo = document.querySelector('[name="tipo_empleador"]');
    const grupoRep = document.getElementById("grupoRepresentante");
    function actualizarRepresentante() {
        if (tipo && grupoRep) grupoRep.classList.toggle("oculto", tipo.value !== "juridica");
    }
    if (tipo) { tipo.addEventListener("change", actualizarRepresentante); actualizarRepresentante(); }

    // Duración del período de prueba solo si se marca el período.
    const chk = document.querySelector('[name="tiene_periodo_prueba"]');
    const duracion = document.querySelector('[name="duracion_periodo_prueba_dias"]');
    const campoDur = duracion ? duracion.closest(".campo") : null;
    function actualizarPrueba() {
        if (chk && campoDur) campoDur.classList.toggle("oculto", !chk.checked);
    }
    if (chk) { chk.addEventListener("change", actualizarPrueba); actualizarPrueba(); }

    // Filtros que se envían al cambiar un select.
    document.querySelectorAll("select[data-autoenviar]").forEach(function (s) {
        s.addEventListener("change", function () { s.form.submit(); });
    });
});
