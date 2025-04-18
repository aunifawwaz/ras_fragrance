<?php
// Start session
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user order history
$user_id = $_SESSION['user_id'];
$order_query = "
    SELECT o.order_id, o.order_date, o.total_amount, o.status, GROUP_CONCAT(p.perfume_name ORDER BY p.perfume_name) AS perfumes
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN perfume p ON oi.perfume_id = p.perfume_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
";
$stmt = $conn->prepare($order_query);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $order_result = $stmt->get_result();
} else {
    die("Query preparation failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Ras Fragrance</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #8C3F49;
            padding: 10px 20px;
            border-bottom: 1px solid #ccc;
        }

        .order-section {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .order-section h2 {
            text-align: center;
            color: #8C3F49;
        }

        .order-details {
            margin: 20px 0;
        }

        .order-item {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }

        .order-item .order-header {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .order-item .order-status {
            color: #8C3F49;
        }

        .cta-buttons {
            text-align: center;
            margin-top: 20px;
        }

        footer {
            text-align: center;
            padding: 15px;
            background-color: #8C3F49;
            color: white;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
    <div class="header-container">
        <div class="logo">
            <a href="home.php" title="Home">
                <img src="images/logo.jpeg" alt="Ras Fragrance Logo">
            </a>
        </div>
        <h1>Ras Fragrance</h1>
        <h2>The Essence of Luxury</h2>
        <nav>
            <ul class="nav-menu">
                <li><a href="home.php">Home</a></li>
                <li><a href="quiz.php">Quiz</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <section class="order-section">
        <h2>Your Order History</h2>
        <?php if ($order_result->num_rows > 0): ?>
            <div class="order-details">
                <?php while ($order = $order_result->fetch_assoc()): ?>
                    <div class="order-item">
                        <div class="order-header">
                            Order #<?= htmlspecialchars($order['order_id']); ?> - <?= htmlspecialchars($order['order_date']); ?>
                        </div>
                        <div>
                            <strong>Status:</strong> <span class="order-status"><?= htmlspecialchars($order['status']); ?></span>
                        </div>
                        <div>
                            <strong>Perfumes:</strong> <?= htmlspecialchars($order['perfumes']); ?>
                        </div>
                        <div>
                            <strong>Total Amount:</strong> $<?= htmlspecialchars($order['total_amount']); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>You have no orders yet.</p>
        <?php endif; ?>
    </section>
</main>

<footer>
    <p>&copy; 2025 Ras Fragrance. All rights reserved.</p>
</footer>
</body>
</html>
