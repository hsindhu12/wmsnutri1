<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // MD5 for now, can use bcrypt in production

    $stmt = $conn->prepare('SELECT * FROM users WHERE username = :username AND password = :password LIMIT 1');
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error'] = 'Invalid Username or Password';
        header('Location: views/login.php');
        exit;
    }
} else {
    header('Location: views/login.php');
    exit;
}
?>
