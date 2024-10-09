<?php
session_start();

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Remove the product from the session cart
    foreach ($_SESSION['cart'] as $key => $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }

    // Reindex the cart array to avoid gaps
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

header("Location: cart.php");
exit;
