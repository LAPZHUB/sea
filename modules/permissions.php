<?php
// permissions.php

require_once '../includes/db.php'; // Conexión a la base de datos
session_start();

// Verifica si el usuario tiene permisos para acceder
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_rol'] != 1) { // Solo superusuarios
    echo "<p>Error: No tienes permisos para acceder a esta página.</p>";
    exit;
}

// Inicialización de variables
$error = "";
$success = "";

// Procesar cambios en permisos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_id = (int)$_POST['role_id'];
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    // Resetear permisos del rol
    $db->query("DELETE FROM role_permissions WHERE role_id = $role_id");

    // Insertar nuevos permisos
    foreach ($permissions as $permission) {
        $permission = $db->real_escape_string($permission);
        $db->query("INSERT INTO role_permissions (role_id, permission) VALUES ($role_id, '$permission')");
    }

    $success = "Permisos actualizados correctamente.";
}

// Consultar roles y permisos
$roles_query = "SELECT id_rol, nombre FROM rol";
$roles_result = $db->query($roles_query);
$roles = $roles_result ? $roles_result->fetch_all(MYSQLI_ASSOC) : [];

$permissions_query = "SELECT permission FROM available_permissions";
$permissions_result = $db->query($permissions_query);
$available_permissions = $permissions_result ? $permissions_result->fetch_all(MYSQLI_ASSOC) : [];

// Consultar permisos actuales por rol
$current_permissions_query = "SELECT role_id, permission FROM role_permissions";
$current_permissions_result = $db->query($current_permissions_query);
$current_permissions = [];
if ($current_permissions_result) {
    while ($row = $current_permissions_result->fetch_assoc()) {
        $current_permissions[$row['role_id']][] = $row['permission'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Permisos</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div class="container">
    <h1>Gestión de Permisos</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="role_id">Seleccionar Rol:</label>
            <select id="role_id" name="role_id" class="form-control" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id_rol']; ?>"><?php echo htmlspecialchars($role['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="permissions">Seleccionar Permisos:</label>
            <?php foreach ($available_permissions as $permission): ?>
                <div class="form-check">
                    <input type="checkbox" id="permission_<?php echo $permission['permission']; ?>" name="permissions[]" 
                           value="<?php echo htmlspecialchars($permission['permission']); ?>"
                           class="form-check-input">
                    <label for="permission_<?php echo $permission['permission']; ?>" class="form-check-label">
                        <?php echo htmlspecialchars($permission['permission']); ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Permisos</button>
    </form>

    <h2>Permisos Actuales</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Rol</th>
            <th>Permisos</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($roles as $role): ?>
            <tr>
                <td><?php echo htmlspecialchars($role['nombre']); ?></td>
                <td>
                    <?php echo isset($current_permissions[$role['id_rol']]) ? implode(', ', $current_permissions[$role['id_rol']]) : 'Sin permisos'; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
