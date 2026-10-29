<?php
/*
 * Reglas de validación para los datos del empleador.
 * Retorna un arreglo asociativo campo => mensaje de error.
 * Un arreglo vacío significa que no hay errores.
 */
function validarEmpresa($datos)
{
    $errores = [];

    if (empty($datos["tipo_empleador"]) ||
        !in_array($datos["tipo_empleador"], ["natural", "juridica"])) {
        $errores["tipo_empleador"] = "Debe indicar si el empleador es persona natural o jurídica.";
    }

    if (empty(trim($datos["razon_social"] ?? ""))) {
        $errores["razon_social"] = "El nombre o razón social es obligatorio.";
    }

    // NIT: 9 a 15 dígitos, opcionalmente con guion y dígito de verificación.
    // Formato de referencia para Colombia; no reemplaza la validación
    // matemática oficial del dígito de verificación ante la DIAN.
    $nit = trim($datos["nit"] ?? "");
    if (empty($nit)) {
        $errores["nit"] = "El NIT o documento de identificación es obligatorio.";
    } elseif (!preg_match('/^[0-9]{5,15}(-[0-9])?$/', $nit)) {
        $errores["nit"] = "El NIT debe contener solo números (5 a 15 dígitos), con guion y dígito de verificación opcional. Ej: 900123456-7.";
    }

    if ($datos["tipo_empleador"] === "juridica") {
        if (empty(trim($datos["representante_legal"] ?? ""))) {
            $errores["representante_legal"] = "Para persona jurídica el representante legal es obligatorio.";
        }
        if (empty(trim($datos["documento_representante"] ?? ""))) {
            $errores["documento_representante"] = "El documento del representante legal es obligatorio.";
        }
    }

    if (empty(trim($datos["direccion"] ?? ""))) {
        $errores["direccion"] = "La dirección es obligatoria.";
    }

    if (empty(trim($datos["ciudad"] ?? ""))) {
        $errores["ciudad"] = "La ciudad es obligatoria.";
    }

    if (empty(trim($datos["departamento"] ?? ""))) {
        $errores["departamento"] = "El departamento es obligatorio.";
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

    if (empty(trim($datos["actividad_economica"] ?? ""))) {
        $errores["actividad_economica"] = "La actividad económica es obligatoria (relevante para la clasificación de riesgo laboral).";
    }

    return $errores;
}
?>
