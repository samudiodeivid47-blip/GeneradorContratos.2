<?php
/*
 * Utilidades de seguridad: CSRF, escape de HTML, IP y bitácora.
 */

// Escapa texto para mostrarlo en HTML (evita XSS).
function h($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, "UTF-8");
}

// ---------- CSRF ----------
// Cada formulario POST lleva un token secreto ligado a la sesión; el
// servidor lo exige. Un sitio ajeno no puede conocerlo, así que no puede
// enviar formularios en nombre del usuario.
function tokenCsrf()
{
    if (empty($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf"];
}

function campoCsrf()
{
    return '<input type="hidden" name="csrf" value="' . h(tokenCsrf()) . '">';
}

// Verifica el token; si falla, vuelve a $rutaVuelta con un aviso y detiene la ejecución.
function exigirCsrf($rutaVuelta)
{
    $enviado = $_POST["csrf"] ?? "";
    if (!is_string($enviado) || !hash_equals(tokenCsrf(), $enviado)) {
        $_SESSION["flash"] = ["error", "La sesión del formulario expiró o no es válida. Intente de nuevo."];
        header("Location: " . $rutaVuelta);
        exit();
    }
}

// Solo acepta envíos por POST.
function exigirPost($rutaVuelta)
{
    if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
        header("Location: " . $rutaVuelta);
        exit();
    }
}

function ipCliente()
{
    // Solo REMOTE_ADDR: las cabeceras X-Forwarded-For las puede falsificar cualquiera.
    return substr($_SERVER["REMOTE_ADDR"] ?? "cli", 0, 45);
}

// ---------- Mensajes entre páginas ----------
function flash($tipo, $mensaje)
{
    $_SESSION["flash"] = [$tipo, $mensaje];
}

function leerFlash()
{
    $f = $_SESSION["flash"] ?? null;
    unset($_SESSION["flash"]);
    return $f;
}

// Registra una acción en la bitácora (seguridad y tratamiento de datos).
function registrarBitacora($conexion, $accion, $entidad, $entidadId = null, $detalle = "")
{
    $usuarioId = $_SESSION["usuario"]["id"] ?? null;
    $ip = ipCliente();
    $detalle = mb_substr((string) $detalle, 0, 300);
    $stmt = $conexion->prepare(
        "INSERT INTO bitacora (usuario_id, accion, entidad, entidad_id, detalle, ip) VALUES (?,?,?,?,?,?)"
    );
    $stmt->bind_param("ississ", $usuarioId, $accion, $entidad, $entidadId, $detalle, $ip);
    $stmt->execute();
    $stmt->close();
}
?>
