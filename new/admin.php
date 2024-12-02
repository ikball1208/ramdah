<?php
// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'ikbal');

// Mengecek apakah koneksi berhasil
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Password yang akan di-hash
$password = 'admin123';

// Hash password menggunakan bcrypt
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Query untuk menambahkan admin
$sql = "INSERT INTO users (username, password, role) VALUES ('admin', '$hashed_password', 'admin')";

// Mengeksekusi query dan memeriksa hasilnya
if ($conn->query($sql) === TRUE) {
    echo "Admin created successfully!";
} else {
    echo "Error: " . $conn->error;
}

// Menutup koneksi
$conn->close();
?>
