<?php
/*
 * Reglas de validación para los datos del trabajador.
 */
function validarTrabajador($datos)
{
    $errores = [];

    if (empty(trim($datos["nombre_completo"] ?? ""))) {
        $errores["nombre_completo"] = "El nombre completo es obligatorio.";
    }

    if (empty($datos["tipo_documento"]) ||
        !in_array($datos["tipo_documento"], ["CC", "CE", "PPT", "PEP", "PASAPORTE"])) {
        $errores["tipo_documento"] = "Debe seleccionar un tipo de documento válido.";
    }

    $documento = trim($datos["numero_documento"] ?? "");
    if (empty($documento)) {
        $errores["numero_documento"] = "El número de documento es obligatorio.";
    } elseif (!preg_match('/^[A-Za-z0-9-]{4,20}$/', $documento)) {
        $errores["numero_documento"] = "El número de documento tiene un formato inválido.";
    }

    // La fecha de nacimiento es opcional en el formulario, pero si se
    // suministra se valida coherencia con la edad mínima legal para
    // trabajar en Colombia (15 años, con restricciones adicionales para
    // menores de 18 según el Código de la Infancia y la Adolescencia).
    // Esta validación es orientativa: la contratación de menores de edad
    // exige autorización del inspector de trabajo y condiciones especiales
    // que este sistema NO gestiona; se recomienda advertir al usuario.
    if (!empty($datos["fecha_nacimiento"])) {
        $nacimiento = DateTime::createFromFormat("Y-m-d", $datos["fecha_nacimiento"]);
        if (!$nacimiento) {
            $errores["fecha_nacimiento"] = "La fecha de nacimiento no es válida.";
        } else {
            $edad = $nacimiento->diff(new DateTime())->y;
            if ($edad < 15) {
                $errores["fecha_nacimiento"] = "La edad mínima legal para trabajar en Colombia es 15 años, con autorización del inspector de trabajo. Verifique este caso con un profesional.";
            }
        }
    }

    if (empty(trim($datos["direccion"] ?? ""))) {
        $errores["direccion"] = "La dirección es obligatoria.";
    }

    if (empty(trim($datos["ciudad"] ?? ""))) {
        $errores["ciudad"] = "La ciudad es obligatoria.";
    }

    $telefono = trim($datos["telefono"] ?? "");
    if (empty($telefono)) {
        $errores["telefono"] = "El teléfono es obligatorio.";
    } elseif (!preg_match('/^[0-9+\s-]{7,15}$/', $telefono)) {
        $errores["telefono"] = "El teléfono tiene un formato inválido.";
    }

    $correo = trim($datos["correo"] ?? "");
    if (empty($correo)) {
        $errores["correo"] = "El correo electrónico es obligatorio.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores["correo"] = "El correo electrónico no es válido.";
    }

    return $errores;
}
?>
