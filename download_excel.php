<?php
require 'includes/auth.php';
require 'includes/db.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'Product SKU');
$sheet->setCellValue('B1', 'Product Name');
$sheet->setCellValue('C1', 'Quantity');
$sheet->setCellValue('D1', 'Amazon ASIN');
$sheet->setCellValue('E1', 'Barcode');

// Fetch products from the database
$stmt = $conn->query('SELECT * FROM products');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$row = 2;
foreach ($products as $product) {
    $sheet->setCellValue("A{$row}", $product['product_sku']);
    $sheet->setCellValue("B{$row}", $product['product_name']);
    $sheet->setCellValue("C{$row}", $product['quantity']);
    $sheet->setCellValue("D{$row}", $product['amazon_asin']);
    $sheet->setCellValue("E{$row}", $product['barcode']);
    $row++;
}

// Create the writer and output to the browser
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="product_data.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
?>
