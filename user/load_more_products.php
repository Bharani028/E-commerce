<?php
session_start();
include '../includes/db.php';

// Pagination settings
$rowsPerPage = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $rowsPerPage * 4; // Adjust for 4 products per row

// Fetch more products from the database
$sql = "SELECT * FROM products LIMIT $offset, " . ($rowsPerPage * 4);
$result = $conn->query($sql);
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$conn->close();

// Return HTML structure instead of JSON
foreach (array_chunk($products, 4) as $row) {
    echo '<div class="row mb-4">'; 
    $productCount = count($row);
    foreach ($row as $product) { ?>
        <div class="col-md-3 col-sm-6 col-6 product-col">
            <div class="card fullbody mb-4">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="card-body">
                    <h5 class="card-title "><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p class="card-text">₹<?php echo htmlspecialchars($product['price']); ?></p>
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-success">View Details</a>
                </div>
            </div>
        </div>
    <?php }
    
    // Add empty cards if fewer than 4 products
    for ($i = $productCount; $i < 4; $i++) {
        echo '<div class="col-md-3 col-sm-6 col-6 product-col"></div>';
    }
    echo '</div>';
}
?>
