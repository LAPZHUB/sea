<?php
session_start();
if ($_SESSION['role'] !== 'lider') {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Consola Líder</title>
</head>
<body>
    <h1>Bienvenido Líder</h1>
    <nav>
        <a href="manage_capturistas.php">Gestionar Capturistas</a>
        <a href="register_affiliate.php">Registrar Afiliado</a>
        <a href="affiliates_report.php">Consultar Afiliados</a>
    </nav>
</body>
</html>
