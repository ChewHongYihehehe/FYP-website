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
        // Insert the product back into the products table
        $stmt = $conn->prepare("INSERT INTO products (name, category, brand) VALUES (:name, :category, :brand)");
        $stmt->bindParam(':name', $deleted_product['name']);
        $stmt->bindParam(':category', $deleted_product['category']);
        $stmt->bindParam(':brand', $deleted_product['brand']);
        $stmt->execute();
        $new_product_id = $conn->lastInsertId(); // Get the new product ID

        // Now restore the variants
        $stmt = $conn->prepare("INSERT INTO product_variants (product_id, size, color, stock, price, image1_thumb, image2_thumb, image3_thumb, image4_thumb, image1_display, image2_display, image3_display, image4_display) VALUES (:product_id, :size, :color, :stock, :price, :image1_thumb, :image2_thumb, :image3_thumb, :image4_thumb, :image1_display, :image2_display, :image3_display, :image4_display)");

        // Bind values for the variants
        $stmt->bindParam(':product_id', $new_product_id);
        $stmt->bindParam(':size', $deleted_product['size']);
        $stmt->bindParam(':color', $deleted_product['color']);
        $stmt->bindParam(':stock', $deleted_product['stock']);
        $stmt->bindParam(':price', $deleted_product['price']);
        $stmt->bindParam(':image1_thumb', $deleted_product['image1_thumb']);
        $stmt->bindParam(':image2_thumb', $deleted_product['image2_thumb']);
        $stmt->bindParam(':image3_thumb', $deleted_product['image3_thumb']);
        $stmt->bindParam(':image4_thumb', $deleted_product['image4_thumb']);
        $stmt->bindParam(':image1_display', $deleted_product['image1_display']);
        $stmt->bindParam(':image2_display', $deleted_product['image2_display']);
        $stmt->bindParam(':image3_display', $deleted_product['image3_display']);
        $stmt->bindParam(':image4_display', $deleted_product['image4_display']);

        // Execute the statement to restore the variants
        $stmt->execute();

        // Now delete the product from the deleted_products table
        $stmt = $conn->prepare("DELETE FROM deleted_products WHERE id = :id");
        $stmt->bindParam(':id', $restore_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: admin_restore_products.php");
        exit();
    } else {
        echo "No deleted product found with ID: " . htmlspecialchars($restore_id); // Debugging line
    }
} else {
    echo "No ID provided for restoration."; // Debugging line
}
