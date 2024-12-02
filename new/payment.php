<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['order_id']) || !isset($_GET['method'])) {
    header('Location: user_dashboard.php');
    exit();
}

$order_id = $_GET['order_id'];
$payment_method = $_GET['method'];

$conn = new mysqli('localhost', 'root', '', 'ikbal');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil detail pesanan
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$order) {
    die("Order not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
</head>
<body>
    <h1>Payment for Order #<?php echo $order_id; ?></h1>
    <p>Total Price: Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></p>
    <p>Shipping Address: <?php echo $order['shipping_address']; ?></p>
    
    <?php if ($payment_method == 'transfer'): ?>
        <h2>Payment Instructions for Transfer Bank</h2>
        <p>Please transfer the total amount to the following bank account:</p>
        <ul>
            <li>Bank Name: Bank Example</li>
            <li>Account Number: 123456789</li>
            <li>Account Name: Store Name</li>
        </ul>
        <p>After making the payment, please upload the payment receipt to confirm your payment.</p>
        <form action="upload_receipt.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="receipt" required><br><br>
            <button type="submit">Upload Receipt</button>
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        </form>
    <?php elseif ($payment_method == 'cod'): ?>
        <h2>Cash On Delivery</h2>
        <p>Your order will be delivered to your address, and you can pay in cash when the product arrives.</p>
        <p>Thank you for choosing Cash On Delivery.</p>
    <?php endif; ?>
</body>
</html>
