<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../dashboards/dashboard_consoles.php");
    exit;
}

echo "<h1>Bienvenido al Sistema SEA</h1>";
echo "<p>Usuario ID: " . $_SESSION['user_id'] . "</p>";
echo "<a href='../templates/logout.php'>Cerrar sesión</a>";
?>
