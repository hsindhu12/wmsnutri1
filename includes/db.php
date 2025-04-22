<?php
// db.php
$host = 'localhost';
$dbname = 'u793820936_inventory';
$username = 'u793820936_inventory_user';  // change if needed
$password = '#c]&=yrj;2Y';      // change if needed

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
