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


$stmt = $conn->prepare("
    SELECT d.*, pv.image1_display
    FROM deleted_product_sizes d
    LEFT JOIN product_variants pv ON d.product_id = pv.product_id AND d.color = pv.color
");
$stmt->execute();
$deleted_sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Deleted Product Sizes</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_categories.css">
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
        <h1>Deleted Product Sizes</h1>
        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Image</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Stock</th> <!-- Added Stock Column -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deleted_sizes)): ?>
                        <tr>
                            <td colspan="6">No deleted sizes found.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $row_count = 1;
                        foreach ($deleted_sizes as $deleted_size): ?>
                            <tr>
                                <td><?= $row_count++; ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($deleted_size['image1_display']); ?>" alt="Product Image" width="50"> <!-- Display Image -->
                                </td>
                                <td><?= htmlspecialchars($deleted_size['size']); ?></td>
                                <td><?= htmlspecialchars($deleted_size['color']); ?></td>
                                <td><?= htmlspecialchars($deleted_size['stock']); ?></td> <!-- Display Stock -->
                                <td>
                                    <a href="restore_product_size.php?id=<?= htmlspecialchars($deleted_size['id']); ?>" class="btn">Restore</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>