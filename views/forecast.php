<?php
require '../includes/db.php'; // Database connection
include '../includes/header.php'; // Include header for navigation
require '../vendor/autoload.php'; // PHPSpreadsheet library

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fetch all products with product_sku
$stmt = $conn->query('SELECT id, product_name, product_sku, barcode FROM products');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Exponential Smoothing Function
function exponential_smoothing($product_id, $conn, $alpha = 0.5, $period = '30_days') {
    $sql = "
        SELECT transaction_type, SUM(quantity) as total_qty, MONTH(transaction_date) as month 
        FROM inventory_transactions 
        WHERE product_id = ? 
        GROUP BY month, transaction_type
        ORDER BY month DESC LIMIT 3";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$product_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $smoothing_value = 0;
    foreach ($transactions as $transaction) {
        $transaction_qty = ($transaction['transaction_type'] == 'outward') ? $transaction['total_qty'] : -$transaction['total_qty'];
        $smoothing_value = $alpha * $transaction_qty + (1 - $alpha) * $smoothing_value;
    }

    // Adjust forecast based on period
    switch ($period) {
        case '15_days':
            return round($smoothing_value / 2); // Half of monthly forecast
        case 'quarterly':
            return round($smoothing_value * 3); // Three times monthly forecast
        default:
            return round($smoothing_value); // Default to monthly
    }
}

// If download is requested
if (isset($_GET['download']) && $_GET['download'] == 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Product Name');
    $sheet->setCellValue('B1', 'Product SKU');
    $sheet->setCellValue('C1', 'Barcode');
    $sheet->setCellValue('D1', '15 Days Forecast');
    $sheet->setCellValue('E1', '30 Days Forecast');
    $sheet->setCellValue('F1', 'Quarterly Forecast');

    $row = 2;
    foreach ($products as $product) {
        $forecast_15_days = exponential_smoothing($product['id'], $conn, 0.5, '15_days');
        $forecast_30_days = exponential_smoothing($product['id'], $conn, 0.5, '30_days');
        $forecast_quarterly = exponential_smoothing($product['id'], $conn, 0.5, 'quarterly');
        
        $sheet->setCellValue('A' . $row, $product['product_name']);
        $sheet->setCellValue('B' . $row, $product['product_sku']);
        $sheet->setCellValue('C' . $row, $product['barcode']);
        $sheet->setCellValue('D' . $row, $forecast_15_days);
        $sheet->setCellValue('E' . $row, $forecast_30_days);
        $sheet->setCellValue('F' . $row, $forecast_quarterly);
        $row++;
    }

    // Create and download Excel file
    $writer = new Xlsx($spreadsheet);
    $fileName = 'Product_Forecast.xlsx';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit;
}
?>

<div class="container mt-4">
    <h2>Product Forecast (Exponential Smoothing)</h2>
    
    <a href="forecast.php?download=excel" class="btn btn-success mb-3">Download as Excel</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Product SKU</th>
                <th>Barcode</th>
                <th>15 Days Forecast</th>
                <th>30 Days Forecast</th>
                <th>Quarterly Forecast</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td><?= $product['product_name']; ?></td>
                    <td><?= $product['product_sku']; ?></td>
                    <td><?= $product['barcode']; ?></td>
                    <td><?= exponential_smoothing($product['id'], $conn, 0.5, '15_days'); ?></td>
                    <td><?= exponential_smoothing($product['id'], $conn, 0.5, '30_days'); ?></td>
                    <td><?= exponential_smoothing($product['id'], $conn, 0.5, 'quarterly'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require '../includes/footer.php'; ?>
