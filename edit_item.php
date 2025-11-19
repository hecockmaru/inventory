<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: main.php");
    exit();
}

// fetch
$stmt = $conn->prepare("SELECT id, name, category, price, stock FROM items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$item = $res->fetch_assoc();
$stmt->close();

if (!$item) {
    header("Location: main.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price_raw = trim($_POST['price'] ?? '');
    $stock = trim($_POST['stock'] ?? '');

    $errors = [];
    if ($name === '') $errors[] = "Name is required.";
    if ($category === '') $errors[] = "Category is required.";
    if ($price_raw === '' || !is_numeric(str_replace(',', '', $price_raw))) $errors[] = "Price is required and must be numeric.";
    if ($stock === '') $errors[] = "Stock is required.";

    if (!empty($errors)) {
        $err = implode(' ', $errors);
    } else {
        $price = floatval(str_replace(',', '', $price_raw));
        $u = $conn->prepare("UPDATE items SET name = ?, category = ?, price = ?, stock = ? WHERE id = ?");
        $u->bind_param("ssdsi", $name, $category, $price, $stock, $id);
        if ($u->execute()) {
            echo "<script>
              sessionStorage.setItem('flash', JSON.stringify({type:'success', title:'Updated', message:'Item updated successfully.'}));
              window.location.href='main.php';
            </script>";
            exit();
        } else {
            $err = "Database error: " . $u->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Item | Inventory System</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="form-page">
    <div class="form-card">
        <h2>Edit Item</h2>
        <?php if (!empty($err)): ?>
            <p class="error"><?= htmlspecialchars($err) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>
            <select name="category" required>
                <option value="<?= htmlspecialchars($item['category']) ?>"><?= htmlspecialchars($item['category']) ?></option>
                <option>Appliances</option>
                <option>Electronics</option>
                <option>Fitness</option>
                <option>Footwear</option>
                <option>Furniture</option>
                <option>Home & Garden</option>
                <option>Stationery</option>
            </select>
            <input type="text" name="price" value="<?= htmlspecialchars($item['price']) ?>" required inputmode="decimal">
            <select name="stock" required>
                <option value="<?= htmlspecialchars($item['stock']) ?>"><?= htmlspecialchars($item['stock']) ?></option>
                <option>All Stock</option>
                <option>In Stock</option>
                <option>Out of Stock</option>
            </select>
            <button type="submit">Update Item</button>
            <a href="main.php" class="btn-back">Cancel</a>
        </form>
    </div>
</body>
</html>
