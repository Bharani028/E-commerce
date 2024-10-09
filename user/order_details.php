<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];

    // Store these in session to pass to the next page (place_order.php)
    $_SESSION['mobile'] = $mobile;
    $_SESSION['address'] = $address;

    // Redirect to place_order.php
    header('Location: place_order.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Order Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php 
        $is_logged_in = isset($_SESSION['user_id']);
        include '../includes/header.php'; 
    ?>
<div class="container mt-5">
    <h2>Enter Your Details</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="mobile">Mobile Number:</label>
            <input type="text" id="mobile" name="mobile" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <textarea id="address" name="address" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Continue to Place Order</button>
    </form>
</div>
</body>
</html>
