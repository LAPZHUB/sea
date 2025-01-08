<?php
require 'C:\xampp\htdocs\SEA\vendor\autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Prueba de creación de una hoja de cálculo
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', '¡Hola Mundo!');

echo "Biblioteca PHPSpreadsheet cargada correctamente.";
