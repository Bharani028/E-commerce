<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $new_quantity = intval($_POST['quantity']);

    // Ensure the quantity is greater than 0
    if ($new_quantity > 0) {
        // Update the quantity in the cart session
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['product_id'] == $product_id) {
                $cart_item['quantity'] = $new_quantity;
                $cart_item['total_price'] = $cart_item['price'] * $new_quantity; // Update total price
                break;
            }
        }
    }
}

header("Location: cart.php");
exit;
