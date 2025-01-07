<?php
session_start();
if ($_SESSION['role'] !== 'dirigente') {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Consola Dirigente</title>
</head>
<body>
    <h1>Bienvenido Dirigente</h1>
    <nav>
        <a href="manage_leaders.php">Gestionar Líderes</a>
        <a href="manage_capturistas.php">Gestionar Capturistas</a>
        <a href="affiliates_report.php">Resumen de Afiliados</a>
    </nav>
</body>
</html>
