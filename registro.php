<?php
// pagina para registrar un nuevo usuario en el sistema

session_start();

// si ya esta logueado lo mando al perfil
if (isset($_SESSION['usuario_cedula'])) {
    header("Location: perfil.php");
    exit();
}

$mensaje_error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/conexion.php';
    require_once 'includes/funciones.php';

    // recojo los datos del formulario
    $cedula = limpiarDato($_POST['cedula']);
    $nombre = limpiarDato($_POST['nombre']);
    $correo = limpiarDato($_POST['correo']);
    $password = $_POST['password'];
    $confirmar = $_POST['confirmar'];

    // validaciones basicas
    if (empty($cedula) || empty($nombre) || empty($correo) || empty($password) || empty($confirmar)) {
        $mensaje_error = "Todos los campos son obligatorios.";
    } else if (!validarCorreo($correo)) {
        $mensaje_error = "El formato del correo no es valido.";
    } else if (strlen($password) < 6) {
        $mensaje_error = "La contrasena debe tener al menos 6 caracteres.";
    } else if ($password != $confirmar) {
        $mensaje_error = "Las contrasenas no coinciden.";
    } else {
        // reviso si la cedula o el correo ya existen
        $sql_check = "SELECT cedula FROM usuarios WHERE cedula = ? OR correo = ?";
        $stmt_check = $conexion->prepare($sql_check);
        $stmt_check->bind_param("ss", $cedula, $correo);
        $stmt_check->execute();
        $resultado_check = $stmt_check->get_result();

        if ($resultado_check->num_rows > 0) {
            $mensaje_error = "Ya existe un usuario con esa cedula o correo.";
        } else {
            // genero el hash de la contrasena antes de guardarla
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // inserto el nuevo usuario
            $sql_insert = "INSERT INTO usuarios (cedula, nombre, correo, password) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conexion->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $cedula, $nombre, $correo, $password_hash);

            if ($stmt_insert->execute()) {
                $stmt_insert->close();
                $conexion->close();
                header("Location: index.php?registro=ok");
                exit();
            } else {
                $mensaje_error = "Ocurrio un error al registrar. Intenta de nuevo.";
            }

            $stmt_insert->close();
        }

        $stmt_check->close();
    }

    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <!-- bootstrap desde cdn -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5 mb-5">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h3 text-center mb-4">Registro</h1>

                        <?php if (!empty($mensaje_error)): ?>
                            <div class="alert alert-danger"><?php echo $mensaje_error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="registro.php">
                            <div class="mb-3">
                                <label for="cedula" class="form-label">Cedula:</label>
                                <input type="text" name="cedula" id="cedula" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre completo:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo:</label>
                                <input type="email" name="correo" id="correo" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contrasena:</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="confirmar" class="form-label">Confirmar contrasena:</label>
                                <input type="password" name="confirmar" id="confirmar" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Registrarme</button>
                        </form>

                        <div class="text-center mt-3">
                            <p class="mb-0">Ya tienes cuenta? <a href="index.php">Inicia sesion</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
