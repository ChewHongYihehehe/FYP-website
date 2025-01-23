<?php
include 'connect.php';
session_start();

// Fetch deleted colors
$stmt = $conn->prepare("SELECT * FROM deleted_colors");
$stmt->execute();
$deleted_colors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Deleted Colors</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_categories.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Deleted Colors</h1>
        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Color Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (empty($deleted_colors)): ?>
                        <tr>
                            <td colspan="3">No deleted colors found.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $row_count = 1;
                        foreach ($deleted_colors as $deleted_color): ?>
                            <tr>
                                <td><?= $row_count++; ?></td>
                                <td><?= htmlspecialchars($deleted_color['color_name']); ?></td>
                                <td>
                                    <a href="restore_color.php?id=<?= htmlspecialchars($deleted_color['id']); ?>" class="btn">Restore</a>
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