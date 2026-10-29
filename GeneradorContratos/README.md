# Generador de Contratos por Obra o Labor Determinada

Aplicación web en PHP + MySQL para que microempresas colombianas generen contratos
individuales de trabajo por duración de obra o labor determinada, con validación
jurídica básica, edición, impresión y descarga.

## 1. Requisitos

- XAMPP (Apache + MySQL/MariaDB + PHP 8.x) — o cualquier stack PHP + MySQL equivalente.
- Extensión `mbstring` de PHP habilitada (viene activada por defecto en XAMPP).
- Un navegador moderno.

## 2. Instalación

1. Copia la carpeta `GeneradorContratos` dentro de `C:\xampp\htdocs\`.
2. Inicia **Apache** y **MySQL** desde el panel de XAMPP.
3. Abre `http://localhost/phpmyadmin`, ve a la pestaña **SQL** y ejecuta el contenido
   completo de `BD/contratos_obra_labor.sql` (crea la base de datos, las 4 tablas y
   carga el salario mínimo/auxilio de transporte vigente).
4. Si tu usuario `root` de MySQL tiene contraseña, edítala en `config/config.php`.
5. Abre `http://localhost/GeneradorContratos/`.

No requiere `composer`, `npm` ni ninguna dependencia externa: todo el código es PHP,
HTML, CSS y JavaScript plano.

## 3. Cómo funciona

1. **Formulario multi-paso** (`index.php` → `vistas/formulario.php`): 3 secciones
   (empleador, trabajador, contrato) con navegación por botones.
2. Al enviar, `procesos/generarContrato.php` valida los tres bloques
   (`logica/validarEmpresa.php`, `validarTrabajador.php`, `validarContrato.php`).
   - Si hay errores, se regresa al formulario **conservando lo digitado** y mostrando
     el mensaje junto a cada campo (patrón Post-Redirect-Get vía sesión).
   - Si todo es correcto, se guarda la empresa y el trabajador (reutilizando el
     registro si el NIT o el documento ya existen) y se genera el contrato.
3. `vistas/verContrato.php` muestra el contrato con opciones para editar el texto en
   pantalla, imprimir (o "Guardar como PDF" desde el diálogo de impresión del
   navegador) y descargar un archivo `.doc` editable en Word.
4. `vistas/listado.php` muestra todos los contratos generados.
5. `vistas/normativa.php` lista las fuentes jurídicas consultadas.

## 4. Generar el PDF

El sistema no usa una librería de generación de PDF en el servidor (para no depender
de `composer`/extensiones adicionales que tu XAMPP podría no tener). En su lugar, usa
la función nativa **Imprimir del navegador → Guardar como PDF**, que en Chrome/Edge/
Firefox produce un PDF idéntico a la vista en pantalla (hay estilos de impresión en
`css/estilos.css` que ocultan los botones y ajustan el documento a tamaño carta).

## 5. Pruebas realizadas (entorno de desarrollo, no XAMPP)

Se instaló PHP 8.3 + MariaDB en el entorno de desarrollo y se ejecutaron pruebas
reales de extremo a extremo (no solo revisión de código):

| Prueba | Resultado |
|---|---|
| `php -l` en los 15 archivos PHP | ✅ Sin errores de sintaxis |
| Creación de la base de datos desde el script SQL | ✅ 4 tablas creadas correctamente |
| Carga de `index.php` | ✅ HTTP 200, sin errores en el log de PHP |
| Envío de formulario con NIT inválido, salario bajo el mínimo, jornada de 48h y descripción de obra genérica | ✅ Los 6 errores se detectaron y mostraron junto al campo correcto; los datos digitados se conservaron |
| Envío de formulario completo y válido (microempresa de panadería ficticia) | ✅ Contrato generado, guardado en BD y mostrado con todos los datos correctos |
| Envío de un segundo contrato con el mismo NIT de empresa | ✅ No duplicó la empresa (1 empresa, 2 contratos) |
| Vista de listado de contratos | ✅ Muestra los contratos generados |
| Vista de normativa y fuentes | ✅ Carga con los enlaces a SUIN-Juriscol |

