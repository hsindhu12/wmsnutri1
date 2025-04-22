<?php
require '../includes/auth.php';
include '../includes/header.php';
require '../includes/db.php';


// Fetch all returns
$stmt = $conn->query("SELECT r.*, p.product_name, p.product_sku FROM returns r JOIN products p ON r.product_id = p.id");
$returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2>Returns Report</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product SKU</th>
                <th>Product Name</th>
                <th>Quantity Returned</th>
                <th>Reason</th>
                <th>Inventory Type</th> <!-- New column for Inventory Type -->
                <th>Return Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($returns as $return): ?>
                <tr>
                    <td><?php echo $return['product_sku']; ?></td>
                    <td><?php echo $return['product_name']; ?></td>
                    <td><?php echo $return['quantity']; ?></td>
                    <td><?php echo $return['reason']; ?></td>
                    <td><?php echo $return['inventory_type']; ?></td> <!-- Display Inventory Type -->
                    <td><?php echo $return['return_date']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
