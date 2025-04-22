<?php
require 'includes/auth.php';
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $product_sku = $_POST['product_sku'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $amazon_asin = $_POST['amazon_asin'];
    $barcode = $_POST['barcode'];

    $stmt = $conn->prepare('UPDATE products SET 
                            product_sku = :product_sku, 
                            product_name = :product_name, 
                            quantity = :quantity, 
                            amazon_asin = :amazon_asin, 
                            barcode = :barcode 
                            WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':product_sku', $product_sku);
    $stmt->bindParam(':product_name', $product_name);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':amazon_asin', $amazon_asin);
    $stmt->bindParam(':barcode', $barcode);
    $stmt->execute();

    header('Location: views/dashboard.php');
    exit;
}
?>
