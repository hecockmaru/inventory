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
<title>Inventory Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="navbar">
  <h1>📦 Inventory Management</h1>
  <div class="nav-right">
    <a href="add_item.php" class="btn-add">+ Add Item</a>
    <a href="logout.php" class="btn-logout">Logout</a>
  </div>
</div>

<div class="filter-bar">
  <input type="text" id="searchInput" placeholder="🔍 Search items...">
  <select id="categoryFilter">
      <option value="All">All</option>
      <option>Appliances</option>
      <option>Electronics</option>
      <option>Fitness</option>
      <option>Footwear</option>
      <option>Furniture</option>
      <option>Home & Garden</option>
      <option>Stationery</option>
  </select>
  <select id="stockFilter">
      <option value="All">All</option>
      <option>All Stock</option>
      <option>In Stock</option>
      <option>Out of Stock</option>
  </select>
</div>

<div class="table-container">
  <table class="styled-table" id="itemsTable">
      <thead>
          <tr>
              <th>Name</th>
              <th>Category</th>
              <th>Price (₱)</th>
              <th>Stock</th>
              <th>Created</th>
              <th>Actions</th>
          </tr>
      </thead>
      <tbody id="tableBody">
          <!-- AJAX loads rows here -->
      </tbody>
  </table>
</div>

<script>
function fetchItems() {
    const search = document.getElementById('searchInput').value;
    const category = document.getElementById('categoryFilter').value;
    const stock = document.getElementById('stockFilter').value;
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `search_item.php?search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&stock=${encodeURIComponent(stock)}`, true);
    xhr.onload = function() {
        if (this.status === 200) {
            document.getElementById('tableBody').innerHTML = this.responseText;
        } else {
            document.getElementById('tableBody').innerHTML = '<tr><td colspan="6">Error loading items.</td></tr>';
        }
    }
    xhr.send();
}

document.getElementById('searchInput').addEventListener('input', fetchItems);
document.getElementById('categoryFilter').addEventListener('change', fetchItems);
document.getElementById('stockFilter').addEventListener('change', fetchItems);
window.addEventListener('load', fetchItems);

// show a SweetAlert if there's a flash message stored in sessionStorage
if (sessionStorage.getItem('flash')) {
    const f = JSON.parse(sessionStorage.getItem('flash'));
    sessionStorage.removeItem('flash');
    Swal.fire({icon: f.type || 'success', title: f.title || '', text: f.message || '', timer: 1500, showConfirmButton: false});
}
</script>
</body>
</html>
