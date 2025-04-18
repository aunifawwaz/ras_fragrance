<?php
// Start the session
session_start();

// Include database connection
include 'db_connection.php';

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['perfume_id'], $_POST['quantity'])) {
    $perfume_id = intval($_POST['perfume_id']);
    $quantity = intval($_POST['quantity']);

    // Fetch perfume details
    $perfume_query = "SELECT perfume_id, perfume_name, price FROM perfume WHERE perfume_id = ?";
    $stmt = $conn->prepare($perfume_query);
    $stmt->bind_param("i", $perfume_id);
    $stmt->execute();
    $perfume_result = $stmt->get_result();
    $perfume = $perfume_result->fetch_assoc();

    if ($perfume) {
        // Add to cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['perfume_id'] == $perfume_id) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'perfume_id' => $perfume['perfume_id'],
                'perfume_name' => $perfume['perfume_name'],
                'price' => $perfume['price'],
                'quantity' => $quantity
            ];
        }
        $_SESSION['message'] = "Perfume added to cart successfully!";
    } else {
        $_SESSION['message'] = "Perfume not found.";
    }
    header("Location: cart.php");
    exit();
}

// Handle remove item from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $remove_id = intval($_POST['remove_id']);
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['perfume_id'] == $remove_id) {
            if ($item['quantity'] > 1) {
                // Reduce the quantity by 1
                $item['quantity']--;
            } else {
                // Remove the item completely if quantity is 1
                $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($cart_item) use ($remove_id) {
                    return $cart_item['perfume_id'] !== $remove_id;
                });
            }
            break; // Exit the loop once the item is found and updated
        }
    }
    $_SESSION['message'] = "Item updated in the cart.";
    header("Location: cart.php");
    exit();
}

// Fetch cart items from session
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cart_count = isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        header {
            background-color: #8C3F49;
            color: white;
            padding: 15px 0;
            text-align: center;
        }

        main {
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table thead {
            background-color: #8C3F49;
            color: white;
        }

        table th, table td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table td img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .cta-button {
            display: inline-block;
            padding: 8px 12px;
            background-color: #8C3F49;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .cta-button:hover {
            background-color: #a64d4d;
        }

        .empty-cart {
            text-align: center;
            color: #555;
            font-size: 18px;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #8C3F49;
            color: white;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
    <h1>Your Cart</h1>
    <?php include 'header.php'; ?>
</header>

<main>
    <?php if (!empty($_SESSION['message'])): ?>
        <script>
            // Show the alert message using JavaScript's alert()
            alert("<?= htmlspecialchars($_SESSION['message']); ?>");
        </script>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['cart'])): ?>
        <form action="cart.php" method="post">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Perfume</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $grand_total = 0; ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <?php 
                        $total = $item['price'] * $item['quantity']; 
                        $grand_total += $total;

                        // Get image for perfume
                        $perfume_image_query = "SELECT image FROM perfume WHERE perfume_id = ?";
                        $stmt = $conn->prepare($perfume_image_query);
                        $stmt->bind_param("i", $item['perfume_id']);
                        $stmt->execute();
                        $image_result = $stmt->get_result();
                        $image = $image_result->fetch_assoc()['image'] ?? 'images/default_image.jpeg';
                        ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($item['perfume_name']) ?>"></td>
                            <td><?= htmlspecialchars($item['perfume_name']); ?></td>
                            <td>RM <?= number_format($item['price'], 2); ?></td>
                            <td><?= $item['quantity']; ?></td>
                            <td>RM <?= number_format($total, 2); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="remove_id" value="<?= $item['perfume_id']; ?>">
                                    <button class="cta-button" type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">Grand Total</td>
                        <td>RM <?= number_format($grand_total, 2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <form action="payment.php" method="post">
                <button type="submit" class="cta-button">Check Out</button>
            </form>
        </form>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
    <a href="products.php" class="cta-button">Back</a><br>
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
