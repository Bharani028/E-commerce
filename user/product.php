<?php
session_start();
include '../includes/db.php';

// Get the product ID from the URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the product details from the database
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit();
}

// Fetch related products based on category
$related_sql = "SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4";
$related_stmt = $conn->prepare($related_sql);
$related_stmt->bind_param('ii', $product['category_id'], $product_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();

$stmt->close();
$related_stmt->close();
$conn->close();

// Calculate the delivery date (current date + 4 days)
$delivery_date = new DateTime();
$delivery_date->modify('+4 days');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - My E-Commerce Site</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <?php 
    $is_logged_in = isset($_SESSION['user_id']);
    include '../includes/header.php'; ?>

    <div class="container mt-5">
        <!-- Back button -->
        <button class="btn btn-secondary mb-3" onclick="history.back()">
            <i class="fas fa-arrow-left"></i> Back
        </button>

        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="col-md-6">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($product['price']); ?></p>
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                <p><strong>Estimated Delivery Date:</strong> <?php echo $delivery_date->format('F j, Y'); ?></p> <!-- Display the calculated delivery date -->

                <!-- Form for adding to cart -->
                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <select id="quantity" name="quantity" class="form-control" onchange="showCustomQuantity(this)">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="custom">10+</option>
                        </select>
                    </div>
                    <div id="custom-quantity" style="display: none;">
                        <label for="custom-quantity-input">Enter Quantity (1-999):</label>
                        <input type="number" id="custom-quantity-input" name="custom_quantity" min="1" max="999" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                </form>

            </div>
        </div>

        <!-- Optional: Display related products -->
        <div class="mt-5">
            <h2>Related Products</h2>
            <?php if ($related_result->num_rows > 0): ?>
                <div class="row">
                    <?php while ($related_product = $related_result->fetch_assoc()): ?>
                        <div class="col-md-3">
                            <div class="card mb-4">
                                <img src="<?php echo htmlspecialchars($related_product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($related_product['name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($related_product['name']); ?></h5>
                                    <p class="card-text">₹<?php echo htmlspecialchars($related_product['price']); ?></p>
                                    <a href="product.php?id=<?php echo $related_product['id']; ?>" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No related products found.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <!-- Bootstrap JS, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function showCustomQuantity(select) {
            const customQuantityDiv = document.getElementById('custom-quantity');
            if (select.value === 'custom') {
                customQuantityDiv.style.display = 'block';
            } else {
                customQuantityDiv.style.display = 'none';
            }
        }
    </script>
</body>
</html>
