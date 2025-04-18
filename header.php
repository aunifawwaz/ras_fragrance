<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Ensure session starts at the very top
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ras Fragrance</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
    .cart-count {
    position: absolute;
    top: 0;
    right: 0;
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 0.2rem 0.5rem;
    font-size: 12px;
}

</style>
<body>
    <!-- Header Section -->
    <header>
        <div class="top-section">
            <div class="logo">
                <a href="home.php" title="Home">
                    <img src="images/logo.jpeg" alt="Logo Icon" title="Logo" class="logo">
                </a>
            </div>

            <div class="icons">
                <a href="search.php" title="Search">
                    <img src="images/search.png" alt="Search Icon" title="Search">
                </a>

                <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] == true): ?>
                    <!-- User logged-in, show profile and cart -->
                    <a href="profile.php" title="Profile">
                        <img src="images/profile.png" alt="Profile Icon" title="Profile">
                    </a>
                    
                    <a href="cart.php" title="Cart">
                        <img src="images/cart.png" alt="Cart Icon" title="Cart">
                        <?php if (isset($_SESSION['cart_count'])): ?>
                            <span class="cart-count"><?= $_SESSION['cart_count']; ?></span> <!-- Display cart count -->
                        <?php endif; ?>
                    </a>

                    <a href="logout.php" title="Logout">
                        <img src="images/logout.png" alt="Logout Icon" title="Logout">
                    </a>
                <?php else: ?>
                    <!-- User not logged in, show login link -->
                    <a href="login.php" title="Login">
                        <img src="images/profile.png" alt="Login Icon" title="Login">
                    </a>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <!-- Admin link -->
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a>
                    </li>
                <?php endif; ?>
            </div>
        </div>

        <h1>Ras Fragrance</h1>
        <h2>The Essence of Luxury</h2>

        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Ras Fragrance</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="quiz.php">Quiz</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarShop" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Shop
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarShop">
                            <a class="dropdown-item" href="products.php?category_id=1">Woody</a>
                            <a class="dropdown-item" href="products.php?category_id=2">Floral</a>
                            <a class="dropdown-item" href="products.php?category_id=3">Citrus</a>
                            <a class="dropdown-item" href="products.php?category_id=4">Fresh</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog.php">Blog</a>
                    </li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
