<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the logged-in user
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();

// Fetch order items and product details
$orders = [];
while ($order = $orders_result->fetch_assoc()) {
    $order_id = $order['id'];
    
    $order_items_sql = "SELECT oi.*, p.name, p.image FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = ?";
    $items_stmt = $conn->prepare($order_items_sql);
    $items_stmt->bind_param('i', $order_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    
    $order['items'] = $items_result->fetch_all(MYSQLI_ASSOC);

    // Check if the expected delivery date has passed
    $delivery_date = new DateTime($order['expected_delivery_date']);
    $current_date = new DateTime();

    // Update the status if the delivery date has passed
    if ($current_date >= $delivery_date && $order['status'] !== 'Delivered') {
        // Update the order status in the database
        $update_sql = "UPDATE orders SET status = 'Delivered' WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('i', $order_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Update the status in the current order array
        $order['status'] = 'Delivered';
        $order['delivered_date'] = $delivery_date->format('F j, Y'); // Store the delivered date for display
    }

    // Format the ordered date
    $order['ordered_date'] = (new DateTime($order['created_at']))->format('F j, Y');

    $orders[] = $order;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-status {
            font-weight: bold;
        }
        .order-status-delivered {
            color: #28a745; /* Green */
        }
        .order-status-pending {
            color: #ffc107; /* Yellow */
        }
        .item-image {
            width: 100%;
            max-width: 100px; /* Set max width for images */
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <?php 
    $is_logged_in = isset($_SESSION['user_id']);
    include '../includes/header.php'; 
    ?>

    <div class="container mt-5">
        <h2 class="mb-4">Your Orders</h2>
        
        <?php if (empty($orders)): ?>
            <div class="alert alert-info">You have no orders.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($orders as $order): ?>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header order-header">
                                <strong>Ordered on: <?php echo htmlspecialchars($order['ordered_date']); ?></strong>
                                <span class="float-right">Total: ₹<?php echo htmlspecialchars($order['total_price']); ?></span>
                            </div>
                            <div class="card-body">
                                <p class="order-status">
                                    <?php
                                    // Display status
                                    if ($order['status'] === 'Delivered') {
                                        echo "<span class='order-status-delivered'>Delivered</span>";
                                        if (isset($order['delivered_date'])) {
                                            echo "<p>Delivered on: " . htmlspecialchars($order['delivered_date']) . "</p>";
                                        }
                                    } else {
                                        echo "<span class='order-status-pending'>Pending</span>";
                                        echo "<p>Estimated Delivery Date: " . $delivery_date->format('F j, Y') . "</p>";
                                    }
                                    ?>
                                </p>
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" class="img-fluid item-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        </div>
                                        <div class="col-md-10">
                                            <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                            <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                            <p>Price: ₹<?php echo htmlspecialchars($item['price']); ?></p>
                                        </div>
                                    </div>
                                    <hr>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
