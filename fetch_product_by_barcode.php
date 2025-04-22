<?php
require 'includes/db.php';

if (isset($_GET['barcode'])) {
    $barcode = $_GET['barcode'];

    // Fetch the product by barcode
    $stmt = $conn->prepare("SELECT id, product_name, product_sku FROM products WHERE barcode = ?");
    $stmt->execute([$barcode]);

    if ($stmt->rowCount() > 0) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            'success' => true,
            'product_id' => $product['id'],
            'product_name' => $product['product_name'],
            'product_sku' => $product['product_sku']
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
