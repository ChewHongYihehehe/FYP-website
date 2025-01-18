<?php
include 'connect.php';
session_start();

// Fetch colors
$colors = [];
$stmt = $conn->prepare("SELECT * FROM color"); // Assuming your table is named 'colors'
$stmt->execute();
$colors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle adding a new color
if (isset($_POST['add_color'])) {
    $color_name = $_POST['color_name']; // Corrected to match the input name
    $stmt = $conn->prepare("INSERT INTO color (color_name) VALUES (:color_name)"); // Changed 'color' to 'color_name'
    $stmt->bindParam(':color_name', $color_name); // Corrected parameter binding
    $stmt->execute();
    header("Location: admin_category_color.php");
    exit();
}

// Handle editing a color
if (isset($_POST['edit_color'])) {
    $color_id = $_POST['color_id']; // Corrected to match the input name
    $color_name = $_POST['color_name']; // Corrected to match the input name
    $stmt = $conn->prepare("UPDATE color SET color_name = :color_name WHERE id = :id"); // Changed 'color' to 'color_name'
    $stmt->bindParam(':color_name', $color_name); // Corrected parameter binding
    $stmt->bindParam(':id', $color_id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: admin_category_color.php");
    exit();
}

// Handle deleting a color
if (isset($_GET['delete_id'])) {
    $color_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM color WHERE id = :id");
    $stmt->bindParam(':id', $color_id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: admin_category_color.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Colors</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_categories.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <!-- Add Color Button -->
        <div class="btn-container page-top">
            <button class="btn-add-new" id="addColorBtn">
                Add New Color
            </button>
        </div>

        <!-- Modal for Add Color Form -->
        <div id="addColorModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeAddColorModal">&times;</span>
                <form method="post">
                    <div class="account-header">
                        <h1 class="account-title">Add a New Color</h1>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Color</label>
                            <input type="text" placeholder="Enter color name" name="color_name" required>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn-save" name="add_color">Add Color</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal for Edit Color Form -->
        <div id="editColorModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeEditColorModal">&times;</span>
                <form method="post">
                    <input type="hidden" name="color_id" id="editColorId">
                    <div class="account-header">
                        <h1 class="account-title">Edit Color</h1>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Color</label>
                            <input type="text" placeholder="Enter color name" name="color_name" id="editColorName" required>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn-save" name="edit_color">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>Color Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($colors as $color): ?>
                        <tr>
                            <td><?= htmlspecialchars($color['color_name']); ?></td>
                            <td>
                                <button class="btn edit-color-btn"
                                    data-id="<?= htmlspecialchars($color['id']); ?>"
                                    data-name="<?= htmlspecialchars($color['color_name']); ?>">
                                    Edit
                                </button>
                                <a href="?delete_id=<?= htmlspecialchars($color['id']); ?>" class="btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

<script src="assets/js/admin_color.js"></script>

</html>