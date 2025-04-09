<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
$rol = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Principal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
  <h3>Bienvenido, <?= $_SESSION['usuario'] ?> (<?= $rol ?>)</h3>
  <a href="logout.php" class="btn btn-secondary btn-sm mb-3">Cerrar sesión</a>
  <div class="list-group">
    <?php if ($rol === 'admin'): ?>
      <a href="equipos.php" class="list-group-item list-group-item-action">🔧 Gestionar Equipos</a>
      <a href="postas.php" class="list-group-item list-group-item-action">📍 Gestionar Postas</a>
      <a href="seguimiento.php" class="list-group-item list-group-item-action">✅ Seguimiento de Postas</a>
    <?php else: ?>
      <a href="competencia.php" class="list-group-item list-group-item-action">🏁 Ver Progreso</a>
    <?php endif; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>