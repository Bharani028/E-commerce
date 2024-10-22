<?php
session_start();
include '../includes/db.php';

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Pagination and infinite scroll settings
$rowsPerPage = 10; // 10 rows, each with 4 products = 40 products per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $rowsPerPage * 4;

// Fetch products from the database for the current page
$sql = "SELECT * FROM products LIMIT $offset, " . ($rowsPerPage * 4);
$result = $conn->query($sql);
$products = [];

// Track if more products exist for the next page
$hasMoreProducts = false;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    // Check if more products exist for the next page
    if ($result->num_rows >= $rowsPerPage * 4) {
        $hasMoreProducts = true;
    }
}

if (isset($_GET['order']) && $_GET['order'] == 'success') {
    echo '<script>
        window.addEventListener("load", function() {
            Swal.fire({
                title: "Success!",
                text: "Your order has been placed successfully.",
                icon: "success",
                confirmButtonText: "OK"
            }).then(function() {
                const url = new URL(window.location);
                url.searchParams.delete("order");
                window.history.replaceState(null, null, url);
            });
        });
    </script>';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocery</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

</head>
<body class="body">
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Welcome to Grocery!</h1>
        <p>Explore our wide range of products!</p>

        <div id="product-list" class="row">
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

        <nav id="pagination" class="pagination justify-content-center">
            <ul class="pagination">
                <li class="page-item" id="prev-page" <?php if ($current_page == 1) echo 'style="display: none;"'; ?>>
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Previous</a>
                </li>
                <li class="page-item" id="next-page" <?php if (!$hasMoreProducts) echo 'style="display: none;"'; ?>>
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentPage = <?php echo $current_page; ?>;
        let hasMoreProducts = <?php echo $hasMoreProducts ? 'true' : 'false'; ?>;
        let loading = false;

        function loadMoreProducts() {
            if (loading || !hasMoreProducts) return;
            loading = true;

            $.ajax({
                url: 'load_more_products.php', // The server-side script that returns the HTML
                method: 'GET',
                data: { page: currentPage + 1 }, // Request the next page of products
                success: function(data) {
                    if (data.trim().length > 0) {
                        // Append the returned HTML to the #product-list container
                        $('#product-list').append(data);
                        currentPage++;

                        // If the response contains fewer than 4 products (1 row), stop loading more
                        if ($(data).find('.product-col').length < 4) {
                            hasMoreProducts = false;
                            $('#next-page').hide();
                        }
                    } else {
                        hasMoreProducts = false;
                        $('#next-page').hide();
                    }

                    loading = false;
                },
                error: function() {
                    loading = false; // Reset loading status on error
                }
            });
        }

        // Implement infinite scroll
        $(window).scroll(function() {
            if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
                loadMoreProducts();
            }
        });
    </script>
</body>
</html>
