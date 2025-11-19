<?php
session_start();
include 'db.php';

$error = '';
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $hash = sha1($password); // matches database.sql default
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $hash);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['user'] = $username;
        $_SESSION['flash_success'] = "Login successful!";
        header("Location: main.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login | Inventory System</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="login-page">
    <div class="login-card">
        <h2>Inventory Login</h2>
        <form method="POST" novalidate>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </form>
    </div>

<?php
// show SweetAlert if redirected with flash
if (!empty($_SESSION['flash_success'])) {
    $msg = htmlspecialchars($_SESSION['flash_success']);
    echo "<script>
    Swal.fire({icon: 'success', title: 'Success', text: '$msg', timer: 1500, showConfirmButton: false});
    </script>";
    unset($_SESSION['flash_success']);
}
?>
</body>
</html>
