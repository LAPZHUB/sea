<?php
// Placeholder para "manage_users.php"
if (!isset($_SESSION)) session_start();
if ($_SESSION['role'] !== 'superuser') {
    header("Location: login.html");
    exit;
}

include 'layout.php';
renderHeader("Gestión de Usuarios");
?>
<p>Bienvenido al módulo de gestión de usuarios. Aquí podrás administrar usuarios del sistema.</p>
<nav>
    <a href="create_user.php">Crear Usuario</a>
    <a href="list_users.php">Listar Usuarios</a>
</nav>
<?php
renderFooter();
?>

<?php
// Placeholder para "manage_leaders.php"
if (!isset($_SESSION)) session_start();
if ($_SESSION['role'] !== 'dirigente') {
    header("Location: login.html");
    exit;
}

include 'layout.php';
renderHeader("Gestión de Líderes");
?>
<p>Bienvenido al módulo de gestión de líderes. Aquí podrás administrar líderes bajo tu supervisión.</p>
<nav>
    <a href="create_leader.php">Crear Líder</a>
    <a href="list_leaders.php">Listar Líderes</a>
</nav>
<?php
renderFooter();
?>

<?php
// Placeholder para "manage_capturistas.php"
if (!isset($_SESSION)) session_start();
if (!in_array($_SESSION['role'], ['dirigente', 'lider'])) {
    header("Location: login.html");
    exit;
}

include 'layout.php';
renderHeader("Gestión de Capturistas");
?>
<p>Bienvenido al módulo de gestión de capturistas. Aquí podrás administrar capturistas asociados a tus equipos.</p>
<nav>
    <a href="create_capturista.php">Crear Capturista</a>
    <a href="list_capturistas.php">Listar Capturistas</a>
</nav>
<?php
renderFooter();
?>

<?php
// Placeholder para "affiliates_report.php"
if (!isset($_SESSION)) session_start();
if (!in_array($_SESSION['role'], ['dirigente', 'lider', 'capturista'])) {
    header("Location: login.html");
    exit;
}

include 'layout.php';
renderHeader("Resumen de Afiliados");
?>
<p>Bienvenido al módulo de resumen de afiliados. Aquí podrás consultar los afiliados registrados en el sistema.</p>
<nav>
    <a href="list_affiliates.php">Listar Afiliados</a>
</nav>
<?php
renderFooter();
?>