<?php
// export_excel.php

require_once '../includes/db.php'; // Conexión a la base de datos
require  'C:\xampp\htdocs\SEA\vendor\autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();

// Verifica si el usuario tiene permisos para exportar
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol'])) {
    echo "<p>Error: Debes iniciar sesión para exportar datos.</p>";
    exit;
}

// Variables del usuario logueado
$usuario_id = $_SESSION['usuario_id'];
$id_rol = $_SESSION['id_rol'];

// Recuperar filtros
$filters = isset($_POST['filters']) ? $_POST['filters'] : "";
if ($id_rol == 2) { // Dirigente
    $filters = "WHERE id_dirigente = $usuario_id " . $filters;
} elseif ($id_rol == 3) { // Líder
    $filters = "WHERE id_lider = $usuario_id " . $filters;
} elseif ($id_rol == 4) { // Capturista
    $filters = "WHERE id_capturista = $usuario_id " . $filters;
}

// Consultar datos filtrados
$query = "SELECT nombre_completo, colonia, seccion FROM afiliado $filters";
$result = $db->query($query);

if (!$result || $result->num_rows === 0) {
    echo "<p>No hay datos para exportar.</p>";
    exit;
}

// Crear el archivo Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Establecer encabezados
$sheet->setCellValue('A1', 'Nombre Completo');
$sheet->setCellValue('B1', 'Colonia');
$sheet->setCellValue('C1', 'Sección');

// Agregar datos
$row = 2;
while ($data = $result->fetch_assoc()) {
    $sheet->setCellValue("A{$row}", $data['nombre_completo']);
    $sheet->setCellValue("B{$row}", $data['colonia']);
    $sheet->setCellValue("C{$row}", $data['seccion']);
    $row++;
}

// Establecer cabeceras de descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="afiliados.xlsx"');
header('Cache-Control: max-age=0');

// Guardar y enviar el archivo Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
