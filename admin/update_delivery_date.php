<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $delivery_date = $_POST['delivery_date'];

    // Update the expected delivery date in the database
    $stmt = $conn->prepare("UPDATE orders SET expected_delivery_date = ? WHERE id = ?");
    $stmt->bind_param("si", $delivery_date, $order_id);

    if ($stmt->execute()) {
        // Success: Redirect back to the orders page
        header('Location: orders.php');
    } else {
        echo "Error updating delivery date: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
