<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

echo "<h1>Bienvenido al Sistema SEA</h1>";
echo "<p>Usuario ID: " . $_SESSION['user_id'] . "</p>";
echo "<a href='logout.php'>Cerrar sesión</a>";
?>
