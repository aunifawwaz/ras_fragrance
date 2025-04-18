<?php
session_start();

// Ensure session role exists and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    // If not an admin, redirect to home or login page
    header('Location: home.php');  // Redirect non-admins to home page
    exit();
}

// Include your database connection
include 'db_connection.php';

// Fetch total users
$user_query = "SELECT COUNT(*) AS total_users FROM User";
$user_result = $conn->query($user_query);
$total_users = $user_result->fetch_assoc()['total_users'];

// Fetch total perfumes
$perfume_query = "SELECT COUNT(*) AS total_perfumes FROM Perfume";
$perfume_result = $conn->query($perfume_query);
$total_perfumes = $perfume_result->fetch_assoc()['total_perfumes'];

// Fetch total orders
//$order_query = "SELECT COUNT(*) AS total_orders FROM Orders";
//$order_result = $conn->query($order_query);
//$total_orders = $order_result->fetch_assoc()['total_orders'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Custom Styles */
        body {
            background-color: #f9f9f9;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: #8C3F49;
            color: white;
            padding-top: 30px;
            display: flex;
            flex-direction: column;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #a64d4d;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .board {
            display: flex;
            gap: 15px;
        }

        .board-item {
            background-color: #ffe4e1;
            padding: 20px;
            border-radius: 10px;
            width: 200px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .board-item:hover {
            transform: scale(1.05);
            background-color: #f0c1b2;
        }

        .board-item h2 {
            font-size: 24px;
            color: #8C3F49;
        }

        .board-item p {
            font-size: 20px;
            color: #333;
        }

        .board-item i {
            font-size: 40px;
            color: #8C3F49;
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
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="manage_users.php">Users</a>
        <a href="manage_perfumes.php">Perfumes</a>
        <a href="manage_orders.php">Orders</a>
        <a href="manage_blog.php">Blog</a>
        <a href="manage_quiz.php">Quiz</a>
        <a href="logout.php" class="mt-auto">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="bg-primary text-white text-center p-4">
            <h1>Admin Dashboard</h1>
            <p>Welcome, <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin'; ?></p>
        </header>

        <div class="board">
            <!-- Users Box -->
            <a href="manage_users.php" class="board-item">
                <i class="fas fa-users"></i>
                <h2>Users</h2>
                <p><?= $total_users ?></p>
            </a>
            <!-- Perfumes Box -->
            <a href="manage_perfumes.php" class="board-item">
                <img src="images/perfume.png" alt="Perfume Icon" style="width: 40px; height: 40px;">
                <h2>Perfumes</h2>
                <p><?= $total_perfumes ?></p>
            </a>
            <!-- Orders Box -->
            <a href="manage_orders.php" class="board-item">
                <i class="fas fa-box"></i>
                <h2>Orders</h2>
                <!--p><?= $total_orders ?></p-->
            </a>
        </div>
    </div>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
