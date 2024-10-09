<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo "No items in the cart.";
    exit();
}

// Check if mobile number and address are set
if (!isset($_SESSION['mobile']) || !isset($_SESSION['address'])) {
    header('Location: order_details.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$mobile = $_SESSION['mobile'];
$address = $_SESSION['address'];
$total_amount = 0;

// Fetch user name from the users table
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

// Calculate the total amount from the cart
foreach ($_SESSION['cart'] as $cart_item) {
    $total_amount += $cart_item['total_price'];
}

// Calculate expected delivery date (7 days from today)
$expected_delivery_date = date('Y-m-d', strtotime('+4 days'));

// Insert order into orders table
$stmt = $conn->prepare("INSERT INTO orders (user_id, user_name, mobile_number, address, total_price, status, expected_delivery_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
$status = 'Pending'; // Default status
$stmt->bind_param("isssdss", $user_id, $username, $mobile, $address, $total_amount, $status, $expected_delivery_date);
$stmt->execute();
$order_id_db = $stmt->insert_id; // Get the inserted order's ID
$stmt->close();

// Prepare order email message
$order_message = "Order Details:\n";
$order_message .= "User: $username\nMobile: $mobile\nAddress: $address\n\n";

// Insert each product from the cart into order_items table and append to email message
foreach ($_SESSION['cart'] as $cart_item) {
    $product_id = $cart_item['product_id'];
    $quantity = $cart_item['quantity'];
    $subtotal = $cart_item['total_price']; // Assuming total_price is per product

    // Fetch product name from the products table
    $stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($product_name);
    $stmt->fetch();
    $stmt->close();

    // Insert order items into the database, including the product name
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisis", $order_id_db, $product_id, $product_name, $quantity, $subtotal);
    $stmt->execute();
    $stmt->close();

    // Append product details to the email message
    $order_message .= "Product: $product_name - Quantity: $quantity - Subtotal: ₹" . number_format($subtotal, 2) . "\n";
}

$order_message .= "Total Amount: ₹" . number_format($total_amount, 2);
$order_message .= "\nExpected Delivery Date: " . $expected_delivery_date;

// Create the email before clearing the cart session
// $to = 'bharanisrinivasan1@gmail.com'; // Admin's email
// $subject = "New Order: Order ID $order_id_db";
// mail($to, $subject, $order_message);

// Clear the cart session after sending the email
unset($_SESSION['cart']);

// Redirect the user to the index page to avoid reloading issue
header('Location: index.php?order=success');
exit();
?>
