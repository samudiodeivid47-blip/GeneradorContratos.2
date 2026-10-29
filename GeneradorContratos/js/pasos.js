document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formContrato");
    if (!form) return;

    const secciones = Array.from(form.querySelectorAll(".seccion-paso"));
    const indicadores = Array.from(document.querySelectorAll(".paso"));
    let pasoActual = 0;

    function mostrarPaso(indice) {
        secciones.forEach((seccion, i) => {
            seccion.classList.toggle("oculto", i !== indice);
        });
        indicadores.forEach((ind, i) => {
            ind.classList.toggle("activo", i === indice);
        });
        pasoActual = indice;
        window.scrollTo({ top: form.offsetTop - 20, behavior: "smooth" });
    }

    function validarPasoActual() {
        const seccion = secciones[pasoActual];
        const campos = seccion.querySelectorAll("input, select, textarea");
        let valido = true;

        campos.forEach((campo) => {
            const esObligatorio = campo.hasAttribute("required") || campo.closest(".campo")?.querySelector("label")?.textContent.includes("*");
            if (esObligatorio && !campo.value.trim() && campo.type !== "checkbox") {
                campo.classList.add("campo-invalido");
                valido = false;
            } else {
                campo.classList.remove("campo-invalido");
            }
        });

        if (!valido) {
            alert("Por favor complete los campos obligatorios (marcados con *) antes de continuar.");
        }

        return valido;
    }

    document.querySelectorAll(".btn-siguiente").forEach((btn) => {
        btn.addEventListener("click", function () {
            if (validarPasoActual() && pasoActual < secciones.length - 1) {
                mostrarPaso(pasoActual + 1);
            }
        });
    });

    document.querySelectorAll(".btn-anterior").forEach((btn) => {
        btn.addEventListener("click", function () {
            if (pasoActual > 0) {
                mostrarPaso(pasoActual - 1);
            }
        });
    });

    // Si el formulario regresa con errores del servidor, se abre
    // directamente en el paso donde está el primer campo inválido.
    const primerCampoInvalido = form.querySelector(".campo-invalido");
    if (primerCampoInvalido) {
        const seccionConError = primerCampoInvalido.closest(".seccion-paso");
        const indice = secciones.indexOf(seccionConError);
        if (indice > -1) {
            mostrarPaso(indice);
        }
    }

    // Mostrar/ocultar representante legal según tipo de empleador.
    const selectTipoEmpleador = form.querySelector('[name="tipo_empleador"]');
    const grupoRepresentante = document.getElementById("grupoRepresentante");

    function actualizarRepresentante() {
        if (!selectTipoEmpleador || !grupoRepresentante) return;
        grupoRepresentante.style.display = selectTipoEmpleador.value === "juridica" ? "grid" : "none";
    }

    if (selectTipoEmpleador) {
        selectTipoEmpleador.addEventListener("change", actualizarRepresentante);
        actualizarRepresentante();
    }

    // Mostrar/ocultar duración del período de prueba según el checkbox.
    const chkPeriodoPrueba = form.querySelector('[name="tiene_periodo_prueba"]');
    const campoDuracionPrueba = form.querySelector('[name="duracion_periodo_prueba_dias"]')?.closest(".campo");

    function actualizarPeriodoPrueba() {
        if (!chkPeriodoPrueba || !campoDuracionPrueba) return;
        campoDuracionPrueba.style.display = chkPeriodoPrueba.checked ? "block" : "none";
    }

    if (chkPeriodoPrueba) {
        chkPeriodoPrueba.addEventListener("change", actualizarPeriodoPrueba);
        actualizarPeriodoPrueba();
    }
});
