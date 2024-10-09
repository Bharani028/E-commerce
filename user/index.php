<?php
session_start();
include '../includes/db.php';

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Pagination and infinite scroll settings
$rowsPerPage = 10; // Number of rows to display per request (10 rows * 4 products = 40 products)
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $rowsPerPage * 4; // Offset based on rows

// Fetch products from the database for the initial load
$sql = "SELECT * FROM products LIMIT $offset, " . ($rowsPerPage * 4);
$result = $conn->query($sql);
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
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
                // Remove the order=success parameter from the URL
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
    <title>My E-Commerce Site</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
        include '../includes/header.php';
    ?>

    <div class="container mt-5">
        <h1>Welcome to My E-Commerce Site</h1>
        <p>Explore our wide range of products!</p>

        <!-- Display products from the database -->
        <div id="product-list" class="row">
            <?php
            foreach (array_chunk($products, 4) as $row): // Display products in rows of 4
                echo '<div class="row mb-4">'; // Create a new row for every 4 products
                foreach ($row as $product): ?>
                    <div class="col-md-3 product-col">
                        <div class="card mb-4">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text">₹<?php echo htmlspecialchars($product['price']); ?></p>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach;
                echo '</div>'; // Close row
            endforeach; ?>
        </div>

        <!-- Pagination Links -->
        <nav id="pagination" class="pagination justify-content-center">
            <ul class="pagination">
                <li class="page-item" id="prev-page" style="display: none;">
                    <a class="page-link" href="#">Previous</a>
                </li>
                <li class="page-item" id="next-page">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentPage = <?php echo $current_page; ?>;
        let loading = false;
        const rowsPerPage = 10; // 10 rows

        function loadMoreProducts() {
            if (loading) return; // Prevent multiple requests
            loading = true;

            $.ajax({
                url: 'load_more_products.php',
                method: 'GET',
                data: { page: currentPage + 1 },
                success: function(data) {
                    const products = JSON.parse(data);
                    if (products.length > 0) {
                        products.forEach(product => {
                            $('#product-list').append(`
                                <div class="col-md-3 product-col">
                                    <div class="card mb-4">
                                        <img src="${product.image}" class="card-img-top" alt="${product.name}">
                                        <div class="card-body">
                                            <h5 class="card-title">${product.name}</h5>
                                            <p class="card-text">₹${product.price}</p>
                                            <a href="product.php?id=${product.id}" class="btn btn-primary">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                        currentPage++;

                        // If we have loaded 10 rows (40 products), hide the next button
                        if (currentPage > rowsPerPage) {
                            $('#next-page').hide();
                        } else {
                            $('#prev-page').show();
                        }
                    } else {
                        $('#next-page').hide(); // Hide next page button if no more products
                    }
                    loading = false;
                },
                error: function() {
                    loading = false;
                }
            });
        }

        $(window).on('scroll', function() {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                loadMoreProducts();
            }
        });

        // Show pagination if initial load has products
        if ($('#product-list .col-md-3').length > 0) {
            $('#pagination').show();
        }
    </script>
</body>
</html>
