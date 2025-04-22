<?php
require '../includes/auth.php';
include '../includes/header.php';
require '../includes/db.php';
?>

<h1>Add New Product</h1>
<form action="../process_add.php" method="POST">
    <div class="mb-3">
        <label for="product_sku" class="form-label">Product SKU</label>
        <input type="text" class="form-control" name="product_sku" required>
    </div>
    <div class="mb-3">
        <label for="product_name" class="form-label">Product Name</label>
        <input type="text" class="form-control" name="product_name" required>
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" class="form-control" name="quantity" required>
    </div>
    <div class="mb-3">
        <label for="amazon_asin" class="form-label">Amazon ASIN</label>
        <input type="text" class="form-control" name="amazon_asin">
    </div>
    <div class="mb-3">
        <label for="barcode" class="form-label">Barcode</label>
        <input type="text" class="form-control" name="barcode">
    </div>
    <button type="submit" class="btn btn-primary">Add Product</button>
</form>

<?php include '../includes/footer.php'; ?>
