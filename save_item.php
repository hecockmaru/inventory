<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: add_item.php");
    exit();
}

$name = trim($_POST['name'] ?? '');
$category = trim($_POST['category'] ?? '');
$price_raw = trim($_POST['price'] ?? '');
$stock = trim($_POST['stock'] ?? '');

$errors = [];
if ($name === '') $errors[] = "Name is required.";
if ($category === '') $errors[] = "Category is required.";
if ($price_raw === '') $errors[] = "Price is required.";
if (!is_numeric(str_replace(',', '', $price_raw))) $errors[] = "Price must be a number.";
if ($stock === '') $errors[] = "Stock is required.";

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' ', $errors);
    header("Location: add_item.php");
    exit();
}

$price = floatval(str_replace(',', '', $price_raw));

$stmt = $conn->prepare("INSERT INTO items (name, category, price, stock, created_at) VALUES (?, ?, ?, ?, NOW())");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ssds", $name, $category, $price, $stock);
if ($stmt->execute()) {
    // set a short-lived flash via sessionStorage for the front-end to show using SweetAlert
    echo "<script>
      sessionStorage.setItem('flash', JSON.stringify({type:'success', title:'Added', message:'Item added successfully.'}));
      window.location.href='main.php';
    </script>";
    exit();
} else {
    $_SESSION['flash_error'] = "Database error: " . $stmt->error;
    header("Location: add_item.php");
    exit();
}
