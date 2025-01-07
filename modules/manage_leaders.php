<?php
// manage_leaders.php

require_once '../includes/db.php'; // Conexión a la base de datos
session_start();

// Verifica si el usuario está logeado
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['id_rol'], [1, 2])) { // Solo superusuario y dirigente
    echo "<p>Error: No tienes permisos para acceder a esta página.</p>";
    exit;
}

// Variables del usuario logueado
$usuario_id = $_SESSION['usuario_id'];
$id_rol = $_SESSION['id_rol'];

// Inicialización de variables
$error = "";
$success = "";
$filters = "";

// Aplicar filtros según el rol
if ($id_rol == 1) { // Superusuario
    $filters = "";
} elseif ($id_rol == 2) { // Dirigente
    $filters = "WHERE id_dirigente = $usuario_id";
}

// Procesar acciones (activar/desactivar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id_lider = (int)($_POST['id_lider'] ?? 0);

    if ($action && $id_lider) {
        if ($action === 'activate') {
            $update_query = "UPDATE lider SET activo = 1 WHERE id_lider = $id_lider";
        } elseif ($action === 'deactivate') {
            $update_query = "UPDATE lider SET activo = 0 WHERE id_lider = $id_lider";
        }

        if (isset($update_query)) {
            if ($db->query($update_query)) {
                $success = "El estado del líder se actualizó correctamente.";
            } else {
                $error = "Error al actualizar el líder: " . htmlspecialchars($db->error);
            }
        }
    }
}

// Consultar líderes
$query = "SELECT id_lider, nom_lider, usuario, estado_lider, activo FROM lider $filters";
$result = $db->query($query);
$leaders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Líderes</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="container">
    <h1>Gestión de Líderes</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <table class="table">
        <thead>
        <tr>
            <th>Nombre Completo</th>
            <th>Usuario</th>
            <th>Estado</th>
            <th>Activo</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($leaders as $leader): ?>
            <tr>
                <td><?php echo htmlspecialchars($leader['nom_lider']); ?></td>
                <td><?php echo htmlspecialchars($leader['usuario']); ?></td>
                <td><?php echo htmlspecialchars($leader['estado_lider']); ?></td>
                <td><?php echo $leader['activo'] ? 'Sí' : 'No'; ?></td>
                <td>
                    <?php if ($leader['activo']): ?>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="id_lider" value="<?php echo $leader['id_lider']; ?>">
                            <input type="hidden" name="action" value="deactivate">
                            <button type="submit" class="btn btn-warning">Desactivar</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="id_lider" value="<?php echo $leader['id_lider']; ?>">
                            <input type="hidden" name="action" value="activate">
                            <button type="submit" class="btn btn-success">Activar</button>
                        </form>
                    <?php endif; ?>
                    <a href="update_leader.php?id=<?php echo $leader['id_lider']; ?>" class="btn btn-primary">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
