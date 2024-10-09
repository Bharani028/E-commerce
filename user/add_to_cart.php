<?php
session_start();
include '../includes/db.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve product ID and quantity from the form
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
    $custom_quantity = isset($_POST['custom_quantity']) ? intval($_POST['custom_quantity']) : 0;

    // Determine the quantity
    if ($quantity === 'custom') {
        $quantity = $custom_quantity;
    } else {
        $quantity = intval($quantity);
    }

    if ($quantity > 0) {
        // Fetch product details
        $sql = "SELECT name, price, image FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if ($product) {
            $product_name = $product['name'];
            $product_price = $product['price'];
            $product_image = $product['image']; // Fetch the image
            $total_price = $product_price * $quantity;

            // Initialize cart if not already set
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Check if product already exists in the cart
            $found = false;
            foreach ($_SESSION['cart'] as &$cart_item) {
                if ($cart_item['product_id'] == $product_id) {
                    // If the product exists, update the quantity and total price
                    $cart_item['quantity'] += $quantity; // Update quantity
                    $cart_item['total_price'] += $total_price; // Update total price
                    $found = true;
                    break;
                }
            }

            // If the product is not in the cart, add it as a new entry
            if (!$found) {
                $_SESSION['cart'][] = [
                    'product_id' => $product_id,
                    'name' => $product_name,
                    'quantity' => $quantity,
                    'price' => $product_price,
                    'total_price' => $total_price,
                    'image' => $product_image // Store the image
                ];
            }

            // Redirect to the cart page
            header("Location: cart.php");
            exit();
        } else {
            echo "Product not found.";
        }

        $stmt->close();
    } else {
        echo "Quantity must be greater than 0.";
    }
}

$conn->close();
?>
