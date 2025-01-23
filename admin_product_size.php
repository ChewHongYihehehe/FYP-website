<?php
include 'connect.php';
session_start();

// Fetch products and their sizes
$product_sizes = [];
$stmt = $conn->prepare("
    SELECT p.id AS product_id, p.name AS product_name, c.name AS category, b.name AS brand, 
           pv.id AS variant_id, pv.size, pv.color, pv.stock, pv.price,
           pv.image1_display, pv.image2_display, pv.image3_display, pv.image4_display,
           pv.image1_thumb, pv.image2_thumb, pv.image3_thumb, pv.image4_thumb
    FROM products p 
    JOIN categories c ON p.category = c.name 
    JOIN brand b ON p.brand = b.name
    LEFT JOIN product_variants pv ON p.id = pv.product_id
    ORDER BY p.id, pv.color, pv.size
");
$stmt->execute();
$product_sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle adding a new size
if (isset($_POST['add_size'])) {
    $product_id = $_POST['product_id'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];

    // Fetch existing images and price for the product and color
    $stmt = $conn->prepare("
    SELECT image1_display, image2_display, image3_display, image4_display,
           image1_thumb, image2_thumb, image3_thumb, image4_thumb,
           price
    FROM product_variants
    WHERE product_id = :product_id AND color = :color
");
    $stmt->bindParam(':product_id', $product_id);
    $stmt->bindParam(':color', $color);
    $stmt->execute();
    $images = $stmt->fetch(PDO::FETCH_ASSOC); // Corrected variable name

    // Insert new size with the fetched images and price
    $stmt = $conn->prepare("
    INSERT INTO product_variants (product_id, size, color, stock, price,
        image1_display, image2_display, image3_display, image4_display,
        image1_thumb, image2_thumb, image3_thumb, image4_thumb)
    VALUES (:product_id, :size, :color, :stock, :price,
        :image1_display, :image2_display, :image3_display, :image4_display,
        :image1_thumb, :image2_thumb, :image3_thumb, :image4_thumb)
");
    $stmt->bindParam(':product_id', $product_id);
    $stmt->bindParam(':size', $size);
    $stmt->bindParam(':color', $color);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindValue(':price', $images['price'] ?? null); // Bind the price

    // Bind the images, using null if they don't exist
    $stmt->bindValue(':image1_display', $images['image1_display'] ?? null);
    $stmt->bindValue(':image2_display', $images['image2_display'] ?? null);
    $stmt->bindValue(':image3_display', $images['image3_display'] ?? null);
    $stmt->bindValue(':image4_display', $images['image4_display'] ?? null);
    $stmt->bindValue(':image1_thumb', $images['image1_thumb'] ?? null);
    $stmt->bindValue(':image2_thumb', $images['image2_thumb'] ?? null);
    $stmt->bindValue(':image3_thumb', $images['image3_thumb'] ?? null);
    $stmt->bindValue(':image4_thumb', $images['image4_thumb'] ?? null);

    $stmt->execute();

    header("Location: admin_product_size.php");
    exit();
}

// Handle editing sizes
if (isset($_POST['edit_size'])) {
    $size_id = $_POST['size_id'];
    $size = $_POST['size'];
    $stock = $_POST['stock'];

    $stmt = $conn->prepare("
        UPDATE product_variants SET size = :size, stock = :stock WHERE id = :id
    ");
    $stmt->bindParam(':size', $size);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':id', $size_id);

    if ($stmt->execute()) {
        header("Location: admin_product_size.php");
        exit();
    } else {
        // Handle error
        echo "Error updating size.";
    }
}


// Handle deletion of a size
if (isset($_GET['delete_id']) && isset($_GET['size'])) {
    $delete_id = $_GET['delete_id'];
    $size = $_GET['size'];


    // Fetch the size details before deletion
    $stmt = $conn->prepare("SELECT * FROM product_variants WHERE id = :id AND size = :size");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->bindParam(':size', $size);
    $stmt->execute();
    $size = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($size) {
        // Insert the size details into the deleted_product_size table
        $stmt = $conn->prepare("
            INSERT INTO deleted_product_sizes(product_id, size, color, stock, price, deleted_at)
            VALUES (:product_id, :size, :color, :stock, :price, NOW())
        ");
        $stmt->bindParam(':product_id', $size['product_id'], PDO::PARAM_INT);
        $stmt->bindParam(':size', $size['size']);
        $stmt->bindParam(':color', $size['color']);
        $stmt->bindParam(':stock', $size['stock'], PDO::PARAM_INT);
        $stmt->bindParam(':price', $size['price']);
        $stmt->execute();

        // Check how many sizes exist for this product
        $stmt = $conn->prepare("SELECT COUNT(*) FROM product_variants WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $size['product_id'], PDO::PARAM_INT);
        $stmt->execute();
        $size_count = $stmt->fetchColumn();

        // Delete the size variant
        $stmt = $conn->prepare("DELETE FROM product_variants WHERE id = :id");
        $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: admin_product_size.php");
        exit();
    }
}


// Fetch products for the dropdown
$products = [];
$stmt = $conn->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch colors for the dropdown
$colors = [];
$stmt = $conn->prepare("SELECT * FROM color");
$stmt->execute();
$colors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle fetching sizes for the edit modal
$sizes = [];
if (isset($_GET['edit_id'])) {
    $product_id = $_GET['edit_id'];
    $stmt = $conn->prepare("
        SELECT id, size, stock FROM product_variants WHERE product_id = :product_id
    ");
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    $sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Product Sizes</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_product.css">
    <style>
        .size-container {
            width: 100%;
        }

        .size-box {
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            padding: 10px;
        }

        .size-content {
            display: flex;
            flex-direction: column;
        }

        .size-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }

        .size-item:last-child {
            border-bottom: none;
        }

        .size-item .size {
            font-weight: bold;
        }

        .size-item .stock {
            color: #666;
            font-size: 0.9em;
            margin-left: 10px;
        }

        .size-item .edit-size-btn {
            width: auto;
            /* Set width to auto for the button */
            padding: 5px 10px;
            /* Adjust padding for a smaller button */
            font-size: 0.9em;
            /* Adjust font size if needed */
            margin-right: 0px;
        }

        .size-item .delete-size-btn {
            width: auto;
            /* Set width to auto for the button */
            padding: 5px 10px;
            /* Adjust padding for a smaller button */
            font-size: 0.9em;
            /* Adjust font size if needed */
            margin-left: 0px;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Image</th>
                        <th>Sizes and Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $processed_combinations = [];
                    $row_count = 1;

                    foreach ($product_sizes as $variant):
                        $unique_key = $variant['product_id'] . '_' . $variant['color'];

                        if (isset($processed_combinations[$unique_key])) {
                            continue;
                        }

                        $processed_combinations[$unique_key] = true;
                    ?>
                        <tr>
                            <td><?= $row_count++; ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($variant['image1_display']); ?>" alt="Product Image" width="100">
                            </td>
                            <td>
                                <div class="size-container">
                                    <div class="size-box">
                                        <div class="size-content">
                                            <?php
                                            // Initialize $current_sizes as an empty array
                                            $current_sizes = array_filter($product_sizes, function ($size) use ($variant) {
                                                return $size['product_id'] == $variant['product_id'] &&
                                                    $size['color'] == $variant['color'];
                                            });

                                            // Check if $current_sizes is not empty before proceeding
                                            if (!empty($current_sizes)) {
                                                usort($current_sizes, function ($a, $b) {
                                                    return strnatcmp($a['size'], $b['size']);
                                                });

                                                foreach ($current_sizes as $size): ?>
                                                    <div class="size-item">
                                                        <span class="size">Size(UK) : <?= htmlspecialchars($size['size']); ?></span>
                                                        <span class="stock">(<?= htmlspecialchars($size['stock']); ?> in stock)</span>
                                                        <button class="btn edit-size-btn"
                                                            data-id="<?= htmlspecialchars($variant['variant_id']); ?>"
                                                            data-product-id="<?= htmlspecialchars($variant['product_id']); ?>"
                                                            data-color="<?= htmlspecialchars($variant['color']); ?>"
                                                            data-size="<?= htmlspecialchars($size['size']); ?>"
                                                            data-stock="<?= htmlspecialchars($size['stock']); ?>"> <!-- Add stock data -->
                                                            Edit
                                                        </button>
                                                        <a href="?delete_id=<?= htmlspecialchars($size['variant_id']); ?>&size=<?= htmlspecialchars($size['size']); ?>" class="btn delete-size-btn">Delete</a>
                                                    </div>
                                            <?php endforeach;
                                            } else {
                                                echo "<div>No sizes available for this product.</div>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button class="btn add-size-btn"
                                    data-product-id="<?= htmlspecialchars($variant['product_id']); ?>"
                                    data-product-name="<?= htmlspecialchars($variant['product_name']); ?>"
                                    data-color="<?= htmlspecialchars($variant['color']); ?>">
                                    Add Size
                                </button>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Add Size Form -->
    <div id="addSizeModal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeAddSizeModal">&times;</span>
            <form method="post">
                <input type="hidden" name="product_id" id="addProductId">
                <input type="hidden" name="color" id="addColor">
                <div class="account-header">
                    <h1 class="account-title">Add a New Size</h1>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Product</label>
                        <span id="addProductName"></span>
                    </div>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Color</label>
                        <span id="addColorDisplay"></span>
                    </div>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Size</label>
                        <input type="text" placeholder="Enter size" name="size" required>
                    </div>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Stock</label>
                        <input type="number" placeholder="Enter stock" name="stock" required>
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn-save" name="add_size">Add Size</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Edit Size Form -->
    <div id="editSizeModal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeEditSizeModal">&times;</span>
            <form method="post">
                <input type="hidden" name="size_id" id="editSizeId">
                <div class="account-header">
                    <h1 class="account-title">Edit Size</h1>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Size</label>
                        <input type="text" name="size" id="editSize" required>
                    </div>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Stock</label>
                        <input type="number" name="stock" id="editStock" required>
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn-save" name="edit_size">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/admin_product_size.js"></script>
</body>

</html>