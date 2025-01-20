<?php
include 'connect.php';
session_start();

// Fetch deleted product sizes, including stock
$stmt = $conn->prepare("SELECT * FROM deleted_product_sizes");
$stmt->execute();
$deleted_sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Deleted Product Sizes</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_categories.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Deleted Product Sizes</h1>
        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Stock</th> <!-- Added Stock Column -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deleted_sizes)): ?>
                        <tr>
                            <td colspan="5">No deleted sizes found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($deleted_sizes as $deleted_size): ?>
                            <tr>
                                <td><?= htmlspecialchars($deleted_size['product_id']); ?></td>
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