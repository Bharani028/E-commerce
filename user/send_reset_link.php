<?php
// send_reset_link.php
require '../includes/db.php'; // include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $query = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $query->bind_param('s', $email);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50)); // generate a 50-byte random token

        // Set the token expiry (e.g., 1 hour)
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Save the token and expiry in the database
        $query = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
        $query->bind_param('sss', $token, $expiry, $email);
        $query->execute();

        // Create a reset link
        $resetLink = "http://localhost/E-commerce/user/reset_password.php?token=$token";

        // Send email with reset link
        $subject = "Password Reset";
        $message = "Click this link to reset your password: $resetLink";
        $headers = 'From: bharanisrinivasan1@gmail.com';
        mail($email, $subject, $message, $headers);

        echo "A password reset link has been sent to your email.";
    } else {
        echo "Email address not found.";
    }
}
?>
