<?php
// archivo de conexion a la base de datos mysql
// aqui se configuran los datos para conectarse al servidor

// se leen las variables de entorno si existen (caso docker)
// si no existen se usan los valores por defecto (caso local)
$host = getenv('DB_HOST') ?: "localhost";
$usuario_db = getenv('DB_USER') ?: "root";
$password_db = getenv('DB_PASSWORD') ?: "";
$nombre_db = getenv('DB_NAME') ?: "sistema_login";

// se crea la conexion usando mysqli
$conexion = new mysqli($host, $usuario_db, $password_db, $nombre_db);

// si hay error en la conexion se detiene el script
if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

// se configura el charset para soportar caracteres especiales
$conexion->set_charset("utf8mb4");
?>
