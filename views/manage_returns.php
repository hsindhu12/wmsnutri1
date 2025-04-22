<?php
require '../includes/auth.php';
include '../includes/header.php';
require '../includes/db.php';

// Fetch all products for selection
$stmt = $conn->query('SELECT * FROM products');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $reason = $_POST['reason'];
    $inventory_type = $_POST['inventory_type']; // Fetch the inventory type

    // Insert into returns table
    $sql = "INSERT INTO returns (product_id, quantity, reason, inventory_type) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$product_id, $quantity, $reason, $inventory_type]);

    // Update the quantity in products table
    $sqlUpdate = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->execute([$quantity, $product_id]);

    echo "Return processed successfully!";
}
?>

<div class="container mt-4">
    <h2>Manage Returns</h2>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="barcode">Scan Barcode:</label>
            <input type="text" id="barcode" name="barcode" class="form-control" placeholder="Scan product barcode" autofocus>
        </div>

        <div class="form-group">
            <label for="product_id">Select Product:</label>
            <select id="product_id" name="product_id" class="form-control" required>
                <option value="">Select a product</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['id']; ?>">
                        <?php echo $product['product_name']; ?> (SKU: <?php echo $product['product_sku']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity Returned:</label>
            <input type="number" name="quantity" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="reason">Reason for Return:</label>
            <textarea name="reason" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="inventory_type">Inventory Type:</label>
            <select name="inventory_type" class="form-control" required>
                <option value="Good">Good</option>
                <option value="Bad">Bad</option>
                <option value="Damaged">Damaged</option>
            </select>
        </div>

        <button type="submit" class="btn btn-danger">Process Return</button>
    </form>
</div>

<script>
// Handle barcode scanning and fetch product details via AJAX
document.getElementById('barcode').addEventListener('input', function () {
    let barcode = this.value;

    if (barcode.length > 5) { // Assuming barcode length > 5 triggers the search
        fetch('../fetch_product_by_barcode.php?barcode=' + barcode)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate the select dropdown with the product
                    document.getElementById('product_id').value = data.product_id;
                    document.getElementById('product_id').options.length = 0; // Clear existing options
                    let option = new Option(data.product_name + ' (SKU: ' + data.product_sku + ')', data.product_id);
                    document.getElementById('product_id').add(option); // Add new option
                    document.getElementById('product_id').value = data.product_id; // Set selected value
                } else {
                    //alert("Product not found!");
                }
            });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
