<?php
// layout.php: Archivo base para incluir encabezado y pie de página
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.html");
    exit;
}

function renderHeader($title) {
    echo "<!DOCTYPE html>\n<html>\n<head>\n<title>{$title}</title>\n<link rel='stylesheet' href='styles.css'>\n</head>\n<body>";
    echo "<header><h1>{$title}</h1></header>";
}

function renderFooter() {
    echo "<footer><p>&copy; 2025 Sistema SEA</p></footer>\n</body>\n</html>";
}
?>

<!-- auth.php: Redirección según el rol -->
<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $query->bindParam(':username', $username);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        switch ($user['role']) {
            case 'superuser':
                header("Location: superuser_dashboard.php");
                break;
            case 'dirigente':
                header("Location: dirigente_dashboard.php");
                break;
            case 'lider':
                header("Location: lider_dashboard.php");
                break;
            case 'capturista':
                header("Location: capturista_dashboard.php");
                break;
            default:
                header("Location: login.html");
        }
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Credenciales inválidas']);
        exit;
    }
}
?>

<!-- manage_users.php -->
<?php
include 'layout.php';
renderHeader("Gestión de Usuarios");
?>
<p>Aquí podrás gestionar todos los usuarios del sistema.</p>
<nav>
    <a href="create_user.php">Crear Usuario</a>
    <a href="list_users.php">Listar Usuarios</a>
</nav>
<?php
renderFooter();
?>

<!-- manage_leaders.php -->
<?php
include 'layout.php';
renderHeader("Gestión de Líderes");
?>
<p>Aquí podrás gestionar a los líderes bajo tu responsabilidad.</p>
<nav>
    <a href="create_leader.php">Crear Líder</a>
    <a href="list_leaders.php">Listar Líderes</a>
</nav>
<?php
renderFooter();
?>

<!-- manage_capturistas.php -->
<?php
include 'layout.php';
renderHeader("Gestión de Capturistas");
?>
<p>Aquí podrás gestionar a los capturistas bajo tu supervisión.</p>
<nav>
    <a href="create_capturista.php">Crear Capturista</a>
    <a href="list_capturistas.php">Listar Capturistas</a>
</nav>
<?php
renderFooter();
?>

<!-- affiliates_report.php -->
<?php
include 'layout.php';
renderHeader("Resumen de Afiliados");
?>
<p>Consulta los afiliados registrados en el sistema.</p>
<nav>
    <a href="list_affiliates.php">Listar Afiliados</a>
</nav>
<?php
renderFooter();
?>
