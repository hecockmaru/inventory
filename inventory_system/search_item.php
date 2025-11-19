<?php
include 'db.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? 'All';
$stock = $_GET['stock'] ?? 'All';

$search_like = "%{$search}%";

$sql = "SELECT id, name, category, price, stock, created_at FROM items WHERE name LIKE ?";
$params = [];
$types = 's';
$params[] = $search_like;

if ($category !== 'All') {
    $sql .= " AND category = ?";
    $types .= 's';
    $params[] = $category;
}
if ($stock !== 'All') {
    $sql .= " AND stock = ?";
    $types .= 's';
    $params[] = $stock;
}

$stmt = $conn->prepare($sql);
if ($types === '') {
    // should not happen, but fallback
    $stmt->bind_param('s', $search_like);
} else {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $id = (int)$row['id'];
        $name = htmlspecialchars($row['name']);
        $category = htmlspecialchars($row['category']);
        $price = number_format($row['price'], 2);
        $stock = htmlspecialchars($row['stock']);
        $created = htmlspecialchars($row['created_at']);

        $badge = "<span class='badge all-stock'>All Stock</span>";
        if ($stock === "In Stock") $badge = "<span class='badge in-stock'>In Stock</span>";
        if ($stock === "Out of Stock") $badge = "<span class='badge out-stock'>Out of Stock</span>";

        echo "<tr>
                <td>{$name}</td>
                <td>{$category}</td>
                <td>{$price}</td>
                <td>{$badge}</td>
                <td>{$created}</td>
                <td>
                  <a class='btn-edit' href='edit_item.php?id={$id}'>Edit</a>
                  <a class='btn-delete' href='delete_item.php?id={$id}' onclick=\"return confirm('Delete this item?')\">Delete</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6' style='text-align:center;color:#888;'>No items found.</td></tr>";
}
$stmt->close();
?>
