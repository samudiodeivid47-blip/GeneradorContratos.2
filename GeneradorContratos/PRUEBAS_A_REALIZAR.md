# Lista de verificación (probar en XAMPP antes de la sustentación)

Marca cada punto cuando lo hayas comprobado. Si algo falla, anota el mensaje exacto
que aparece en pantalla (o en `C:\\xampp\\apache\\logs\\error.log`).

## Preparación
- [ ] Apache y MySQL encendidos en XAMPP.
- [ ] Base de datos creada con `BD/contratos_obra_labor.sql` (instalación nueva) o
      actualizada con `BD/actualizacion_login.sql` (si ya la tenías).

## Acceso
- [ ] Abrir `http://localhost/GeneradorContratos/` sin sesión: te lleva al login.
- [ ] Registrar el primer usuario: queda como **administrador** (se ve en la barra superior).
- [ ] Registro con contraseña corta o sin números: muestra el error en rojo.
- [ ] Registro con un usuario repetido: avisa que ya está en uso.
- [ ] Login con clave incorrecta: "Usuario o contraseña incorrectos".
- [ ] Login correcto: abre el formulario. "Cerrar sesión" vuelve al login.
- [ ] Sin sesión, abrir directamente `vistas/listado.php`: te manda al login.

## Contratos
- [ ] Generar un contrato válido: se muestra el contrato.
- [ ] El texto del contrato **no** se puede editar con el cursor.
- [ ] Imprimir / Guardar PDF: no salen los botones ni el historial.
- [ ] Probar un nombre de empresa con `<b>hola</b>`: debe verse como texto, sin negrita.

## Edición de funciones e historial
- [ ] "Editar funciones" sin cambiar nada: avisa que no hay cambios.
- [ ] Cambiar las funciones sin motivo: pide el motivo.
- [ ] Cambio correcto: vuelve al contrato con mensaje verde y aparece una fila en el historial.

## Control de acceso por usuario
- [ ] Registrar un segundo usuario, generar un contrato: ve **solo** su contrato.
- [ ] Con el segundo usuario, abrir `verContrato.php?id=` de un contrato del administrador: lo manda al listado.
- [ ] El administrador ve todos los contratos y la columna "Generado por".
