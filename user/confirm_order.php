<?php
session_start();

// Check if the payment method is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_method'])) {
    $payment_method = $_POST['payment_method'];

    // Here, you can handle the order processing, like saving to the database, sending confirmation emails, etc.
    
    // Clear the cart after successful order processing
    unset($_SESSION['cart']);

    echo "<div class='container mt-5'><h2>Thank you for your order!</h2>";
    echo "<p>Your order will be processed with the payment method: $payment_method.</p></div>";
} else {
    echo "<div class='container mt-5'><h2>Error: Payment method not selected.</h2></div>";
}
?>
