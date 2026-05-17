<?php
// archivo para cerrar la sesion del usuario

session_start();

// limpio todas las variables de sesion
$_SESSION = array();

// destruyo la cookie de sesion si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// destruyo la sesion por completo
session_destroy();

// redirijo al login con mensaje de confirmacion
header("Location: index.php?cerrado=ok");
exit();
?>
