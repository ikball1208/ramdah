<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'ikbal');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get products
$product_sql = "SELECT * FROM products";
$product_result = $conn->query($product_sql);

// Get orders
$order_sql = "SELECT * FROM orders WHERE user_id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param('s', $_SESSION['username']);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>
<body>
    <h1>User Dashboard</h1>
    <h2>Your Products</h2>
    <ul>
        <?php while ($product = $product_result->fetch_assoc()): ?>
            <li>
                <?php echo $product['name']; ?> - Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit">Add to Cart</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>

    <h2>Your Orders</h2>
    <ul>
        <?php while ($order = $order_result->fetch_assoc()): ?>
            <li>
                Order #<?php echo $order['id']; ?> - Status: <?php echo $order['shipping_status']; ?>
                <?php if ($order['rating'] == NULL && $order['shipping_status'] == 'shipped'): ?>
                    <form action="rate_product.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <input type="number" name="rating" min="1" max="5" required>
                        <button type="submit">Rate Product</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>
    
    <a href="logout.php">Logout</a>
</body>
</html>

<?php $conn->close(); ?>
