<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['receipt'])) {
    $order_id = $_POST['order_id'];
    $receipt = $_FILES['receipt'];

    // Pastikan file diterima
    if ($receipt['error'] != 0) {
        echo "Failed to upload file.";
        exit();
    }

    // Tentukan lokasi penyimpanan file
    $upload_dir = 'uploads/receipts/';
    $receipt_filename = uniqid() . '.' . pathinfo($receipt['name'], PATHINFO_EXTENSION);
    $upload_path = $upload_dir . $receipt_filename;

    // Cek apakah direktori upload ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Pindahkan file ke direktori tujuan
    if (move_uploaded_file($receipt['tmp_name'], $upload_path)) {
        // Simpan path file di database
        $conn = new mysqli('localhost', 'root', '', 'ikbal');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Update status pesanan dan simpan nama file bukti transfer
        $sql = "UPDATE orders SET payment_receipt = ?, payment_status = 'pending' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $receipt_filename, $order_id);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        echo "Receipt uploaded successfully. Please wait for confirmation.";
    } else {
        echo "Failed to upload the receipt.";
    }
} else {
    echo "Invalid request.";
}
?>
