<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add New Item</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
  <h1>Add New Item 🛒</h1>
  <form method="POST" action="save_item.php" class="add-form">
    <input type="text" name="name" placeholder="Item Name" required>
    <select name="category" required>
        <option disabled selected>Select Category</option>
        <option>Appliances</option>
        <option>Electronics</option>
        <option>Fitness</option>
        <option>Footwear</option>
        <option>Furniture</option>
        <option>Home & Garden</option>
        <option>Stationery</option>
    </select>
    <input type="text" name="price" placeholder="Price (₱)" inputmode="decimal" pattern="^\d+(\.\d{1,2})?$" required>
    <select name="stock" required>
        <option disabled selected>Select Stock</option>
        <option>All Stock</option>
        <option>In Stock</option>
        <option>Out of Stock</option>
    </select>
    <button type="submit">Add Item</button>
  </form>
  <a href="main.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
