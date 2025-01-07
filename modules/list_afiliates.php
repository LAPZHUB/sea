<?php
// list_afiliates.php

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

// Inicialización de variables
$error = "";
$filters = "";

// Generar filtros según el rol
if ($id_rol == 1) { // Superusuario
    $filters = "";
} elseif ($id_rol == 2) { // Dirigente
    $filters = "WHERE id_dirigente = $usuario_id";
} elseif ($id_rol == 3) { // Líder
    $filters = "WHERE id_lider = $usuario_id";
} elseif ($id_rol == 4) { // Capturista
    $filters = "WHERE id_capturista = $usuario_id";
}

// Procesar filtros de búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $nombre = isset($_GET['nombre']) ? $db->real_escape_string($_GET['nombre']) : null;
    $colonia = isset($_GET['colonia']) ? $db->real_escape_string($_GET['colonia']) : null;
    $seccion = isset($_GET['seccion']) ? $db->real_escape_string($_GET['seccion']) : null;

    if ($nombre) {
        $filters .= " AND nombre_completo LIKE '%$nombre%'";
    }
    if ($colonia) {
        $filters .= " AND colonia LIKE '%$colonia%'";
    }
    if ($seccion) {
        $filters .= " AND seccion = '$seccion'";
    }
}

// Consultar datos de afiliados
$query = "SELECT nombre_completo, colonia, seccion FROM afiliado $filters";
$result = $db->query($query);
$afiliados = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Afiliados</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="container">
    <h1>Lista de Afiliados</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="GET" action="">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Buscar por nombre">
        </div>
        <div class="form-group">
            <label for="colonia">Colonia:</label>
            <input type="text" id="colonia" name="colonia" class="form-control" placeholder="Buscar por colonia">
        </div>
        <div class="form-group">
            <label for="seccion">Sección:</label>
            <input type="text" id="seccion" name="seccion" class="form-control" placeholder="Buscar por sección">
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <table class="table">
        <thead>
        <tr>
            <th>Nombre Completo</th>
            <th>Colonia</th>
            <th>Sección</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($afiliados as $afiliado): ?>
            <tr>
                <td><?php echo htmlspecialchars($afiliado['nombre_completo']); ?></td>
                <td><?php echo htmlspecialchars($afiliado['colonia']); ?></td>
                <td><?php echo htmlspecialchars($afiliado['seccion']); ?></td>
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
