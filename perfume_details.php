<?php
// Include database connection
include 'db_connection.php';

// Get the perfume ID and category ID from the URL
$perfume_id = isset($_GET['perfume_id']) ? intval($_GET['perfume_id']) : null;
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;  // Capture category_id from the URL

// Redirect to shop page if no perfume ID is provided
if (!$perfume_id) {
    header("Location: shop.php");
    exit();
}

// Fetch perfume details
$perfume_query = "SELECT * FROM perfume WHERE perfume_id = ?";
$stmt = $conn->prepare($perfume_query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $perfume_id);
$stmt->execute();
$perfume_result = $stmt->get_result();
$perfume = $perfume_result->fetch_assoc();

// Redirect if the perfume doesn't exist
if (!$perfume) {
    header("Location: shop.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($perfume['perfume_name']); ?> Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4e7e7;
            color: #5a2a2a;
            margin: 0;
            padding: 0;
        }

        footer {
            text-align: center;
            padding: 1rem;
            background-color: #8C3F49;
            color: white;
        }

        .perfume-details img {
            width: 100%;
            max-width: 300px;
            height: auto;
            object-fit: cover;
            border-radius: 8px;
        }

        .cta-button {
            display: inline-block;
            background-color: #8C3F49;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
        }

        .cta-button:hover {
            background-color: #a64d4d;
        }
    </style>
</head>
<body>

<header class="bg-danger text-white text-center py-3">
    <h1><?= htmlspecialchars($perfume['perfume_name']); ?></h1>
</header>

<main class="container mt-5">
    <div class="row">
        <!-- Perfume Image -->
        <div class="col-md-6">
            <div class="perfume-details">
                <img src="<?= htmlspecialchars($perfume['image'] ?: 'images/default_image.jpeg') ?>" alt="<?= htmlspecialchars($perfume['perfume_name']); ?>">
            </div>
        </div>

        <!-- Perfume Info and Add to Cart Form -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2><?= htmlspecialchars($perfume['perfume_name']); ?></h2>
                    <p><?= htmlspecialchars($perfume['description']); ?></p>
                    <p><strong>Price:</strong> RM <?= number_format($perfume['price'], 2); ?></p>
                    
                    <!-- Add to Cart Form -->
                    <form action="cart.php" method="post">
                        <input type="hidden" name="perfume_id" value="<?= $perfume['perfume_id']; ?>">
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button, pointing to the correct category page -->
    <a href="products.php?category_id=<?= $category_id; ?>" class="cta-button mt-4">Back to Products</a>
</main>

<footer>
    <p>&copy; 2025 Ras Fragrance. All rights reserved.</p>
</footer>

<!-- Bootstrap JS and Dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
