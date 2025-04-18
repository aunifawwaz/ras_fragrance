<?php
session_start();

// Ensure session role exists and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header('Location: login.php');
    exit(); // Stop script execution
}

include 'db_connection.php';

$message = "";

// Handle deleting a perfume
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM `Perfume` WHERE perfume_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $message = "Perfume deleted successfully.";
    } else {
        $message = "Error deleting perfume: " . $stmt->error;
    }
    $stmt->close();
}

// Handle editing a perfume (price update only)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_perfume'])) {
    $perfume_id = $_POST['perfume_id'];
    $price = $_POST['price'];
    
    // Get the current image path from the database
    $stmt = $conn->prepare("SELECT image FROM Perfume WHERE perfume_id = ?");
    $stmt->bind_param("i", $perfume_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $perfume = $result->fetch_assoc();
    $current_image = $perfume['image']; // Current image

    // Handle image upload only if a new image is provided
    $image = $current_image;
    if ($_FILES['image']['error'] == 0) {
        // New image is uploaded, replace the old image
        $image = 'images/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Update perfume with the new price (and keep the same image if no new image is uploaded)
    $update_sql = "UPDATE Perfume SET price = ?, image = ? WHERE perfume_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("dsi", $price, $image, $perfume_id);
    
    if ($stmt->execute()) {
        $message = "Perfume updated successfully!";
    } else {
        $message = "Error updating perfume: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all categories
$category_query = "SELECT * FROM Categories";
$category_result = $conn->query($category_query);
$categories = $category_result->fetch_all(MYSQLI_ASSOC);

// Fetch all perfumes
$perfume_query = "SELECT p.*, c.name AS category_name FROM Perfume p LEFT JOIN Categories c ON p.category_id = c.id";
$perfume_result = $conn->query($perfume_query);
$perfumes = $perfume_result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Perfumes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container my-5">
        <header class="text-center mb-4">
            <h1 class="text-primary">Admin Panel - Manage Perfumes</h1>
            <div class container>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </header>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Perfume List -->
        <div class="row">
            <?php foreach ($perfumes as $perfume): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?= htmlspecialchars($perfume['image'] ?: 'images/default_image.jpeg') ?>" alt="<?= htmlspecialchars($perfume['perfume_name']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($perfume['perfume_name']) ?></h5>
                            <p class="card-text">Category: <?= htmlspecialchars($perfume['category_name']) ?></p>
                            <p class="card-text">Price: RM <?= number_format($perfume['price'], 2) ?></p>
                            <p class="card-text">Quantity: <?= htmlspecialchars($perfume['quantity']) ?></p>
                            <a href="?delete_id=<?= $perfume['perfume_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this perfume?')">Delete</a>
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editPerfumeModal" onclick="fillEditForm(<?= $perfume['perfume_id'] ?>, '<?= htmlspecialchars($perfume['perfume_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($perfume['description'], ENT_QUOTES) ?>', <?= $perfume['price'] ?>, <?= $perfume['quantity'] ?>, <?= $perfume['category_id'] ?>, '<?= htmlspecialchars($perfume['image']) ?>')">Edit</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
<!-- Add New Perfume Form -->
<form method="POST" enctype="multipart/form-data" class="mt-5">
            <h2 class="text-center">Add New Perfume</h2>
            <div class="mb-3">
                <label for="perfume_name" class="form-label">Name:</label>
                <input type="text" class="form-control" name="perfume_name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" name="description" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price:</label>
                <input type="number" class="form-control" name="price" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity:</label>
                <input type="number" class="form-control" name="quantity" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category:</label>
                <select class="form-select" name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image:</label>
                <input type="file" class="form-control" name="image">
            </div>
            <button type="submit" name="add_perfume" class="btn btn-primary">Add Perfume</button>
        </form>
    </div>
        <!-- Edit Perfume Modal -->
        <div class="modal fade" id="editPerfumeModal" tabindex="-1" aria-labelledby="editPerfumeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPerfumeModalLabel">Edit Perfume</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="perfume_id" id="perfume_id">
                            <div class="mb-3">
                                <label for="perfume_name" class="form-label">Name:</label>
                                <input type="text" class="form-control" name="perfume_name" id="perfume_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description:</label>
                                <textarea class="form-control" name="description" id="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price:</label>
                                <input type="number" class="form-control" name="price" id="price" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity:</label>
                                <input type="number" class="form-control" name="quantity" id="quantity" required>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category:</label>
                                <select class="form-select" name="category_id" id="category_id" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Image:</label>
                                <input type="file" class="form-control" name="image" id="image">
                            </div>
                            <button type="submit" name="edit_perfume" class="btn btn-primary">Update Perfume</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        // Function to fill edit form with perfume data
        function fillEditForm(id, name, description, price, quantity, category_id, image) {
            document.getElementById('perfume_id').value = id;
            document.getElementById('perfume_name').value = name;
            document.getElementById('description').value = description;
            document.getElementById('price').value = price;
            document.getElementById('quantity').value = quantity;
            document.getElementById('category_id').value = category_id;
            document.getElementById('image').value = image;
        }
    </script>
</body>
</html>
