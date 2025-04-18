<?php
session_start();

// Ensure session role exists and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header('Location: login.php');
    exit(); // Stop script execution
}

// Include database connection
include 'db_connection.php';

// Fetch all orders (Dummy data for demonstration)
$order_query = "SELECT * FROM Orders";
$order_result = $conn->query($order_query);

if (!$order_result) {
    die("Query failed: " . $conn->error);
}

$orders = $order_result->fetch_all(MYSQLI_ASSOC);

// Handle the status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    // Update the order status in the database
    $update_query = "UPDATE Orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Order status updated successfully.";
    } else {
        $_SESSION['message'] = "Failed to update order status: " . $stmt->error;
    }
    $stmt->close();

    // Redirect back to the same page to show the updated order status
    header("Location: manage_orders.php");
    exit();
}

// Dummy data for orders (in case no data exists in the database)
if (count($orders) == 0) {
    $orders = [
        ['order_id' => 1, 'customer_name' => 'John Doe', 'total' => 150.00, 'status' => 'Pending'],
        ['order_id' => 2, 'customer_name' => 'Jane Smith', 'total' => 250.00, 'status' => 'Completed'],
        ['order_id' => 3, 'customer_name' => 'Michael Johnson', 'total' => 120.00, 'status' => 'Shipped'],
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container my-5">
        <header class="text-center mb-4">
            <h1 class="text-primary">Admin Panel - Manage Orders</h1>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </header>

        <!-- Show total number of orders -->
        <div class="alert alert-info">
            <strong>Total Orders: <?= $total_orders ?></strong>
        </div>

        <!-- Show status update message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($_SESSION['message']); ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['order_id']) ?></td>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td>RM <?= number_format($row['total'], 2) ?></td>
                            <td>
                                <form action="manage_orders.php" method="POST" class="form-inline">
                                    <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                    <select name="status" class="form-control status-dropdown">
                                        <option value="Pending" <?= ($row['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                        <option value="Shipped" <?= ($row['status'] == 'Shipped') ? 'selected' : '' ?>>Shipped</option>
                                        <option value="Completed" <?= ($row['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary ml-2">Update</button>
                                </form>
                            </td>
                            <td>
                                <a href="view_order.php?order_id=<?= $row['order_id'] ?>" class="btn btn-info btn-sm">View</a>
                                <a href="manage_orders.php?delete_id=<?= $row['order_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Dummy footer -->
        <footer class="text-center mt-4">
            <p>&copy; 2025 Ras Fragrance. All rights reserved.</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
