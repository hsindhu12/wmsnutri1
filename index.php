<?php
// index.php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Redirect to dashboard if logged in
    header('Location: views/dashboard.php');
    exit;
} else {
    // Redirect to login page if not logged in
    header('Location: views/login.php');
    exit;
}
?>
