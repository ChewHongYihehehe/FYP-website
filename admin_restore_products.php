<?php
include 'connect.php';
session_start();
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