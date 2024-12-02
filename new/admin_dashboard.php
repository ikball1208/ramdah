<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'ikbal');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil daftar pengguna
$user_sql = "SELECT username, email, role FROM users";
$user_result = $conn->query($user_sql);

// Ambil daftar pesanan
$order_sql = "SELECT orders.id, orders.user_id, orders.total_price, orders.shipping_status, orders.payment_status, orders.rating, users.username 
              FROM orders 
              INNER JOIN users ON orders.user_id = users.username";
$order_result = $conn->query($order_sql);

// Ambil daftar produk
$product_sql = "SELECT id, name, description, price, rating FROM products";
$product_result = $conn->query($product_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    
    <!-- Daftar Pengguna -->
    <h2>Daftar Pengguna</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $user_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo ucfirst($user['role']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <hr>

    <!-- Daftar Pesanan -->
    <h2>Daftar Pesanan</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Total Price</th>
                <th>Shipping Status</th>
                <th>Payment Status</th>
                <th>Rating</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $order_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['username']; ?></td>
                    <td>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></td>
                    <td><?php echo ucfirst($order['shipping_status']); ?></td>
                    <td><?php echo ucfirst($order['payment_status']); ?></td>
                    <td>
                        <?php if ($order['rating'] != NULL): ?>
                            <?php echo $order['rating']; ?> / 5
                        <?php else: ?>
                            Not Rated
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <hr>

    <!-- Daftar Produk -->
    <h2>Daftar Produk</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Rating</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = $product_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['description']; ?></td>
                    <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                    <td>
                        <?php if ($product['rating'] !== NULL): ?>
                            <?php echo number_format($product['rating'], 1); ?> / 5
                        <?php else: ?>
                            Not Rated
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <hr>

    <a href="logout.php">Logout</a>
</body>
</html>

<?php $conn->close(); ?>
