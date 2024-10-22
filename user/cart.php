<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    <link rel="stylesheet" href="css/cart.css"> <!-- Add your own CSS for additional styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>

<!-- Include the header here -->
<?php include '../includes/header.php'; ?>

<div class="container mt-5 cart-container">
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

        <div class="cart-table">
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

                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo $product_name; ?>" class="img-fluid">
                    <div class="item-details">
                        <h5><?php echo $product_name; ?></h5>
                        <p>Price: ₹<span class="product-price"><?php echo number_format($product_price, 2); ?></span></p>
                        <p>Subtotal: ₹<span class="item-subtotal"><?php echo number_format($subtotal, 2); ?></span></p>
                        <p class="remove-link" onclick="window.location.href='remove_from_cart.php?product_id=<?php echo $cart_item['product_id']; ?>'">Remove</p>
                    </div>
                    <div class="item-actions">
                        <div class="quantity-control">
                            <form action="update_cart.php" method="POST" class="d-inline">
                                <input type="number" name="quantity" value="<?php echo $product_quantity; ?>" min="1" max="999" class="quantity-input" data-price="<?php echo $product_price; ?>" data-index="<?php echo $index; ?>">
                                <input type="hidden" name="product_id" value="<?php echo $cart_item['product_id']; ?>">
                            </form>
                        </div>
                    </div>
                </div>

                <?php
            } // Closing the foreach loop
            ?>

<div class="cart-summary">
    <h5>Cart Summary</h5>
    <p><strong>Total:</strong> ₹<span class="total-amount"><?php echo number_format($total_amount, 2); ?></span></p>
    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
    <button class="btn btn-success" id="placeOrderButton">Place Order</button> <!-- Use a button with an ID -->
</div>

        </div>
        <?php
    }
    ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
     document.getElementById('placeOrderButton').addEventListener('click', function() {
        let validOrder = true;

        // Check if all quantities are valid (greater than 0)
        document.querySelectorAll('.quantity-input').forEach(input => {
            const quantity = parseInt(input.value);
            if (isNaN(quantity) || quantity < 1) {
                validOrder = false; // If any quantity is less than 1, set validOrder to false
            }
        });

        if (!validOrder) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Quantity',
                text: 'Please ensure that all quantities are at least 1 before placing your order.',
                confirmButtonText: 'Okay'
            });
        } else {
            // If all quantities are valid, redirect to order details page
            window.location.href = 'order_details.php';
        }
    });
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('input', function() {
            const price = parseFloat(this.getAttribute('data-price'));
            const quantity = parseInt(this.value) || 0; // Get the quantity or set to 0 if invalid
            const subtotalElement = this.closest('.cart-item').querySelector('.item-subtotal');
            const totalAmountElement = document.querySelector('.total-amount');

            // Calculate new subtotal
            const newSubtotal = price * quantity;
            subtotalElement.textContent = newSubtotal.toFixed(2);

            // Update total amount
            let totalAmount = 0;
            document.querySelectorAll('.item-subtotal').forEach(subtotal => {
                totalAmount += parseFloat(subtotal.textContent);
            });
            totalAmountElement.textContent = totalAmount.toFixed(2);
        });
    });
</script>
</body>
</html>
