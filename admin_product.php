<?php
include 'connect.php';

session_start();

//Fetch categories
$categories = [];

$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$brands = [];
$stmt = $conn->prepare("SELECT * FROM brand");
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sizes = [];
$stmt = $conn->prepare("SELECT * FROM sizes");
$stmt->execute();
$sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch products with variants
$products = [];
$stmt = $conn->prepare("
    SELECT p.*, 
           c.name AS category_name, 
           b.name AS brand_name, 
           pv.color, 
           pv.image1_display,
           pv.price AS variant_price 
    FROM products p
    JOIN categories c ON p.id = c.id
    JOIN brand b ON p.id = b.id
    JOIN product_variants pv ON p.id = pv.product_id
");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" type="text/css" href="assets/css/admin_product.css">

</head>

<body>

    <div class=" container">

        <div class="admin-product-form-container">

            <form method="post" enctype="multipart/form-data">
                <h3>Add a New Product</h3>
                <input type="text" placeholder="Enter product name" name="product_name" class="box" required>


                <select name="product_category" class="box" required>
                    <option value="" disabled selected>Select Product Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id']); ?>"><?= htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="product_brand" class="box" required>
                    <option value="" disabled selected>Select Product Brand</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= htmlspecialchars($brand['id']); ?>"><?= htmlspecialchars($brand['name']); ?></option>
                    <?php endforeach; ?>
                </select>


                <select name="product_size" class="box" required>
                    <option value="" disabled selected>Select Product Size</option>
                    <?php foreach ($sizes as $size): ?>
                        <option value="<?= htmlspecialchars($size['id']); ?>"><?= htmlspecialchars($size['size']); ?></option> <!-- Displaying the size -->
                    <?php endforeach; ?>
                </select>


                <input type="number" placeholder="Enter product price" name="product_price" class="box" required>
                <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box" required>
                <input type="submit" class="btn" name="add_product" value="Add Product">
            </form>

        </div>

        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Product Category</th>
                        <th>Product Brand</th>
                        <th>Product Color</th>
                        <th>Product Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($product['image1_display']); ?>" alt="Product Image" width="50"></td>
                            <td><?= htmlspecialchars($product['name']); ?></td>
                            <td><?= htmlspecialchars($product['category_name']); ?></td>
                            <td><?= htmlspecialchars($product['brand_name']); ?></td>
                            <td><?= htmlspecialchars($product['color']); ?></td>
                            <td><?= htmlspecialchars($product['variant_price']); ?></td>
                            <td>
                                <a href="edit_product.php?id= <?= htmlspecialchars($product['id']); ?>" class="btn">Edit</a>
                                <a href="delete_product.php?id=<?= htmlspecialchars($product['id']); ?>" class="btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>

</html>