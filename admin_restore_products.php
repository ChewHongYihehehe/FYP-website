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


// Fetch deleted products
$stmt = $conn->prepare("
    SELECT 
        MIN(id) AS id, 
        name, 
        category, 
        brand, 
        color, 
        price, 
        image1_display 
    FROM deleted_products 
    GROUP BY product_id
");
$stmt->execute();
$deleted_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Restore Products</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_product.css"> <!-- Link to the same CSS file -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Check if there is an error message to display
        var errorMessage = <?= json_encode($error_message); ?>; // Convert PHP variable to JavaScript

        if (errorMessage) {
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'admin_login.php';
                });
            };
        }
    </script>
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Restore Deleted Products</h1>
        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Color</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row_count = 1;
                    foreach ($deleted_products as $product): ?>
                        <tr>
                            <td><?= $row_count++; ?></td>
                            <td><img src="<?= htmlspecialchars($product['image1_display']); ?>" alt="Product Image" width="100"></td>
                            <td><?= htmlspecialchars($product['name']); ?></td>
                            <td><?= htmlspecialchars($product['category']); ?></td>
                            <td><?= htmlspecialchars($product['brand']); ?></td>
                            <td><?= htmlspecialchars($product['color']); ?></td>
                            <td>RM <?= htmlspecialchars($product['price']); ?></td>
                            <td>
                                <a class="btn" href="restore_product.php?id=<?= htmlspecialchars($product['id']); ?>">Restore</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>