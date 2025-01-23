<?php
include 'connect.php';
session_start();

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