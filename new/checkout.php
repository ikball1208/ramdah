<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if (empty($_SESSION['cart'])) {
    header('Location: user_dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = $_POST['shipping_address'];
    $payment_method = $_POST['payment_method'];

    if (empty($shipping_address) || empty($payment_method)) {
        echo "Shipping address and payment method cannot be empty.";
        exit();
    }

    $conn = new mysqli('localhost', 'root', '', 'ikbal');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_id = $_SESSION['username'];
    $total_price = 0;

    foreach ($_SESSION['cart'] as $product) {
        $total_price += $product['price'] * $product['quantity'];
    }

    $sql = "INSERT INTO orders (user_id, total_price, shipping_address, payment_method) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sdss', $user_id, $total_price, $shipping_address, $payment_method);
    $stmt->execute();

    $order_id = $stmt->insert_id;

    foreach ($_SESSION['cart'] as $product_id => $product) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iiid', $order_id, $product_id, $product['quantity'], $product['price']);
        $stmt->execute();
    }

    unset($_SESSION['cart']);
    $stmt->close();
    $conn->close();

    // Redirect to payment page based on payment method
    if ($payment_method == "Transfer Bank") {
        header('Location: payment.php?order_id=' . $order_id . '&method=transfer');
    } else if ($payment_method == "Cash On Delivery") {
        header('Location: payment.php?order_id=' . $order_id . '&method=cod');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>
<body>
    <h1>Checkout</h1>
    <form action="checkout.php" method="POST">
        <textarea name="shipping_address" rows="4" cols="50" placeholder="Enter your shipping address"></textarea><br><br>

        <label for="payment_method">Choose Payment Method:</label><br>
        <input type="radio" name="payment_method" value="Transfer Bank" required> Transfer Bank<br>
        <input type="radio" name="payment_method" value="Cash On Delivery" required> Cash On Delivery<br><br>

        <button type="submit">Complete Checkout</button>
    </form>
</body>
</html>
