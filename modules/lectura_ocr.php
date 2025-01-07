<?php
// lectura_ocr.php

require_once '../includes/db.php'; // Conexión a la base de datos
session_start();

// Verificar si el usuario tiene permisos
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['id_rol'], [1, 2, 3, 4])) {
    echo "<p>Error: No tienes permisos para acceder a esta página.</p>";
    exit;
}

// Procesar imagen recibida
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['photo'])) {
    echo "<p>Error: No se recibió ninguna imagen.</p>";
    exit;
}

$photoData = $data['photo'];
$tempPhotoPath = '../uploads/temp_credencial.png';

// Guardar imagen temporalmente
file_put_contents($tempPhotoPath, file_get_contents($photoData));

// Proporciones para recortar la firma (ajustables si es necesario)
$image = imagecreatefrompng($tempPhotoPath);
$imageWidth = imagesx($image);
$imageHeight = imagesy($image);

$signatureBox = [
    (int)(0.02 * $imageWidth), // 2% desde la izquierda
    (int)(0.80 * $imageHeight), // 80% desde arriba
    (int)(0.35 * $imageWidth), // 35% del ancho total
    (int)(0.15 * $imageHeight), // 15% de la altura total
];

// Extraer el área de la firma
$signatureImage = imagecrop($image, [
    'x' => $signatureBox[0],
    'y' => $signatureBox[1],
    'width' => $signatureBox[2],
    'height' => $signatureBox[3]
]);

$signaturePath = '../uploads/temp_firma.png';
imagepng($signatureImage, $signaturePath);
imagedestroy($image);
imagedestroy($signatureImage);

// OCR con Google Vision API (Ejemplo de integración)
require '../vendor/autoload.php';
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

putenv('GOOGLE_APPLICATION_CREDENTIALS=../config/google_credentials.json');
$imageAnnotator = new ImageAnnotatorClient();
$response = $imageAnnotator->textDetection(file_get_contents($tempPhotoPath));
$texts = $response->getTextAnnotations();

// Procesar los textos obtenidos
$extractedData = [
    'nombre_completo' => '',
    'domicilio' => '',
    'clave_elector' => '',
    'curp' => '',
    'seccion' => '',
    'estado' => '',
    'municipio' => '',
];

if ($texts) {
    $fullText = $texts[0]->getDescription();
    // Patrón de extracción de datos (ajustar según necesidad)
    if (preg_match('/NOMBRE\s+(.*)/', $fullText, $matches)) {
        $extractedData['nombre_completo'] = trim($matches[1]);
    }
    if (preg_match('/DOMICILIO\s+(.*)/', $fullText, $matches)) {
        $extractedData['domicilio'] = trim($matches[1]);
    }
    if (preg_match('/CLAVE DE ELECTOR\s+(.*)/', $fullText, $matches)) {
        $extractedData['clave_elector'] = trim($matches[1]);
    }
    if (preg_match('/CURP\s+(.*)/', $fullText, $matches)) {
        $extractedData['curp'] = trim($matches[1]);
    }
    if (preg_match('/SECCION\s+(\d+)/', $fullText, $matches)) {
        $extractedData['seccion'] = trim($matches[1]);
    }
}

$imageAnnotator->close();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lectura OCR y Vista Previa</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="container">
    <h1>Vista Previa de los Datos</h1>

    <form method="POST" action="register_affiliate.php">
        <div class="form-group">
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo htmlspecialchars($extractedData['nombre_completo']); ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="domicilio">Domicilio:</label>
            <textarea id="domicilio" name="domicilio" class="form-control" required><?php echo htmlspecialchars($extractedData['domicilio']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="clave_elector">Clave de Elector:</label>
            <input type="text" id="clave_elector" name="clave_elector" value="<?php echo htmlspecialchars($extractedData['clave_elector']); ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="curp">CURP:</label>
            <input type="text" id="curp" name="curp" value="<?php echo htmlspecialchars($extractedData['curp']); ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="seccion">Sección:</label>
            <input type="text" id="seccion" name="seccion" value="<?php echo htmlspecialchars($extractedData['seccion']); ?>" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
</body>
</html>
