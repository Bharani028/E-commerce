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
</head>
<body>

    <?php 
    $is_logged_in = isset($_SESSION['user_id']);
    include '../includes/header.php'; 
    ?>

    <div class="container mt-5">
        <h2>Your Orders</h2>
        
        <?php if (empty($orders)): ?>
            <p>You have no orders.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($orders as $order): ?>
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>Order #<?php echo $order['id']; ?> (Placed on: <?php echo date('F j, Y', strtotime($order['created_at'])); ?>)</strong>
                                <span class="float-right">Total: ₹<?php echo htmlspecialchars($order['total_price']); ?></span>
                            </div>
                            <div class="card-body">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        </div>
                                        <div class="col-md-10">
                                            <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                            <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                            <p>Price: ₹<?php echo htmlspecialchars($item['price']); ?></p>

                                            <?php
                                            // Fetch the delivery date from the order
                                            $delivery_date = new DateTime($order['expected_delivery_date']);
                                            $current_date = new DateTime();

                                            // Display status based on delivery date
                                            if ($current_date >= $delivery_date && $order['status'] === 'Delivered') {
                                                echo "<span class='badge badge-success'>Delivered</span>";
                                                echo "<p>Delivered on: " . $delivery_date->format('F j, Y') . "</p>";
                                            } else {
                                                echo "<p>Estimated Delivery Date: " . $delivery_date->format('F j, Y') . "</p>";
                                            }
                                            ?>
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
