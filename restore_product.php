<?php
include 'connect.php';
session_start();
if (isset($_GET['id'])) {
    $restore_id = $_GET['id'];
    echo "Restoring product with ID: " . htmlspecialchars($restore_id); // Debugging line

    // Fetch the deleted product details
    $stmt = $conn->prepare("SELECT * FROM deleted_products WHERE id = :id");
    $stmt->bindParam(':id', $restore_id, PDO::PARAM_INT);
    $stmt->execute();
    $deleted_product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($deleted_product) {
        // Check if the product already exists in the products table
        $stmt = $conn->prepare("SELECT * FROM products WHERE name = :name AND category = :category AND brand = :brand");
        $stmt->bindParam(':name', $deleted_product['name']);
        $stmt->bindParam(':category', $deleted_product['category']);
        $stmt->bindParam(':brand', $deleted_product['brand']);
        $stmt->execute();
        $existing_product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing_product) {
            // Insert the product back into the products table if it doesn't exist
            $stmt = $conn->prepare("INSERT INTO products (name, category, brand) VALUES (:name, :category, :brand)");
            $stmt->bindParam(':name', $deleted_product['name']);
            $stmt->bindParam(':category', $deleted_product['category']);
            $stmt->bindParam(':brand', $deleted_product['brand']);
            $stmt->execute();
            $new_product_id = $conn->lastInsertId(); // Get the new product ID


        } else {
            // If the product already exists, use the existing product ID
            $new_product_id = $existing_product['id'];
        }

        // Now restore all variants from deleted_product
        $stmt = $conn->prepare("SELECT * FROM deleted_products WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $deleted_product['product_id']); // Use the product_id from deleted_products
        $stmt->execute();
        $variants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($variants as $variant) {
            $stmt = $conn->prepare("INSERT INTO product_variants (product_id, size, color, stock, price, image1_thumb, image2_thumb, image3_thumb, image4_thumb, image1_display, image2_display, image3_display, image4_display) VALUES (:product_id, :size, :color, :stock, :price, :image1_thumb, :image2_thumb, :image3_thumb, :image4_thumb, :image1_display, :image2_display, :image3_display, :image4_display)");

            // Bind values for the variants
            $stmt->bindParam(':product_id', $new_product_id);
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

            // Execute the statement to restore the variant
            $stmt->execute();
        }

        // Now delete the product from the deleted_products table
        $stmt = $conn->prepare("DELETE FROM deleted_products WHERE product_id = :product_id AND color = :color");
        $stmt->bindParam(':product_id', $deleted_product['product_id'], PDO::PARAM_INT);
        $stmt->bindParam(':color', $variant['color']);
        $stmt->execute();

        header("Location: admin_restore_products.php");
        exit();
    } else {
        echo "No deleted product found with ID: " . htmlspecialchars($restore_id); // Debugging line
    }
} else {
    echo "No ID provided for restoration."; // Debugging line
}
