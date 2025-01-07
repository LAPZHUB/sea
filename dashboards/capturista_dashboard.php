<?php
session_start();
if ($_SESSION['role'] !== 'capturista') {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Consola Capturista</title>
</head>
<body>
    <h1>Bienvenido Capturista</h1>
    <nav>
        <a href="register_affiliate.php">Registrar Afiliado</a>
        <a href="affiliates_report.php">Consultar Afiliados</a>
    </nav>
</body>
</html>
