<?php
include 'connect.php';
session_start();

if (isset($_GET['id'])) {
    $restore_id = $_GET['id'];

    // Fetch the deleted size details
    $stmt = $conn->prepare("SELECT * FROM deleted_product_sizes WHERE id = :id");
    $stmt->bindParam(':id', $restore_id, PDO::PARAM_INT);
    $stmt->execute();
    $deleted_size = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($deleted_size) {
        // Check if the price is null
        $price = $deleted_size['price'];

        // If price is null, fetch the current price from the product_variants table
        if (is_null($price)) {
            $stmt = $conn->prepare("SELECT price FROM product_variants WHERE product_id = :product_id AND size = :size AND color = :color");
            $stmt->bindParam(':product_id', $deleted_size['product_id']);
            $stmt->bindParam(':size', $deleted_size['size']);
            $stmt->bindParam(':color', $deleted_size['color']);
            $stmt->execute();
            $current_price = $stmt->fetch(PDO::FETCH_ASSOC);
            $price = $current_price['price'] ?? 0; // Default to 0 if no price found
        }

        // Fetch the images from the product_variants table based on product_id and color
        $stmt = $conn->prepare("SELECT image1_display, image2_display, image3_display, image4_display,
                                        image1_thumb, image2_thumb, image3_thumb, image4_thumb
                                 FROM product_variants 
                                 WHERE product_id = :product_id AND color = :color");
        $stmt->bindParam(':product_id', $deleted_size['product_id']);
        $stmt->bindParam(':color', $deleted_size['color']);
        $stmt->execute();
        $images = $stmt->fetch(PDO::FETCH_ASSOC);

        // Prepare to insert the size back into the product_variants table, including images
        $stmt = $conn->prepare("
            INSERT INTO product_variants (product_id, size, color, stock, price,
                image1_display, image2_display, image3_display, image4_display,
                image1_thumb, image2_thumb, image3_thumb, image4_thumb)
            VALUES (:product_id, :size, :color, :stock, :price,
                :image1_display, :image2_display, :image3_display, :image4_display,
                :image1_thumb, :image2_thumb, :image3_thumb, :image4_thumb)
        ");
        $stmt->bindParam(':product_id', $deleted_size['product_id']);
        $stmt->bindParam(':size', $deleted_size['size']);
        $stmt->bindParam(':color', $deleted_size['color']);
        $stmt->bindParam(':stock', $deleted_size['stock']);
        $stmt->bindParam(':price', $price); // Use the fetched or default price

        // Use temporary variables to avoid passing by reference issues
        $image1_display = $images['image1_display'] ?? null;
        $image2_display = $images['image2_display'] ?? null;
        $image3_display = $images['image3_display'] ?? null;
        $image4_display = $images['image4_display'] ?? null;
        $image1_thumb = $images['image1_thumb'] ?? null;
        $image2_thumb = $images['image2_thumb'] ?? null;
        $image3_thumb = $images['image3_thumb'] ?? null;
        $image4_thumb = $images['image4_thumb'] ?? null;

        // Bind the image fields
        $stmt->bindParam(':image1_display', $image1_display);
        $stmt->bindParam(':image2_display', $image2_display);
        $stmt->bindParam(':image3_display', $image3_display);
        $stmt->bindParam(':image4_display', $image4_display);
        $stmt->bindParam(':image1_thumb', $image1_thumb);
        $stmt->bindParam(':image2_thumb', $image2_thumb);
        $stmt->bindParam(':image3_thumb', $image3_thumb);
        $stmt->bindParam(':image4_thumb', $image4_thumb);

        // Execute the insert
        $stmt->execute();

        // Now delete the size from the deleted_product _sizes table
        $stmt = $conn->prepare("DELETE FROM deleted_product_sizes WHERE id = :id");
        $stmt->bindParam(':id', $restore_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: admin_restore_product_size.php"); // Redirect back to the restore sizes page
        exit();
    }
}
