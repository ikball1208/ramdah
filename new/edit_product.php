<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$product_id = $_GET['id'];

// Ambil data produk yang ada
$conn = new mysqli('localhost', 'root', '', 'ikbal');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_product = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql_product);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();

$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Menangani upload gambar produk baru (opsional)
    $image = $_FILES['image']['name'];
    if ($image) {
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_error = $_FILES['image']['error'];

        if ($image_error === 0) {
            $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($image_ext, $allowed_ext)) {
                $image_name_new = uniqid('', true) . "." . $image_ext;
                $image_path = "uploads/products/" . $image_name_new;

                move_uploaded_file($image_tmp, $image_path);
            }
        }
    } else {
        $image_path = $product['image']; // jika tidak ada gambar baru, tetap gunakan gambar lama
    }

    // Update data produk
    $sql_update = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('ssdisi', $name, $description, $price, $category_id, $image_path, $product_id);
    $stmt_update->execute();
    $stmt_update->close();
    header('Location: admin_dashboard.php');
    exit();
}
?>

<form method="post" enctype="multipart/form-data">
    <label>Product Name:</label><br>
    <input type="text" name="name" value="<?php echo $product['name']; ?>" required><br>

    <label>Description:</label><br>
    <textarea name="description" required><?php echo $product['description']; ?></textarea><br>

    <label>Price:</label><br>
    <input type="number" name="price" value="<?php echo $product['price']; ?>" required><br>

    <label>Category:</label><br>
    <select name="category_id" required>
        <?php while ($category = $result_categories->fetch_assoc()): ?>
            <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                <?php echo $category['name']; ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <label>Product Image (Leave blank to keep current):</label><br>
    <input type="file" name="image" accept="image/*"><br>

    <button type="submit">Update Product</button>
</form>

<?php
$conn->close();
?>
