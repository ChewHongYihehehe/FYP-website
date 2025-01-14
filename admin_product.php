<?php

include 'connect.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Add Product
    if ($action === 'add') {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $brand = $_POST['brand'];
        $size = $_POST['size'];
        $color = $_POST['color'];
        $stock = $_POST['stock'];
        $price = $_POST['price'];

        $stmt = $pdo->prepare("INSERT INTO products (name, categories, brand) VALUES (?, ?, ?)");
        $stmt->execute([$name, $category, $brand]);
        $product_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO product_variants (product_id, size, color, stock, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$product_id, $size, $color, $stock, $price]);

        echo "Product added successfully!";
    }

    // Delete Product
    if ($action === 'delete') {
        $product_id = $_POST['product_id'];
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$product_id]);
        $pdo->prepare("DELETE FROM product_variants WHERE product_id = ?")->execute([$product_id]);

        echo "Product deleted successfully!";
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin - Manage Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        form {
            margin: 20px 0;
        }

        button {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h1>Admin - Manage Products</h1>

    <!-- Add Product Form -->
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <label>Name:</label> <input type="text" name="name" required><br>
        <label>Category:</label> <input type="text" name="category" required><br>
        <label>Brand:</label> <input type="text" name="brand" required><br>
        <label>Size:</label> <input type="text" name="size" required><br>
        <label>Color:</label> <input type="text" name="color" required><br>
        <label>Stock:</label> <input type="number" name="stock" required><br>
        <label>Price:</label> <input type="number" step="0.01" name="price" required><br>
        <button type="submit">Add Product</button>
    </form>

    <!-- Products Table -->
    <h2>Products</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Brand</th>
            <th>Size</th>
            <th>Color</th>
            <th>Stock</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['categories']) ?></td>
                <td><?= htmlspecialchars($product['brand']) ?></td>
                <td><?= htmlspecialchars($product['size']) ?></td>
                <td><?= htmlspecialchars($product['color']) ?></td>
                <td><?= $product['stock'] ?></td>
                <td>$<?= number_format($product['price'], 2) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" onclick="return confirm('Delete this product?')">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>