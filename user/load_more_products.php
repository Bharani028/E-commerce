<?php
session_start();
include '../includes/db.php';

// Pagination settings
$rowsPerPage = 10; // Number of rows to load (10 rows * 3 products)
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $rowsPerPage * 3; // Offset based on rows

// Fetch more products from the database
$sql = "SELECT * FROM products LIMIT $offset, " . ($rowsPerPage * 3);
$result = $conn->query($sql);
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$conn->close();

// Return products as JSON
echo json_encode($products);
?>
