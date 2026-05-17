<?php
// archivo con funciones que se usan en varias partes del sistema

// funcion para limpiar los datos que llegan del formulario
// evita espacios en blanco y caracteres raros
function limpiarDato($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $dato;
}

// funcion que valida si un correo tiene formato correcto
function validarCorreo($correo) {
    if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

// funcion para revisar si el usuario tiene sesion activa
// si no tiene sesion lo manda al login
function verificarSesion() {
    if (!isset($_SESSION['usuario_cedula'])) {
        header("Location: index.php");
        exit();
    }
}
?>
