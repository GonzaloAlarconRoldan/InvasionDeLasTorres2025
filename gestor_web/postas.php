<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_posta'])) {
    $dataFile = 'data.json';
    $data = json_decode(file_get_contents($dataFile), true);

    $nuevaPosta = [
        'nombre' => $_POST['nombre_posta'],
        'ubicacion' => $_POST['ubicacion'],
        'estado' => 'pendiente',
        'hora_aprobacion' => null
    ];

    foreach ($data['equipos'] as &$equipo) {
        $equipo['postas'][] = $nuevaPosta;
    }

    $data['postas'][] = $nuevaPosta; // Registrar la posta globalmente
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

    echo 'Posta registrada correctamente';
}

$dataFile = 'data.json';
$data = json_decode(file_get_contents($dataFile), true);
$postas = $data['postas'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Postas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
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
    <h1 class="mb-4">Gestión de Postas</h1>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="nombre_posta" class="form-label">Nombre de la Posta</label>
            <input type="text" id="nombre_posta" name="nombre_posta" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="ubicacion" class="form-label">Ubicación</label>
            <input type="text" id="ubicacion" name="ubicacion" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Posta</button>
    </form>

    <h2>Postas Registradas</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <th>Hora de Aprobación</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($postas as $posta): ?>
                <tr>
                    <td><?= htmlspecialchars($posta['nombre']) ?></td>
                    <td><?= htmlspecialchars($posta['ubicacion']) ?></td>
                    <td><?= htmlspecialchars($posta['estado']) ?></td>
                    <td><?= htmlspecialchars($posta['hora_aprobacion'] ?? 'N/A') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>