<?php
require 'includes/auth.php';
require 'includes/db.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fetch all products
$stmt = $conn->prepare('SELECT * FROM products');
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total inventory from returns
$stmtInventory = $conn->prepare('
    SELECT 
        SUM(CASE WHEN inventory_type = "Good" THEN quantity ELSE 0 END) as total_good_inventory,
        SUM(CASE WHEN inventory_type = "Bad" THEN quantity ELSE 0 END) as total_bad_inventory,
        SUM(CASE WHEN inventory_type = "Damaged" THEN quantity ELSE 0 END) as total_damaged_inventory
    FROM returns
');
$stmtInventory->execute();
$inventoryResult = $stmtInventory->fetch(PDO::FETCH_ASSOC);
$totalGoodInventory = $inventoryResult['total_good_inventory'] ?? 0; // Default to 0 if null
$totalBadInventory = $inventoryResult['total_bad_inventory'] ?? 0; // Default to 0 if null
$totalDamagedInventory = $inventoryResult['total_damaged_inventory'] ?? 0; // Default to 0 if null

// Create new Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set headers
$sheet->setCellValue('A1', 'ID')
      ->setCellValue('B1', 'Product SKU')
      ->setCellValue('C1', 'Product Name')
      ->setCellValue('D1', 'Quantity')
      ->setCellValue('E1', 'Total Usable Quantity (Good Inventory)')
      ->setCellValue('F1', 'Total Bad Inventory')
      ->setCellValue('G1', 'Total Damaged Inventory')
      ->setCellValue('H1', 'Amazon ASIN')
      ->setCellValue('I1', 'Barcode');

// Populate the spreadsheet with data
$row = 2; // Start from row 2
foreach ($products as $product) {
    $sheet->setCellValue('A' . $row, $product['id']);
    $sheet->setCellValue('B' . $row, $product['product_sku']);
    $sheet->setCellValue('C' . $row, $product['product_name']);
    $sheet->setCellValue('D' . $row, $product['quantity']);
    $sheet->setCellValue('E' . $row, $product['quantity'] + $totalGoodInventory); // Total Usable Quantity
    $sheet->setCellValue('F' . $row, $totalBadInventory); // Total Bad Inventory
    $sheet->setCellValue('G' . $row, $totalDamagedInventory); // Total Damaged Inventory
    $sheet->setCellValue('H' . $row, $product['amazon_asin']);
    $sheet->setCellValue('I' . $row, $product['barcode']);
    $row++;
}

// Write the file
$writer = new Xlsx($spreadsheet);
$filename = 'inventory_' . date('Y-m-d_H-i-s') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$writer->save('php://output');
exit;
?>
