<?php
// update_afiliate.php

require_once '../includes/db.php'; // Conexión a la base de datos
session_start();

// Verifica si el usuario tiene permisos para acceder
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['id_rol'], [1, 2, 3, 4])) {
    echo "<p>Error: No tienes permisos para acceder a esta página.</p>";
    exit;
}

// Inicialización de variables
$error = "";
$success = "";

// Validar el ID del afiliado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Error: ID de afiliado inválido.</p>";
    exit;
}

$id_afiliado = (int)$_GET['id'];

// Consultar datos actuales del afiliado
$query = "SELECT * FROM afiliado WHERE id_afiliado = $id_afiliado";
$result = $db->query($query);

if (!$result || $result->num_rows === 0) {
    echo "<p>Error: Afiliado no encontrado.</p>";
    exit;
}

$afiliado = $result->fetch_assoc();

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = $db->real_escape_string(trim($_POST['nombre_completo']));
    $colonia = $db->real_escape_string(trim($_POST['colonia']));
    $seccion = $db->real_escape_string(trim($_POST['seccion']));
    $calle = $db->real_escape_string(trim($_POST['calle']));
    $codigo_postal = $db->real_escape_string(trim($_POST['codigo_postal']));

    // Validaciones básicas
    if (empty($nombre_completo) || empty($colonia) || empty($seccion)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Actualizar en la base de datos
        $update_query = "UPDATE afiliado 
                         SET nombre_completo = '$nombre_completo',
                             colonia = '$colonia',
                             seccion = '$seccion',
                             calle = '$calle',
                             codigo_postal = '$codigo_postal'
                         WHERE id_afiliado = $id_afiliado";
        if ($db->query($update_query)) {
            $success = "Afiliado actualizado exitosamente.";
        } else {
            $error = "Error al actualizar el afiliado: " . htmlspecialchars($db->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Afiliado</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="container">
    <h1>Actualizar Afiliado</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" 
                   value="<?php echo htmlspecialchars($afiliado['nombre_completo']); ?>" required>
        </div>
        <div class="form-group">
            <label for="colonia">Colonia:</label>
            <input type="text" id="colonia" name="colonia" class="form-control" 
                   value="<?php echo htmlspecialchars($afiliado['colonia']); ?>" required>
        </div>
        <div class="form-group">
            <label for="seccion">Sección:</label>
            <input type=
