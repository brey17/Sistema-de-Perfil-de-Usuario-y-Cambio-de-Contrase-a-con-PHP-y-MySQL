<?php
// pagina principal de login
// aqui el usuario entra con su correo y contrasena

session_start();

// si ya tiene sesion activa lo mando al perfil
if (isset($_SESSION['usuario_cedula'])) {
    header("Location: perfil.php");
    exit();
}

$mensaje_error = "";
$mensaje_exito = "";

// reviso si vino un mensaje desde otra pagina (por ejemplo registro exitoso)
if (isset($_GET['registro']) && $_GET['registro'] == "ok") {
    $mensaje_exito = "Registro exitoso. Ahora puedes iniciar sesion.";
}

if (isset($_GET['cerrado']) && $_GET['cerrado'] == "ok") {
    $mensaje_exito = "Sesion cerrada correctamente.";
}

// si se envio el formulario proceso el login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/conexion.php';
    require_once 'includes/funciones.php';

    $correo = limpiarDato($_POST['correo']);
    $password = $_POST['password'];

    // valido que los campos no esten vacios
    if (empty($correo) || empty($password)) {
        $mensaje_error = "Todos los campos son obligatorios.";
    } else if (!validarCorreo($correo)) {
        $mensaje_error = "El formato del correo no es valido.";
    } else {
        // busco al usuario en la base de datos
        // uso prepared statements para evitar inyeccion sql
        $sql = "SELECT cedula, nombre, correo, password FROM usuarios WHERE correo = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $usuario = $resultado->fetch_assoc();

            // verifico la contrasena usando password_verify
            if (password_verify($password, $usuario['password'])) {
                // creo las variables de sesion
                $_SESSION['usuario_cedula'] = $usuario['cedula'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_correo'] = $usuario['correo'];

                header("Location: perfil.php");
                exit();
            } else {
                $mensaje_error = "Correo o contrasena incorrectos.";
            }
        } else {
            $mensaje_error = "Correo o contrasena incorrectos.";
        }

        $stmt->close();
    }

    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesion</title>
    <!-- bootstrap desde cdn -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h3 text-center mb-4">Iniciar Sesion</h1>

                        <?php if (!empty($mensaje_error)): ?>
                            <div class="alert alert-danger"><?php echo $mensaje_error; ?></div>
                        <?php endif; ?>

                        <?php if (!empty($mensaje_exito)): ?>
                            <div class="alert alert-success"><?php echo $mensaje_exito; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="index.php">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo:</label>
                                <input type="email" name="correo" id="correo" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contrasena:</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>

                        <div class="text-center mt-3">
                            <p class="mb-0">No tienes cuenta? <a href="registro.php">Registrate aqui</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
