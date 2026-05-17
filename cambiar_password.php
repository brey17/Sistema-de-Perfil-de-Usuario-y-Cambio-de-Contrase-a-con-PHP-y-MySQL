<?php
// pagina para cambiar la contrasena del usuario logueado

session_start();

require_once 'includes/funciones.php';

// verifico que tenga sesion activa
verificarSesion();

$mensaje_error = "";
$mensaje_exito = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/conexion.php';

    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $password_confirmar = $_POST['password_confirmar'];

    // validaciones de campos
    if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
        $mensaje_error = "Todos los campos son obligatorios.";
    } else if (strlen($password_nueva) < 6) {
        $mensaje_error = "La nueva contrasena debe tener al menos 6 caracteres.";
    } else if ($password_nueva != $password_confirmar) {
        $mensaje_error = "Las contrasenas nuevas no coinciden.";
    } else {
        // primero obtengo la contrasena actual de la base de datos
        $sql = "SELECT password FROM usuarios WHERE cedula = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $_SESSION['usuario_cedula']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();

        // verifico que la contrasena actual sea correcta
        if (!password_verify($password_actual, $usuario['password'])) {
            $mensaje_error = "La contrasena actual no es correcta.";
        } else if (password_verify($password_nueva, $usuario['password'])) {
            // si la nueva es igual a la actual no tiene sentido cambiarla
            $mensaje_error = "La nueva contrasena no puede ser igual a la actual.";
        } else {
            // genero el hash de la nueva contrasena
            $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);

            // actualizo la contrasena en la base de datos
            $sql_update = "UPDATE usuarios SET password = ? WHERE cedula = ?";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bind_param("ss", $password_hash, $_SESSION['usuario_cedula']);

            if ($stmt_update->execute()) {
                $mensaje_exito = "Contrasena actualizada correctamente.";
            } else {
                $mensaje_error = "Error al actualizar la contrasena.";
            }

            $stmt_update->close();
        }

        $conexion->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contrasena</title>
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
                    <li class="nav-item"><a class="nav-link" href="perfil.php">Perfil</a></li>
                    <li class="nav-item"><a class="nav-link active" href="cambiar_password.php">Cambiar Contrasena</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center mt-4 mb-5">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h3 text-center mb-4">Cambiar Contrasena</h1>

                        <?php if (!empty($mensaje_error)): ?>
                            <div class="alert alert-danger"><?php echo $mensaje_error; ?></div>
                        <?php endif; ?>

                        <?php if (!empty($mensaje_exito)): ?>
                            <div class="alert alert-success"><?php echo $mensaje_exito; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="cambiar_password.php">
                            <div class="mb-3">
                                <label for="password_actual" class="form-label">Contrasena actual:</label>
                                <input type="password" name="password_actual" id="password_actual" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="password_nueva" class="form-label">Contrasena nueva:</label>
                                <input type="password" name="password_nueva" id="password_nueva" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmar" class="form-label">Confirmar contrasena nueva:</label>
                                <input type="password" name="password_confirmar" id="password_confirmar" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Cambiar Contrasena</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
