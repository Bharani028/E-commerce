<?php 
session_start();
include '../includes/db.php';

// Get and trim the search query from URL parameters
$query = isset($_GET['query']) ? trim($conn->real_escape_string($_GET['query'])) : '';
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : '';

// Split query into words for improved search
$words = explode(' ', $query);

// Base SQL query to search products by name or category name
$sql = "
    SELECT products.*, categories.name AS category_name 
    FROM products 
    JOIN categories ON products.category_id = categories.id 
    WHERE 1=1
";

// Add conditions for each word
if (!empty($query)) {
    foreach ($words as $word) {
        $word = trim($word); // Trim each word just in case
        if (!empty($word)) {
            $sql .= " AND (products.name LIKE '%$word%' OR categories.name LIKE '%$word%')";
        }
    }
}

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
        .body {
            background-color: #f4f4f4;
            color: #333;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            margin-bottom: 3px !important;
        }
        .card-img-top {
            width: 100%;
            height: 55%;
            object-fit: cover;
        }
        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .product-col {
            flex-grow: 1;
            flex-basis: calc(25% - 15px);
            max-width: calc(25% - 15px);
            min-height: 100%;
            margin-bottom: 15px;
        }

        @media (max-width: 767px) {
            .fullbody {
                height: 110%;
            }
            .product-col {
                flex-basis: calc(50% - 10px);
                max-width: calc(50% - 10px);
                padding-left: 5px !important;
                padding-right: 5px !important;
                margin-bottom: 50px !important;
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .product-col {
                flex-basis: calc(33.33% - 10px);
                max-width: calc(33.33% - 10px);
                padding-left: 10px !important;
                padding-right: 10px !important;
            }
        }

        @media (min-width: 992px) {
            .product-col {
                padding-left: 15px;
                padding-right: 15px;
            }
        }
    </style>
</head>
<body class="body">

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
                    <div class="col-md-3 col-sm-6 col-6 product-col">
                        <div class="card fullbody mb-4">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text">₹<?php echo htmlspecialchars($product['price']); ?></p>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-success">View Details</a>
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
