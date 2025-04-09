<?php
$dataFile = 'data.json';
$data = json_decode(file_get_contents($dataFile), true);
$equipos = $data['equipos'] ?? [];
$postas = $data['postas'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado del Recorrido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .aprobado {
            background-color: #d4edda !important; /* Verde para postas aprobadas */
        }
        .pendiente {
            background-color: #f8d7da !important; /* Rojo claro para postas pendientes */
        }
    </style>
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
    <h1 class="mb-4 text-center">Estado del Recorrido</h1>
    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>Equipo</th>
                <?php foreach ($postas as $posta): ?>
                    <th><?= htmlspecialchars($posta['nombre']) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipos as $equipo): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($equipo['nombre']) ?></strong></td>
                    <?php foreach ($postas as $posta): ?>
                        <?php
                        $estadoPosta = 'pendiente';
                        foreach ($equipo['postas'] as $postaEquipo) {
                            if ($postaEquipo['nombre'] === $posta['nombre']) {
                                $estadoPosta = $postaEquipo['estado'];
                                break;
                            }
                        }
                        ?>
                        <td class="<?= $estadoPosta === 'aprobado' ? 'aprobado' : 'pendiente' ?>">
                            <?= $estadoPosta === 'aprobado' ? '✔' : 'Pendiente' ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>