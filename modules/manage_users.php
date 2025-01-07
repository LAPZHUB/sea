<?php
// manage_users.php: Listar usuarios
session_start();
if ($_SESSION['role'] !== 'superuser') {
    header("Location: /templates/login.html");
    exit;
}

include '/includes/layout.php';
include '/includes/db.php';

renderHeader("Gestión de Usuarios");

// Obtener usuarios desde la base de datos
try {
    $query = $conn->query("SELECT id, username, role, date_create, activo FROM users");
    $users = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Error al obtener usuarios: " . $e->getMessage() . "</p>";
    renderFooter();
    exit;
}

?>

<p>Aquí podrás gestionar los usuarios del sistema. Abajo se muestra la lista completa.</p>
<table border="1" style="width:100%; text-align:left;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Fecha de Creación</th>
            <th>Activo</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= htmlspecialchars($user['date_create']) ?></td>
                <td><?= $user['activo'] ? 'Sí' : 'No' ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<nav>
     <a href="/modules/create_user.php">Crear Usuario</a>
</nav>

<?php
renderFooter();
?>
