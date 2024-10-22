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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .body-bg {
            background-color: #f8f9fa;
        }
        .form-container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .form-title {
            margin-bottom: 20px;
            font-weight: 600;
            color: #343a40;
        }
        .custom-input {
            border-radius: 5px;
        }
        .btn-custom {
            background-color: #28a745;
            border: none;
        }
        .btn-custom:hover {
            background-color: #218838;
        }
        .mobile-icon {
            position: absolute;
            margin-left: -30px;
            margin-top: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body class="body-bg">

<?php 
    include '../includes/header.php'; 
?>

<div class="container form-container mt-5">
    <h2 class="form-title">Enter Your Details</h2>
    <form action="" method="POST" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="mobile">Mobile Number:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-mobile-alt mobile-icon"></i></span>
                </div>
                <input type="text" id="mobile" name="mobile" class="form-control custom-input" required pattern="\d{10}" title="Please enter a valid 10-digit mobile number.">
            </div>
            <small class="form-text text-muted">Please enter a 10-digit mobile number.</small>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <textarea id="address" name="address" class="form-control custom-input" required rows="4"></textarea>
        </div>
        <button type="submit" class="btn btn-custom btn-block">Continue to Place Order</button>
    </form>
</div>

<script>
function validateForm() {
    const mobileInput = document.getElementById('mobile');
    const mobileValue = mobileInput.value.trim();
    
    // Check if the mobile number is exactly 10 digits
    if (!/^\d{10}$/.test(mobileValue)) {
        alert("Please enter a valid 10-digit mobile number.");
        mobileInput.focus();
        return false; // Prevent form submission
    }

    return true; // Allow form submission
}
</script>
</body>
</html>
