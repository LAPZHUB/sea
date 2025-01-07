<?php
// layout.php: Archivo base para incluir encabezado y pie de página
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: /templates/login.html");
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
include '/includes/layout.php';
if ($_SESSION['role'] !== 'superuser') {
    header("Location: /templates/login.html");
    exit;
}
renderHeader("Consola Superusuario");
?>
<nav>
    <a href="/modules/manage_users.php">Gestionar Usuarios</a>
    <a href="/modules/global_reports.php">Reportes Globales</a>
    <a href="/modules/permissions.php">Gestión de Permisos</a>
</nav>
<?php
renderFooter();
?>

<!-- dirigente_dashboard.php -->
<?php
include '/includes/layout.php';
if ($_SESSION['role'] !== 'dirigente') {
    header("Location: /templates/login.html");
    exit;
}
renderHeader("Consola Dirigente");
?>
<nav>
    <a href="/modules/manage_leaders.php">Gestionar Líderes</a>
    <a href="/modules/manage_capturistas.php">Gestionar Capturistas</a>
    <a href="/modules/affiliates_report.php">Resumen de Afiliados</a>
</nav>
<?php
renderFooter();
?>

<!-- lider_dashboard.php -->
<?php
include '/includes/layout.php';
if ($_SESSION['role'] !== 'lider') {
    header("Location: /templates/login.html");
    exit;
}
renderHeader("Consola Líder");
?>
<nav>
    <a href="/modules/manage_capturistas.php">Gestionar Capturistas</a>
    <a href="/modules/register_affiliate.php">Registrar Afiliado</a>
    <a href="/modules/affiliates_report.php">Consultar Afiliados</a>
</nav>
<?php
renderFooter();
?>

<!-- capturista_dashboard.php -->
<?php
include '/includes/layout.php';
if ($_SESSION['role'] !== 'capturista') {
    header("Location: /templates/login.html");
    exit;
}
renderHeader("Consola Capturista");
?>
<nav>
    <a href="/modules/register_affiliate.php">Registrar Afiliado</a>
    <a href="/modules/affiliates_report.php">Consultar Afiliados</a>
</nav>
<?php
renderFooter();
?>
