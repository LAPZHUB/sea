<?php
// layout.php: Archivo base para incluir encabezado y pie de página
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.html");
    exit;
}

function renderHeader($title) {
    echo "<!DOCTYPE html>\n<html>\n<head>\n<title>{$title}</title>\n</head>\n<body>";
    echo "<h1>{$title}</h1>";
}

function renderFooter() {
    echo "</body>\n</html>";
}
?>

<!-- superuser_dashboard.php -->
<?php
include 'layout.php';
if ($_SESSION['role'] !== 'superuser') {
    header("Location: login.html");
    exit;
}
renderHeader("Consola Superusuario");
?>
<nav>
    <a href="manage_users.php">Gestionar Usuarios</a>
    <a href="global_reports.php">Reportes Globales</a>
    <a href="permissions.php">Gestión de Permisos</a>
</nav>
<?php
renderFooter();
?>

<!-- dirigente_dashboard.php -->
<?php
include 'layout.php';
if ($_SESSION['role'] !== 'dirigente') {
    header("Location: login.html");
    exit;
}
renderHeader("Consola Dirigente");
?>
<nav>
    <a href="manage_leaders.php">Gestionar Líderes</a>
    <a href="manage_capturistas.php">Gestionar Capturistas</a>
    <a href="affiliates_report.php">Resumen de Afiliados</a>
</nav>
<?php
renderFooter();
?>

<!-- lider_dashboard.php -->
<?php
include 'layout.php';
if ($_SESSION['role'] !== 'lider') {
    header("Location: login.html");
    exit;
}
renderHeader("Consola Líder");
?>
<nav>
    <a href="manage_capturistas.php">Gestionar Capturistas</a>
    <a href="register_affiliate.php">Registrar Afiliado</a>
    <a href="affiliates_report.php">Consultar Afiliados</a>
</nav>
<?php
renderFooter();
?>

<!-- capturista_dashboard.php -->
<?php
include 'layout.php';
if ($_SESSION['role'] !== 'capturista') {
    header("Location: login.html");
    exit;
}
renderHeader("Consola Capturista");
?>
<nav>
    <a href="register_affiliate.php">Registrar Afiliado</a>
    <a href="affiliates_report.php">Consultar Afiliados</a>
</nav>
<?php
renderFooter();
?>
