<?php
include 'connect.php';
session_start();

// Fetch products
$products = [];
$stmt = $conn->prepare("
    SELECT p.id, p.name, c.name AS category, b.name AS brand, 
           pv.size, pv.color, pv.stock, pv.price,
           pv.image1_display, pv.image2_display, pv.image3_display, pv.image4_display,
           pv.image1_thumb, pv.image2_thumb, pv.image3_thumb, pv.image4_thumb
    FROM products p 
    JOIN categories c ON p.category = c.name 
    JOIN brand b ON p.brand = b.name
    LEFT JOIN product_variants pv ON p.id = pv.product_id
    ORDER BY p.id, pv.color
");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['add_product'])) {
    try {
        // Start a transaction
        $conn->beginTransaction();

        $product_name = $_POST['product_name'];
        $category_name = $_POST['category_name'];
        $brand_name = $_POST['brand_name'];
        $price = $_POST['price']; // Capture price

        // Insert product
        $stmt = $conn->prepare("INSERT INTO products (name, category, brand) VALUES (:name, :category, :brand)"); // Exclude price in insert
        $stmt->bindParam(':name', $product_name);
        $stmt->bindParam(':category', $category_name);
        $stmt->bindParam(':brand', $brand_name);
        $stmt->execute();
        $product_id = $conn->lastInsertId();

        // Prepare colors
        $colors = $_POST['colors'];

        // Create directories
        $thumbs_dir = "img/showcase/thumbs/shoe{$product_id}-1/";
        $showcase_dir = "img/showcase/shoe{$product_id}-1/";

        if (!is_dir($thumbs_dir)) {
            mkdir($thumbs_dir, 0777, true);
        }
        if (!is_dir($showcase_dir)) {
            mkdir($showcase_dir, 0777, true);
        }

        // Process and move uploaded images
        $thumb_image_paths = [];
        $showcase_image_paths = [];

        // Process thumbnail images
        for ($i = 1; $i <= 4; $i++) {
            $thumb_image_key = "thumb_image{$i}";
            if (isset($_FILES[$thumb_image_key]) && $_FILES[$thumb_image_key]['error'] == UPLOAD_ERR_OK) {
                $image_tmp = $_FILES[$thumb_image_key]['tmp_name'];
                $image_name = $_FILES[$thumb_image_key]['name'];
                $unique_filename = uniqid() . '_' . basename($image_name);
                $thumb_image_path = $thumbs_dir . $unique_filename;

                if (move_uploaded_file($image_tmp, $thumb_image_path)) {
                    $thumb_image_paths[] = $thumb_image_path;
                } else {
                    throw new Exception("Failed to upload thumbnail image: " . $image_name);
                }
            }
        }

        // Process showcase images
        for ($i = 1; $i <= 4; $i++) {
            $showcase_image_key = "showcase_image{$i}";
            if (isset($_FILES[$showcase_image_key]) && $_FILES[$showcase_image_key]['error'] == UPLOAD_ERR_OK) {
                $image_tmp = $_FILES[$showcase_image_key]['tmp_name'];
                $image_name = $_FILES[$showcase_image_key]['name'];
                $unique_filename = uniqid() . '_' . basename($image_name);
                $showcase_image_path = $showcase_dir . $unique_filename;

                if (move_uploaded_file($image_tmp, $showcase_image_path)) {
                    $showcase_image_paths[] = $showcase_image_path;
                } else {
                    throw new Exception("Failed to upload showcase image: " . $image_name);
                }
            }
        }

        // Insert product variants
        foreach ($colors as $color) {
            // Prepare the SQL statement
            $stmt = $conn->prepare("
                INSERT INTO product_variants (
                    product_id, color, 
                    image1_display, image2_display, image3_display, image4_display,
                    image1_thumb, image2_thumb, image3_thumb, image4_thumb,
                    price
                ) VALUES (
                    :product_id, :color, 
                    :image1_display, :image2_display, :image3_display, :image4_display,
                    :image1_thumb, :image2_thumb, :image3_thumb, :image4_thumb,
                    :price
                )
            ");

            // Prepare image paths with null fallback
            $display_images = array_pad($showcase_image_paths, 4, null);
            $thumb_images = array_pad($thumb_image_paths, 4, null);

            // Bind values
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindParam(':color', $color);
            $stmt->bindValue(':price', $price); // Bind price

            // Bind display images
            $stmt->bindValue(':image1_display', $display_images[0]);
            $stmt->bindValue(':image2_display', $display_images[1]);
            $stmt->bindValue(':image3_display', $display_images[2]);
            $stmt->bindValue(':image4_display', $display_images[3]);

            // Bind thumbnail images
            $stmt->bindValue(':image1_thumb', $thumb_images[0]);
            $stmt->bindValue(':image2_thumb', $thumb_images[1]);
            $stmt->bindValue(':image3_thumb', $thumb_images[2]);
            $stmt->bindValue(':image4_thumb', $thumb_images[3]);

            // Execute the statement
            if (!$stmt->execute()) {
                // Detailed error logging
                $errorInfo = $stmt->errorInfo();
                error_log("Failed to insert product variant. Error details: " . print_r($errorInfo, true));

                // Prepare detailed error message
                $error_details = [
                    'product_id' => $product_id,
                    'color' => $color,
                    'display_images' => $display_images,
                    'thumb_images' => $thumb_images
                ];
                error_log("Variant Insert Failure Details: " . print_r($error_details, true));

                throw new Exception("Failed to insert product variant for color: " . $color);
            }
        }

        // Commit the transaction
        $conn->commit();

        // Redirect on success
        header("Location: admin_product.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction
        $conn->rollBack();

        // Log the error
        error_log("Product addition error: " . $e->getMessage());

        // Set error message
        $error_message = "Error adding product: " . $e->getMessage();

        // Optional: Display error message to user
        echo "<div style='color:red;'>" . htmlspecialchars($error_message) . "</div>";
    }
}


// Handle editing a product
if (isset($_POST['edit_product'])) {
    try {
        // Start a transaction
        $conn->beginTransaction();

        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $category_name = $_POST['category_name'];
        $brand_name = $_POST['brand_name'];
        $price = $_POST['price'];
        $color = $_POST['color'];

        // Prepare SQL to update product
        $stmt = $conn->prepare("UPDATE products SET name = :name, category = :category, brand = :brand WHERE id = :id");
        $stmt->bindParam(':name', $product_name);
        $stmt->bindParam(':category', $category_name);
        $stmt->bindParam(':brand', $brand_name);
        $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch existing product variant
        $stmt = $conn->prepare("SELECT * FROM product_variants WHERE product_id = :id AND color = :color LIMIT 1");
        $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':color', $color);
        $stmt->execute();
        $existing_variant = $stmt->fetch(PDO::FETCH_ASSOC);

        // Prepare image paths
        $thumb_image_paths = [
            $existing_variant['image1_thumb'],
            $existing_variant['image2_thumb'],
            $existing_variant['image3_thumb'],
            $existing_variant['image4_thumb']
        ];
        $showcase_image_paths = [
            $existing_variant['image1_display'],
            $existing_variant['image2_display'],
            $existing_variant['image3_display'],
            $existing_variant['image4_display']
        ];

        // Create directories if they don't exist
        $thumbs_dir = "img/showcase/thumbs/shoe{$product_id}-{$color}/";
        $showcase_dir = "img/showcase/shoe{$product_id}-{$color}/";

        if (!is_dir($thumbs_dir)) {
            mkdir($thumbs_dir, 0777, true);
        }
        if (!is_dir($showcase_dir)) {
            mkdir($showcase_dir, 0777, true);
        }

        // Process thumbnail images
        for ($i = 1; $i <= 4; $i++) {
            $thumb_image_key = "thumb_image{$i}";
            if (isset($_FILES[$thumb_image_key]) && $_FILES[$thumb_image_key]['error'] == UPLOAD_ERR_OK) {
                $image_tmp = $_FILES[$thumb_image_key]['tmp_name'];
                $image_name = $_FILES[$thumb_image_key]['name'];
                $unique_filename = uniqid() . '_' . basename($image_name);
                $thumb_image_path = $thumbs_dir . $unique_filename;

                if (move_uploaded_file($image_tmp, $thumb_image_path)) {
                    // Remove old image if exists
                    if (!empty($thumb_image_paths[$i - 1]) && file_exists($thumb_image_paths[$i - 1])) {
                        unlink($thumb_image_paths[$i - 1]);
                    }
                    $thumb_image_paths[$i - 1] = $thumb_image_path;
                }
            }
        }

        // Process showcase images
        for ($i = 1; $i <= 4; $i++) {
            $showcase_image_key = "showcase_image{$i}";
            if (isset($_FILES[$showcase_image_key]) && $_FILES[$showcase_image_key]['error'] == UPLOAD_ERR_OK) {
                $image_tmp = $_FILES[$showcase_image_key]['tmp_name'];
                $image_name = $_FILES[$showcase_image_key]['name'];
                $unique_filename = uniqid() . '_' . basename($image_name);
                $showcase_image_path = $showcase_dir . $unique_filename;

                if (move_uploaded_file($image_tmp, $showcase_image_path)) {
                    // Remove old image if exists
                    if (!empty($showcase_image_paths[$i - 1]) && file_exists($showcase_image_paths[$i - 1])) {
                        unlink($showcase_image_paths[$i - 1]);
                    }
                    $showcase_image_paths[$i - 1] = $showcase_image_path;
                }
            }
        }

        // Update product variant with new images and price
        $stmt = $conn->prepare("
    UPDATE product_variants 
    SET price = :price, 
        image1_thumb = :image1_thumb, 
        image2_thumb = :image2_thumb, 
        image3_thumb = :image3_thumb, 
        image4_thumb = :image4_thumb,
        image1_display = :image1_display, 
        image2_display = :image2_display, 
        image3_display = :image3_display, 
        image4_display = :image4_display
    WHERE product_id = :product_id AND color = :color
");

        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image1_thumb', $thumb_image_paths[0]);
        $stmt->bindParam(':image2_thumb', $thumb_image_paths[1]);
        $stmt->bindParam(':image3_thumb', $thumb_image_paths[2]);
        $stmt->bindParam(':image4_thumb', $thumb_image_paths[3]);
        $stmt->bindParam(':image1_display', $showcase_image_paths[0]);
        $stmt->bindParam(':image2_display', $showcase_image_paths[1]);
        $stmt->bindParam(':image3_display', $showcase_image_paths[2]);
        $stmt->bindParam(':image4_display', $showcase_image_paths[3]);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':color', $_POST['color']); // Bind the color

        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        header("Location: admin_product.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction
        $conn->rollBack();

        // Log the error
        error_log("Product edit error: " . $e->getMessage());

        // Set error message
        $error_message = "Error editing product: " . $e->getMessage();

        // Optional: Display error message to user
        echo "<div style='color:red;'>" . htmlspecialchars($error_message) . "</div>";
    }
}

// Handle deletion of a product
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Fetch the product details before deletion
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch the product variants
    $stmt = $conn->prepare("SELECT * FROM product_variants WHERE product_id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();
    $variants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Insert the product into the deleted_products table
    $stmt = $conn->prepare("INSERT INTO deleted_products (product_id, name, category, brand, size, color, stock, price, image1_thumb, image2_thumb, image3_thumb, image4_thumb, image1_display, image2_display, image3_display, image4_display) VALUES (:product_id, :name, :category, :brand, :size, :color, :stock, :price, :image1_thumb, :image2_thumb, :image3_thumb, :image4_thumb, :image1_display, :image2_display, :image3_display, :image4_display)");

    // Bind values for the main product
    $stmt->bindParam(':product_id', $delete_id, PDO::PARAM_INT); // Bind the product_id
    $stmt->bindParam(':name', $product['name']);
    $stmt->bindParam(':category', $product['category']);
    $stmt->bindParam(':brand', $product['brand']);

    // Loop through each variant and insert into deleted_products
    foreach ($variants as $variant) {
        $stmt->bindParam(':size', $variant['size']);
        $stmt->bindParam(':color', $variant['color']);
        $stmt->bindParam(':stock', $variant['stock']);
        $stmt->bindParam(':price', $variant['price']);
        $stmt->bindParam(':image1_thumb', $variant['image1_thumb']);
        $stmt->bindParam(':image2_thumb', $variant['image2_thumb']);
        $stmt->bindParam(':image3_thumb', $variant['image3_thumb']);
        $stmt->bindParam(':image4_thumb', $variant['image4_thumb']);
        $stmt->bindParam(':image1_display', $variant['image1_display']);
        $stmt->bindParam(':image2_display', $variant['image2_display']);
        $stmt->bindParam(':image3_display', $variant['image3_display']);
        $stmt->bindParam(':image4_display', $variant['image4_display']);

        // Execute the statement to insert into deleted_products
        $stmt->execute();
    }

    // Now delete the product from the products table
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: admin_product.php");
    exit();
}

// Fetch categories and brands for dropdowns
$categories = [];
$brands = [];
$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM brand");
$stmt->execute();
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM color");
$stmt->execute();
$colors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_product.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <div class="btn-container page-top">
            <button class="btn-add-new" id="addProductBtn">Add New Product</button>
        </div>

        <!-- Modal for Add Product Form -->
        <div id="addProductModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeAddModal">&times;</span>
                <form method="post" enctype="multipart/form-data">
                    <div class="account-header">
                        <h1 class="account-title">Add a New Product</h1>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Product Name</label>
                            <input type="text" placeholder="Enter product name" name="product_name" required>
                        </div>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Category</label>
                            <select name="category_name" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['name']); ?>"><?= htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Brand</label>
                            <select name="brand_name" required>
                                <option value="">Select Brand</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= htmlspecialchars($brand['name']); ?>"><?= htmlspecialchars($brand['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Colors</label>
                            <select name="colors[]" multiple required>
                                <?php foreach ($colors as $color): ?>
                                    <option value="<?= htmlspecialchars($color['color_name']); ?>"><?= htmlspecialchars($color['color_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Price</label>
                            <input type="number" step="0.01" min="0" placeholder="Enter product price" name="price" required>
                        </div>
                    </div>

                    <!-- Thumbnail Images -->
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Thumbnail Images</label>
                            <div class="image-uploads">
                                <label for="thumb_image1">Image 1:</label>
                                <input type="file" name="thumb_image1" accept="image/*" required></br>
                                <label for="thumb_image2">Image 2:</label>
                                <input type="file" name="thumb_image2" accept="image/*" required></br>
                                <label for="thumb_image3">Image 3:</label>
                                <input type="file" name="thumb_image3" accept="image/*" required></br>
                                <label for="thumb_image4">Image 4:</label>
                                <input type="file" name="thumb_image4" accept="image/*" required></br>
                            </div>
                        </div>
                    </div>

                    <!-- Showcase Images -->
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Showcase Images</label>
                            <div class="image-uploads">
                                <label for="showcase_image1">Image 1:</label>
                                <input type="file" name="showcase_image1" accept="image/*" required></br>
                                <label for="showcase_image2">Image 2:</label>
                                <input type="file" name="showcase_image2" accept="image/*" required></br>
                                <label for="showcase_image3">Image 3:</label>
                                <input type="file" name="showcase_image3" accept="image/*" required></br>
                                <label for="showcase_image4">Image 4:</label>
                                <input type="file" name="showcase_image4" accept="image/*" required></br>
                            </div>
                        </div>
                    </div>

                    <div class="btn-container">
                        <button type="submit" class="btn-save" name="add_product">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal for Edit Product Form -->
        <div id="editProductModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeEditModal">&times;</span>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" id="editProductId">
                    <input type="hidden" name="color" id="editColor" required>
                    <div class="account-header">
                        <h1 class="account-title">Edit Product</h1>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Product Name</label>
                            <input type="text" placeholder="Enter product name" name="product_name" id="editProductName" required>
                        </div>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Price</label>
                            <input type="number" step="0.01" min="0" placeholder="Enter product price" name="price" id="editProductPrice" required>
                        </div>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Category</label>
                            <select name="category_name" id="editCategoryName" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['name']); ?>"><?= htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Brand</label>
                            <select name="brand_name" id="editBrandName" required>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?=
                                                    htmlspecialchars($brand['name']); ?>"><?= htmlspecialchars($brand['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Colors</label>
                            <select name="color" id="editColorName" required>
                                <?php foreach ($colors as $color): ?>
                                    <option value="<?= htmlspecialchars($color['color_name']); ?>"><?= htmlspecialchars($color['color_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Current Thumbnail Images -->
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Current Thumbnail Images</label>
                            <div class="image-uploads">
                                <img id="currentThumb1" src="" alt="Current Thumbnail 1" width="100">
                                <img id="currentThumb2" src="" alt="Current Thumbnail 2" width="100">
                                <img id="currentThumb3" src="" alt="Current Thumbnail 3" width="100">
                                <img id="currentThumb4" src="" alt="Current Thumbnail 4" width="100">
                            </div>
                            <label>New Thumbnail Images (optional)</label>
                            <div class="image-uploads">
                                <label for="thumb_image1">Image 1:</label>
                                <input type="file" name="thumb_image1" accept="image/*"></br>
                                <label for="thumb_image2">Image 2:</label>
                                <input type="file" name="thumb_image2" accept="image/*"></br>
                                <label for="thumb_image3">Image 3:</label>
                                <input type="file" name="thumb_image3" accept="image/*"></br>
                                <label for="thumb_image4">Image 4:</label>
                                <input type="file" name="thumb_image4" accept="image/*"></br>
                            </div>
                        </div>
                    </div>

                    <!-- Current Showcase Images -->
                    <div class="account-edit">
                        <div class="input-container">
                            <label>Current Showcase Images</label>
                            <div class="image-uploads">
                                <img id="currentShowcase1" src="" alt="Current Showcase 1" width="100">
                                <img id="currentShowcase2" src="" alt="Current Showcase 2" width="100">
                                <img id="currentShowcase3" src="" alt="Current Showcase 3" width="100">
                                <img id="currentShowcase4" src="" alt="Current Showcase 4" width="100">
                            </div>
                            <label>New Showcase Images (optional)</label>
                            <div class="image-uploads">
                                <label for="showcase_image1">Image 1:</label>
                                <input type="file" name="showcase_image1" accept="image/*"></br>
                                <label for="showcase_image2">Image 2:</label>
                                <input type="file" name="showcase_image2" accept="image/*"></br>
                                <label for="showcase_image3">Image 3:</label>
                                <input type="file" name="showcase_image3" accept="image/*"></br>
                                <label for="showcase_image4">Image 4:</label>
                                <input type="file" name="showcase_image4" accept="image/*"></br>
                            </div>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn-save" name="edit_product">Save</button>
                    </div>
                </form>
            </div>
        </div>

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
                    $current_product_id = null;
                    $current_color = null;
                    $row_count = 1;
                    foreach ($products as $product):
                        if ($current_product_id !== $product['id'] || $current_color !== $product['color']):
                            $current_product_id = $product['id'];
                            $current_color = $product['color']; ?>
                            <tr>
                                <td><?= $row_count++; ?></td>
                                <td><img src="<?= htmlspecialchars($product['image1_display']); ?>" alt="Product Image" width="100"></td>
                                <td><?= htmlspecialchars($product['name']); ?></td>
                                <td><?= htmlspecialchars($product['category']); ?></td>
                                <td><?= htmlspecialchars($product['brand']); ?></td>
                                <td><?= htmlspecialchars($product['color']); ?></td>
                                <td>RM <?= htmlspecialchars($product['price']); ?></td>
                                <td>
                                    <button class="btn edit-product-btn"
                                        data-id="<?= htmlspecialchars($product['id']); ?>"
                                        data-name="<?= htmlspecialchars($product['name']); ?>"
                                        data-category="<?= htmlspecialchars($product['category']); ?>"
                                        data-brand="<?= htmlspecialchars($product['brand']); ?>"
                                        data-color="<?= htmlspecialchars($product['color']); ?>"
                                        data-price="<?= htmlspecialchars($product['price']); ?>"
                                        data-thumb1="<?= htmlspecialchars($product['image1_thumb'] ?? ''); ?>"
                                        data-thumb2="<?= htmlspecialchars($product['image2_thumb'] ?? ''); ?>"
                                        data-thumb3="<?= htmlspecialchars($product['image3_thumb'] ?? ''); ?>"
                                        data-thumb4="<?= htmlspecialchars($product['image4_thumb'] ?? ''); ?>"
                                        data-showcase1="<?= htmlspecialchars($product['image1_display'] ?? ''); ?>"
                                        data-showcase2="<?= htmlspecialchars($product['image2_display'] ?? ''); ?>"
                                        data-showcase3="<?= htmlspecialchars($product['image3_display'] ?? ''); ?>"
                                        data-showcase4="<?= htmlspecialchars($product['image4_display'] ?? ''); ?>">
                                        Edit
                                    </button>
                                    <a href="?delete_id=<?= htmlspecialchars($product['id']); ?>" class="btn">Delete</a>
                                </td>
                            </tr>
                    <?php endif;
                    endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

<script src="assets/js/admin_product.js"></script>

</html>