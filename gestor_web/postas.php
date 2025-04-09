<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataFile = 'data.json';
    $data = json_decode(file_get_contents($dataFile), true);

    if (isset($_POST['accion'])) {
        if ($_POST['accion'] === 'editar') {
            $nombrePosta = $_POST['nombre_posta'];
            $ubicacion = $_POST['ubicacion'];
            $nombreOriginal = $_POST['nombre_original'];

            // Verificar si la posta ya existe con el mismo nombre y ubicación (excluyendo la original)
            foreach ($data['postas'] as $posta) {
                if ($posta['nombre'] === $nombrePosta && $posta['ubicacion'] === $ubicacion && $posta['nombre'] !== $nombreOriginal) {
                    echo "<script>alert('Ya existe una posta con el mismo nombre y ubicación.'); window.location.href='postas.php';</script>";
                    exit;
                }
            }

            // Actualizar la posta
            foreach ($data['postas'] as &$posta) {
                if ($posta['nombre'] === $nombreOriginal) {
                    $posta['nombre'] = $nombrePosta;
                    $posta['ubicacion'] = $ubicacion;
                }
            }
            foreach ($data['equipos'] as &$equipo) {
                foreach ($equipo['postas'] as &$postaEquipo) {
                    if ($postaEquipo['nombre'] === $nombreOriginal) {
                        $postaEquipo['nombre'] = $nombrePosta;
                        $postaEquipo['ubicacion'] = $ubicacion;
                    }
                }
            }

            file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
            echo "<script>alert('Posta editada correctamente.'); window.location.href='postas.php';</script>";
        } elseif ($_POST['accion'] === 'eliminar') {
            $data['postas'] = array_filter($data['postas'], function ($posta) {
                return $posta['nombre'] !== $_POST['nombre_posta'];
            });
            foreach ($data['equipos'] as &$equipo) {
                $equipo['postas'] = array_filter($equipo['postas'], function ($postaEquipo) {
                    return $postaEquipo['nombre'] !== $_POST['nombre_posta'];
                });
            }
        }
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
        header('Location: postas.php');
        exit;
    } elseif (isset($_POST['nombre_posta'], $_POST['ubicacion'])) {
        $nombrePosta = $_POST['nombre_posta'];
        $ubicacion = $_POST['ubicacion'];

        // Verificar si la posta ya existe con el mismo nombre y ubicación
        foreach ($data['postas'] as $posta) {
            if ($posta['nombre'] === $nombrePosta && $posta['ubicacion'] === $ubicacion) {
                echo "<script>alert('La posta con el mismo nombre y ubicación ya existe.'); window.location.href='postas.php';</script>";
                exit;
            }
        }

        $nuevaPosta = [
            'nombre' => $nombrePosta,
            'ubicacion' => $ubicacion,
            'estado' => 'pendiente',
            'hora_aprobacion' => null
        ];

        foreach ($data['equipos'] as &$equipo) {
            $equipo['postas'][] = $nuevaPosta;
        }

        $data['postas'][] = $nuevaPosta; // Registrar la posta globalmente
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

        echo "<script>alert('Posta registrada correctamente.'); window.location.href='postas.php';</script>";
    }
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
            <select id="ubicacion" name="ubicacion" class="form-select" required>
                <option value="Torre Innovacion">Torre Innovacion</option>
                <option value="Torre Maestra">Torre Maestra</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Posta</button>
    </form>

    <h2>Postas Registradas</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre - Ubicación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($postas as $posta): ?>
                <tr>
                    <td><?= htmlspecialchars($posta['nombre']) . ' - ' . htmlspecialchars($posta['ubicacion']) ?></td>
                    <td>
                        <button type="button" class="btn btn-warning" onclick="abrirModalEditar('<?= htmlspecialchars($posta['nombre']) ?>', '<?= htmlspecialchars($posta['ubicacion']) ?>')">Editar</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="nombre_posta" value="<?= htmlspecialchars($posta['nombre']) ?>">
                            <button type="submit" name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Modal para editar información de la posta -->
<div class="modal fade" id="editarPostaModal" tabindex="-1" aria-labelledby="editarPostaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarPostaModalLabel">Editar Posta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" id="nombre_original" name="nombre_original">
                    <div class="mb-3">
                        <label for="nombre_posta" class="form-label">Nombre de la Posta</label>
                        <input type="text" id="nombre_posta" name="nombre_posta" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Ubicación</label>
                        <select id="ubicacion" name="ubicacion" class="form-select" required>
                            <option value="Torre Innovacion">Torre Innovacion</option>
                            <option value="Torre Maestra">Torre Maestra</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="accion" value="editar" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Función para abrir el modal con los datos de la posta
    function abrirModalEditar(nombre, ubicacion) {
        document.getElementById('nombre_original').value = nombre;
        document.querySelector('#editarPostaModal #nombre_posta').value = nombre; // Cargar el nombre actual en el input del modal
        document.querySelector('#editarPostaModal #ubicacion').value = ubicacion; // Cargar la ubicación actual en el dropdown del modal
        var modal = new bootstrap.Modal(document.getElementById('editarPostaModal'));
        modal.show();
    }

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(event) {
            const action = this.querySelector('button[name=accion]').value;
            if (['editar', 'eliminar'].includes(action)) {
                const confirmMessage = action === 'editar' ? '¿Estás seguro de que deseas editar esta posta?'
                                    : '¿Estás seguro de que deseas eliminar esta posta?';
                if (!confirm(confirmMessage)) {
                    event.preventDefault();
                }
            }
        });
    });
</script>
</body>
</html>