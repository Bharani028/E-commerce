<?php 
session_start();
include '../includes/db.php';

// Get search query from URL parameters
$query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : '';

// Base SQL query to search products by name or category name
$sql = "
    SELECT products.*, categories.name AS category_name 
    FROM products 
    JOIN categories ON products.category_id = categories.id 
    WHERE (products.name LIKE '%$query%' OR categories.name LIKE '%$query%')
";

// Modify SQL based on sorting option
if ($sort_order == 'price_asc') {
    $sql .= " ORDER BY products.price ASC";
} elseif ($sort_order == 'price_desc') {
    $sql .= " ORDER BY products.price DESC";
}

$result = $conn->query($sql);
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - My E-Commerce Site</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%; /* Ensures that all cards have the same height */
        }

        .card-img-top {
            width: 100%;
            height: 55%; /* Fixed height for the images */
            object-fit: cover; /* Keeps the aspect ratio of images and prevents stretching */
        }

        .card-body {
            flex-grow: 1; /* Fills the remaining height of the card */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    <?php 
        $is_logged_in = isset($_SESSION['user_id']);
        include '../includes/header.php'; 
    ?>

    <div class="container mt-5">
        <h1>Search Results for "<?php echo htmlspecialchars($query); ?>"</h1>

        <div class="mb-3">
            <form action="search.php" method="GET" class="form-inline">
                <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">

                <!-- Sort by Price -->
                <label for="sort" class="mr-2">Sort by:</label>
                <select name="sort" id="sort" class="form-control mr-2" onchange="this.form.submit()">
                    <option value="">Select</option>
                    <option value="price_asc" <?php if ($sort_order == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php if ($sort_order == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
                </select>
                <noscript><input type="submit" value="Sort" class="btn btn-primary"></noscript>
            </form>
        </div>

        <?php if (!empty($products)): ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text">₹<?php echo htmlspecialchars($product['price']); ?></p>
                                <p class="card-text"><small class="text-muted">Category: <?php echo htmlspecialchars($product['category_name']); ?></small></p>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
