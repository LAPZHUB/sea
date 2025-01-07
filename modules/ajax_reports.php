<?php
// ajax_reports.php

// Conexión a la base de datos con manejo de errores
require_once '../includes/db.php';

if ($db->connect_error) {
    http_response_code(500);
    echo "Error en la conexión a la base de datos: " . htmlspecialchars($db->connect_error);
    exit;
}

session_start();

// Verifica si el usuario está logeado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol'])) {
    http_response_code(403);
    echo "<p>Error: Debes iniciar sesión para acceder a esta página. Por favor, <a href='../templates/login.html'>inicia sesión aquí</a>.</p>";
    exit;
}

// Variables del usuario logeado
$usuario_id = $_SESSION['usuario_id'];
$id_rol = $_SESSION['id_rol'];

// Validar tipo de reporte
if (!isset($_GET['type'])) {
    http_response_code(400);
    echo "Tipo de reporte no especificado.";
    exit;
}

$type = filter_var($_GET['type'], FILTER_SANITIZE_STRING); // Validación y sanitización del tipo de reporte

// Función para obtener datos según el tipo de reporte
function getReportData($type, $usuario_id, $id_rol, $db) {
    $allowed_types = ['day', 'week', 'month', 'map', 'stats', 'list']; // Tipos permitidos de reportes

    if (!in_array($type, $allowed_types)) {
        throw new Exception("Tipo de reporte no permitido: $type");
    }

    $filters = "";
    $usuario_id = (int)$usuario_id; // Sanitización del ID de usuario

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
        case 'map':
            $query = "SELECT nombre_completo, latitud_foto, longitud_foto, latitud_dir, longitud_dir FROM afiliado $filters";
            break;
        case 'stats':
            $query = "SELECT COUNT(*) AS total, colonia, id_lider, id_dirigente, seccion FROM afiliado $filters GROUP BY colonia, id_lider, seccion";
            break;
        case 'list':
            $query = "SELECT nombre_completo, colonia, seccion FROM afiliado $filters";
            break;
    }

    $result = $db->query($query);
    if (!$result) {
        throw new Exception("Error en la consulta: " . htmlspecialchars($db->error));
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

try {
    // Procesar datos del reporte
    $data = getReportData($type, $usuario_id, $id_rol, $db);

    if (empty($data)) {
        echo "<p>No se encontraron datos para el reporte solicitado.</p>";
        exit;
    }

    // Generar salida según el tipo de reporte
    if ($type == 'map') {
        // Generar tabla para mapa
        echo "<table class='table'>";
        echo "<thead><tr><th>Nombre</th><th>Latitud Foto</th><th>Longitud Foto</th><th>Latitud Dirección</th><th>Longitud Dirección</th></tr></thead><tbody>";
        foreach ($data as $row) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['nombre_completo']) . "</td>
                    <td>" . htmlspecialchars($row['latitud_foto']) . "</td>
                    <td>" . htmlspecialchars($row['longitud_foto']) . "</td>
                    <td>" . htmlspecialchars($row['latitud_dir']) . "</td>
                    <td>" . htmlspecialchars($row['longitud_dir']) . "</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } elseif ($type == 'stats') {
        // Generar datos para estadísticas
        echo "<script>
            const statsData = " . json_encode($data) . ";
            renderStatsChart(statsData);
        </script>";
    } else {
        // Generar tabla para cortes/lista
        echo "<table class='table'>";
        echo "<thead><tr>";
        foreach (array_keys($data[0]) as $key) {
            echo "<th>" . ucfirst(htmlspecialchars($key)) . "</th>";
        }
        echo "</tr></thead><tbody>";
        foreach ($data as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

$db->close();

?>
