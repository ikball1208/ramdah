<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Successful</title>
</head>
<body>
    <h1>Checkout Berhasil!</h1>
    <p>Terima kasih telah melakukan pemesanan. Pesanan Anda telah diproses.</p>
    <a href="user_dashboard.php">Kembali ke Dashboard</a>
</body>
</html>
