<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php'); // Redirect if not admin
    exit();
}

// Fetch all orders from the database
$sql = "SELECT o.id, o.total_price, o.mobile_number, o.address, o.status, o.expected_delivery_date, o.created_at, 
               u.username, p.name AS product_name, p.image, oi.quantity
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN users u ON o.user_id = u.id
        JOIN products p ON oi.product_id = p.id";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include 'includes/header.html'; ?>
    <div class="container mt-5">
        <h2>Orders</h2>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User Name</th>
                    <th>Product Name</th>
                    <th>Product Image</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Mobile Number</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Ordered Date</th> <!-- Add Ordered Date Column -->
                    <th>Delivery Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" style="width: 50px; height: 50px;">
                            </td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td>₹<?php echo number_format($row['total_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['mobile_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td>
                                <form action="update_order_status.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="Pending" <?php if($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Delivered" <?php if($row['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                    </select>
                                </form>
                            </td>
                            <td><?php echo date('d-m-Y', strtotime($row['created_at'])); ?></td> <!-- Display Ordered Date -->
                            <td>
                                <!-- Change delivery date form -->
                                <form action="update_delivery_date.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <input type="date" name="delivery_date" class="form-control" value="<?php echo $row['expected_delivery_date']; ?>" onchange="this.form.submit()">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" class="text-center">No orders found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>

