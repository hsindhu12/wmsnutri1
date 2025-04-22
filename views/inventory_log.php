<?php
// inventory_log.php
require '../includes/auth.php';
include '../includes/header.php';
require '../includes/db.php';

// Fetch inventory transactions
$sql = "
    SELECT it.*, p.product_name, p.product_sku 
    FROM inventory_transactions it 
    JOIN products p ON it.product_id = p.id
    ORDER BY it.transaction_date DESC
";
$stmt = $conn->query($sql);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>Inventory Transaction Log</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Product SKU</th>
                <th>Product Name</th>
                <th>Transaction Type</th>
                <th>Quantity</th>
                <th>Transaction Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction['product_sku']; ?></td>
                    <td><?= $transaction['product_name']; ?></td>
                    <td><?= ucfirst($transaction['transaction_type']); ?></td>
                    <td><?= $transaction['quantity']; ?></td>
                    <td><?= $transaction['transaction_date']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require '../includes/footer.php'; ?>
