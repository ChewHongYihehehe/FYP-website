<?php
include 'connect.php';
session_start();

// Fetch deleted sizes
$stmt = $conn->prepare("SELECT * FROM deleted_sizes");
$stmt->execute();
$deleted_sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Deleted Sizes</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_categories.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Deleted Sizes</h1>
        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Size Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deleted_sizes)): ?>
                        <tr>
                            <td colspan="2">No deleted sizes found.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $row_count = 1;
                        foreach ($deleted_sizes as $deleted_size): ?>
                            <tr>
                                <td><?= $row_count++; ?></td>
                                <td><?= htmlspecialchars($deleted_size['size']); ?></td>
                                <td>
                                    <a href="restore_size.php?id=<?= htmlspecialchars($deleted_size['id']); ?>" class="btn">Restore</a>
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