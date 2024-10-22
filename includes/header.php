<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopsy-like Header</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Header Styles */
        .custom-header {
            background-color: #50C878; /* Shopsy's Blue */
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 10px 15px;
        }

        .custom-header .navbar-brand {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        .custom-header .search-bar {
            flex-grow: 1;
        }

        .custom-header .search-bar input {
            border-radius: 0;
            width: 100%;
        }

        .custom-header .search-icon {
            background-color: white;
            border: none;
            color: #2874f0;
            padding: 0 10px;
        }

        /* Cart and User Icons */
        .custom-header .navbar-icons a {
            color: white;
            margin-left: 15px;
            font-size: 1.5rem; /* Increase font size */
            padding: 10px; /* Add padding for clickable area */
        }

        /* Mobile Specific Styles */
        @media (max-width: 767px) {
            .custom-header .search-bar {
                display: none; /* Hide main search bar in header for mobile */
            }

            /* Show the search bar below the header */
            .mobile-search-bar {
                display: block;
                background-color: white;
                padding: 10px;
            }

            .mobile-search-bar input {
                width: 100%;
                border-radius: 4px;
            }

            .mobile-search-bar .search-icon {
                background-color: #2874f0;
                color: white;
            }
        }

        /* Laptop/Tablet Specific Styles */
        @media (min-width: 768px) {
            .mobile-search-bar {
                display: none; /* Hide mobile search bar on larger screens */
            }
        }
    </style>

</head>
<body>

<!-- Header -->
<header class="custom-header">
    <div class="container">
        <div class="row align-items-center">
            <!-- Logo -->
            <div class="col-6 col-md-2">
                <a class="navbar-brand" href="index.php">Grocery</a>
            </div>

            <!-- Search Bar for larger screens -->
            <div class="col-md-6 search-bar">
                <form class="d-flex" action="search.php" method="GET">
                    <input type="text" class="form-control" name="query" placeholder="Search for products...">
                    <button class="search-icon" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Cart and Profile/Login Icons -->
            <div class="col-6 col-md-4 text-right">
                <!-- Cart and Profile/Login icons -->
                <div class="navbar-icons d-inline-block">
                    <a href="cart.php"><i class="fas fa-shopping-cart"></i></a> <!-- Cart Icon -->

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Show Profile icon if user is logged in -->
                        <a href="profile.php"><i class="fas fa-user"></i></a>
                    <?php else: ?>
                        <!-- Show Login icon if user is not logged in -->
                        <a href="login.php"><i class="fas fa-sign-in-alt"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Search Bar (visible below header on mobile) -->
<div class="mobile-search-bar">
    <div class="container">
        <form class="d-flex" action="search.php" method="GET">
            <input type="text" class="form-control" name="query" placeholder="Search for products...">
            <button class="search-icon" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

</body>
</html>
