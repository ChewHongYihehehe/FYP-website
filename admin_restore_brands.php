<?php
include 'connect.php';
session_start();

// Fetch deleted brands
$stmt = $conn->prepare("SELECT * FROM deleted_brands");
$stmt->execute();
$deleted_brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Deleted Brands</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_categories.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Deleted Brands</h1>
        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>Brand Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deleted_brands)): ?>
                        <tr>
                            <td colspan="2">No deleted brands found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($deleted_brands as $deleted_brand): ?>
                            <tr>
                                <td><?= htmlspecialchars($deleted_brand['name']); ?></td>
                                <td>
                                    <a href="restore_brand.php?id=<?= htmlspecialchars($deleted_brand['id']); ?>" class="btn">Restore</a>
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