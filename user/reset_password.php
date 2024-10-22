<!-- reset_password.php -->
<?php
require '../includes/db.php'; // include database connection

// Check if the token exists and is valid
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $query = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
    $query->bind_param('s', $token);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Token is valid, allow user to reset the password
        ?>
        <form action="update_password.php" method="POST">
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" required>
            <button type="submit">Reset Password</button>
        </form>
        <?php
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No token provided.";
}
?>
