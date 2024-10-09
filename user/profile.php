<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
include '../includes/db.php';
include '../includes/header.php'; // Include the header file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

$stmt->close();

// Check if username and email keys exist in the $user array
$username = isset($user['username']) ? $user['username'] : '';
$email = isset($user['email']) ? $user['email'] : '';

// Handle form submission for editing username and email
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update username without password verification
    if (isset($_POST['username']) && $_POST['username'] !== $username) {
        $new_username = trim($_POST['username']);
        $sql = "UPDATE users SET username = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $new_username, $user_id);
        $stmt->execute();
        $stmt->close();
        $success = true;
    }

    // Update email with password verification
    if (isset($_POST['email']) && $_POST['email'] !== $email) {
        $password = trim($_POST['password']);
        if (empty($password)) {
            $errors[] = "Password is required to update email.";
        } else {
            // Verify the password entered by the user
            if (password_verify($password, $user['password'])) {
                $new_email = trim($_POST['email']);
                $sql = "UPDATE users SET email = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('si', $new_email, $user_id);
                $stmt->execute();
                $stmt->close();
                $success = true;
            } else {
                $errors[] = "Incorrect password.";
            }
        }
    }

    // Return response as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'errors' => $errors
    ]);
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - My E-Commerce Site</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Your Profile</h1>

        <div id="message"></div> <!-- Container for messages -->

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Profile Information</h5>
                <p class="card-text"><strong>Name:</strong> <span id="username-display"><?php echo htmlspecialchars($username); ?></span>
                    <button id="edit-username" class="btn btn-sm btn-primary float-right">Edit</button>
                </p>
                <p class="card-text"><strong>Email:</strong> <span id="email-display"><?php echo htmlspecialchars($email); ?></span>
                    <button id="edit-email" class="btn btn-sm btn-primary float-right">Edit</button>
                </p>
                <div id="edit-username-form" style="display: none;">
                    <input type="text" id="username-input" class="form-control" value="<?php echo htmlspecialchars($username); ?>">
                    <button id="save-username" class="btn btn-sm btn-success mt-2">Save</button>
                </div>
                <div id="edit-email-form" style="display: none;">
                    <input type="email" id="email-input" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="password" id="password-input" class="form-control mt-2" placeholder="Enter password to confirm">
                    <button id="save-email" class="btn btn-sm btn-success mt-2">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-username').click(function() {
                $('#edit-username-form').toggle();
                $(this).text($(this).text() === 'Edit' ? 'Cancel' : 'Edit');
            });

            $('#save-username').click(function() {
                const newUsername = $('#username-input').val();
                $.post('profile.php', { username: newUsername }, function(response) {
                    location.reload(); // Reload the page to reflect changes
                });
            });

            $('#edit-email').click(function() {
                $('#edit-email-form').toggle();
                $(this).text($(this).text() === 'Edit' ? 'Cancel' : 'Edit');
            });

            $('#save-email').click(function() {
                const newEmail = $('#email-input').val();
                const password = $('#password-input').val();
                $.post('profile.php', { email: newEmail, password: password }, function(response) {
                    // Clear previous messages
                    $('#message').html('');
                    
                    // Display success or error messages
                    if (response.success) {
                        $('#message').html('<div class="alert alert-success">Profile updated successfully!</div>');
                        setTimeout(() => {
                            location.reload(); // Reload the page to reflect changes
                        }, 1500);
                    } else if (response.errors.length > 0) {
                        response.errors.forEach(function(error) {
                            $('#message').append('<div class="alert alert-danger">' + error + '</div>');
                        });
                    }
                }, 'json');
            });
        });
    </script>
</body>
</html>
