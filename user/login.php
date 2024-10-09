<?php
session_start();
include '../includes/db.php';

// Initialize error message variable
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // Check if the email exists in the admin table
    $admin_sql = "SELECT id, email, password FROM admin WHERE email = ?";
    $admin_stmt = $conn->prepare($admin_sql);
    $admin_stmt->bind_param('s', $email);
    $admin_stmt->execute();
    $admin_result = $admin_stmt->get_result();

    if ($admin_result->num_rows == 1) {
        // Admin login
        $admin_row = $admin_result->fetch_assoc();
        if (password_verify($password, $admin_row['password'])) {
            // Set session variables for the admin
            $_SESSION['user_id'] = $admin_row['id'];
            $_SESSION['is_admin'] = true;

            header("Location: ../admin/index.php"); // Redirect to admin page
            exit();
        } else {
            $error_message = "Incorrect admin password!";
        }
    } else {
        // Check if the email exists in the users table for regular users
        $user_sql = "SELECT id, username, password FROM users WHERE email = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param('s', $email);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();

        if ($user_result->num_rows == 1) {
            $user_row = $user_result->fetch_assoc();
            // Verify user password
            if (password_verify($password, $user_row['password'])) {
                // Set session variables for regular users
                $_SESSION['user_id'] = $user_row['id'];
                $_SESSION['username'] = $user_row['username'];
                $_SESSION['is_admin'] = false;

                header("Location: index.php"); // Redirect to homepage
                exit();
            } else {
                $error_message = "Incorrect password!";
            }
        } else {
            $error_message = "No user found with that email!";
        }
        $user_stmt->close();
    }
    $admin_stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Login</h2>

        <!-- Display error message if set -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
