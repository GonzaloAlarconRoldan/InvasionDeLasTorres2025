<?php
$dataFile = 'data.json';
$data = json_decode(file_get_contents($dataFile), true);
$equipos = $data['equipos'] ?? [];
$postas = $data['postas'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipo_id'], $_POST['posta_nombre'], $_POST['accion'])) {
    $codigoCorrecto = false;

    foreach ($equipos as &$equipo) {
        if ($equipo['id'] === $_POST['equipo_id']) {
            // Solo validar código si la acción es aprobar
            if ($_POST['accion'] === 'aprobar') {
                if (!isset($_POST['codigo_equipo']) || $_POST['codigo_equipo'] !== $equipo['id']) {
                    break;
                }
                $codigoCorrecto = true;
            } else {
                // Para quitar no se necesita validar código
                $codigoCorrecto = true;
            }

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

    if ($codigoCorrecto) {
        $data['equipos'] = $equipos;
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    } else {
        echo "<script>alert('Código de equipo incorrecto. La acción no fue realizada.');</script>";
    }

    echo "<script>window.location.href='seguimiento.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento de Postas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .aprobado {
            background-color: #d4edda !important;
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Dashboard</a>
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
                <th><?= htmlspecialchars($posta['nombre']) . ' - ' . htmlspecialchars($posta['ubicacion']) ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($equipos as $equipo): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($equipo['nombre']) ?>
                    <br><small class="text-muted">ID: <?= htmlspecialchars($equipo['id']) ?></small>
                </td>
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
                        <?= $horaAprobacion ? '<small>Hora: ' . htmlspecialchars($horaAprobacion) . '</small><br>' : '' ?>
                        <button class="btn <?= $estadoPosta === 'aprobado' ? 'btn-warning' : 'btn-success' ?> btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCodigo"
                                data-equipo-id="<?= $equipo['id'] ?>"
                                data-posta-nombre="<?= $posta['nombre'] ?>"
                                data-accion="<?= $estadoPosta === 'aprobado' ? 'quitar' : 'aprobar' ?>">
                            <?= $estadoPosta === 'aprobado' ? 'Quitar selección' : 'Aprobar' ?>
                        </button>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- MODAL QR -->
<div class="modal fade" id="modalCodigo" tabindex="-1" aria-labelledby="modalCodigoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="formCodigo" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCodigoLabel">Escanear QR del equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="equipo_id" id="equipoIdInput">
                <input type="hidden" name="posta_nombre" id="postaNombreInput">
                <input type="hidden" name="accion" id="accionInput">
                <input type="hidden" name="codigo_equipo" id="codigoEquipoInput">
                <div id="qr-reader" style="width: 100%;"></div>
                <div class="text-center mt-2">
                    <small>Escanea el QR del equipo para confirmar la acción</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelScanBtn">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let qrScanner;
    const modal = document.getElementById('modalCodigo');

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('equipoIdInput').value = button.getAttribute('data-equipo-id');
        document.getElementById('postaNombreInput').value = button.getAttribute('data-posta-nombre');
        document.getElementById('accionInput').value = button.getAttribute('data-accion');
        document.getElementById('codigoEquipoInput').value = '';

        const accion = button.getAttribute('data-accion');
        if (accion === 'quitar') return; // no abrir escáner si se va a quitar

        const qrReader = new Html5Qrcode("qr-reader");
        const config = { fps: 10, qrbox: 250 };

        qrScanner = qrReader;
        qrScanner.start(
            { facingMode: "environment" },
            config,
            qrCodeMessage => {
                document.getElementById('codigoEquipoInput').value = qrCodeMessage;
                qrScanner.stop().then(() => {
                    qrScanner.clear();
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    modalInstance.hide();
                    document.getElementById('formCodigo').submit();
                });
            },
            errorMessage => {
                // Silencio errores comunes
            }
        ).catch(err => {
            console.error("Error iniciando escáner QR:", err);
        });
    });

    modal.addEventListener('hide.bs.modal', function () {
        if (qrScanner) {
            qrScanner.stop().then(() => qrScanner.clear()).catch(() => {});
        }
    });
</script>
</body>
</html>
