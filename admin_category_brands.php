<?php
include 'connect.php';
session_start();

// Fetch brands
$brands = [];
$stmt = $conn->prepare("SELECT * FROM brand"); // Assuming your table is named 'brands'
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle adding a new brand
if (isset($_POST['add_brand'])) {
    $brand_name = $_POST['brand_name']; // Corrected to match the input name
    $stmt = $conn->prepare("INSERT INTO brand (name) VALUES (:name)"); // Changed 'brand' to 'name'
    $stmt->bindParam(':name', $brand_name); // Corrected parameter binding
    $stmt->execute();
    header("Location: admin_category_brands.php");
    exit();
}

// Handle editing a brand
if (isset($_POST['edit_brand'])) {
    $brand_id = $_POST['brand_id']; // Corrected to match the input name
    $brand_name = $_POST['brand_name']; // Corrected to match the input name
    $stmt = $conn->prepare("UPDATE brand SET name = :name WHERE id = :id"); // Changed 'brand' to 'name'
    $stmt->bindParam(':name', $brand_name); // Corrected parameter binding
    $stmt->bindParam(':id', $brand_id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: admin_category_brands.php");
    exit();
}

// Handle deletion of a brand
if (isset($_GET['delete_id'])) {
    $brand_id = $_GET['delete_id'];

    // Fetch the brand details before deletion
    $stmt = $conn->prepare("SELECT * FROM brand WHERE id = :id");
    $stmt->bindParam(':id', $brand_id, PDO::PARAM_INT);
    $stmt->execute();
    $brand = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($brand) {
        // Insert the brand into the deleted_brands table
        $stmt = $conn->prepare("INSERT INTO deleted_brands (name) VALUES (:name)");
        $stmt->bindParam(':name', $brand['name']);
        $stmt->execute();

        // Now delete the brand from the brand table
        $stmt = $conn->prepare("DELETE FROM brand WHERE id = :id");
        $stmt->bindParam(':id', $brand_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: admin_category_brands.php");
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
    <title>Manage Brands</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_categories.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <!-- Add Brand Button -->
        <div class="btn-container page-top">
            <button class="btn-add-new" id="addBrandBtn">
                Add New Brand
            </button>
        </div>

        <!-- Modal for Add Brand Form -->
        <div id="addBrandModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeAddBrandModal">&times;</span>
                <form method="post">
                    <div class="account-header">
                        <h1 class="account-title">Add a New Brand</h1>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Brand Name</label>
                            <input type="text" placeholder="Enter brand name" name="brand_name" required>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn-save" name="add_brand">Add Brand</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal for Edit Brand Form -->
        <div id="editBrandModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeEditBrandModal">&times;</span>
                <form method="post">
                    <input type="hidden" name="brand_id" id="editBrandId">
                    <div class="account-header">
                        <h1 class="account-title">Edit Brand</h1>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Brand Name</label>
                            <input type="text" placeholder="Enter brand name" name="brand_name" id="editBrandName" required>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn-save" name="edit_brand">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="product-display">
            <table class=" product-display-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Brand Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row_count = 1;
                    foreach ($brands as $brand): ?>
                        <tr>
                            <td><?= $row_count++; ?></td>
                            <td><?= htmlspecialchars($brand['name']); ?></td>
                            <td>
                                <button class="btn edit-brand-btn"
                                    data-id="<?= htmlspecialchars($brand['id']); ?>"
                                    data-name="<?= htmlspecialchars($brand['name']); ?>">
                                    Edit
                                </button>
                                <a href="?delete_id=<?= htmlspecialchars($brand['id']); ?>" class="btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

<script src="assets/js/admin_brands.js"></script>

</html>