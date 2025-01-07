<?php
// list_capturistas.php

require_once '../includes/db.php'; // Conexión a la base de datos
session_start();

// Verifica si el usuario está logeado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol'])) {
    echo "<p>Error: Debes iniciar sesión para acceder a esta página.</p>";
    exit;
}

// Variables del usuario logueado
$usuario_id = $_SESSION['usuario_id'];
$id_rol = $_SESSION['id_rol'];

// Inicialización de filtros
$error = "";
$filters = "";

// Aplicar filtros según rol
if ($id_rol == 1) { // Superusuario
    $filters = "";
} elseif ($id_rol == 2) { // Dirigente
    $filters = "WHERE id_dirigente = $usuario_id";
}

// Procesar filtros de búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $nombre = isset($_GET['nombre']) ? $db->real_escape_string($_GET['nombre']) : null;
    $estado = isset($_GET['estado']) ? $db->real_escape_string($_GET['estado']) : null;
    $activo = isset($_GET['activo']) ? (int)$_GET['activo'] : null;

    if ($nombre) {
        $filters .= " AND nom_capturista LIKE '%$nombre%'";
    }
    if ($estado) {
        $filters .= " AND estado_lider LIKE '%$estado%'";
    }
    if ($activo !== null) {
        $filters .= " AND activo = $activo";
    }
}

// Consultar capturistas
$query = "SELECT nom_capturista, usuario, estado_lider, activo FROM capturista $filters";
$result = $db->query($query);
$capturistas = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Capturistas</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="container">
    <h1>Lista de Capturistas</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="GET" action="">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Buscar por nombre">
        </div>
        <div class="form-group">
            <label for="estado">Estado:</label>
            <input type="text" id="estado" name="estado" class="form-control" placeholder="Buscar por estado">
        </div>
        <div class="form-group">
            <label for="activo">Activo:</label>
            <select id="activo" name="activo" class="form-control">
                <option value="">Todos</option>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <table class="table">
        <thead>
        <tr>
            <th>Nombre Completo</th>
            <th>Usuario</th>
            <th>Estado</th>
            <th>Activo</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($capturistas as $capturista): ?>
            <tr>
                <td><?php echo htmlspecialchars($capturista['nom_capturista']); ?></td>
                <td><?php echo htmlspecialchars($capturista['usuario']); ?></td>
                <td><?php echo htmlspecialchars($capturista['estado_lider']); ?></td>
                <td><?php echo $capturista['activo'] ? 'Sí' : 'No'; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <form method="POST" action="export_excel.php">
        <input type="hidden" name="filters" value="<?php echo htmlspecialchars($filters); ?>">
        <button type="submit" class="btn btn-success">Exportar a Excel</button>
    </form>
</div>
</body>
</html>
