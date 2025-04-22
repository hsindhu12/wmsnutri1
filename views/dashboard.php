<?php
require '../includes/auth.php';
require '../includes/db.php';

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

include '../includes/header.php';
?>

<h1>Product Inventory</h1>

<a href="../process_upload.php" class="btn btn-primary">Upload Excel</a>
<a href="../download_inventory.php" class="btn btn-success">Download Inventory as Excel</a>
<a href="../views/manage_returns.php" class="btn btn-secondary">Manage Returns</a>
<a href="../views/returns_report.php" class="btn btn-info">Returns Report</a>

<table class="table table-bordered mt-4">
    <thead>
        <tr>
            <th>ID</th>
            <th>Product SKU</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Total Usable Quantity (Good Inventory)</th>
            <th>Total Bad Inventory</th>
            <th>Total Damaged Inventory</th>
            <th>Amazon ASIN</th>
            <th>Barcode</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?= $product['id'] ?></td>
            <td><?= $product['product_sku'] ?></td>
            <td><?= $product['product_name'] ?></td>
            <td><?= $product['quantity'] ?></td>
            <td><?= $product['quantity'] + $totalGoodInventory ?></td> <!-- Total Usable Quantity -->
            <td><?= $totalBadInventory ?></td> <!-- Total Bad Inventory -->
            <td><?= $totalDamagedInventory ?></td> <!-- Total Damaged Inventory -->
            <td><?= $product['amazon_asin'] ?></td>
            <td><?= $product['barcode'] ?></td>
            <td>
                <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-warning">Edit</a>
                <a href="../process_delete.php?id=<?= $product['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
