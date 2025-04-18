<?php
// Include database connection
include 'db_connection.php';

// Fetch categories from the database
$query = "SELECT id,name FROM categories";
$result = $conn->query($query);

// Check if categories are available
$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = ['id' => $row['id'], 'name' => $row['name']]; // Store both `id` and `name
    }
} else {
    $categories[] = "No categories available";
}

// Fetch perfumes sorted by popularity
$popular_query = "SELECT * FROM perfume ORDER BY popularity_score DESC LIMIT 8"; // Display top 8 popular perfumes
$popular_result = $conn->query($popular_query);
$popular_perfumes = $popular_result->fetch_all(MYSQLI_ASSOC);

// Fetch expert's choice perfumes
$experts_choice_query = "SELECT * FROM perfume WHERE expert_choice = 1 LIMIT 8";
$experts_choice_result = $conn->query($experts_choice_query);
$experts_choice_perfumes = $experts_choice_result->fetch_all(MYSQLI_ASSOC);

// Fetch beginner's choice perfumes
$beginners_choice_query = "SELECT * FROM perfume WHERE beginners_choice = 1 LIMIT 8";
$beginners_choice_result = $conn->query($beginners_choice_query);
$beginners_choice_perfumes = $beginners_choice_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Ras Fragrance</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Categories Section Styling */
        .categories-section {
            text-align: center;
            padding: 2rem;
            background-color: #f4f4f4;
            border-radius: 10px;
            margin: 2rem auto;
            max-width: 80%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .categories-section h2 {
            font-size: 2rem;
            color: #8C3F49;
            margin-bottom: 1.5rem;
        }

        .categories-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }

        .category-card {
            background-color: #ffffff;
            color: #8C3F49;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            padding: 1rem;
            border: 1px solid #8C3F49;
            border-radius: 10px;
            width: 150px;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .category-card:hover {
            transform: scale(1.05);
        }

        /* Page Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .perfume-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            padding: 2rem;
        }

        .perfume-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 200px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            position: relative; /* Position context for the cart icon */
        }

        .perfume-card:hover {
            transform: scale(1.05);
        }

        .perfume-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .perfume-card h3 {
            font-size: 18px;
            margin: 0.5rem 0;
            color: #333;
        }

        .perfume-card p {
            font-size: 14px;
            color: #555;
            margin: 0.5rem;
        }

        .perfume-card a {
            display: block;
            text-decoration: none;
            color: white;
            background-color: #8C3F49;
            padding: 0.5rem;
            margin: 1rem;
            border-radius: 4px;
        }

        .perfume-card a:hover {
            background-color: #a64d4d;
        }

        /* Add to Cart Icon */
        .add-to-cart {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 5px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            cursor: pointer;
            z-index: 10; /* Ensure the icon is above the image */
        }

        .add-to-cart img {
            width: 20px;
            height: 20px;
        }

        .add-to-cart:hover {
            background-color: rgba(0, 0, 0, 0.7);
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>

    <main>
        <!-- Categories Section -->
        <section class="categories-section">
            <h2>CATEGORIES</h2>
            <div class="categories-container">
                <?php foreach ($categories as $category): ?>
                    <a href="products.php?category_id=<?= $category['id'] ?>" class="category-card">
                        <?= htmlspecialchars($category['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Popular Perfumes Section -->
        <section class="favorites-section">
            <h2>Popular Perfumes</h2>
            <div class="perfume-container">
                <?php foreach ($popular_perfumes as $perfume): ?>
                    <div class="perfume-card">
                        <img src="<?= htmlspecialchars($perfume['image'] ?: 'images/default_image.jpeg') ?>" alt="Perfume Image">
                        <h3><?= htmlspecialchars($perfume['perfume_name']); ?></h3>
                        <p><strong>Price:</strong> RM <?= number_format($perfume['price'], 2); ?></p>
                        <a href="perfume_details.php?perfume_id=<?= $perfume['perfume_id']; ?>">View Details</a>

                        <!-- Add to Cart Button -->
                        <form id="cart-form-<?= $perfume['perfume_id']; ?>" method="POST" style="display:inline;">
                            <input type="hidden" name="perfume_id" value="<?= $perfume['perfume_id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="button" class="add-to-cart" onclick="addToCart(<?= $perfume['perfume_id']; ?>)">
                                <img src="images/cart.png" alt="Cart Icon">
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Expert's Choice Section -->
        <section class="favorites-section">
            <h2>Expert's Choice</h2>
            <div class="perfume-container">
                <?php foreach ($experts_choice_perfumes as $perfume): ?>
                    <div class="perfume-card">
                        <img src="<?= htmlspecialchars($perfume['image'] ?: 'images/default_image.jpeg') ?>" alt="Perfume Image">
                        <h3><?= htmlspecialchars($perfume['perfume_name']); ?></h3>
                        <p><strong>Price:</strong> RM <?= number_format($perfume['price'], 2); ?></p>
                        <a href="perfume_details.php?perfume_id=<?= $perfume['perfume_id']; ?>">View Details</a>

                        <!-- Add to Cart Button -->
                        <form id="cart-form-<?= $perfume['perfume_id']; ?>" method="POST" style="display:inline;">
                            <input type="hidden" name="perfume_id" value="<?= $perfume['perfume_id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="button" class="add-to-cart" onclick="addToCart(<?= $perfume['perfume_id']; ?>)">
                                <img src="images/cart.png" alt="Cart Icon">
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Beginner's Choice Section -->
        <section class="favorites-section">
            <h2>Beginner's Choice</h2>
            <div class="perfume-container">
                <?php foreach ($beginners_choice_perfumes as $perfume): ?>
                    <div class="perfume-card">
                        <img src="<?= htmlspecialchars($perfume['image'] ?: 'images/default_image.jpeg') ?>" alt="Perfume Image">
                        <h3><?= htmlspecialchars($perfume['perfume_name']); ?></h3>
                        <p><strong>Price:</strong> RM <?= number_format($perfume['price'], 2); ?></p>
                        <a href="perfume_details.php?perfume_id=<?= $perfume['perfume_id']; ?>">View Details</a>

                        <!-- Add to Cart Button -->
                        <form id="cart-form-<?= $perfume['perfume_id']; ?>" method="POST" style="display:inline;">
                            <input type="hidden" name="perfume_id" value="<?= $perfume['perfume_id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="button" class="add-to-cart" onclick="addToCart(<?= $perfume['perfume_id']; ?>)">
                                <img src="images/cart.png" alt="Cart Icon">
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <script>
        function addToCart(perfumeId) {
            var form = document.getElementById('cart-form-' + perfumeId);
            var formData = new FormData(form);

            fetch('shop.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Update the cart count dynamically
                document.getElementById('cart-count').textContent = data;
            })
            .catch(error => console.error('Error:', error));
        }
    </script>

    <?php include('footer.php'); ?>
</body>
</html>
