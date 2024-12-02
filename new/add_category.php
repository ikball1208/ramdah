<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];

    $conn = new mysqli('localhost', 'root', '', 'ikbal');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query untuk menambahkan kategori baru
    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $category_name);
    
    if ($stmt->execute()) {
        echo "Category added successfully!";
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
    <title>Add Category</title>
</head>
<body>
    <div class="container">
        <h1>Add New Category</h1>
        <form method="POST">
            <label for="category_name">Category Name:</label><br>
            <input type="text" name="category_name" id="category_name" required><br><br>
            <button type="submit">Add Category</button>
        </form>
    </div>
</body>
</html>
