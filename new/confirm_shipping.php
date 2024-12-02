<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    $conn = new mysqli('localhost', 'root', '', 'ikbal');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "UPDATE orders SET shipping_status = 'shipped' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header('Location: admin_dashboard.php');
    exit();
}
?>
