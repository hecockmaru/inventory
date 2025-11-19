<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    // notify via sessionStorage flash and redirect
    echo "<script>
      sessionStorage.setItem('flash', JSON.stringify({type:'success', title:'Deleted', message:'Item deleted successfully.'}));
      window.location.href='main.php';
    </script>";
    exit();
} else {
    header("Location: main.php");
    exit();
}
?>
