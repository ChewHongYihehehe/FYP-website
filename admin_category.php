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


// Fetch categories
$categories = [];
$stmt = $conn->prepare("SELECT * FROM categories ORDER BY id DESC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];

    // Handle file upload
    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
        $target_dir = "assets/image/";

        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Generate unique filename
        $file_extension = pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $unique_filename;

        // Move uploaded file
        if (move_uploaded_file($_FILES['category_image']['tmp_name'], $target_file)) {
            // Prepare SQL to insert new category with image
            $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (:name, :image)");
            $stmt->bindParam(':name', $category_name);
            $stmt->bindParam(':image', $unique_filename);
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    } else {
        // Prepare SQL to insert new category without image
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->bindParam(':name', $category_name);
    }

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: admin_category.php");
        exit();
    } else {
        $error_message = "Error adding category.";
    }
}


// Handle editing a category
if (isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];

    // Handle file upload
    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == 0) {
        $target_dir = "assets/image/";

        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Generate unique filename
        $file_extension = pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $unique_filename;

        // Move uploaded file
        if (move_uploaded_file($_FILES['category_image']['tmp_name'], $target_file)) {
            // Prepare SQL to update with new image
            $stmt = $conn->prepare("UPDATE categories SET name = :name, image = :image WHERE id = :id");
            $stmt->bindParam(':name', $category_name);
            $stmt->bindParam(':image', $unique_filename);
            $stmt->bindParam(':id', $category_id, PDO::PARAM_INT);
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    } else {
        // Update category without changing the image
        $stmt = $conn->prepare("UPDATE categories SET name = :name WHERE id = :id");
        $stmt->bindParam(':name', $category_name);
        $stmt->bindParam(':id', $category_id, PDO::PARAM_INT);
    }

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: admin_category.php");
        exit();
    } else {
        $error_message = "Error updating category.";
    }
}

// Handle deletion of a category
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Fetch the category details before deletion
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) {
        // Check if there are any products with the same category name
        $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category = :category_name");
        $stmt->bindParam(':category_name', $category['name']);
        $stmt->execute();
        $product_count = $stmt->fetchColumn();

        if ($product_count > 0) {
            $error_message = "This category cannot be deleted because it has associated products.";
        } else {
            // Insert the category into the deleted_categories table
            $stmt = $conn->prepare("INSERT INTO deleted_categories (name, image) VALUES (:name, :image)");
            $stmt->bindParam(':name', $category['name']);
            $stmt->bindParam(':image', $category['image']);
            $stmt->execute();

            // Now delete the category from the categories table
            $stmt = $conn->prepare("DELETE FROM categories WHERE id = :id");
            $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: admin_category.php");
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
    <title>Manage Categories</title>
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
                    text = errorMessage;
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

    <?php
    include 'sidebar.php';
    ?>
    <div class="container">
        <!-- Add Category Button -->
        <div class="btn-container page-top">
            <button class="btn-add-new" id="addCategoryBtn">
                Add New Category
            </button>
        </div>

        <!-- Modal for Add Category Form -->
        <div id="addCategoryModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeAddModal">&times;</span>
                <form method="post" enctype="multipart/form-data">
                    <div class="account-header">
                        <h1 class="account-title">Add a New Category</h1>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Category Name</label>
                            <input type="text" placeholder="Enter category name" name="category_name" required>
                        </div>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Category Image</label>
                            <input type="file" name="category_image" accept="image/*">
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn-save" name="add_category">Add Category</button>
                    </div>
                </form>
            </div>
        </div>







        <!-- Modal for Edit Category Form -->
        <div id="editCategoryModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeEditModal">&times;</span>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="category_id" id="editCategoryId">
                    <div class="account-header">
                        <h1 class="account-title">Edit Category</h1>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Category Name</label>
                            <input type="text" placeholder="Enter category name" name="category_name" id="editCategoryName" required>
                        </div>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Category Image</label>
                            <input type="file" name="category_image" accept="image/*">
                        </div>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Current Image</label>
                            <div id="currentImageContainer">
                                <img id="currentCategoryImage" src="" alt="Current Category Image" class="category-image" style="max-width:100px; display:none;">
                                <p id="noImageText" style="display: none;">No Image</p>
                            </div>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn-save" name="edit_category">Save</button>
                    </div>
                </form>
            </div>
        </div>





        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Category Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row_count = 1;
                    foreach ($categories as $category): ?>
                        <tr>
                            <td><?= $row_count++; ?></td>
                            <td>
                                <?php if (!empty($category['image'])): ?>
                                    <img src="assets/image/<?= htmlspecialchars($category['image']); ?>"
                                        alt="<?= htmlspecialchars($category['name']); ?>"
                                        class="category-image">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($category['name']); ?></td>
                            <td>
                                <button class="btn edit-category-btn"
                                    data-id="<?= htmlspecialchars($category['id']); ?>"
                                    data-name="<?= htmlspecialchars($category['name']); ?>"
                                    data-image="<?= htmlspecialchars($category['image']); ?>"> <!-- Add this line -->
                                    Edit
                                </button>
                                <a href="?delete_id=<?= htmlspecialchars($category['id']); ?>" class="btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

<script src="assets/js/admin_category.js"></script>

</html>