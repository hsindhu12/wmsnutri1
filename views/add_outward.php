<?php
require '../includes/auth.php';
include '../includes/header.php';
require '../includes/db.php';


// Fetch all products initially for manual selection if needed
$stmt = $conn->query('SELECT * FROM products');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Check if enough stock is available
    $stmtCheck = $conn->prepare('SELECT quantity FROM products WHERE id = ?');
    $stmtCheck->execute([$product_id]);
    $product = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($product['quantity'] < $quantity) {
        echo "Error: Not enough stock available!";
    } else {
        // Insert into inventory_transactions
        $sql = "INSERT INTO inventory_transactions (product_id, transaction_type, quantity) VALUES (?, 'outward', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$product_id, $quantity]);

        // Update the quantity in products table
        $sqlUpdate = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->execute([$quantity, $product_id]);

        echo "Stock dispatched successfully!";
    }
}
?>

<div class="container mt-4">
    <h2>Outward Stock</h2>

    <form method="POST" action="">
        <div class="form-group">
            <label for="barcode">Scan Barcode:</label>
            <input type="text" id="barcode" name="barcode" class="form-control" placeholder="Scan product barcode" autofocus>
        </div>

        <div class="form-group">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" class="form-control" readonly>
        </div>

        <div class="form-group">
            <label for="product_sku">Product SKU:</label>
            <input type="text" id="product_sku" name="product_sku" class="form-control" readonly>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" class="form-control" required>
        </div>

        <input type="hidden" id="product_id" name="product_id">

        <button type="submit" class="btn btn-primary">Dispatch Stock</button>
    </form>
</div>

<script>
// Handle barcode scanning and fetch product details via AJAX
document.getElementById('barcode').addEventListener('input', function () {
    let barcode = this.value;

    if (barcode.length > 3) { // Assuming barcode length > 5 triggers the search
        fetch('../fetch_product_by_barcode.php?barcode=' + barcode)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate the fields with fetched product details
                    document.getElementById('product_name').value = data.product_name;
                    document.getElementById('product_sku').value = data.product_sku;
                    document.getElementById('product_id').value = data.product_id;
                } else {
                    //alert("Product not found!");
                }
            });
    }
});
</script>

<?php require '../includes/footer.php'; ?>
