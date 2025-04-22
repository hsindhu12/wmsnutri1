<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files
require 'includes/auth.php'; // Ensure user is authenticated
require 'includes/db.php'; // Database connection
require 'vendor/autoload.php'; // Autoload for PHPSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file was uploaded
    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
        // Load spreadsheet file
        $file = $_FILES['excel_file']['tmp_name'];
        
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Process each row (skip header)
            foreach ($rows as $key => $row) {
                if ($key === 0) continue; // Skip the header row

                // Fetching data from the row
                $product_sku = $row[0];
                $product_name = $row[1];
                $quantity = $row[2];
                $amazon_asin = $row[3];
                $barcode = $row[4];

                // Validate data
                if (empty($product_sku) || empty($product_name) || !is_numeric($quantity)) {
                    continue; // Skip invalid rows
                }

                // Prepare and execute the insert statement
                $stmt = $conn->prepare('INSERT INTO products (product_sku, product_name, quantity, amazon_asin, barcode) 
                                        VALUES (:product_sku, :product_name, :quantity, :amazon_asin, :barcode)');
                $stmt->bindParam(':product_sku', $product_sku);
                $stmt->bindParam(':product_name', $product_name);
                $stmt->bindParam(':quantity', $quantity);
                $stmt->bindParam(':amazon_asin', $amazon_asin);
                $stmt->bindParam(':barcode', $barcode);

                // Execute the statement
                if (!$stmt->execute()) {
                    // Log the error for debugging (optional)
                    error_log("Failed to insert: " . implode(", ", $stmt->errorInfo()));
                }
            }

            // Redirect to dashboard after successful upload
            header('Location: views/dashboard.php');
            exit;
        } catch (Exception $e) {
            // Handle exception and display error message
            echo "Error loading file: " . $e->getMessage();
        }
    } else {
        echo "Error uploading file. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel File</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Upload Excel File</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="excel_file">Select Excel File:</label>
                <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xls,.xlsx" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</body>
</html>
