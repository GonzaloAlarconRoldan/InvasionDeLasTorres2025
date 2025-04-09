<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit();
}
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuarios = json_decode(file_get_contents('usuarios.json'), true);
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];
    $usuarioEncontrado = false;

    foreach ($usuarios as $u) {
        if ($u['usuario'] === $usuario) {
            $usuarioEncontrado = true;
            if ($u['clave'] === $clave) {
                $_SESSION['usuario'] = $usuario;
                $_SESSION['rol'] = $u['rol'];
                header("Location: dashboard.php");
                exit();
            } else {
                $mensaje = "Contraseña incorrecta";
            }
        }
    }

    if (!$usuarioEncontrado) {
        $mensaje = "Usuario no encontrado";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Gestor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
  <div class="card shadow p-4" style="min-width: 320px; max-width: 400px;">
    <h4 class="text-center mb-3">Ingreso al Gestor</h4>
    <?php if ($mensaje): ?>
      <div class="alert alert-danger"><?= $mensaje ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Usuario</label>
        <input type="text" name="usuario" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="clave" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>
  </div>
</body>
</html>