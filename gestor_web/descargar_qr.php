<?php
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipo_id'], $_POST['equipo_nombre'])) {
    $equipoId = $_POST['equipo_id'];
    $equipoNombre = $_POST['equipo_nombre'];

    // Generar el contenido HTML para el PDF
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($equipoId) . "&size=300x300";
    $html = "<div style='text-align: center;'>
                <h1>" . htmlspecialchars($equipoNombre) . "</h1>
                <img src='" . $qrUrl . "' alt='QR Code'>
             </div>";

    // Configurar Dompdf
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Enviar el PDF al navegador para descargar
    $dompdf->stream($equipoNombre . ".pdf", ["Attachment" => true]);
    exit;
}
?>