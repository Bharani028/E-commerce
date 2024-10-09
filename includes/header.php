<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">E-Commerce</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="orders.php">Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cart.php">Cart</a>
            </li>
        </ul>
        <form class="form-inline ml-auto" action="search.php" method="GET">
            <input class="form-control mr-sm-2" type="search" name="query" placeholder="Search products..." aria-label="Search" required>
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
        <div class="ml-auto">
            <?php if ($is_logged_in): ?>
                <a href="profile.php" class="btn btn-outline-primary">Profile</a>
                <a href="logout.php" class="btn btn-outline-danger ml-2">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-primary">Login</a>
                <a href="register.php" class="btn btn-outline-success ml-2">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
