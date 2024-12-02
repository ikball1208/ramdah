<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'user'; // Default role for new users

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $conn = new mysqli('localhost', 'root', '', 'ikbal');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $username, $hashed_password, $role);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header('Location: login.php');
    exit();
}
?>

<form method="post">
    <label>Username</label><br>
    <input type="text" name="username" required><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br>

    <button type="submit">Register</button>
</form>
