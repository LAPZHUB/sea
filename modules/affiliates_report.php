<?php
// affiliates_report.php

// Conexión a la base de datos con manejo de errores
require_once '../includes/db.php';

if ($db->connect_error) {
    die("Error en la conexión a la base de datos: " . $db->connect_error);
}

require_once '../includes/layout.php'; // Encabezado y estilos

session_start();

// Verifica si el usuario está logeado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol'])) {
    echo "<h3>Error: Debes iniciar sesión para acceder a esta página.</h3>";
    header("Location: ../templates/login.html");
    exit;
}

// Variables del usuario logeado
$usuario_id = $_SESSION['usuario_id'];
$id_rol = $_SESSION['id_rol'];

// Define las opciones disponibles según el rol
$report_options = [
    1 => ['day', 'week', 'month', 'map', 'stats', 'list', 'export'], // Superusuario: todo incluyendo exportación
    2 => ['day', 'week', 'month', 'map', 'stats', 'list'], // Dirigente
    3 => ['day', 'week', 'month', 'map', 'stats', 'list'], // Líder
    4 => ['day', 'week', 'month', 'map', 'list'], // Capturista: sin estadísticas
];

// Verifica el acceso según el rol
if (!array_key_exists($id_rol, $report_options)) {
    echo "<h3>No tienes permiso para acceder a esta página.</h3>";
    exit;
}

// Función para obtener datos según el tipo de corte
function get_report_data($type, $usuario_id, $id_rol, $db) {
    // Sanitización de variables
    $type = $db->real_escape_string($type);
    $usuario_id = (int)$usuario_id;
    $filters = "";

    if ($id_rol == 2) {
        $filters = "WHERE id_dirigente = $usuario_id";
    } elseif ($id_rol == 3) {
        $filters = "WHERE id_lider = $usuario_id";
    } elseif ($id_rol == 4) {
        $filters = "WHERE id_capturista = $usuario_id";
    }

    $query = "";
    switch ($type) {
        case 'day':
            $query = "SELECT * FROM afiliado $filters AND DATE(fecha_captura) = CURDATE()";
            break;
        case 'week':
            $query = "SELECT * FROM afiliado $filters AND WEEK(fecha_captura) = WEEK(CURDATE())";
            break;
        case 'month':
            $query = "SELECT * FROM afiliado $filters AND MONTH(fecha_captura) = MONTH(CURDATE())";
            break;
        default:
            throw new Exception("Tipo de reporte no válido: $type");
    }

    // Ejecución segura de la consulta
    $result = $db->query($query);
    if (!$result) {
        throw new Exception("Error en la consulta: " . $db->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

// Renderización del dashboard mediante plantilla
template_render('dashboard', [
    'report_options' => $report_options[$id_rol],
    'id_rol' => $id_rol,
]);

?>