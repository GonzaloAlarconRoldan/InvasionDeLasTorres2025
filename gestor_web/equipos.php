<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_equipo'])) {
    $dataFile = 'data.json';
    $data = json_decode(file_get_contents($dataFile), true);

    $nuevoEquipo = [
        'id' => 'eq-' . uniqid(),
        'nombre' => $_POST['nombre_equipo'],
        'integrantes' => [],
        'postas' => $data['postas'] ?? [] // Agregar todas las postas existentes
    ];

    $data['equipos'][] = $nuevoEquipo;
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

    echo 'Equipo registrado correctamente';
}

$dataFile = 'data.json';
$data = json_decode(file_get_contents($dataFile), true);
$equipos = $data['equipos'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Equipos</title>
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
    <h1 class="mb-4">Gestión de Equipos</h1>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="nombre_equipo" class="form-label">Nombre del Equipo</label>
            <input type="text" id="nombre_equipo" name="nombre_equipo" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Equipo</button>
    </form>

    <h2>Equipos Registrados</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Integrantes</th>
                <th>Postas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipos as $equipo): ?>
                <tr>
                    <td><?= htmlspecialchars($equipo['id']) ?></td>
                    <td><?= htmlspecialchars($equipo['nombre']) ?></td>
                    <td>
                        <ul>
                            <?php foreach ($equipo['integrantes'] as $integrante): ?>
                                <li><?= htmlspecialchars($integrante['nombre']) ?> (<?= htmlspecialchars($integrante['paralelo']) ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <?php foreach ($equipo['postas'] as $posta): ?>
                                <li><?= htmlspecialchars($posta['nombre']) ?> (<?= htmlspecialchars($posta['ubicacion']) ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>