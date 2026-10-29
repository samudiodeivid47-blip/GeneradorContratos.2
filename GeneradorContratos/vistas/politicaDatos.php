<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../logica/sesion.php";
require_once __DIR__ . "/_encabezado.php";
// Página pública: se puede leer sin iniciar sesión.
encabezadoPagina("Política de tratamiento de datos personales", "../", estaAutenticado());
?>
    <h2>Política de tratamiento de datos personales</h2>
    <p class="subtitulo">Versión <?= h(POLITICA_VERSION) ?> — Ley 1581 de 2012</p>

    <div class="aviso-legal">
        Modelo general. Complete los datos del responsable en <code>config/config.php</code> y revíselo con un
        abogado antes de usarlo con datos reales de trabajadores.
    </div>

    <h3>1. Responsable del tratamiento</h3>
    <p><?= h(RESPONSABLE_NOMBRE) ?> (NIT <?= h(RESPONSABLE_NIT) ?>). Dirección: <?= h(RESPONSABLE_DIRECCION) ?>.
       Correo para solicitudes: <?= h(RESPONSABLE_CORREO) ?>. Teléfono: <?= h(RESPONSABLE_TELEFONO) ?>.</p>

    <h3>2. Datos que se recolectan</h3>
    <p>De los trabajadores: nombre, documento, fecha de nacimiento, dirección, teléfono, correo, EPS y fondo de pensión.
       De los empleadores: razón social, NIT, representante, dirección, contacto, actividad económica y ARL.
       De los usuarios del sistema: nombre, usuario, correo, teléfono y cargo, además del registro de sus acciones
       (bitácora con fecha e IP). No se piden datos sensibles.</p>

    <h3>3. Finalidades</h3>
    <p>Elaborar, revisar y archivar contratos de trabajo por obra o labor; verificar el cumplimiento de requisitos
       legales; afiliaciones a la seguridad social; controlar el acceso y la seguridad del sistema; y atender
       solicitudes de los titulares.</p>

    <h3>4. Derechos del titular</h3>
    <p>Conocer, actualizar y rectificar sus datos; pedir prueba de la autorización; ser informado del uso de sus datos;
       presentar quejas ante la Superintendencia de Industria y Comercio; revocar la autorización y solicitar la
       supresión cuando no exista un deber legal de conservar los datos.</p>
    <p>Las <strong>consultas</strong> se responden en máximo 10 días hábiles y los <strong>reclamos</strong> en máximo
       15 días hábiles (prorrogables una vez por 5 y 8 días hábiles). El sistema calcula estos plazos con el calendario
       de festivos de Colombia y los muestra en el calendario. [VERIFICAR]</p>

    <h3>5. Autorización</h3>
    <p>Los datos de un trabajador solo se registran si quien los ingresa confirma que el trabajador autorizó su
       tratamiento (por escrito o por medio electrónico). El sistema guarda quién lo registró, cuándo, con qué versión
       de esta política y por qué medio.</p>

    <h3>6. Seguridad</h3>
    <p>Contraseñas cifradas, bloqueo por intentos fallidos, cierre de sesión por inactividad, protección contra
       falsificación de formularios (CSRF), control de acceso por perfil (cada usuario solo ve lo que registró;
       el supervisor y el administrador ven todo) y bitácora de acciones.</p>

    <h3>7. Menores de edad</h3>
    <p>El tratamiento de datos de adolescentes exige cuidado especial y la autorización de sus representantes legales
       y del inspector de trabajo. El sistema marca esos contratos con riesgo grave.</p>

    <h3>8. Conservación</h3>
    <p>Los datos se conservan mientras dure la relación laboral y el tiempo que exijan las normas laborales y de
       seguridad social. Los contratos no se borran desde el sistema: se anulan. [VERIFICAR plazos con un abogado]</p>

    <h3>9. Registro Nacional de Bases de Datos</h3>
    <p>Verifique si la entidad está obligada a inscribir esta base de datos en el RNBD de la SIC (depende del tamaño de
       la empresa). [VERIFICAR]</p>
<?php piePagina("../"); ?>
