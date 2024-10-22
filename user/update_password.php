<?php
// update_password.php
require '../includes/db.php'; // include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // hash the password

    // Find the user by the token
    $query = $conn->prepare("SELECT * FROM users WHERE reset_token = ?");
    $query->bind_param('s', $token);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Update the user's password
        $query = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE reset_token = ?");
        $query->bind_param('ss', $new_password, $token);
        $query->execute();

        echo "Your password has been successfully reset.";
    } else {
        echo "Invalid token.";
    }
}
?>
