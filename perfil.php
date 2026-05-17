<?php
// zona privada del usuario
// aqui puede ver sus datos y actualizar nombre o correo

session_start();

require_once 'includes/funciones.php';

// si no hay sesion lo regreso al login
verificarSesion();

require_once 'config/conexion.php';

$mensaje_error = "";
$mensaje_exito = "";

// si el formulario fue enviado actualizo los datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = limpiarDato($_POST['nombre']);
    $correo = limpiarDato($_POST['correo']);

    if (empty($nombre) || empty($correo)) {
        $mensaje_error = "Todos los campos son obligatorios.";
    } else if (!validarCorreo($correo)) {
        $mensaje_error = "El formato del correo no es valido.";
    } else {
        // verifico que el correo nuevo no pertenezca a otro usuario
        $sql_check = "SELECT cedula FROM usuarios WHERE correo = ? AND cedula != ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("ss", $correo, $_SESSION['usuario_cedula']);
        $stmt_check->execute();
        $resultado_check = $stmt_check->get_result();

        if ($resultado_check->num_rows > 0) {
            $mensaje_error = "Ese correo ya esta en uso por otro usuario.";
        } else {
            // actualizo los datos
            $sql_update = "UPDATE usuarios SET nombre = ?, correo = ? WHERE cedula = ?";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bind_param("sss", $nombre, $correo, $_SESSION['usuario_cedula']);

            if ($stmt_update->execute()) {
                // actualizo tambien las variables de sesion
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_correo'] = $correo;
                $mensaje_exito = "Datos actualizados correctamente.";
            } else {
                $mensaje_error = "Error al actualizar los datos.";
            }

            $stmt_update->close();
        }

        $stmt_check->close();
    }
}

// obtengo los datos actualizados del usuario
$sql = "SELECT cedula, nombre, correo, fecha_registro FROM usuarios WHERE cedula = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $_SESSION['usuario_cedula']);
$stmt->execute();
$resultado = $stmt->get_result();
$datos_usuario = $resultado->fetch_assoc();
$stmt->close();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <!-- bootstrap desde cdn -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- barra de navegacion para usuarios autenticados -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand">Sistema Login</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menuNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="perfil.php">Perfil</a></li>
                    <li class="nav-item"><a class="nav-link" href="cambiar_password.php">Cambiar Contrasena</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center mt-4 mb-5">
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h3 mb-4">Mi Perfil</h1>

                        <div class="bg-light border rounded p-3 mb-4">
                            <p class="mb-1"><strong>Cedula:</strong> <?php echo $datos_usuario['cedula']; ?></p>
                            <p class="mb-0"><strong>Fecha de registro:</strong> <?php echo $datos_usuario['fecha_registro']; ?></p>
                        </div>

                        <?php if (!empty($mensaje_error)): ?>
                            <div class="alert alert-danger"><?php echo $mensaje_error; ?></div>
                        <?php endif; ?>

                        <?php if (!empty($mensaje_exito)): ?>
                            <div class="alert alert-success"><?php echo $mensaje_exito; ?></div>
                        <?php endif; ?>

                        <h2 class="h5 mb-3">Actualizar Datos</h2>

                        <form method="POST" action="perfil.php">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo $datos_usuario['nombre']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo:</label>
                                <input type="email" name="correo" id="correo" class="form-control" value="<?php echo $datos_usuario['correo']; ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
