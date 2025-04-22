<?php

if ($userRole !== 'admin') {
    header('Location: dashboard.php');
    exit; // Block access for non-admins
}
require '../includes/auth.php';
require '../includes/db.php';

$id = $_GET['id'];
$stmt = $conn->prepare('SELECT * FROM products WHERE id = :id');
$stmt->bindParam(':id', $id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<h1>Edit Product</h1>
<form action="../process_edit.php" method="POST">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    <div class="mb-3">
        <label for="product_sku" class="form-label">Product SKU</label>
        <input type="text" class="form-control" name="product_sku" value="<?= $product['product_sku'] ?>" required>
    </div>
    <div class="mb-3">
        <label for="product_name" class="form-label">Product Name</label>
        <input type="text" class="form-control" name="product_name" value="<?= $product['product_name'] ?>" required>
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" class="form-control" name="quantity" value="<?= $product['quantity'] ?>" required>
    </div>
    <div class="mb-3">
        <label for="amazon_asin" class="form-label">Amazon ASIN</label>
        <input type="text" class="form-control" name="amazon_asin" value="<?= $product['amazon_asin'] ?>">
    </div>
    <div class="mb-3">
        <label for="barcode" class="form-label">Barcode</label>
        <input type="text" class="form-control" name="barcode" value="<?= $product['barcode'] ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update Product</button>
</form>

<?php include '../includes/footer.php'; ?>
