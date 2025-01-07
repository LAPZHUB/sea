<?php
session_start();
if ($_SESSION['role'] !== 'superuser') {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Consola Superusuario</title>
</head>
<body>
    <h1>Bienvenido Superusuario</h1>
    <nav>
        <a href="manage_users.php">Gestionar Usuarios</a>
        <a href="global_reports.php">Reportes Globales</a>
        <a href="permissions.php">Gestión de Permisos</a>
    </nav>
</body>
</html>
