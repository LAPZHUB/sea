<?php
// fotografia.php

require_once '../includes/db.php'; // Conexión a la base de datos
session_start();

// Verificar si el usuario tiene permisos
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['id_rol'], [1, 2, 3, 4])) {
    echo "<p>Error: No tienes permisos para acceder a esta página.</p>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captura de Fotografía</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js"></script>
</head>
<body>
<div class="container">
    <h1>Captura de Fotografía</h1>

    <div class="camera-container">
        <video id="camera" autoplay playsinline></video>
        <canvas id="snapshot" style="display: none;"></canvas>
        <div class="overlay"></div>
        <button id="captureButton" class="btn btn-primary">Capturar Fotografía</button>
    </div>

    <div id="photoPreviewContainer" style="display: none;">
        <h2>Vista Previa</h2>
        <img id="photoPreview" alt="Vista previa de la credencial">
        <button id="confirmButton" class="btn btn-success">Confirmar</button>
        <button id="retakeButton" class="btn btn-warning">Reintentar</button>
    </div>
</div>

<script>
const camera = document.getElementById('camera');
const snapshot = document.getElementById('snapshot');
const photoPreview = document.getElementById('photoPreview');
const photoPreviewContainer = document.getElementById('photoPreviewContainer');
const captureButton = document.getElementById('captureButton');
const confirmButton = document.getElementById('confirmButton');
const retakeButton = document.getElementById('retakeButton');

// Acceder a la cámara
navigator.mediaDevices.getUserMedia({ video: true }).then((stream) => {
    camera.srcObject = stream;
});

// Capturar la imagen
captureButton.addEventListener('click', () => {
    const context = snapshot.getContext('2d');
    snapshot.width = camera.videoWidth;
    snapshot.height = camera.videoHeight;
    context.drawImage(camera, 0, 0, snapshot.width, snapshot.height);

    // Mostrar la vista previa
    photoPreview.src = snapshot.toDataURL('image/png');
    photoPreviewContainer.style.display = 'block';
    captureButton.style.display = 'none';
});

// Confirmar y enviar la foto
confirmButton.addEventListener('click', () => {
    const photoData = snapshot.toDataURL('image/png');

    // Enviar la imagen a lectura_ocr.php
    fetch('lectura_ocr.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ photo: photoData })
    }).then(response => {
        if (response.ok) {
            window.location.href = 'lectura_ocr.php';
        } else {
            alert('Error al procesar la imagen. Por favor, reintenta.');
        }
    });
});

// Reintentar la captura
retakeButton.addEventListener('click', () => {
    photoPreviewContainer.style.display = 'none';
    captureButton.style.display = 'block';
});
</script>
</body>
</html>
