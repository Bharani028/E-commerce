<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // If status is delivered, set the current date as delivery date
    if ($status === 'Delivered') {
        $delivery_date = date('Y-m-d'); // Set today's date
        $stmt = $conn->prepare("UPDATE orders SET status = ?, expected_delivery_date = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $delivery_date, $order_id);
    } else {
        // Only update the status if not delivered
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
    }

    if ($stmt->execute()) {
        header('Location: orders.php');
        exit();
    } else {
        echo "Error updating order status: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
