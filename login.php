<?php
session_start(); // Ensure session is started at the beginning

include 'db_connection.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch input values
    $username = isset($_POST['username']) ? $conn->real_escape_string($_POST['username']) : '';
    $password = isset($_POST['password']) ? $conn->real_escape_string($_POST['password']) : '';

    if (!empty($username) && !empty($password)) {
        // Query the database for the username
        $query = "SELECT * FROM `User` WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Compare plaintext passwords (NOT RECOMMENDED in production)
            if ($password === $user['password']) {
                // Set session variables
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on user role
                if ($user['role'] == 1) { // 1 = Admin
                    header('Location: admin_dashboard.php');
                } else { // 0 = Regular User
                    header('Location: profile.php'); // Redirect to profile page
                }
                exit;
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "Invalid username.";
        }
        $stmt->close();
    } else {
        $message = "Please fill in all the fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4e7e7;
        }
        .form-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .register-link {
            display: block;
            text-align: center;
            margin-top: 10px;
        }
        footer {
            text-align: center;
            padding: 1rem;
            background-color: #8C3F49;
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>

<header class="bg-danger text-white text-center py-3">
    <h1>Login</h1>
</header>

<main>
    <div class="form-container">
        <!-- Display Error or Success Message -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo strpos($message, 'Incorrect') !== false ? 'danger' : 'success'; ?>" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-danger btn-block">Login</button>
        </form>

        <a href="register.php" class="register-link">Don't have an account? Register here</a>
    </div>
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
