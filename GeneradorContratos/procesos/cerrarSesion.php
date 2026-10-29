<?php
require_once __DIR__ . "/../config/config.php";

// Vacía la sesión, borra la cookie y destruye la sesión en el servidor.
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), "", time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
}
session_destroy();

header("Location: ../vistas/login.php");
exit();
?>
