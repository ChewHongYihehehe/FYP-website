<?php
include 'connect.php';
session_start();

// Fetch deleted categories
$stmt = $conn->prepare("SELECT * FROM deleted_categories");
$stmt->execute();
$deleted_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Deleted Categories</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_categories.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Deleted Categories</h1>
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
                    <?php if (empty($deleted_categories)): ?>
                        <tr>
                            <td colspan="3">No deleted categories found.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $row_count = 1;
                        foreach ($deleted_categories as $deleted_category): ?>
                            <tr>
                                <td><?= $row_count++; ?></td>
                                <td>
                                    <?php if (!empty($deleted_category['image'])): ?>
                                        <img src="assets/image/<?= htmlspecialchars($deleted_category['image']); ?>" alt="<?= htmlspecialchars($deleted_category['name']); ?>" class="category-image" style="max-width:100px;">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($deleted_category['name']); ?></td>
                                <td>
                                    <a href="restore_categories.php?id=<?= htmlspecialchars($deleted_category['id']); ?>" class="btn">Restore</a>
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