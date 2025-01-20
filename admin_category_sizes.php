<?php
include 'connect.php';
session_start();

// Fetch sizes
$sizes = [];
$stmt = $conn->prepare("SELECT * FROM sizes");
$stmt->execute();
$sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle adding a new size
if (isset($_POST['add_size'])) {
    $size_name = $_POST['size_name']; // Corrected to match the input name
    $stmt = $conn->prepare("INSERT INTO sizes (size) VALUES (:size)"); // Changed 'name' to 'size'
    $stmt->bindParam(':size', $size_name); // Corrected parameter binding
    $stmt->execute();
    header("Location: admin_category_sizes.php");
    exit();
}

// Handle editing a size
if (isset($_POST['edit_size'])) {
    $size_id = $_POST['size_id']; // Corrected to match the input name
    $size_name = $_POST['size_name']; // Corrected to match the input name
    $stmt = $conn->prepare("UPDATE sizes SET size = :size WHERE id = :id"); // Changed 'name' to 'size'
    $stmt->bindParam(':size', $size_name); // Corrected parameter binding
    $stmt->bindParam(':id', $size_id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: admin_category_sizes.php");
    exit();
}

// Handle deletion of a size
if (isset($_GET['delete_id'])) {
    $size_id = $_GET['delete_id'];

    // Fetch the size details before deletion
    $stmt = $conn->prepare("SELECT * FROM sizes WHERE id = :id");
    $stmt->bindParam(':id', $size_id, PDO::PARAM_INT);
    $stmt->execute();
    $size = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($size) {
        // Insert the size into the deleted_sizes table
        $stmt = $conn->prepare("INSERT INTO deleted_sizes (size) VALUES (:size)");
        $stmt->bindParam(':size', $size['size']);
        $stmt->execute();

        // Now delete the size from the sizes table
        $stmt = $conn->prepare("DELETE FROM sizes WHERE id = :id");
        $stmt->bindParam(':id', $size_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: admin_category_sizes.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sizes</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_categories.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <!-- Add Size Button -->
        <div class="btn-container page-top">
            <button class="btn-add-new" id="addSizeBtn">
                Add New Size
            </button>
        </div>

        <!-- Modal for Add Size Form -->
        <div id="addSizeModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeAddSizeModal">&times;</span>
                <form method="post">
                    <div class="account-header">
                        <h1 class="account-title">Add a New Size</h1>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Size</label>
                            <input type="text" placeholder="Enter size name" name="size_name" required>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn-save" name="add_size">Add Size</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal for Edit Size Form -->
        <div id="editSizeModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeEditSizeModal">&times;</span>
                <form method="post">
                    <input type="hidden" name="size_id" id="editSizeId">
                    <div class="account-header">
                        <h1 class="account-title">Edit Size</h1>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Size</label>
                            <input type="text" placeholder="Enter size name" name="size_name" id="editSizeName" required>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn-save" name="edit_size">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>Size(UK)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sizes as $size): ?>
                        <tr>
                            <td><?= htmlspecialchars($size['size']); ?></td> <!-- Assuming the column in the database is 'name' -->
                            <td>
                                <button class="btn edit-size-btn"
                                    data-id="<?= htmlspecialchars($size['id']); ?>"
                                    data-name="<?= htmlspecialchars($size['size']); ?>">

                                    Edit
                                </button>
                                <a href="?delete_id=<?= htmlspecialchars($size['id']); ?>" class="btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

<script src="assets/js/admin_size.js"></script>

</html>