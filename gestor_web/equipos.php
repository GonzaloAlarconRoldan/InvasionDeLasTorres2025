<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataFile = 'data.json';
    $data = json_decode(file_get_contents($dataFile), true);

    if (isset($_POST['accion'])) {
        if ($_POST['accion'] === 'editar') {
            foreach ($data['equipos'] as &$equipo) {
                if ($equipo['id'] === $_POST['equipo_id']) {
                    $equipo['nombre'] = $_POST['nombre_equipo'];
                }
            }
        } elseif ($_POST['accion'] === 'eliminar') {
            $data['equipos'] = array_filter($data['equipos'], function ($equipo) {
                return $equipo['id'] !== $_POST['equipo_id'];
            });
        } elseif ($_POST['accion'] === 'agregar_integrante') {
            foreach ($data['equipos'] as &$equipo) {
                if ($equipo['id'] === $_POST['equipo_id']) {
                    $equipo['integrantes'][] = [
                        'nombre' => $_POST['nombre_integrante'],
                        'paralelo' => $_POST['paralelo']
                    ];
                }
            }
        } elseif ($_POST['accion'] === 'eliminar_integrante') {
            foreach ($data['equipos'] as &$equipo) {
                if ($equipo['id'] === $_POST['equipo_id']) {
                    $equipo['integrantes'] = array_filter($equipo['integrantes'], function ($integrante) {
                        return $integrante['nombre'] !== $_POST['nombre_integrante'];
                    });
                }
            }
        }
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
        header('Location: equipos.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_equipo'])) {
    $nombreEquipo = $_POST['nombre_equipo'];

    // Verificar si el equipo ya existe
    foreach ($data['equipos'] as $equipo) {
        if ($equipo['nombre'] === $nombreEquipo) {
            echo "<script>alert('El equipo ya existe.'); window.location.href='equipos.php';</script>";
            exit;
        }
    }

    $nuevoEquipo = [
        'id' => 'eq-' . uniqid(),
        'nombre' => $nombreEquipo,
        'integrantes' => [],
        'postas' => $data['postas'] ?? [] // Agregar todas las postas existentes
    ];

    $data['equipos'][] = $nuevoEquipo;
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

    echo "<script>alert('Equipo registrado correctamente.'); window.location.href='equipos.php';</script>";
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
                <th>QR</th>
                <th>Acciones</th>
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
                                <li>
                                    <?= htmlspecialchars($integrante['nombre']) ?> (<?= htmlspecialchars($integrante['paralelo']) ?>)
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="equipo_id" value="<?= htmlspecialchars($equipo['id']) ?>">
                                        <input type="hidden" name="nombre_integrante" value="<?= htmlspecialchars($integrante['nombre']) ?>">
                                        <button type="submit" name="accion" value="eliminar_integrante" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <form method="POST" class="mt-2">
                            <input type="hidden" name="equipo_id" value="<?= htmlspecialchars($equipo['id']) ?>">
                            <div class="mb-2">
                                <input type="text" name="nombre_integrante" class="form-control" placeholder="Nombre del Integrante" required>
                            </div>
                            <div class="mb-2">
                                <label for="paralelo" class="form-label">Paralelo</label>
                                <select name="paralelo" class="form-select" required>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                    <option value="E">E</option>
                                </select>
                            </div>
                            <button type="submit" name="accion" value="agregar_integrante" class="btn btn-success btn-sm">Agregar Integrante</button>
                        </form>
                    </td>
                    <td>
                        <div class="text-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?= urlencode($equipo['id']) ?>&size=200x200" alt="QR Code" class="mb-2">
                            <a href="https://api.qrserver.com/v1/create-qr-code/?data=<?= urlencode($equipo['id']) ?>&size=300x300" download="<?= htmlspecialchars($equipo['nombre']) ?>.png">
                                <button class="btn btn-primary btn-sm">Descargar QR</button>
                            </a>
                        </div>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="equipo_id" value="<?= htmlspecialchars($equipo['id']) ?>">
                            <input type="text" name="nombre_equipo" value="<?= htmlspecialchars($equipo['nombre']) ?>" class="form-control mb-2" required>
                            <button type="submit" name="accion" value="editar" class="btn btn-warning">Editar</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="equipo_id" value="<?= htmlspecialchars($equipo['id']) ?>">
                            <button type="submit" name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(event) {
            const action = this.querySelector('button[name=accion]').value;
            if (['editar', 'eliminar', 'quitar', 'eliminar_integrante', 'agregar_integrante'].includes(action)) {
                const confirmMessage = action === 'editar' ? '¿Estás seguro de que deseas editar esta información?'
                                    : action === 'eliminar' ? '¿Estás seguro de que deseas eliminar este equipo?'
                                    : action === 'eliminar_integrante' ? '¿Estás seguro de que deseas eliminar este integrante?'
                                    : action === 'agregar_integrante' ? '¿Estás seguro de que deseas agregar este integrante?'
                                    : '¿Estás seguro de que deseas quitar la selección?';
                if (!confirm(confirmMessage)) {
                    event.preventDefault();
                }
            }
        });
    });
</script>
</body>
</html>