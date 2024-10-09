<?php
session_start();
include '../includes/db.php';

// Ensure only admin can access
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../login.php');
    exit();
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
}

header('Location: index.php');
exit();
?>