Durante estas pruebas se encontraron y corrigieron 2 errores reales antes de la
entrega: una dependencia de la extensión `mbstring` no declarada, y un acceso a
`$_POST` sin verificar existencia que generaba un *warning*.

**No se probó en XAMPP real** (solo en un entorno Linux con PHP 8.3 + MariaDB
equivalente). Es razonablemente cercano a XAMPP, pero si encuentras alguna diferencia
de comportamiento avísame para corregirla.

**No se probó la exportación a PDF ni la edición en pantalla del contrato**, porque
ambas dependen del navegador (impresión del sistema, `contenteditable`) y no pude
automatizarlas sin un navegador real disponible en este entorno.

## 6. Fuentes jurídicas usadas

Verificadas contra fuentes oficiales durante el desarrollo:

- Código Sustantivo del Trabajo — SUIN-Juriscol.
- Ley 2466 de 2025 (contrato por obra o labor determinada) — SUIN-Juriscol.
- Ley 2101 de 2021 (jornada máxima de 42 horas semanales).
- Salario mínimo y auxilio de transporte 2026: **$1.750.905** y **$249.095**
  respectivamente (Decreto 1469 de 2025, Decreto 1470 de 2025, ratificados
  transitoriamente por el Decreto 0159 de 2026 — verificado por búsqueda web el
  28/08/2026, tras la suspensión provisional del decreto original por el Consejo de
  Estado).
- Ley 1581 de 2012 (protección de datos personales).
- Ley 1562 de 2012 (Sistema General de Riesgos Laborales).

Ver el detalle y enlaces oficiales completos en `vistas/normativa.php`.

## 7. Limitaciones conocidas (léelas antes de usar el sistema con datos reales)

- **No es asesoría jurídica.** El contrato generado es un modelo de referencia; las
  cláusulas marcadas `[VERIFICAR]` (indemnización, período de prueba, recargos por
  trabajo suplementario/nocturno/festivo) requieren confirmación de un abogado
  laboralista para el caso concreto.
- El NIT y los documentos de identidad se validan solo por **formato** (patrón de
  dígitos), no se verifica el dígito de verificación oficial ante la DIAN ni la
  existencia real del documento.
- Las cifras de `parametros_legales` (salario mínimo, auxilio de transporte) deben
  actualizarse cada año; el sistema no las actualiza automáticamente.
- No se implementó envío de correos, autenticación de usuarios ni control de acceso:
  cualquier persona con acceso a la URL puede ver el listado de contratos generados.
  Si vas a usarlo con datos reales de trabajadores, esto debe añadirse antes.
- Los datos de empresa/trabajador/contrato se guardan en la base de datos local (no
  se envían a ningún servicio externo), pero **si vas a usar esto en producción**,
  debes adoptar una política de tratamiento de datos personales conforme a la Ley
  1581 de 2012 y revisar tus obligaciones como responsable del tratamiento.
- No se generó una versión React/Vite: el sistema es PHP + MySQL, coherente con las
  tecnologías que usas en tu formación SENA.

## 8. Estructura del proyecto

```
GeneradorContratos/
├── BD/contratos_obra_labor.sql
├── config/
│   ├── config.php
│   └── conexionBDD.php
├── datos/
│   ├── empresaDAO.php
│   ├── trabajadorDAO.php
│   └── contratoDAO.php
├── logica/
│   ├── validarEmpresa.php
│   ├── validarTrabajador.php
│   └── validarContrato.php
├── procesos/
│   └── generarContrato.php
├── plantillas/
│   └── contratoObraLabor.php
├── vistas/
│   ├── formulario.php
│   ├── verContrato.php
│   ├── listado.php
│   └── normativa.php
├── css/estilos.css
├── js/pasos.js
└── index.php
```
