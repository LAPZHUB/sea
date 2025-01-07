<?php
// Mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ver información de PHP
phpinfo();

// Intentar conectar a la base de datos
$host = 'localhost';
$db = 'sea'; // Reemplaza con el nombre de tu base de datos real
$user = 'LAPZ_1'; // Reemplaza con tu nombre de usuario real
$pass = '+-19851808Ap.'; // Reemplaza con tu contraseña real

$conn = new mysqli($host, $user, $pass, $db);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
} else {
    echo "Conexión exitosa a la base de datos.";
}

$conn->close();
?>