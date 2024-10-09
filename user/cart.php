<?php
session_start();
include '../includes/db.php'; // Include your database connection

$total_amount = 0;

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Fetch the expected delivery date if an order is placed
$expected_delivery_date = null;

if (isset($_SESSION['order_id'])) {
    $order_id = $_SESSION['order_id'];
    
    // Fetch the expected delivery date for the current order
    $stmt = $conn->prepare("SELECT expected_delivery_date FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->bind_result($expected_delivery_date);
    $stmt->fetch();
    $stmt->close();
}

// Include the header file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css"> <!-- Add your own CSS for additional styling -->
    <style>
        /* Add custom styles here */
        .cart-table th, .cart-table td {
            text-align: center;
        }
        .cart-image {
            width: 100px;
            height: auto;
        }
    </style>
</head>
<body>

<!-- Include the header here -->
<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <?php
    // Check if the cart is empty
    if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
        echo "<h2>Your cart is empty!</h2>";
    } else {
        ?>
        <h1>Your Cart</h1>

        <!-- Display the expected delivery date if available -->
        <?php if ($expected_delivery_date): ?>
            <p><strong>Expected Delivery Date:</strong> <?php echo htmlspecialchars($expected_delivery_date); ?></p>
        <?php endif; ?>

        <table class="table cart-table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Loop through the cart and display the items
            foreach ($_SESSION['cart'] as $index => $cart_item) {
                $product_name = htmlspecialchars($cart_item['name']);
                $product_price = $cart_item['price'];
                $product_quantity = $cart_item['quantity'];
                $subtotal = $cart_item['total_price'];
                $product_image = htmlspecialchars($cart_item['image']); // Ensure you include an image field

                // Calculate the total amount for all products
                $total_amount += $subtotal;
                ?>

                <tr>
                    <td>
                        <?php if (isset($cart_item['image']) && !empty($cart_item['image'])): ?>
                            <img src="<?php echo htmlspecialchars($cart_item['image']); ?>" alt="<?php echo $product_name; ?>" class="cart-image">
                        <?php else: ?>
                            <img src="path/to/default-image.jpg" alt="Default Image" class="cart-image"> <!-- Optional default image -->
                        <?php endif; ?>
                        <div><?php echo $product_name; ?></div>
                    </td>
                    <td>₹<?php echo number_format($product_price, 2); ?></td>
                    <td>
                        <!-- Quantity Update Form -->
                        <form action="update_cart.php" method="POST">
                            <input type="number" name="quantity" value="<?php echo $product_quantity; ?>" min="1" max="999">
                            <input type="hidden" name="product_id" value="<?php echo $cart_item['product_id']; ?>">
                            <button type="submit" class="btn btn-sm btn-warning">Update</button>
                        </form>
                    </td>
                    <td>₹<?php echo number_format($subtotal, 2); ?></td>
                    <td>
                        <!-- Remove from Cart Link -->
                        <a href="remove_from_cart.php?product_id=<?php echo $cart_item['product_id']; ?>" class="btn btn-danger btn-sm">Remove</a>
                    </td>
                </tr>

                <?php
            } // Closing the foreach loop
            ?>

            <tr>
                <td colspan="3" align="right"><strong>Total Amount:</strong></td>
                <td colspan="2">₹<?php echo number_format($total_amount, 2); ?></td>
            </tr>

            </tbody>
        </table>

        <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        <a href="order_details.php" class="btn btn-success">Place Order</a> <!-- Redirects to order details page -->
        <?php
    }
    ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
