<?php
// register_affiliate.php

require_once '../includes/db.php'; // Conexión a la base de datos
session_start();

// Verificar si el usuario tiene permisos
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['id_rol'], [1, 2, 3, 4])) {
    echo "<p>Error: No tienes permisos para acceder a esta página.</p>";
    exit;
}

// Procesar los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = $db->real_escape_string(trim($_POST['nombre_completo']));
    $domicilio = $db->real_escape_string(trim($_POST['domicilio']));
    $clave_elector = $db->real_escape_string(trim($_POST['clave_elector']));
    $curp = $db->real_escape_string(trim($_POST['curp']));
    $seccion = $db->real_escape_string(trim($_POST['seccion']));
    
    // Variables adicionales
    $usuario_id = $_SESSION['usuario_id'];
    $id_rol = $_SESSION['id_rol'];
    $id_dirigente = ($id_rol == 2) ? $usuario_id : ($_POST['id_dirigente'] ?? null);
    $id_lider = ($id_rol == 3) ? $usuario_id : ($_POST['id_lider'] ?? null);
    $id_capturista = ($id_rol == 4) ? $usuario_id : ($_POST['id_capturista'] ?? null);

    // Insertar datos en la base de datos
    $query = "INSERT INTO afiliado (nombre_completo, domicilio, clave_elector, curp, seccion, id_dirigente, id_lider, id_capturista, fecha_captura) 
              VALUES ('$nombre_completo', '$domicilio', '$clave_elector', '$curp', '$seccion', '$id_dirigente', '$id_lider', '$id_capturista', NOW())";

    if ($db->query($query)) {
        $id_afiliado = $db->insert_id;

        // Renombrar y mover las imágenes finales
        $basePath = '../assets/images/';
        $folderPath = $basePath . "{$id_dirigente}_{$id_lider}_{$id_capturista}/";
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Renombrar imagen de la credencial
        $tempPhotoPath = '../uploads/temp_credencial.png';
        $finalPhotoPath = $folderPath . "{$id_dirigente}_{$id_lider}_{$id_capturista}_{$id_afiliado}_foto_credencial.png";
        if (file_exists($tempPhotoPath)) {
            rename($tempPhotoPath, $finalPhotoPath);
        }

        // Renombrar imagen de la firma
        $tempSignaturePath = '../uploads/temp_firma.png';
        $finalSignaturePath = $folderPath . "{$id_dirigente}_{$id_lider}_{$id_capturista}_{$id_afiliado}_foto_firma.png";
        if (file_exists($tempSignaturePath)) {
            rename($tempSignaturePath, $finalSignaturePath);
        }

        echo "<p>Afiliado registrado exitosamente. ID: $id_afiliado</p>";
        echo "<a href='register_affiliate.php'>Registrar otro afiliado</a>";
    } else {
        echo "<p>Error al registrar al afiliado: " . htmlspecialchars($db->error) . "</p>";
    }
}
?>
