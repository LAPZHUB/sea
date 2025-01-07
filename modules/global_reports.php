<?php
// global_reports.php

require_once '../includes/db.php'; // Conexión a la base de datos
session_start();

// Verifica si el usuario tiene permisos para acceder
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['id_rol'], [1, 2])) { // Superusuario o dirigente
    echo "<p>Error: No tienes permisos para acceder a esta página.</p>";
    exit;
}

// Inicialización de variables para errores y mensajes
$error = "";

// Procesar los filtros
$filters = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $estado = isset($_GET['estado']) ? $db->real_escape_string($_GET['estado']) : null;
    $fecha_inicio = isset($_GET['fecha_inicio']) ? $db->real_escape_string($_GET['fecha_inicio']) : null;
    $fecha_fin = isset($_GET['fecha_fin']) ? $db->real_escape_string($_GET['fecha_fin']) : null;

    if ($estado) {
        $filters .= " AND estado = '$estado'";
    }
    if ($fecha_inicio && $fecha_fin) {
        $filters .= " AND fecha_captura BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
}

// Consultar datos generales
$query = "SELECT estado, COUNT(*) AS total_afiliados 
          FROM afiliado 
          WHERE 1=1 $filters 
          GROUP BY estado";
$result = $db->query($query);
$report_data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Globales</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container">
    <h1>Reportes Globales</h1>

    <form method="GET" action="">
        <div class="form-group">
            <label for="estado">Estado:</label>
            <input type="text" id="estado" name="estado" class="form-control" placeholder="Ingrese un estado">
        </div>
        <div class="form-group">
            <label for="fecha_inicio">Fecha Inicio:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control">
        </div>
        <div class="form-group">
            <label for="fecha_fin">Fecha Fin:</label>
            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div>
        <h2>Resumen por Estado</h2>
        <canvas id="reportChart"></canvas>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>Estado</th>
            <th>Total Afiliados</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($report_data as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['estado']); ?></td>
                <td><?php echo htmlspecialchars($row['total_afiliados']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Renderizar gráfico
const ctx = document.getElementById('reportChart').getContext('2d');
const chartData = {
    labels: <?php echo json_encode(array_column($report_data, 'estado')); ?>,
    datasets: [{
        label: 'Afiliados por Estado',
        data: <?php echo json_encode(array_column($report_data, 'total_afiliados')); ?>,
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
    }]
};
new Chart(ctx, {
    type: 'bar',
    data: chartData,
    options: {
        responsive: true,
        plugins: {
            legend: { display: true },
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
</body>
</html>
