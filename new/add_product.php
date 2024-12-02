<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $product_name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    
    // Cek apakah file gambar diupload
    if (isset($_FILES['image'])) {
        $image = $_FILES['image'];
        
        // Tentukan folder tujuan untuk menyimpan gambar
        $upload_dir = 'uploads/products/';
        
        // Buat nama file yang unik untuk menghindari bentrok nama
        $image_name = uniqid() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
        $target_path = $upload_dir . $image_name;

        // Cek apakah folder upload ada dan jika tidak buat folder
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Membuat folder dengan hak akses penuh
        }

        // Cek jika upload gambar berhasil
        if (move_uploaded_file($image['tmp_name'], $target_path)) {
            $image_path = $target_path;
        } else {
            echo "Failed to upload image.";
            exit();
        }
    }

    // Koneksi ke database
    $conn = new mysqli('localhost', 'root', '', 'ikbal');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query untuk menambah produk
    $sql = "INSERT INTO products (name, description, price, category_id, image) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssdis', $product_name, $description, $price, $category_id, $image_path);

    if ($stmt->execute()) {
        echo "Product added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
</head>
<body>
    <h1>Add New Product</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name:</label><br>
        <input type="text" name="name" id="name" required><br><br>

        <label for="description">Description:</label><br>
        <textarea name="description" id="description" required></textarea><br><br>

        <label for="price">Price:</label><br>
        <input type="number" name="price" id="price" required><br><br>

        <label for="category_id">Category:</label><br>
        <select name="category_id" id="category_id" required>
            <!-- Kode untuk menampilkan kategori dari database -->
            <?php
            $conn = new mysqli('localhost', 'root', '', 'ikbal');
            $result = $conn->query("SELECT * FROM categories");
            while ($category = $result->fetch_assoc()) {
                echo "<option value='" . $category['id'] . "'>" . $category['name'] . "</option>";
            }
            $conn->close();
            ?>
        </select><br><br>

        <label for="image">Product Image:</label><br>
        <input type="file" name="image" id="image" required><br><br>

        <button type="submit">Add Product</button>
    </form>
</body>
</html>
