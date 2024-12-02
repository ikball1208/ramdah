<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$product_id = $_GET['id'];

$conn = new mysqli('localhost', 'root', '', 'ikbal');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data produk untuk menghapus gambar (jika ada)
$sql_product = "SELECT image FROM products WHERE id = ?";
$stmt = $conn->prepare($sql_product);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();

if ($product) {
    // Menghapus gambar produk jika ada
    if (file_exists($product['image'])) {
        unlink($product['image']);
    }

    // Hapus produk dari database
    $sql_delete = "DELETE FROM products WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param('i', $product_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    echo "Product deleted successfully!";
    header('Location: admin_dashboard.php');
    exit();
} else {
    echo "Product not found!";
}

$conn->close();
?>
