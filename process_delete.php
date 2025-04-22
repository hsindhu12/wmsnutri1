<?php
require 'includes/auth.php';
require 'includes/db.php';

if ($userRole !== 'admin') {
    header('Location: dashboard.php');
    exit; // Block access for non-admins
}

$id = $_GET['id'];
$stmt = $conn->prepare('DELETE FROM products WHERE id = :id');
$stmt->bindParam(':id', $id);
$stmt->execute();

header('Location: views/dashboard.php');
exit;
?>
