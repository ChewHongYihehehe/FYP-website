<?php
include 'connect.php';
session_start();


$error_message = '';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    $error_message = 'You must be logged in to view this page.';
} else {
    // Fetch admin details
    $admin_id = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT admin_status FROM admin WHERE id = :admin_id");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the admin's status is terminated
    if ($admin && strtolower($admin['admin_status']) === 'terminated') {
        $error_message = 'Your account has been terminated. Please contact support.';
    }
}



// Fetch sizes
$sizes = [];
$stmt = $conn->prepare("SELECT * FROM sizes ORDER BY size DESC");
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
        // Check if there are any product variants with the same size name
        $stmt = $conn->prepare("SELECT COUNT(*) FROM product_variants WHERE size = :size_name");
        $stmt->bindParam(':size_name', $size['size']);
        $stmt->execute();
        $product_count = $stmt->fetchColumn();

        if ($product_count > 0) {
            $error_message = "Cannot delete this size because it has associated product variants.";
        } else {
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Check if there is an error message to display
        var errorMessage = <?= json_encode($error_message); ?>; // Convert PHP variable to JavaScript


        if (errorMessage) {
            window.onload = function() {
                let title = 'Error';
                let text = errorMessage;

                if (errorMessage.includes('not logged in') || errorMessage.includes('terminated')) {
                    title = 'Access Denied';
                    Swal.fire({
                        icon: 'error',
                        title: title,
                        text: text,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'admin_login.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: title,
                        text: text,
                        confirmButtonText: 'OK'
                    });
                }
            };
        }
    </script>
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
                        <th>#</th>
                        <th>Size(UK)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row_count = 1;
                    foreach ($sizes as $size): ?>
                        <tr>
                            <td><?= $row_count++; ?></td>
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