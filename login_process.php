<?php
session_start();

$conn = new PDO("mysql:host=localhost;dbname=db_bakeryrecord_system", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the login table for user credentials
    $stmt = $conn->prepare("SELECT * FROM tb_login WHERE username = :username AND password = :password");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['username'];
        header('Location: sidebars/dashboard.php');
        exit;
    } else {
        header('Location: login.php?error=1');
        exit;
    }
}