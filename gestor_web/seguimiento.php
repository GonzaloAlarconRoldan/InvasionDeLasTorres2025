<?php
$dataFile = 'data.json';
$data = json_decode(file_get_contents($dataFile), true);
$equipos = $data['equipos'] ?? [];
$postas = $data['postas'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipo_id'], $_POST['posta_nombre'], $_POST['accion'])) {
    foreach ($equipos as &$equipo) {
        if ($equipo['id'] === $_POST['equipo_id']) {
            foreach ($equipo['postas'] as &$posta) {
                if ($posta['nombre'] === $_POST['posta_nombre']) {
                    if ($_POST['accion'] === 'aprobar') {
                        $posta['estado'] = 'aprobado';
                        $posta['hora_aprobacion'] = date('Y-m-d H:i:s');
                    } elseif ($_POST['accion'] === 'quitar') {
                        $posta['estado'] = 'pendiente';
                        $posta['hora_aprobacion'] = null;
                    }
                }
            }
        }
    }
    $data['equipos'] = $equipos;
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    header('Location: seguimiento.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Postas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .aprobado {
            background-color: #d4edda !important; /* Asegura que el color verde se aplique correctamente */
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
    <h1 class="mb-4">Seguimiento de Postas</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre del Equipo</th>
                <?php foreach ($postas as $posta): ?>
                    <th><?= htmlspecialchars($posta['nombre']) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipos as $equipo): ?>
                <tr>
                    <td><?= htmlspecialchars($equipo['nombre']) ?></td>
                    <?php foreach ($postas as $posta): ?>
                        <?php
                        $estadoPosta = 'pendiente';
                        $horaAprobacion = null;
                        foreach ($equipo['postas'] as $postaEquipo) {
                            if ($postaEquipo['nombre'] === $posta['nombre']) {
                                $estadoPosta = $postaEquipo['estado'];
                                $horaAprobacion = $postaEquipo['hora_aprobacion'];
                                break;
                            }
                        }
                        ?>
                        <td class="<?= $estadoPosta === 'aprobado' ? 'aprobado' : '' ?>">
                            <div>
                                <?= $horaAprobacion ? '<small>Hora: ' . htmlspecialchars($horaAprobacion) . '</small>' : '' ?>
                            </div>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="equipo_id" value="<?= htmlspecialchars($equipo['id']) ?>">
                                <input type="hidden" name="posta_nombre" value="<?= htmlspecialchars($posta['nombre']) ?>">
                                <?php if ($estadoPosta === 'aprobado'): ?>
                                    <button type="submit" name="accion" value="quitar" class="btn btn-warning">Quitar selección</button>
                                <?php else: ?>
                                    <button type="submit" name="accion" value="aprobar" class="btn btn-success">Aprobar</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>