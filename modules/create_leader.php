<?php
// create_leader.php

require_once '../includes/db.php'; // Conexión a la base de datos
session_start();

// Verifica si el usuario tiene permisos para acceder
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_rol'] != 1) { // Solo superusuarios pueden crear líderes
    echo "<p>Error: No tienes permisos para acceder a esta página.</p>";
    exit;
}

// Inicialización de variables para errores y mensajes
$error = "";
$success = "";

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_lider = $db->real_escape_string(trim($_POST['nom_lider']));
    $usuario = $db->real_escape_string(trim($_POST['usuario']));
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT); // Encriptar la contraseña
    $estado_lider = $db->real_escape_string(trim($_POST['estado_lider']));
    $id_dirigente = (int)$_POST['id_dirigente'];
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Validaciones básicas
    if (empty($nom_lider) || empty($usuario) || empty($_POST['password']) || empty($estado_lider)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (strlen($_POST['password']) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        // Insertar en la base de datos
        $query = "INSERT INTO lider (nom_lider, usuario, password, estado_lider, id_dirigente, activo) 
                  VALUES ('$nom_lider', '$usuario', '$password', '$estado_lider', $id_dirigente, $activo)";
        if ($db->query($query)) {
            $success = "Líder creado exitosamente.";
        } else {
            $error = "Error al crear el líder: " . htmlspecialchars($db->error);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Líder</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="container">
    <h1>Crear Líder</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="nom_lider">Nombre Completo:</label>
            <input type="text" id="nom_lider" name="nom_lider" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="estado_lider">Estado:</label>
            <input type="text" id="estado_lider" name="estado_lider" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="id_dirigente">Dirigente Asociado:</label>
            <input type="number" id="id_dirigente" name="id_dirigente" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="activo">
                <input type="checkbox" id="activo" name="activo"> Activo
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
</body>
</html>
