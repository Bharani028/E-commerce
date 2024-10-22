<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    <style>
        body {
            background-image: url('../images/loginbg.webp'); /* Add your background image */
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 20px;
        }
        .btn-primary {
            border-radius: 20px;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
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
            <button type="submit" class="btn btn-primary btn-block">Login</button>
            <a href="forgot_password.php" class="d-block text-center mt-2">Forgot password?</a>
        </form>

        <!-- Option to switch to register -->
        <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
